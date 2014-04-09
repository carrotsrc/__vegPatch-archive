<?php

	if(!include($_SERVER["DOCUMENT_ROOT"]."/ksysconfig.php"))
			die("Setup Problem: Cannot locate SystemConfig.");
	
	include(SystemConfig::relativeAppPath("system/helpers/session.php"));
	Session::start();
	if(isset($_GET['cache']))
		if($_GET['cache'] == "nuke")
			Session::wipe();

	if(Session::get('_rootid') == null) {
		include(SystemConfig::relativeAppPath("system/helpers/httpheader.php"));
		HttpHeader::redirect("login.php");
	}

	include(SystemConfig::relativeAppPath("system/koda/koda.php"));
	$db = Koda::getDatabaseConnection('mysql');

	$db->connect(SystemConfig::$dbcUsername, SystemConfig::$dbcPassword);
	$db->selectDatabase(SystemConfig::$dbcDatabase);
	
	$rtool = "home";
	$fm = Koda::getFileManager();
	if(isset($_GET['tool']))
		$rtool = $_GET['tool'];
?>
<html>
	<head>
		<title>VegPatch Root Tools</title>
		<link type="text/css" rel="stylesheet" href="template.css" />
	</head>

<body>
<div id="header">
<!-- <div id="vp-title"> -->
	<span class="ok-text-grey tx-xlarge">v<span class="ok-text-green">Patch</span></span>
<!-- </div> -->

<div id="vp-version">
	VPatch 0.2
</div>
</div>

<div id="kr-layout-column">
	<div id="kr-layout">
			<?php 
				define('_ROOT_TOOL', 0xff);
				include("$rtool/index.php");
			?>
	</div>
</div>

</body>
</html>
