<?php
/* (C)opyright 2014, Carrotsrc.org
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

		public function process(&$params)
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
					return $this->getCmptLocation($params);
			}

			return $this->getIndexLocation($params);
		}

		private function getIndexLocation(&$params)
		{
	
			$location = $_GET['loc'];
			$layout = 0;

			$atoms = explode('/', $location);
			$params['area'] = $atoms[0];
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

					$params['area'] = $atoms[0];
					$layout = Managers::ResourceManager()->queryAssoc("Layout('{$atoms[1]}'){r}<Area('{$atoms[0]}');");
					if(!$layout)
						return false;
				}
				$layout = $layout[0][1];
			}

			$params['layout'] = $layout;

			$sz = sizeof($atoms);
			$params['lcr'] = array_slice($atoms, 2);
			return $params;
		}

		private function getCmptLocation(&$params)
		{
			$location = $_GET['cpl'];
			$atoms = explode('/', $location);
			if(sizeof($atoms) != 4)
				return false;

			$params['area'] = $atoms[0];
			$params['cmpt'] = $atoms[1];
			$params['inst'] = $atoms[2];
			$params['jack'] = $atoms[3];
			return $params;
		}

		public function getConfigList()
		{
			return array("e404", "nu_redir");
		}
	}

?>
