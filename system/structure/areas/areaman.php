<?php
/* Copyright 2014, Charlie Fyvie-Gauld (Carrotsrg.org)
 *
 * This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/.
 */
	include_once("area.php");

	function core_get_area($area, $db)
	{
			$query = null;

			if(is_numeric($area))
				$query = "SELECT * FROM `areapool` WHERE id='$area';";
			else
				$query = "SELECT * FROM `areapool` WHERE name='$area';";

			$result = $db->sendQuery($query);
			if(!$result)
				return null;
				
			if(mysql_num_rows($result) == 0)
				return null;
				
			return new Area($result['id'], $result['name'], $result['s_id'], $result['st_id']);
	}
?>
