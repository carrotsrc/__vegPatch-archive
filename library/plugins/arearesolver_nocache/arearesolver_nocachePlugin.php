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

		public function process(&$params)
		{
			if(!isset($params['area']))
				return false;

			$area = null;
			$prid = null;
			$srid = Session::get('aid');
			$area = $params['area'];
			$rid = $this->resolveArea($area);
			if($rid == null)
				return false;

			if($srid != null) {
				/*if($srid == $rid) {
					// Session is already in the area
					$areaObj = Managers::AreaManager()->getArea($area);
					if($areaObj == null)
						return false;

					$params['area'] = $areaObj;
					return $params;
				}*/
			}
			
			while($prid != $rid) {
				$prid = $rid;
				if(!($params = $this->runChannel($area, $params)))
					return false;
				$area = $params['area'];
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

			$params['area'] = $areaObj;
			return $params;
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

		private function runChannel($ref, $params)
		{
			$rq = "Channel()<Area('$ref');";
			$r = $this->resourceManager->queryAssoc($rq);
			if(!$r) 
				return $params;

			$cid = $r[0][0];
			$ref = $this->resourceManager->getHandlerRef($cid);
			$channel = $this->channelManager->getChannel($ref);
			if($channel == null)	// There are no channel nodes
				return $params;

			return $channel->runSignal($params);
		}

	}
?>
