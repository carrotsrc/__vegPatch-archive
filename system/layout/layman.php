<?php
/* (C)opyright 2014, Carrotsrc.org
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

			$sql = "SELECT name, cml FROM layoutpool WHERE id = '$id';";
			$result = $this->db->sendQuery($sql, false, false);

			if(!$result)
				return null;

			if(!isset($result[0][1]))
				return null;

			$wgen = new WireframeGenerator();
			$wireframe = $wgen->processCML($result[0][1]);
			return $wireframe;
		}
	}
?>
