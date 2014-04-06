<?php
	require_once("../../system/koda/koda.php");
	echo "getting connection from Koda...";
	$connection = Koda::getDatabaseConnection("mysql");
	echo "done<br />";
	
	echo "connection status...";
	if(!$connection->connect("root", "lopana75"))
		die("failed");
	else
		echo "connected!<br />";
	
	echo "selecting database 'kit'...";
	if(!$connection->selectDatabase("kit"))
		die("failed");
	else
		echo "done!";
	
?>
