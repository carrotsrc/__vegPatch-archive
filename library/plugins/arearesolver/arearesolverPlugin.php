<?php
/* (C)opyright 2014, Carrotsrc.org
 *
 * This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/.
 */
	class arearesolverPlugin extends Plugin 
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
			if($rid == null) {
				KLog::error("Unable to resolve area");
				return false;
			}

			if($srid != null) {
				if($srid == $rid) {
					// Session is already in the area
					if(!($params = $this->runChannel($area, $params)))
						return false;

					$areaObj = Managers::AreaManager()->getArea($area);
					if($areaObj == null)
						return false;

					$params['area'] = $areaObj;
					return $params;
				}
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
			$this->apCache($areaObj->getSurround(), intval($areaObj->getId()), $params);
			return $params;
		}

		private function resolveArea($area)
		{
			$rid = 0;
			$rq = "Area('$area');";
			$res = null;
			if(!($res = $this->resourceManager->queryAssoc($rq)))
				return null;

			$rid = $res[0][0];

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

		private function apCache($sId, $aId, &$params)
		{
			$assets = SurroundMan::getAssetPaths($sId, $this->db);
			if($assets == null)
				return;
			if(!isset($params['acache']))
				$params['acache'] = array('js' => array(), 'css' => array());

			$cache = &$params['acache'];

			foreach($assets as $t => $list)
				foreach($list as $p)
					$cache[$t][] = array($aId, 0, 0, $p);

		}
	}

?>
