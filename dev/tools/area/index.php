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
	$panel = "";

	if(isset($_GET['mode']) && $_GET['mode'] == "manarea") {
		ob_start();
		areaManagerPanel($db, $rman);
		$panel = ob_get_contents();
		ob_end_clean();
	}
	else
	if(isset($_GET['mode']) && $_GET['mode'] == "newarea") {
		ob_start();
		newAreaPanel($db);
		$panel = ob_get_contents();
		ob_end_clean();
	}
	else
	if(isset($_GET['mode']) && $_GET['mode'] == "mansur") {
		ob_start();
		manageSurround($db, $fm);
		$panel = ob_get_contents();
		ob_end_clean();
	}
	else
	if(isset($_GET['mode']) && $_GET['mode'] == "regsur") {
		ob_start();
		surroundRegister($db, $fm);
		$panel = ob_get_contents();
		ob_end_clean();
	}
	else {
		ob_start();
		stats($db, $rman);
		$panel = ob_get_contents();
		ob_end_clean();
	}
	$areas = $db->sendQuery("SELECT * FROM areapool;", false, false);
	$surrounds = $db->sendQuery("SELECT * FROM surpool;", false, false);
	$mlist = $fm->listDirectories("../");
?>
<html>
	<head>
		<title>VegPatch Area Manager</title>
		<link type="text/css" rel="stylesheet" href="template.css" />
	</head>

<body>
<div id="header">
<div id="vp-title">
	Area Manager
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
			<b>Areas</b>
			<form action="index.php?mode=newarea" method="post">
				<input type="submit" value="New Area" class="form-button" />
			</form>
			<form action="index.php?mode=manarea" method="post">
				<select name="aid" class="form-text form-select">
					<?php
					foreach($areas as $a)
						echo "<option value=\"{$a[0]}\">{$a[2]}</option>";
					?>
				</select><br />
				<input type="submit" value="Manage Area" class="form-button" />
			</form>
			</div>

			<div class="tool-panel">
			<b>Surrounds</b>
			<form action="index.php?mode=regsur" method="post">
				<input type="submit" value="Surround Register" class="form-button" />
			</form>
			<form action="index.php?mode=mansur" method="post">
				<select name="sid" class="form-text form-select">
					<?php
					foreach($surrounds as $s)
						echo "<option value=\"{$s[0]}\">{$s[1]}</option>";
					?>
				</select><br />
				<input type="submit" value="Manage Surround" class="form-button" />
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
