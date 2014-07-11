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
			$this->connection = mysql_connect("localhost", $username, $password);
			if($this->connection == false)
				return false;
			
			return true;
		}
		
		public function connectAtServer($server, $username, $password)
		{
			$this->connection = mysql_connect($server, $username, $password);
			if($this->connection == false)
				return false;

			return true;
		}

		public function selectDatabase($database)
		{
			return mysql_select_db($database);
		}

		public function sendQuery($query, $raw = true, $assoc = true)
		{
			if($query == null || $query == "")
				return false;

			if($query[strlen($query)-1] != ';')
				$query .= ';';

			$result =  mysql_query($query);

			if(!$result)
				return false;

			if($result === true)
				return true;

			if(!mysql_num_rows($result))
				return false;
			
			
			if($raw)
				return $result;
				
			$rArray = array();
			if(!$assoc)
				while(($row = mysql_fetch_row($result)) != null)
					$rArray[] = $row;
			else
				while(($row = mysql_fetch_assoc($result)) != null)
					$rArray[] = $row;
			
			return $rArray;
		}
		
		public function getLastId()
		{
			return mysql_insert_id();
		}		
	}
?>
