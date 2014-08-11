<?php
/* Copyright 2014, Charlie Fyvie-Gauld (Carrotsrg.org)
 *
 * This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/.
 */
	require_once("wireframegenerator.php");
	class LayMan extends DBAcc
	{
		public function __construct($databaseConnection)
		{
			$this->db = $databaseConnection;
		}

		public function loadLayout($id)
		{
			if($this->db == null)
				return null;

			$sql = "SELECT `name`, `cml` FROM `layoutpool` WHERE `id`='$id';";
			$result = $this->db->sendQuery($sql);

			if(!$result)
				return null;

			if(!isset($result[0]['name']))
				return null;

			$wgen = new WireframeGenerator();
			$wireframe = $wgen->processCML($result[0]['cml']);
			return $wireframe;
		}
	}
?>
