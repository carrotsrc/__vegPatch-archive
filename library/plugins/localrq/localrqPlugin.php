<?php
/* (C)opyright 2014, Carrotsrc.org
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

		public function process(&$params)
		{
			$wireframe = $params['layout'];
			if($wireframe == null)
				return $params;

			if(get_class($wireframe) != 'Wireframe')
				return $params;

			return $this->makeRequests($wireframe->getHeader(), $params);
		}

		private function makeRequests($header, $params)
		{
			foreach($header as $panel) {
				$requests = $panel->getComponentRequests();
				if($requests == null)
					continue;
				$params = $this->runInstanceChannel($panel->getComponentId(),
								    $panel->getInstanceId(),
								    $params);
				if(!$params)
					continue;

				foreach($requests as $key => $rq) {
					$result = $this->runRequest($rq, $params);
					if(!$result)
						continue;
					$requests[$key] = $result;
				}
				$panel->applyRequest($requests);
			}

			return $params;
		}

		private function runRequest($rq, &$params)
		{
			$jint= $this->checkJackInterface($rq);
			if($jint != null) {
				$params = $this->runInterfaceChannel($jint->getId(), $params);
				if(!$params)
					return false;
			}
			return $this->runComponentSignal($rq, $params);
		}

		private function runInstanceChannel($cmpt, $cinst, $params)
		{
			/* TODO
			*  Check to see if channel has been run
			*/
			$rq = "Channel()<(Instance('$cinst')<Component('$cmpt'));";
			$res = $this->resourceManager->queryAssoc($rq);
			if(!$res)
				return $params;

			$cid = $res[0][0];
			$channel = $this->channelManager->getChannel($cid);
			if($channel == null)
				return false;

			return $channel->runSignal($params);
		}

		private function runInterfaceChannel($interface, $params)
		{
			$rq = "Channel()<Interface('$interface');";
			$res = $this->resourceManager->queryAssoc($rq);
			if($res == null)
				return $params;

			$cid = $res[0][0];
			$channel = $this->channelManager->getChannel($cid);
			if($channel == null)
				return false;

			$params = $channel->runSignal($params);
			return $params;
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

		private function runComponentSignal($rq, &$params)
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
			$params['rid'] = $module->getRio();
			$rq['result'] = $result;
			$this->runCrudOps($rq['cmpt'], $rq['inst'], $params);
			return $rq;
		}

		private function runCrudOps($cmpt, $inst, &$params)
		{
			$ridc = $this->resourceManager->queryAssoc("CrudOps()<(Instance('$inst')<Component('$cmpt'));");
			if(!$ridc)
				return;

			$ref = $this->resourceManager->getHandlerRef($ridc[0][0]);
			$channel = $this->channelManager->getChannel($ref);
			$channel->runSignal($params);
		}
	}
?>
