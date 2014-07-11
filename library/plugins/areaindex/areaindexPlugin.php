<?php
/* Copyright 2014, Charlie Fyvie-Gauld (Carrotsrg.org)
 *
 * This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/.
 */
	class areaindexPlugin extends Filter
	{
		public function init($instance)
		{
			if($instance == null)
				return false;

			$this->setInstance($instance);
		}

		public function process(&$signal = null)
		{
			$msgBox = array();
			$result = null;
			$area = null;

			if(($area = Session::get('aid')) == null) {
				$msgBox[] = array('o'=>"App", 'm' => "die Area ID not defined");
				return $this->rtn(true, $msgBox);
			}
			
			$rql = "Layout()<(Instance()^Index()&Area('$area'));";
			
			if(!($result = Managers::ResourceManager()->queryAssoc($rql))) {
				$msgBox[] = array('o'=>"App", 'm' => "cmd cl -1");
				return $this->rtn(true, $msgBox);
			}

			$href = Managers::ResourceManager()->getHandlerRef($result[0][0]);
			$msgBox[] = array('o'=>"App", 'm' => "cmd cl ".$href);
			return $this->rtn(true, $msgBox);
			
		}
	}
?>
