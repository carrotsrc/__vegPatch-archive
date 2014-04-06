<?php
/* (C)opyright 2014, Carrotsrc.org
 *
 * This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/.
 */

	class QStringModifier 
	{
		static public function modifyParams($params = null, $nolink = false)
		{
			$newParams = $_GET;

			if($params != null)
				foreach($params as $key => $value) {
					if($value == null)
						unset($newParams[$key]);
					else
						$newParams[$key] = $value;
				}

			$qstr = http_build_query($newParams);
			if($nolink)
				return $qstr;
			else
				return self::generateNewLink($qstr);
		}

		static public function generateNewLink($qstring)
		{
			$script = $_SERVER['SCRIPT_NAME'];
			$server = $_SERVER['SERVER_NAME'];
			$url = "$script?$qstring";
			return $url;
		}
	}
?>
