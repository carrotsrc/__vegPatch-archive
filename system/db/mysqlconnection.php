<?php
/* Copyright 2014, Charlie Fyvie-Gauld (Carrotsrg.org)
 *
 * This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/.
 */

	class mysqlConnection extends DBConnection
	{
		public function __construct()
		{
		
		}

		public function connect($username, $password)
		{
			$this->connection = new mysqli("localhost", $username, $password);
			if($this->connection->connect_errno)
				return false;
			return true;
		}

		public function connectAtServer($server, $username, $password)
		{
			$this->connection = new mysqli($server, $username, $password);
			if($this->connection->connect_errno)
				return false;
			return true;
		}

		public function selectDatabase($database)
		{
			return $this->connection->select_db($database);
		}

		public function sendQuery($query)
		{
			if($query == null || $query == "")
				return false;

			$r =  $this->connection->query($query);

			if($r === false || $r === true)
				return $r;

			if(!$r->num_rows) {
				$r->close();
				return false;
			}

			$rows = array();
			while(($t = $r->fetch_assoc()) != NULL)
				$rows[] = $t;
			$r->close();
			return $rows;
		}
		
		public function getLastId()
		{
			return $this->connection->insert_id;
		}
	}
?>
