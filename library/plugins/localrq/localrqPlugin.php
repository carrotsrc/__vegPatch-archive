<?php
/* Copyright 2014, Charlie Fyvie-Gauld (Carrotsrg.org)
 *
 * This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/.
 */
	/*
	*  This plugin runs a component
	*  query while panel is server side.
	*  this means that the panel can fill
	*  out the data is needs before dispatch.
	*  It also means that it is a similar
	*  process to the ajax queries.
	*/
	class localrqPlugin extends Plugin
	{
		private $resourceManager = null;
		private $channelManager;
		private $modules;
		public function init($instance)
		{
			$this->instance = $instance;
			$this->resourceManager = Managers::ResourceManager();
			$this->channelManager = Managers::ChannelManager();
			$this->modules = array();
		}

		public function process(&$signal)
		{
			$wireframe = $signal['layout'];
			if($wireframe == null)
				return $signal;

			if(get_class($wireframe) != 'Wireframe')
				return $signal;

			return $this->makeRequests($wireframe->getHeader(), $signal);
		}

		private function makeRequests($header, $signal)
		{
			foreach($header as $panel) {
				$requests = $panel->getComponentRequests();
				if($requests == null)
					continue;
				$signal = $this->runInstanceChannel($panel->getComponentId(),
								    $panel->getInstanceId(),
								    $signal);
				if(!$signal)
					continue;

				foreach($requests as $key => $rq) {
					$result = $this->runRequest($rq, $signal);
					if(!$result)
						continue;
					$requests[$key] = $result;
				}
				$panel->applyRequest($requests);
			}

			return $signal;
		}

		private function runRequest($rq, &$signal)
		{
			$jint= $this->checkJackInterface($rq);
			if($jint != null) {
				$signal = $this->runInterfaceChannel($jint->getId(), $signal);
				if(!$signal)
					return false;
			}
			return $this->runComponentSignal($rq, $signal);
		}

		private function runInstanceChannel($cmpt, $cinst, $signal)
		{
			/* TODO
			*  Check to see if channel has been run
			*/
			$rq = "Channel()<(Instance('$cinst')<Component('$cmpt'));";
			$res = $this->resourceManager->queryAssoc($rq);
			if(!$res)
				return $signal;

			$cid = $res[0][0];
			$channel = $this->channelManager->getChannel($cid);
			if($channel == null)
				return false;

			return $channel->runSignal($signal);
		}

		private function runInterfaceChannel($interface, $signal)
		{
			$rq = "Channel()<Interface('$interface');";
			$res = $this->resourceManager->queryAssoc($rq);
			if($res == null)
				return $signal;

			$cid = $res[0][0];
			$channel = $this->channelManager->getChannel($cid);
			if($channel == null)
				return false;

			$signal = $channel->runSignal($signal);
			return $signal;
		}

		private function checkJackInterface($rq)
		{
			$rql = "Interface()<Instance('{$rq['inst']}')&Component('{$rq['cmpt']}');";

			$res = $this->resourceManager->queryAssoc($rql);

			if(!$res)
				return null;

			$jid = array();

			foreach($res as $intr)
				$jid[] = $this->resourceManager->getHandlerRef($intr[0]);
			
			$jint = ModMan::getInterface($jid, $rq['jack'], $this->db);
			return $jint;
		}

		private function runComponentSignal($rq, &$signal)
		{
			$module = null;
			foreach($this->modules as $mod)
				if($mod[0] == $rq['cmpt'] && $mod[1] == $rq['inst'])
					$module = $mod[2];

			if($module == null) {
				$module = ModMan::getComponent($rq['cmpt'], $rq['inst'], $this->db);
				$this->modules[] = array($rq['cmpt'], $rq['inst'], $module);
			}

			if($module == null)
				return false;
			$module->initialize();

			$result = $module->run($rq['jack'], $rq['params']);
			$signal['rid'] = $module->getRio();
			$rq['result'] = $result;
			$this->runCrudOps($rq['cmpt'], $rq['inst'], $signal);
			return $rq;
		}

		private function runCrudOps($cmpt, $inst, &$signal)
		{
			$ridc = $this->resourceManager->queryAssoc("CrudOps()<(Instance('$inst')<Component('$cmpt'));");
			if(!$ridc)
				return;

			$ref = $this->resourceManager->getHandlerRef($ridc[0][0]);
			$channel = $this->channelManager->getChannel($ref);
			$channel->runSignal($signal);
		}
	}
?>
