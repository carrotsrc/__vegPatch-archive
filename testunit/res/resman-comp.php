<?php

	error_reporting(E_ALL);
	ini_set('display_errors', '1');
	
	require_once("../../system/koda/koda.php");
	require_once("../../system/resource/resman.php");
	require_once("../../system/dbacc.php");
	
	$connection = Koda::getDatabaseConnection("mysql");
	
	if(!$connection->connect("root", "lopana75"))
		die("failed");
	
	if(!$connection->selectDatabase("kura"))
		die("failed");

	if(!ResCast::init($connection))
		die("could not get rescast");

	$resman = new ResMan($connection);
	//$query = "id(User('charlie')) ^ (Area()<Area('forum'))&(Role('admin')^Area('forum'));";
	//$query = "id(User()) ^ Area('forum') & (Role('admin')^Area('forum'));";
//	$query = "Instance()^Role('admin')&User('charlie')&Area('home');";
	//$query = "Instance()^Role('admin')&(Area()^User('charlie'));";
	//$query = "id(User())^(Area()<Area('forum'));";
	//$query = "id(User('dave'))^(Area()<(Area('forum')^Area('home')));";
//	$query = "User('charlie');";
	$query = "[seed]^Instance('home_admin');";
	//$query = "Instance('9')^[seed];";
	$qRpl = str_replace("<", "&lt;", $query);
	echo "$qRpl<br /><br />";
	echo "<i>" . $resman->convertRQL($query) . "</i>";
	
	
?>
