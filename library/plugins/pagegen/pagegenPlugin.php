<?php
/* Copyright 2014, Charlie Fyvie-Gauld (Carrotsrg.org)
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

		public function process(&$signal)
		{
			if(!isset($signal['area']))
				return false;
			$area = $signal['area'];
			unset($signal['area']);

			$layout = null;
			$assets = null;
			$page = null;

			if(!get_class($area))
				return false;

			if(isset($signal['layout'])) {
				$layout = $signal['layout'];
				unset($signal['layout']);
			}
			if(isset($signal['assets'])) {
				$assets = $signal['assets'];
				unset($signal['assets']);
			}
			$layoutParams = new CBlank();
			$layoutParams->app = new CBlank();

			$layoutParams->app->title = $signal['root']['title'];
			$layoutParams->onload = "";
			$layoutParams->assets = $assets;
			$layoutParams->nodym = false;
			if(($nodym = Session::get('nodym')) != null) {
				if($nodym == 1)
					$layoutParams->nodym = true;
			}

			if(isset($signal['onload']) && is_array($signal['onload'])) {
				foreach($signal['onload'] as $param) {
					$layoutParams->onload .= $param;
				}
			}

			if(isset($signal['globvar'])) {
				foreach($signal['globvar'] as $k => $v) {
					unset($_GET[$k]);
					$layout->addGlobalParam($k, $v);
				}
			}

			if(($page = $this->generatePage($area, $layout, $assets, $layoutParams)) == null)
				return false;

			$signal['page'] = $page;
			return $signal;
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
