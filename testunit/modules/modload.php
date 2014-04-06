<html>
<title>Kaiakit Component Loader/Tester</title>
<head>
<body>
<?php

	/*
	*	Import the configuration class
	*	We need this first because we
	*	need to configure the settings
	*	for the initialization import
	*	tree
	*/
	error_reporting(E_ALL);
	ini_set('display_errors', '1');
	
	if(!include($_SERVER["DOCUMENT_ROOT"]."/ksysconfig.php"))
		die("Setup Problem: Cannot locate SystemConfig.");
	
	//	Configure settings
	SystemConfig::$KS_FLAG = KS_MOD;

	//	Import the app class
	include_once(SystemConfig::appRootPath("system/app.php"));

	$db = Koda::getDatabaseConnection('mysql');
	if(!$db->connect('root', 'lopana75'))
		die("Failed to connect to database");
	if(!$db->selectDatabase('kura'))
		die("Failed to select database");

	$jack = $instance = $cid = null;

	if(isset($_GET['cid']))
		$cid = $_GET['cid'];
	else
		die("No component specified");

	if(isset($_GET['inst']))
		$instance = $_GET['inst'];
	else
		die("No instance specified");

	if($instance == 0)
		die("Cannot test component in maintainence mode");

	if(isset($_GET['jack']))
		$jack = $_GET['jack'];
	else
		die("No jack interface specified");

	$resman = new ResMan($db);
	Managers::setResourceManager($resman);

	$cmpt = ModMan::getComponent($cid, $instance, $db);
	$cmpt->initialize();

	$cmpt->run($jack, null);
?>
</body>
</html>
