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
	$mlist = $fm->listDirectories("../");

	if(isset($_GET['mode']) && $_GET['mode'] == "root") {
		ob_start();
		strapRootPanel($fm, $db, $rman);
		$panel = ob_get_contents();
		ob_end_clean();
	}
	else
	if(isset($_GET['mode']) && $_GET['mode'] == "system") {
		ob_start();
		strapSystemPanel($fm, $db, $rman);
		$panel = ob_get_contents();
		ob_end_clean();
	} else {
		ob_start();
		strapRootPanel($fm, $db, $rman);
		$panel = ob_get_contents();
		ob_end_clean();
	}
?>
<html>
	<head>
		<title>VegPatch Strap System</title>
		<link type="text/css" rel="stylesheet" href="template.css" />
	</head>

<body>
<div id="header">
<div id="vp-title">
	Strap System
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
			<b>Straps</b>
			<form method="post" action="index.php?mode=root">
				<input type="submit" value="Root System" class="form-button" style="width: 120px;" />
			</form>
			<form method="post" action="index.php?mode=system">
				<input type="submit" value="Other Systems" class="form-button" style="width: 120px; margin-top: -20px;" />
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
