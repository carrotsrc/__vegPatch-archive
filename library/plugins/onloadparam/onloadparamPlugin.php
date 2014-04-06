<?php
/* (C)opyright 2014, Carrotsrc.org
 *
 * This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/.
 */
	class onloadparamPlugin extends Plugin
	{
		public function init($instance)
		{
			if($instance == null)
				return false;

			$this->setInstance($instance);
		}

		public function process(&$params = null)
		{
			$onloadParams = "KitJS.addGlobalParam(\"glob\",\"hello\");\n";
			if(isset($params['onload']))
				if(!is_array($params['onload']))
					$params['onload'] = array();

			$params['onload'][]= $onloadParams;
			return $params;
		}
	}
?>
