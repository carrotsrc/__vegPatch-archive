<?php
/* (C)opyright 2014, Carrotsrc.org
 *
 * This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/.
 */
	class bblogLibrary extends DBAcc
	{
		public function __construct($database)
		{
			$this->db = $database;
		}
		
		public function getLastEntryId($instance)
		{
			$res = $this->db->sendQuery("SELECT lid FROM kbasic_bblog_post WHERE instance='$instance' ORDER BY lid DESC LIMIT 1;", false, false);
			if(!$res)
				return 0;

			return $res[0][0];
		}

		public function addEntry($instance, $title, $contents)
		{
			$lid = $this->getLastEntryId($instance);
			$lid++;
			if(!$this->arrayInsert('kbasic_bblog_post', array('instance' => $instance,
									'lid' => $lid,
									'title' => $title,
									'contents' => $contents)))
				return false;

			return $lid;
		}

		public function getEntry($instance, $id)
		{
			$res = $this->db->sendQuery("SELECT title, contents FROM kbasic_bblog_post WHERE instance='$instance' AND lid='$id';", false, false);
			if(!$res)
				return false;

			return $res;
		}

		public function getEntries($instance, $count = null)
		{
			$sql = "SELECT title, contents FROM kbasic_bblog_post WHERE instance='$instance'";
			if($count != null)

			$res = $this->db->sendQuery("SELECT title, contents FROM kbasic_bblog_post WHERE instance='$instance'", false, false);
			if(!$res)
				return false;

			return $res;
		}
	}
?>
