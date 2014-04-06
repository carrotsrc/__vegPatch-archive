<?php
/* (C)opyright 2014, Carrotsrc.org
 *
 * This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/.
 */
	include_once("area.php");
	
	class AreaMan
	{
		private $db = null;

		public function __construct($dbconnection)
		{
			$this->db = $dbconnection;
		}

		public function getArea($area)
		{
			$query = null;

			if(is_numeric($area))
				$query = "SELECT * FROM areapool WHERE id='$area';";
			else
				$query = "SELECT * FROM areapool WHERE name='$area';";

			$result = $this->db->sendQuery($query);
			if(!$result)
				return null;
				
			if(mysql_num_rows($result) == 0)
				return null;
				
			return $this->generateArea(mysql_fetch_assoc($result));
		}

		private function generateArea($assoc)
		{
			return new Area($assoc['id'], $assoc['name'], $assoc['s_id'], $assoc['st_id']);
		}
	}
?>
