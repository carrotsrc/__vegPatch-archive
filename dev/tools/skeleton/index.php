<?php
/* (C)opyright 2014, Carrotsrc.org
 *
 * This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/.
 */
	
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
	$mlist = $fm->listDirectories("../");
	if(isset($_GET['mode']) && $_GET['mode'] == 'component') {
		if(isset($_POST['op']) && $_POST['op'] == 1) {
			$ires = false;
			if(isset($_POST['res']))
				$ires = true;

			generateComponent($_POST['label'], $ires, $fm);
		}
		ob_start();
		newComponentPanel();
		$panel = ob_get_contents();
		ob_end_clean();
	}
	else
	if(isset($_GET['mode']) && $_GET['mode'] == 'panel') {
		if(isset($_POST['op']) && $_POST['op'] == 1) {
			$newSpace = false;
			$space = "";
			if(isset($_POST['space']) && $_POST['space'] == "000") {
				$newSpace = true;
				$space = $_POST['nspace'];
			}
			else
			if(isset($_POST['space'])) {
				$space = $_POST['space'];
			}

			generatePanel($_POST['label'], $space, $newSpace, $fm);
		}
		ob_start();
		newPanelPanel($fm);
		$panel = ob_get_contents();
		ob_end_clean();
	}
	else
	if(isset($_GET['mode']) && $_GET['mode'] == 'library') {
		if(isset($_POST['op']) && $_POST['op'] == 1) {
			$newSpace = false;
			$space = "";
			$dbe = false;
			if(isset($_POST['space']) && $_POST['space'] == "000") {
				$newSpace = true;
				$space = $_POST['nspace'];
			}
			else
			if(isset($_POST['space'])) {
				$space = $_POST['space'];
			}

			if(isset($_POST['db']))
				$dbe = true;

			generateLibrary($_POST['label'], $space, $newSpace, $dbe, $fm);
		}
		ob_start();
		newLibraryPanel($fm);
		$panel = ob_get_contents();
		ob_end_clean();
	}
	else
	if(isset($_GET['mode']) && $_GET['mode'] == 'plugin') {
		if(isset($_POST['op']) && $_POST['op'] == 1) {
			$ires = false;
			if(isset($_POST['res']))
				$ires = true;

			generatePlugin($_POST['label'], $ires, $fm);
		}
		ob_start();
		newPluginPanel();
		$panel = ob_get_contents();
		ob_end_clean();
	}
	else {
		ob_start();
		newComponentPanel();
		$panel = ob_get_contents();
		ob_end_clean();
	}
?>
<html>
	<head>
		<title>VegPatch Skeleton Classes</title>
		<link type="text/css" rel="stylesheet" href="template.css" />
	</head>

<body>
<div id="header">
<div id="vp-title">
	Skeleton Class Generator
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
			<b>Class Type</b>
			<form method="get" action="index.php">
				<input type="hidden" name="mode" value="component" />
				<input type="submit" value="Component" class="form-button" style="width: 100px" />
			</form>
			<form method="get" action="index.php" style="margin-top: -20px;">
				<input type="hidden" name="mode" value="panel" />
				<input type="submit" value="Panel" class="form-button" style="width: 100px" />
			</form>
			<form method="get" action="index.php" style="margin-top: -20px;">
				<input type="hidden" name="mode" value="library" />
				<input type="submit" value="Library" class="form-button" style="width: 100px" />
			</form>
			<form method="get" action="index.php" style="margin-top: -20px;">
				<input type="hidden" name="mode" value="plugin" />
				<input type="submit" value="Plugin" class="form-button" style="width: 100px" />
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
