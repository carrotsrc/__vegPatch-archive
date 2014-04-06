<?php
/* (C)opyright 2014, Carrotsrc.org
 *
 * This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/.
 */

	class QStringModifier 
	{
		static public function modifyParams($params)
		{
			$newParams = $_GET;
			foreach($params as $key => $value)
				$newParams[$key] = $value

			$qstr = http_build_query($newParams);
			
		}
	}
?>
