<?php
/* Copyright 2014, Charlie Fyvie-Gauld (Carrotsrg.org)
 *
 * This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/.
 */
	require_once("db/tools/dbconnection.php");
	
	/*
	*	Koda
	*
	*	Kosaaga Data Access
	*	Used as an interface generator for accessing
	*	data on the disk or in a database
	*/
	
	class Koda
	{
		static public function getDatabaseConnection($type)
		{
			include_once("db/".$type."/".$type."connection.php");
			$class = $type."Connection";
			$connection = new $class();
			
			return $connection;
		}
		
		static public function getFileManager()
		{
			include_once("fm/filemanager.php");
			return new FileManager();
		}
	}
?>
