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
		SystemConfig::$KS_FLAG = KS_SURROUND | KS_IS_PAGE | KS_MOD | KS_USER | KS_TRACK | KS_PHP_ERROR;

	
	//	Import the app class
	include_once(SystemConfig::appRootPath("system/app.php"));
	
	if(isset($_GET['cache']))
		if($_GET['cache'] == "nuke")
			Session::wipe();


	$app = new App();
	if(!$app->init())
		die("Major Malfunction: Application failed to initialize!");
		
	$page = $app->getPage();
	if(!$page)
		die("Major Malfunction: failed to generate page");

	$page->renderPage();
if(SystemConfig::$KS_FLAG&KS_DEBUG_MICRO && (!isset($_GET['dbm-hidden']) || $_GET['dbm-hidden'] != 1)) {
	$dbm = new DebugMicro();
	echo "<div class=\"container\" style=\"float: right; border: 1px solid #808080; background-color: white; display: inline;\">";
	echo $dbm->pmu();
	echo "<hr />";
	echo $dbm->ldsess();
	echo "<hr />";
	echo $dbm->cacheNuker();
	echo "<hr />";
	echo $dbm->service();
	echo "</div>";
}
?>
