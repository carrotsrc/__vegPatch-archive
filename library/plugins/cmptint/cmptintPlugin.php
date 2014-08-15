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
		private $moduleManager = null;

		public function init($instance)
		{
			$this->instance = $instance;
			$this->resourceManager = Managers::ResourceManager();
		}

		public function process(&$signal)
		{
			if(!isset($signal['inst']))
				return false;

			$instance = $signal['inst'];

			if(!isset($signal['cmpt']))
				return false;

			$component = $signal['cmpt'];

			if(!isset($signal['jack']))
				return false;

			$jack = $signal['jack'];

			$ref= $instance<<(PHP_INT_SIZE<<2);
			$ref ^= $signal['area']->getId();

			$scmpt = Session::get('scmpt');
			if($scmpt == null) {
//				header("HTTP/1.0 403 Forbidden");
				return false;
			}

			if(isset($scmpt[$component]) && !in_array($ref, $scmpt[$component])) {
//				header("HTTP/1.0 403 Forbidden");
				return false;
			}


			$signal = $this->runInstanceChannel($component, $instance, $signal);
			if(!$signal)
				return false;

			$jint = $this->checkJackInterface($component, $instance, $jack);
			if($jint != null) {
				$signal = $this->runInterfaceChannel($jint->getId(), $signal);
				if(!$signal)
					return false;
			}

			return $this->runComponentSignal($signal);
		}

		private function runInstanceChannel($cmpt, $cinst, $signal)
		{
			$rq = "Channel()<(Instance('$cinst')<Component('$cmpt'));";
			$res = $this->resourceManager->queryAssoc($rq);
			if(!$res)
				return $signal;

			$cid = $res[0][0];
			$channel = core_get_channel($cid, $this->db);
			if($channel == null)
				return false;

			return $channel->runSignal($signal);
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

		private function runInterfaceChannel($interface, $signal)
		{
			$rq = "Channel()<Interface('$interface');";
			$res = $this->resourceManager->queryAssoc($rq);
			if($res == null)
				return $signal;

			$cid = $res[0][0];
			$channel = core_get_channel($cid, $this->db);
			if($channel == null)
				return false;

			return $channel->runSignal($signal);
		}

		private function runComponentSignal($signal)
		{
			$module = ModMan::getComponent($signal['cmpt'], $signal['inst'], $this->db);
			if($module == null)
				return false;
			$module->initialize();

			$args = null;
			if(isset($_GET['args']))
				$args = $_GET['args'];
			ob_start();
				$module->run($signal['jack'], $args);
			$result = ob_get_contents();
			ob_end_clean();

			$signal['response'] = $result;
			$signal['rid'] = $module->getRio();
			
			return $signal;
		}
	}
?>
