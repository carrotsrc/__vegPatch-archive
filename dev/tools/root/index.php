<?php
	if(!include($_SERVER["DOCUMENT_ROOT"]."/ksysconfig.php"))
			die("Setup Problem: Cannot locate SystemConfig.");
	
	include(SystemConfig::relativeAppPath("system/koda/koda.php"));
	include(SystemConfig::relativeAppPath("system/resource/resman.php"));
	include("lib.php");
	$db = Koda::getDatabaseConnection('mysql');
	$db->connect(SystemConfig::$dbcUsername, SystemConfig::$dbcPassword);
	$db->selectDatabase(SystemConfig::$dbcDatabase);
	$fm = Koda::getFileManager();
	$panel = "";
	$rman = new ResMan($db);
	if(isset($_GET['mode']) && $_GET['mode'] == 'config') {
		ob_start();
		rootConfig($db);
		$panel = ob_get_contents();
		ob_end_clean();
	}
	else
	if(isset($_GET['mode']) && $_GET['mode'] == 'asset') {
		ob_start();
		rootAssets($db, $fm);
		$panel = ob_get_contents();
		ob_end_clean();
	}
	else {
		ob_start();
		rootConfig($db);
		$panel = ob_get_contents();
		ob_end_clean();
	}
		
	$mlist = $fm->listDirectories("../");
?>
<html>
	<head>
		<title>VegPatch Root System</title>
		<link type="text/css" rel="stylesheet" href="template.css" />
	</head>

<body>
<div id="header">
<div id="vp-title">
	Root System
</div>

<div id="vp-version">
	SuperRoot VegPatch v0.1
</div>
</div>

<div id="link-bar">
<?php
	echo "| ";
	foreach($mlist as $d) {
		if($d == 'tool-template')
			continue;

		echo "<a href=\"../$d\">$d</a>";
		echo " | ";
	}
?>
</div>
<div id="kr-layout-column">
	<div id="kr-layout">
		<div class="tools">
			<div class="tool-panel">
			<b>Toolkit</b>
			<form method="post" action="index.php?mode=config">
				<input type="submit" value="Root Config" class="form-button" style="width: 100px;" />
			</form>
			<form method="post" action="index.php?mode=asset" style="margin-top: -20px;">
				<input type="submit" value="Root Assets" class="form-button" style="width: 100px;" />
			</form>
			</div>
		</div>

			<div class="panel">
				<?php echo $panel; ?>
			</div>
		</div>
</div>

</body>
</html>
