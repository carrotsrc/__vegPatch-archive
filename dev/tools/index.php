<?php
	if(!include($_SERVER["DOCUMENT_ROOT"]."/ksysconfig.php"))
			die("Setup Problem: Cannot locate SystemConfig.");
	
	include(SystemConfig::relativeAppPath("system/koda/koda.php"));
	$db = Koda::getDatabaseConnection('mysql');

	$db->connect(SystemConfig::$dbcUsername, SystemConfig::$dbcPassword);
	$db->selectDatabase(SystemConfig::$dbcDatabase);
	
	$fm = Koda::getFileManager();
?>
<html>
	<head>
		<title>VegPatch Root Tools</title>
		<link type="text/css" rel="stylesheet" href="template.css" />
	</head>

<body>
<div id="header">
<div id="vp-title">
	Root Tools
</div>

<div id="vp-version">
	SuperRoot VegPatch v0.1
</div>
</div>

<div id="kr-layout-column">
	<div id="kr-layout">
			<div class="panel">
				Login
				<?php //echo $panel; ?>
			</div>
		</div>
</div>

</body>
</html>
