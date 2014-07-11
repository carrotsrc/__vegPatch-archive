<?php
/* Copyright 2014, Charlie Fyvie-Gauld (Carrotsrg.org)
 *
 * This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/.
 */
	class AreasLibrary extends DBAcc
	{
		public function __construct($database)
		{
			$this->db = $database;
		}

		public function addArea($name, $surround, $template)
		{
			if(!$this->arrayInsert('areapool', array('name' => $name,
								's_id' => $surround, 
								'st_id' => $template)))
				return null;

			return $this->db->getLastId();			
		}

		public function removeArea($id)
		{
			return $this->db->sendQuery("DELETE FROM areapool WHERE id='$id';");
		}

		public function getAreaDetails($id)
		{
			$result = $this->db->sendQuery("SELECT name, s_id, st_id FROM areapool WHERE id='$id';", false, false);
			if(!$result)
				return null;

			return $result[0];
		}

		public function updateArea($id, $name, $surround, $template)
		{
			$details = array();

			if($name != null)
				$details['name'] = $name;

			if($surround != null)
				$details['s_id'] = $surround;


			if($template != null)
				$details['st_id'] = $template;

			return $this->arrayUpdate('areapool', $details, "id='$id'");
		}
	}
?>
