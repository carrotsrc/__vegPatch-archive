<?php
/* Copyright 2014, Charlie Fyvie-Gauld (Carrotsrg.org)
 *
 * This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/.
 */
	/*
	*   This plugin loads a component
	*   and interfaces with it's jack
	*/
	class cmptintPlugin extends Plugin
	{
		private $resourceManager = null;
		private $channelManager = null;
		private $moduleManager = null;

		public function init($instance)
		{
			$this->instance = $instance;
			$this->resourceManager = Managers::ResourceManager();
			$this->channelManager = Managers::ChannelManager();
		}

		public function process(&$params)
		{
			if(!isset($params['inst']))
				return false;

			$instance = $params['inst'];

			if(!isset($params['cmpt']))
				return false;

			$component = $params['cmpt'];

			if(!isset($params['jack']))
				return false;

			$jack = $params['jack'];

			$ref= $instance<<(PHP_INT_SIZE<<2);
			$ref ^= $params['area']->getId();

			$scmpt = Session::get('scmpt');
			if($scmpt == null) {
//				header("HTTP/1.0 403 Forbidden");
				return false;
			}

			if(isset($scmpt[$component]) && !in_array($ref, $scmpt[$component])) {
//				header("HTTP/1.0 403 Forbidden");
				return false;
			}


			$params = $this->runInstanceChannel($component, $instance, $params);
			if(!$params)
				return false;

			$jint = $this->checkJackInterface($component, $instance, $jack);
			if($jint != null) {
				$params = $this->runInterfaceChannel($jint->getId(), $params);
				if(!$params)
					return false;
			}

			return $this->runComponentSignal($params);
		}

		private function runInstanceChannel($cmpt, $cinst, $params)
		{
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

		private function checkJackInterface($component, $instance, $jack)
		{
			$rq = "Interface()<(Instance('$instance')<Component('$component'));";

			$res = $this->resourceManager->queryAssoc($rq);
			if(!$res)
				return null;
			$jid = array();

			foreach($res as $intr)
				$jid[] = $this->resourceManager->getHandlerRef($intr[0]);

			$jint = ModMan::getInterface($jid, $jack, $this->db);
			return $jint;
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

			return $channel->runSignal($params);
		}

		private function runComponentSignal($params)
		{
			$module = ModMan::getComponent($params['cmpt'], $params['inst'], $this->db);
			if($module == null)
				return false;
			$module->initialize();

			$args = null;
			if(isset($_GET['args']))
				$args = $_GET['args'];
			ob_start();
				$module->run($params['jack'], $args);
			$result = ob_get_contents();
			ob_end_clean();

			$params['response'] = $result;
			$params['rid'] = $module->getRio();
			
			return $params;
		}
	}
?>
