<?php
	if(!include($_SERVER["DOCUMENT_ROOT"]."/ksysconfig.php"))
			die("Setup Problem: Cannot locate SystemConfig.");
	
	include(SystemConfig::relativeAppPath("system/helpers/session.php"));
	include(SystemConfig::relativeAppPath("system/file/filemanager.php"));
	include(SystemConfig::relativeAppPath("system/db/db.php"));
	include(SystemConfig::relativeAppPath("system/helpers/strings.php"));
	var_dump($_SESSION);
	if(Session::get('_rootid') == null)
		die("101");

	$db = core_create_db('mysql');

	$db->connect(SystemConfig::$dbcUsername, SystemConfig::$dbcPassword);
	$db->selectDatabase(SystemConfig::$dbcDatabase);
	
?>
