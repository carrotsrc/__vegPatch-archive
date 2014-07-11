<?php
/* Copyright 2014, Charlie Fyvie-Gauld (Carrotsrg.org)
 *
 * This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/.
 */

	abstract class DBConnection
	{
		protected $connection;
		
		public function __construct()
		{
		
		}
		
		abstract public function connect($username, $password);
		abstract public function connectAtServer($server, $username, $password);
		abstract public function selectDatabase($database);
		abstract public function sendQuery($query, $raw = true, $assoc = true);
		abstract public function getLastId();
	}
?>
