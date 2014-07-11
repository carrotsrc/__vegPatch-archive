<?php
/* Copyright 2014, Charlie Fyvie-Gauld (Carrotsrg.org)
 *
 * This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/.
 */
	class LibLoad
	{
		static private $list = array();
		static public function shared($space, $lib)
		{
			return SystemConfig::libraryPath("lib/$space/{$lib}Library.php");
		}

		static public function component($space, $component)
		{

		}

		static public function trial($space, $lib)
		{
			if(include(SystemConfig::libraryPath("lib/$space/{$lib}Library.php"))) {
				$this->list[] = array($space, $lib);
			}
		}
	}
?>
