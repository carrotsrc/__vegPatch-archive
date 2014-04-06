<?php
	require_once("../../system/koda/db/tools/dbconnection.php");
	include("../../system/koda/db/mysql/mysqlconnection.php");
	
	
	$connection = new MySqlConnection();
	
	if(!$connection->connect("root", "lopana75"))
		echo "Error";
	else
		echo "Connected!";
?>
