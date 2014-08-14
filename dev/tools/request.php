<?php
	if(!include($_SERVER["DOCUMENT_ROOT"]."/ksysconfig.php"))
			die("Setup Problem: Cannot locate SystemConfig.");
	$flag = KS_MOD|KS_SESSION;

	include(SystemConfig::relativeAppPath("system/helpers/session.php"));
	include(SystemConfig::relativeAppPath("system/file/filemanager.php"));
	include(SystemConfig::relativeAppPath("system/db/db.php"));
	include(SystemConfig::relativeAppPath("system/helpers/strings.php"));
	include(SystemConfig::relativeAppPath("system/resource/resman.php"));
	Session::start();
	if(Session::get('_rootid') == null)
		die('101');

	$db = core_create_db('mysql');

	$db->connect(SystemConfig::$dbcUsername, SystemConfig::$dbcPassword);
	$db->selectDatabase(SystemConfig::$dbcDatabase);
	$resman = new ResMan($db);

	if(!isset($_GET['tool']))
		die('102');

	if(!file_exists("{$_GET['tool']}/request.php"))
		die('103');

	include("{$_GET['tool']}/request.php");
	request();
?>
