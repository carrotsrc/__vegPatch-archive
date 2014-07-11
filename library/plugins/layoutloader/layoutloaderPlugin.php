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

		public function process(&$signal)
		{
			if(!isset($signal['layout'])) 
				return false;

			if($signal['layout'] == null)
				return $signal;

			if(!is_numeric($signal['layout'])) {
				if(!($id = $this->resManager->queryAssoc("Layout('{$signal['layout']}'){r};"))) {
					KLog::error("Layout resource does not exist");
					if(!$this->errorPage($signal))
						return false;

				}
				else
					$signal['layout'] = $id[0][1];
			}

			$wireframe = Managers::LayoutManager()->loadLayout($signal['layout']);
			if($wireframe == null) {
				KLog::error("Failed to load wireframe");
				return false;
			}

			$wireframe->setId($signal['layout']);

			$signal['layout'] = $wireframe;

			return $signal;
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

			$layout = Managers::ResourceManager()->queryAssoc("Layout('{$atoms[1]}'){r}<Area('{$atoms[0]}');");
			if(!$layout)
				return false;

			$signal['layout'] = $layout[0][1]; // just temporarily attach the error layout to the area
			return $signal;
		}
	}

?>
