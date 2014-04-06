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

	$rman = new ResMan($db);
	if(isset($_GET['mode']) && $_GET['mode'] == "newres") {
		ob_start();
		newResourcePanel($db, $rman);
		$panel = ob_get_contents();
		ob_end_clean();
	}
	else
	if(isset($_GET['mode']) && $_GET['mode'] == "newtype") {
		ob_start();
		newTypePanel($db, $rman);
		$panel = ob_get_contents();
		ob_end_clean();
	}
	else {
		ob_start();
		newResourcePanel($db, $rman);
		$panel = ob_get_contents();
		ob_end_clean();
	}
	$mlist = $fm->listDirectories("../");
?>
<html>
	<head>
		<title>VegPatch Resources</title>
		<link type="text/css" rel="stylesheet" href="template.css" />
	</head>

<body>
<div id="header">
<div id="vp-title">
	Resources
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
			<div class="tool-panel" style="width: 120px">
			<b>Resources</b>
			<form method="post" action="index.php?mode=newres">
				<input type="submit" value="New Resource" class="form-button"/>
			</form>
			</div>
			<div class="tool-panel" style="width: 120px">
			<b>Types</b>
			<form method="post" action="index.php?mode=newtype">
				<input type="submit" value="New ResType" class="form-button"/>
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
