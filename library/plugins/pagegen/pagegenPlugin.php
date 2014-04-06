<?php
/* (C)opyright 2014, Carrotsrc.org
 *
 * This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/.
 */
	/*
	*   This plugin generates a page
	*   from a template and layout
	*/

	class pagegenPlugin extends Plugin
	{
		public function init($instance)
		{
			$this->instance = $instance;
		}

		public function process(&$params)
		{
			if(!isset($params['area']))
				return false;

			$area = $params['area'];
			unset($params['area']);

			$layout = null;
			$assets = null;
			$page = null;

			if(!get_class($area))
				return false;

			if(isset($params['layout'])) {
				$layout = $params['layout'];
				unset($params['layout']);
			}
			if(isset($params['assets'])) {
				$assets = $params['assets'];
				unset($params['assets']);
			}
			$layoutParams = new CBlank();
			$layoutParams->app = new CBlank();

			$layoutParams->app->title = $params['root']['title'];
			$layoutParams->onload = "";
			$layoutParams->assets = $assets;
			$layoutParams->nodym = false;
			if(($nodym = Session::get('nodym')) != null) {
				if($nodym == 1)
					$layoutParams->nodym = true;
			}

			if(isset($params['onload']) && is_array($params['onload'])) {
				foreach($params['onload'] as $param) {
					$layoutParams->onload .= $param;
				}
			}

			if(isset($params['globvar'])) {
				foreach($params['globvar'] as $k => $v) {
					unset($_GET[$k]);
					$layout->addGlobalParam($k, $v);
				}
			}

			if(($page = $this->generatePage($area, $layout, $assets, $layoutParams)) == null)
				return false;

			$params['page'] = $page;
			return $params;
		}

		private function generatePage($area, $layout, $assets, $layoutParams)
		{
			$page = new Page();
			$page->setDatabase($this->db);
			$page->setArea($area);

			$layoutParams->app->layout = $layout->generateHTML();
			$page->setLayoutParams($layoutParams);
			$layoutParams->onload .= $layout->generateComInt();
			
			return $page;
		}
	}

?>
