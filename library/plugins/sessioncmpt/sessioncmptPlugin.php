<?php
/* Copyright 2014, Charlie Fyvie-Gauld (Carrotsrg.org)
 *
 * This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/.
 */
	class sessioncmptPlugin extends Plugin
	{
		private $loadedComponents;
		private $resourceManager;
		private $channelManager;

		public function init($instance)
		{
			$this->instance = $instance;
			$this->loadedComponents = Session::get('scmpt');

			if($this->loadedComponents == null)
				$this->loadedComponents = array();

			$this->resourceManager = Managers::ResourceManager();
			$this->channelManager = Managers::ChannelManager();
		}

		public function process(&$params)
		{
			$wireframe = $params['layout'];
			if($wireframe == null)
				return $params;

			if(get_class($wireframe) != 'Wireframe')
				return $params;

			$assets = $this->setSession($wireframe->getHeader(), $params);

			if($assets->getNumAssets() > 0) {
				if(isset($params['assets']))
					$params['assets']->addAssetArray($assets->getAssets());
				else
					$params['assets'] = $assets;
			}

			return $params;
		}

		private function setSession($header, &$params)
		{
			$area = $params['area']->getId();
			$layout = $params['layout']->getId();
			$assetHolder = new AssetHolder();
			$cbuf = array('js' => array(), 'css' => array());
			$cml = null;
			if(isset($_GET['_cml'])) {
				$cml = explode('/', $_GET['_cml']) ;
			}
			// generate the panel and save the cid and ins of cmpt
			// since these have already passed relationship checks
			// and can be accessed by ajax without checking the
			// relationships again.
			foreach($header as $panel)
			{
				$cid = $panel->getComponentId();
				$inst = $panel->getRef();
				$ref = $inst<<(PHP_INT_SIZE<<2);
				$ref ^= $area;
				
				if(!isset($this->loadedComponents[$cid]))
				{
					// Not in session at all
					$rq = "Channel()<Component('$cid');";
					if(!$this->runChannel($rq, $panel, $params))
						continue;

					$rq = "Channel()<(Instance('$inst')<Component('$cid'));";
					if(!$this->runChannel($rq, $panel, $params))
						continue;

					$this->loadedComponents[$cid][] = $ref;
				}
				else
				if(isset($this->loadedComponents[$cid]) && !in_array($ref, $this->loadedComponents[$cid])){
					// Reference not in session yet
					$rq = "Channel()<(Instance('$inst')<Component('$cid'));";
					if(!$this->runChannel($rq, $panel, $params))
						continue;

					$this->loadedComponents[$cid][] = $ref;
				}
					

				$pId = $panel->getPanelId();
				if($pId == "")
					continue;

				$pObj = ModMan::getPanel($pId, $this->db);
				if($pObj == null)
					continue;

				$pId = $pObj->getModuleId();
				$panel->setPanel($pObj);

				// Set the crud mode if it is there
				// is one set
				if($cml != null) {
					if($panel->getPanelId() == $cml[0])
						$pObj->setCrudMode($cml[1]);
				}

				// init panel here. Any problem?
				$pObj->initialize($params);
				// Add panel to the session for getting assets
				if(!isset($this->loadedComponents[$pId]))
					$this->loadedComponents[$pId]['_load'] = 1;

				// Add assets and flag panel so they are not added again
				// Each panel has a common interface so only needs that
				// to be called once
				if(!isset($this->loadedComponents[$pId]['_a'])) {
					$paths = $pObj->getAssetPaths('js');
					$this->loadAssetPaths($area, $layout, $pId, $paths, $cbuf['js']);
					$paths = $pObj->getAssetPaths('css');
					$this->loadAssetPaths($area, $layout, $pId, $paths, $cbuf['css']);

					$this->loadedComponents[$pId]['_a'] = 1;
				}

			}
			// Clear out asset loaded flag
			foreach($this->loadedComponents as $key => $value)
				if($key == 0 || isset($this->loadedComponents[$key]['_load']))
					unset($this->loadedComponents[$key]);

			$this->cache($cbuf, $params);
			Session::set('scmpt', $this->loadedComponents);

			// add the asset links to the params
			$params['assets'] = array('js'=>array(), 'css'=>array());
			$params['assets']['js'][] = ALinkGen::generateBatchLinkJS($area, $layout);
			$params['assets']['css'][] = ALinkGen::generateBatchLinkCSS($area, $layout);

			return $assetHolder;
		}

		private function cache($assets, &$params)
		{
			if(!isset($params['acache']))
				$params['acache'] = array('js' => array(), 'css' => array());

			$cache = &$params['acache'];
			foreach($assets as $t => $list)
				foreach($list as $p)
					$cache[$t][] = array($p[0], $p[1], $p[2], $p[3]['value']);
		}

		private function loadAssetPaths($area, $layout, $panel, $paths, &$cache)
		{
			$dup = false;
			foreach($paths as $p) {
				$dup = false;
				foreach($cache as &$a) {
					if($p['name'] == $a[3]['name']) {
						$dup = true;
						$a[2] = 0; // make it shared
					}
				}

				if(!$dup)
					$cache[] = array($area, $layout, $panel, $p);
			}

		}

		private function runChannel($rql, &$panel, &$params)
		{
			$chlist = $this->resourceManager->queryAssoc($rql);
			if(is_array($chlist)) {
				$cid = $this->resourceManager->getHandlerRef($chlist[0][0]);
				$channel = $this->channelManager->getChannel($cid);
				$params['_pnl'] = &$panel;
				if(!$channel->runSignal($params)) {
					unset($params['_pnl']);
					return false;
				}

				unset($params['_pnl']);
			}

			return true;
		}
	}
?>
