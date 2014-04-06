<?php
	if(!include($_SERVER["DOCUMENT_ROOT"]."/ksysconfig.php"))
			die("Setup Problem: Cannot locate SystemConfig.");
	
	include(SystemConfig::relativeAppPath("system/koda/koda.php"));
	include(SystemConfig::relativeAppPath("system/resource/resman.php"));

	$db = Koda::getDatabaseConnection('mysql');
	$db->connect(SystemConfig::$dbcUsername, SystemConfig::$dbcPassword);
	$db->selectDatabase(SystemConfig::$dbcDatabase);

	$fm = Koda::getFileManager();
	$rman = new ResMan($db);

	$edit = null;
	if(isset($_POST['op']) && $_POST['op'] == 3) {
		$sql = "UPDATE layoutpool SET ";
		$sql .= "name='".mysql_real_escape_string($_POST['name'])."', ";
		$sql .= "cml='".mysql_real_escape_string($_POST['cml'])."' ";
		$sql .= "WHERE id='{$_POST['lid']}'";
		$db->sendQuery($sql);
	}
	else
	if(isset($_POST['op']) && $_POST['op'] == 1) {
		$sql = "INSERT INTO layoutpool ";
		$sql .= "(`name`, `cml`) ";
		$sql .= "VALUES ";
		$sql .= "('".mysql_real_escape_string($_POST['name'])."',";
		$sql .= "'".mysql_real_escape_string($_POST['cml'])."')";
		if($db->sendQuery($sql))
			$_POST['lid'] = $db->getLastId();
	}
	else
	if(isset($_POST['op']) && $_POST['op'] == 5) {
		$res = $rman->queryAssoc("Layout('{$_POST['lid']}');");
		if(!$res)
			$rman->addResource('Layout', $_POST['lid'], $_POST['name']);

	}
	else
	if(isset($_POST['op']) && $_POST['op'] == 4) {
		$sql = "DELETE FROM layoutpool WHERE id='{$_POST['lid']}'";
		if($db->sendQuery($sql)) {
			$res = $rman->queryAssoc("Layout('{$_POST['lid']}');");
			if($res) {
				$rman->removeResource($res[0][0]);
			}
		
			unset($_POST['lid']);
		}
	}
	$list = $db->sendQuery("SELECT id, name FROM layoutpool", false, false);
	$hasres = null;
	$res = null;
	if(isset($_POST['lid'])) {
		$edit = $db->sendQuery("SELECT * FROM layoutpool WHERE id='{$_POST['lid']}'", false, false);
		$edit = $edit[0];
		$res = $rman->queryAssoc("Layout('{$edit[0]}');");
		if(!$res)
			$hasres = false;
		else
			$hasres = $res[0][0];
	}

	$mlist = $fm->listDirectories("../");
?>
<html>
	<head>
		<title>VegPatch Layouts</title>
		<link type="text/css" rel="stylesheet" href="layout.css" />
	</head>

<body>
<div id="header">
<div id="vp-title">
	System Layouts
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
	<div class="splash">
	<b>New Layout</b>
	<form method="post" action="index.php">
	<input type="submit" class="form-button" value="Create new layout" />
	</form>
	</div>
	<div class="splash">
	<b>Edit layout</b>
	<form method="post" action="index.php">
		<select name="lid" style="width: 150px" class="form-text form-select">
		<?php
			foreach($list as $ls) {
				if($edit == null || $edit[0] != $ls[0])
					echo "\t<option value=\"{$ls[0]}\"> {$ls[1]}</option>\n";
				else
				if($edit[0] == $ls[0])
					echo "\t<option value=\"{$ls[0]}\" selected> {$ls[1]}</option>\n";
			}
		?>
		</select><br />
		<input type="submit" value="Edit" class="float-r form-button"/>
	</form>
	</div>
</div>

<div class="panel">
<b>Layout Editor</b><br /><br />
<?php
?>
		<form name="layout-edit" method="post" action="index.php">
		
		<?php
			if($edit !== null) {
				echo "<input type=\"hidden\" name=\"op\" value=\"3\" />";
				echo "<input type=\"hidden\" name=\"lid\" value=\"{$edit[0]}\" />";
				echo "<input type=\"text\" name=\"name\" class=\"form-text\" value=\"{$edit[1]}\" /> ({$edit[0]})<br />";
				echo "<textarea class=\"form-text\" name=\"cml\" rows=\"20\" cols=\"60\">{$edit[2]}</textarea><br />";
			}
			else {
				echo "<input type=\"hidden\" name=\"op\" value=\"1\" />";
				echo "<input type=\"text\" name=\"name\" class=\"form-text\" value=\"\" /><br />";
				echo "<textarea class=\"form-text\" name=\"cml\" rows=\"20\" cols=\"60\"></textarea><br />";
			}
		?>
		<input class="form-button float-r" type="submit" value="Save" /><br />
		</form>
		<?php
			if($hasres !== null && $hasres !== false) {
				echo "<div style=\"float: left; margin-top: -25px;\">";
				echo "<div style=\"float: left;\">Layout( $hasres )</div>";
				echo "<form method=\"post\" action=\"index.php\" style=\"float: left; margin-left: 5px;\">";
				echo "<input type=\"hidden\" name=\"op\" value=\"4\" />";
				echo "<input type=\"hidden\" name=\"lid\" value=\"{$edit[0]}\" />";
				echo "<input type=\"submit\" class=\"form-button\" style=\"margin-top: 0px;\" value=\"Remove\" />";
				echo "</form>";

				echo "</div>";
			}
			else
			if($hasres !== null && $hasres === false) {
				echo "<div style=\"float: left; margin-top: -25px;\">\n";
				echo "<div style=\"float: left;\">Unregistered</div>\n";
				echo "<form method=\"post\" action=\"index.php\" style=\"float: left; margin-left: 5px;\">\n";
					echo "<input type=\"hidden\" name=\"op\" value=\"5\" />\n";
					echo "<input type=\"hidden\" name=\"lid\" value=\"{$edit[0]}\" />\n";
					echo "<input type=\"hidden\" name=\"name\" value=\"{$edit[1]}\" />\n";
					echo "<input type=\"submit\" class=\"form-button\" style=\"margin-top: 0px;\" value=\"Register\" />\n";
				echo "</form>\n";

				echo "<form method=\"post\" action=\"index.php\" style=\"float: left; margin-left: 5px;\">\n";
					echo "<input type=\"hidden\" name=\"op\" value=\"4\" />\n";
					echo "<input type=\"hidden\" name=\"lid\" value=\"{$edit[0]}\" />\n";
					echo "<input type=\"submit\" class=\"form-button\" style=\"margin-top: 0px;\" value=\"Remove\" />\n";
				echo "</form>";
				echo "</div>";
			}
			else
				echo "<font style=\"float: left; margin-top: -25px;\">Not created</font>";


		?>

</div>
</div>
</div>
</body>
</html>
