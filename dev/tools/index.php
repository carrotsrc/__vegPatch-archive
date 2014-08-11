<?php
/* Copyright 2014, Charlie Fyvie-Gauld (Carrotsrg.org)
 *
 * This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/.
 */
	
	if(!include($_SERVER["DOCUMENT_ROOT"]."/ksysconfig.php"))
			die("Setup Problem: Cannot locate SystemConfig.");
	
	include(SystemConfig::relativeAppPath("system/helpers/session.php"));
	include(SystemConfig::relativeAppPath("system/file/filemanager.php"));
	include(SystemConfig::relativeAppPath("system/db/db.php"));

	Session::start();
	if(isset($_GET['taction']))
		if($_GET['taction'] == "logout")
			Session::uset('_rootid');

	if(isset($_GET['cache']))
		if($_GET['cache'] == "nuke")
			Session::wipe();

	if(Session::get('_rootid') == null) {
		include(SystemConfig::relativeAppPath("system/helpers/httpheader.php"));
		HttpHeader::redirect("login.php");
	}

	$db = core_create_db('mysql');

	$db->connect(SystemConfig::$dbcUsername, SystemConfig::$dbcPassword);
	$db->selectDatabase(SystemConfig::$dbcDatabase);
	
	$rtool = "home";
	$fm = new FileManager();
	if(isset($_GET['tool']))
		$rtool = $_GET['tool'];
?>
<html>
	<head>
		<title>vegPatch Root Tools</title>
		<link type="text/css" rel="stylesheet" href="template.css" />
	</head>

<body>
<div id="header">
<!-- <div id="vp-title"> -->
	<a href="index.php"><span class="ok-text-grey tx-xlarge">veg<span class="ok-text-green">Patch</span></span></a>
<!-- </div> -->

<div id="vp-version" style="margin-right: 10px;">
	<a href="?taction=logout"><span class="ok-text-grey">Logout</span></a> 
</div>
</div>
<div class="info-tool">
	<strong>Server:</strong> <?php echo $_SERVER['SERVER_NAME'] . " / " . $_SERVER['SERVER_ADDR']; ?> 
</div>
<div id="kr-layout-column">
			<?php 
				define('_ROOT_TOOL', 0xff);
				include("$rtool/index.php");
			?>
</div>
</body>
</html>
