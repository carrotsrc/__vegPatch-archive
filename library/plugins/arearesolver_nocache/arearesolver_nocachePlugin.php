<?php
/* Copyright 2014, Charlie Fyvie-Gauld (Carrotsrg.org)
 *
 * This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/.
 */
	class arearesolver_nocachePlugin extends Plugin
	{
		private $resourceManager;
		private $channelManager;

		public function init($instance)
		{
			if($instance == null)
				return false;

			$this->setInstance($instance);
			$this->resourceManager = Managers::ResourceManager();
			$this->channelManager = Managers::ChannelManager();
		}

		public function process(&$signal)
		{
			if(!isset($signal['area']))
				return false;

			$area = null;
			$prid = null;
			$srid = Session::get('aid');
			$area = $signal['area'];
			$rid = $this->resolveArea($area);
			if($rid == null)
				return false;

			if($srid != null) {
				/*if($srid == $rid) {
					// Session is already in the area
					$areaObj = Managers::AreaManager()->getArea($area);
					if($areaObj == null)
						return false;

					$signal['area'] = $areaObj;
					return $signal;
				}*/
			}
			
			while($prid != $rid) {
				$prid = $rid;
				if(!($signal = $this->runChannel($area, $signal)))
					return false;
				$area = $signal['area'];
				$rid = $this->resolveArea($area);
			}

			if($rid == null)
				return false;

			if($srid != $rid) {
				// reset the session
				Session::uset('sessioncmpt');
				Session::set('aid', $rid);
			}


			$areaObj = Managers::AreaManager()->getArea($area);
			if($areaObj == null)
				return false;

			$signal['area'] = $areaObj;
			return $signal;
		}

		private function resolveArea($area)
		{
			$rid = 0;
			if(!is_numeric($area)) {
				$rq = "Area('$area');";
				$res = null;
				if(!($res = $this->resourceManager->queryAssoc($rq)))
					return null;

				$rid = $res[0][0];
			}
			else
				$rid = $this->resourceManager->getRID('1', $area);

			if(!$rid)
				return null;

			return $rid; 
		}

		private function runChannel($ref, $signal)
		{
			$rq = "Channel()<Area('$ref');";
			$r = $this->resourceManager->queryAssoc($rq);
			if(!$r) 
				return $signal;

			$cid = $r[0][0];
			$ref = $this->resourceManager->getHandlerRef($cid);
			$channel = $this->channelManager->getChannel($ref);
			if($channel == null)	// There are no channel nodes
				return $signal;

			return $channel->runSignal($signal);
		}

	}
?>
