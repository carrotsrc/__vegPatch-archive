<?php
/* (C)opyright 2014, Carrotsrc.org
 *
 * This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/.
 */
	class LayoutLibrary extends DBAcc
	{
		public function __construct($database)
		{
			$this->db = $database;
		}

		public function addLayout($name, $markup)
		{
			if(!$this->arrayInsert('layoutpool', array( 'name' => $name, 'cml' => $markup)))
				return false;

			return $this->db->getLastId();
		}

		public function removeLayout($id)
		{
			return $this->db->sendQuery("DELETE FROM layoutpool WHERE id='$id';");
		}

		public function updateLayout($id, $name, $markup)
		{
			return $this->arrayUpdate('layoutpool', array( 'name' => $name, 'cml' => $markup));
		}

		public function getLayout($id)
		{
			$res = null;
			if(!($res = $this->db->sendQuery("SELECT * FROM layoutpool WHERE id='$id';", false, false)))
				return false;

			return $res[0];
		}
	}
?>
