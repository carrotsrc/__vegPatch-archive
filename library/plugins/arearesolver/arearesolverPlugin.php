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

		public function process(&$signal)
		{
			if(!isset($signal['area'])) {
				return false;
			}

			$area = null;
			$prid = null;
			$srid = Session::get('aid');
			$area = $signal['area'];
			$rid = $this->resolveArea($area);
			if($rid == null) {
				KLog::error("Unable to resolve area");
				// redirect to the error page
				if(!$this->errorPage($signal))
					return false;

				// reresolve everything
				$area = $signal['area'];
				$rid = $this->resolveArea($area);
			}

			if($srid != null) {
				if($srid == $rid) {
					// Session is already in the area
					if(!($signal = $this->runChannel($area, $signal)))
						return false;

					$areaObj = Managers::AreaManager()->getArea($area);
					if($areaObj == null)
						return false;

					$signal['area'] = $areaObj;
					return $signal;
				}
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
			$this->apCache($areaObj->getSurround(), intval($areaObj->getId()), $signal);
			return $signal;
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

		private function runChannel($ref, $signal)
		{
			$r = $this->resourceManager->queryAssoc("Channel(){r}<Area('$ref');");
			if(!$r) 
				return $signal;

			$ref = $r[0][1];
			$channel = $this->channelManager->getChannel($ref);
			if($channel == null)	// There are no channel nodes
				return $signal;

			return $channel->runSignal($signal);
		}

		private function apCache($sId, $aId, &$signal)
		{
			$assets = SurroundMan::getAssetPaths($sId, $this->db);
			if($assets == null)
				return;
			if(!isset($signal['acache']))
				$signal['acache'] = array('js' => array(), 'css' => array());

			$cache = &$signal['acache'];

			foreach($assets as $t => $list)
				foreach($list as $p)
					$cache[$t][] = array($aId, 0, 0, $p);

		}

		public function getConfigList()
		{
			return array("e404");
		}

		private function errorPage(&$signal)
		{
			$epage = $this->getConfig("e404");
			if($epage == null)
				return false;

			$atoms = explode("/", $epage);

			$signal['area'] = $atoms[0];
			$layout = Managers::ResourceManager()->queryAssoc("Layout('{$atoms[1]}'){r}<Area('{$atoms[0]}');");
			if(!$layout)
				return false;

			$signal['layout'] = $layout[0][1];
			return $signal;
		}
	}

?>
