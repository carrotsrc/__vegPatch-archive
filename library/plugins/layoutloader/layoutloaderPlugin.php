<?php
/* Copyright 2014, Charlie Fyvie-Gauld (Carrotsrg.org)
 *
 * This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/.
 */
	/*
	*   This plugin is used to resolve the layout
	*   from the id given. if the param layout is null
	*   that means that no layout is needed
	*/

	class layoutloaderPlugin extends Plugin
	{
		private $resManager;
		public function init($instance)
		{
			$this->instance = $instance;
			$this->resManager = Managers::ResourceManager();
		}

		public function process(&$params)
		{
			if(!isset($params['layout'])) 
				return false;

			if($params['layout'] == null)
				return $params;

			if(!is_numeric($params['layout'])) {
				$id = $this->resManager->queryAssoc("Layout('{$params['layout']}');");
				if(!$id) {
					KLog::error("Layout resource does not exist");
					return false;
				}

				$params['layout'] = $this->resManager->getHandlerRef($id[0][0]);
			}

			$wireframe = $this->loadWireframe($params['layout']);
			if($wireframe == null) {
				KLog::error("Failed to load wireframe");
				return false;
			}

			$wireframe->setId($params['layout']);

			$params['layout'] = $wireframe;

			return $params;
		}

		private function loadWireframe($id)
		{
			return Managers::LayoutManager()->loadLayout($id);
		}
	}

?>
