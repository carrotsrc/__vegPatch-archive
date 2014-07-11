<?php
/* Copyright 2014, Charlie Fyvie-Gauld (Carrotsrg.org)
 *
 * This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/.
 */
	/*
	*   This plugin is used to resolve the location
	*   from area/layout format
	*/

	class getlocPlugin extends Plugin
	{
		public function init($instance)
		{
			$this->instance = $instance;
		}

		public function process(&$signal)
		{
			if(!isset($_GET['loc'])) {
				if(!isset($_GET['cpl'])) {
					$uid = Session::get('uid');
					if($uid == null) {
						$v = $this->getConfig('nu_redir');
						if($v == null)
							$_GET['loc'] = "web";
						else
							$_GET['loc'] = $v;
					}
					else
						$_GET['loc'] = "home";
				}
				else
					return $this->getCmptLocation($signal);
			}

			return $this->getIndexLocation($signal);
		}

		private function getIndexLocation(&$signal)
		{
	
			$location = $_GET['loc'];
			$layout = 0;

			$atoms = explode('/', $location);
			$signal['area'] = $atoms[0];
			if(sizeof($atoms) > 1 && $atoms[1] != "")
				$layout = $atoms[1];
			else {
				$layout = Managers::ResourceManager()->queryAssoc("Layout(){r}<Area('{$atoms[0]}'):index;");
				if(!$layout) {
					KLog::error("Failed to find index for area");
					$epage = $this->getConfig("e404");
					if($epage == null)
						return false;

					$atoms = explode("/", $epage);

					$signal['area'] = $atoms[0];
					$layout = Managers::ResourceManager()->queryAssoc("Layout('{$atoms[1]}'){r}<Area('{$atoms[0]}');");
					if(!$layout)
						return false;
				}
				$layout = $layout[0][1];
			}

			$signal['layout'] = $layout;

			$sz = sizeof($atoms);
			$signal['lcr'] = array_slice($atoms, 2);
			return $signal;
		}

		private function getCmptLocation(&$signal)
		{
			$location = $_GET['cpl'];
			$atoms = explode('/', $location);
			if(sizeof($atoms) != 4)
				return false;

			$signal['area'] = $atoms[0];
			$signal['cmpt'] = $atoms[1];
			$signal['inst'] = $atoms[2];
			$signal['jack'] = $atoms[3];
			return $signal;
		}

		public function getConfigList()
		{
			return array("e404", "nu_redir");
		}
	}

?>
