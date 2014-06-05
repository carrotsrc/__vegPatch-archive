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
			if(!isset($params['area'])) {
				return false;
			}

			$area = null;
			$prid = null;
			$srid = Session::get('aid');
			$area = $params['area'];
			$rid = $this->resolveArea($area);
			if($rid == null) {
				KLog::error("Unable to resolve area");
				// redirect to the error page
				if(!$this->errorPage($params))
					return false;

				// reresolve everything
				$area = $params['area'];
				$rid = $this->resolveArea($area);
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
			$r = $this->resourceManager->queryAssoc("Channel(){r}<Area('$ref');");
			if(!$r) 
				return $params;

			$ref = $r[0][1];
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

		public function getConfigList()
		{
			return array("e404");
		}

		private function errorPage(&$params)
		{
			$epage = $this->getConfig("e404");
			if($epage == null)
				return false;

			$atoms = explode("/", $epage);

			$params['area'] = $atoms[0];
			$layout = Managers::ResourceManager()->queryAssoc("Layout('{$atoms[1]}'){r}<Area('{$atoms[0]}');");
			if(!$layout)
				return false;

			$params['layout'] = $layout[0][1];
			return $params;
		}
	}

?>
