<?php
	abstract class DBConnection
	{
		protected $connection;
		
		public function __construct()
		{
		
		}
		
		abstract public function connect($username, $password);
		abstract public function connectAtServer($server, $username, $password);
		abstract public function selectDatabase($database);
		abstract public function sendQuery($query);
		abstract public function getLastId();
	}

	function core_create_db($type)
	{
		include($type."connection.php");
		$class = $type."Connection";
		$connection = new $class();

		return $connection;
	}
?>
