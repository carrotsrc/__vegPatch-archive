<?php
/* (C)opyright 2014, Carrotsrc.org
 *
 * This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/.
 */
	
	if(!include($_SERVER["DOCUMENT_ROOT"]."/ksysconfig.php"))
			die("Setup Problem: Cannot locate SystemConfig.");

	include(SystemConfig::relativeAppPath("system/helpers/session.php"));
	Session::start();
	if(isset($_GET['cache']))
		if($_GET['cache'] == "nuke")
			Session::wipe();

	$fail = false;
	if(isset($_GET['action']) && $_GET['action'] == "login") {
		$user = $_POST['user'];
		$pass = $_POST['pass'];
		if($user == SystemConfig::$rootUser && $pass == SystemConfig::$rootPass) {
			Session::set("_rootid", 0xff);
			include(SystemConfig::relativeAppPath("system/helpers/httpheader.php"));
			HttpHeader::redirect("index.php");
			return;
		}

		$fail = true;
	}
?>
<html>
	<head>
		<title>VegPatch Root Tools</title>
		<link type="text/css" rel="stylesheet" href="template.css" />
	</head>

<body>
<div id="header">
	<span class="ok-text-grey tx-xlarge">veg<span class="ok-text-green">Patch</span></span>

<div id="vp-version">
	VPatch 0.2.1
</div>
</div>
<div class="info-tool">
	<strong>Server:</strong> <?php echo $_SERVER['SERVER_NAME'] . " / " . $_SERVER['SERVER_ADDR']; ?> 
</div>
<div id="kr-layout-column">
	<div id="kr-layout" style="margin: 15px;">
	<form action="?action=login" method="post">
		<span class="ok-text-green tx-bold">Username:</span><br />
		<input class="form-text tx-large" type="text" name="user" autocomplete="off" /><br /><br />
		<span class="ok-text-green tx-bold">Password:</span><br />
		<input class="form-text tx-large" type="password" name="pass" /><br />
		<input class="form-button tx-large" type="submit" value="login" />
	</form>
	<?php
	if($fail)
		echo "<span class=\"ok-text-error tx-large\">Incorrect login!</span>";
	?>
	</div>
</div>

</body>
</html>
