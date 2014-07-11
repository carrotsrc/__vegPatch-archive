<?php
/* Copyright 2014, Charlie Fyvie-Gauld (Carrotsrg.org)
 *
 * This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/.
 */
	
	function surroundData($sid, $tid, $db)
	{
		$sql = "SELECT name, value FROM surpool JOIN surtemplate ON surtemplate.s_id = surpool.id WHERE surpool.id='$sid' AND surtemplate.t_id='$tid';"; 
		$td = $db->sendQuery($sql, false, false);
		if(!$td)
			return false;

		return $td[0];
	}
	function surroundAssets($sid, $db)
	{
		
	}

	function registerAreaResource($aid, $db, $rman)
	{
		$det = $db->sendQuery("SELECT name FROM areapool WHERE id='$aid';", false, false);
		if(!$det)
			return;
		$rman->addResource('Area', $aid, $det[0][0]);
	}

	function areaManagerPanel($db, $rman)
	{
		$aid = null;
		$editMode = false;

		if(isset($_POST['aid']))
			$aid = $_POST['aid'];

		if(isset($_POST['op'])) {
			if($_POST['op'] == 1) {
				registerAreaResource($aid, $db, $rman);
			}
			else
			if($_POST['op'] == 2 || $_POST['op'] == 3) {
				$editMode = true;
			}
			else
			if($_POST['op'] == 4) {
				$sql = "UPDATE areapool SET ";
				$sql .= "name='{$_POST['label']}', ";
				$sql .= "s_id='{$_POST['sid']}', ";
				$sql .= "st_id='{$_POST['tid']}' ";
				$sql .= "WHERE id='{$_POST['aid']}';";

				$db->sendQuery($sql, false, false);

			}
		}

		$res = $rman->queryAssoc("Area('$aid');");

		$det = $db->sendQuery("SELECT * FROM areapool WHERE id='$aid';", false, false);
		$det = $det[0];
		echo "<b>Area Manager</b><br />";
		echo "<div class=\"form-item\">";
		echo "<b>{$det[2]}</b> ($aid)";
		echo "</div>";
		if(!$res) {
			?>
			<form method="post" action="index.php?tool=area&mode=manarea">
				<input type="hidden" name="aid" value="<?php echo $aid; ?>" />
				<input type="hidden" name="op" value="1" />
				<input type="submit" class="form-button" value="Register Resource" />
			</form>
			<?php
			return;
		}

		echo "<div class=\"form-item font-small\">";
		echo "Area( {$res[0][0]} )";
		echo "</div>";
		$td = surroundData($det[3], $det[4], $db);
		if(!$td)
			return;

		echo "{$td[0]} / {$td[1]}";
		if(!$editMode) {
		?>

			<form method="post" action="index.php?tool=area&mode=manarea">
				<input type="hidden" name="aid" value="<?php echo $aid; ?>" />
				<input type="hidden" name="op" value="2" />
				<input type="submit" value="Modify" class="form-button float-r" />
			</form>
		<?php
			return;
		}
		if($editMode) {
			$surrounds = $db->sendQuery("SELECT id, name FROM surpool;", false, false);
			if(!$surrounds)
				return;
		?>
			<hr />
			<form method="post" action="index.php?tool=area&mode=manarea">
			<?php
			if($_POST['op'] == 2)  {
			?>
				<input name="label" class="form-text" value="<?php echo $det[2]; ?>" /><br />
			
				<select name="sid" class="form-text form-select">
				<?php
				foreach($surrounds as $s)
					if($s[0] == $det[3])
						echo "<option value=\"{$s[0]}\" selected>{$s[1]}</option>";
					else
						echo "<option value=\"{$s[0]}\">{$s[1]}</option>";
				?>
				</select>
				<input type="hidden" name="op" value="3" />
				<input type="submit" value="Next" class="form-button"/>
			<?php
			}
			else
			if($_POST['op'] == 3) {
				$templates = $db->sendQuery("SELECT t_id, value FROM surtemplate WHERE s_id='{$_POST['sid']}';", false, false);
				$slabel = "";
				foreach($surrounds as $s)
					if($s[0] == $_POST['sid'])
						$slabel = $s[1];
			?>
				<input class="form-text form-disabled" value="<?php echo $_POST['label']; ?>" disabled/><br />
				<input type="hidden" name="label" value="<?php echo $_POST['label']; ?>" />
				<input type="text" style="width: auto;" class="form-text form-disabled" value="<?php echo $slabel; ?>" disabled>
				<input type="hidden" name="op" value="4" /><br />
				<select name="tid" class="form-text form-select">
				<?php
				foreach($templates as $t)
					echo "<option value=\"{$t[0]}\">{$t[1]}</option>";
				?>
				</select>
				<input type="hidden" name="sid" value="<?php echo $_POST['sid']; ?>" />
				<input type="submit" value="Modify" class="form-button" />
			<?php
			}
			?>
			<input type="hidden" name="aid" value="<?php echo $aid; ?>" />
			</form>

			<form method="post" action="index.php?tool=area&mode=manarea">
			<input type="hidden" name="aid" value="<?php echo $aid; ?>" />
			<input type="submit" value="Cancel" class="form-button"/>
			</form>
		<?php
		}
	}

	function newAreaPanel($db)
	{
		echo "<b>New Area</b><br >";
		echo "<div clas=\"form-item\">";
		if(isset($_POST['op']) && $_POST['op'] == 2) {
			$sql = "INSERT INTO areapool ";
			$sql .= "(name, s_id, st_id) VALUES ";
			$sql .= "('{$_POST['label']}','{$_POST['sid']}','{$_POST['tid']}');";
			$db->sendQuery($sql, false, false);
			unset($_POST['op']);
		}
		$surrounds = $db->sendQuery("SELECT id, name FROM surpool;", false, false);
		if(!$surrounds)
			return;

		echo "<form method=\"post\" action=\"index.php?tool=area&mode=newarea\">";
		if(!isset($_POST['op'])) { 
		?>
			<input name="label" class="form-text" /><br />
			<select name="sid" class="form-text form-select">
			<?php
			foreach($surrounds as $s)
					echo "<option value=\"{$s[0]}\">{$s[1]}</option>";
			?>
			</select>
			<input type="hidden" name="op" value="1" />
			<input type="submit" value="Next" class="form-button"/>
			
		<?php
		}
		else {
			$templates = $db->sendQuery("SELECT t_id, value FROM surtemplate WHERE s_id='{$_POST['sid']}';", false, false);
			$slabel = "";
			foreach($surrounds as $s)
				if($s[0] == $_POST['sid'])
					$slabel = $s[1];
		?>
			<input type="hidden" name="op" value="2" />
			<input type="hidden" name="label" value="<?php echo $_POST['label'] ?>" />
			<input type="hidden" name="sid" value="<?php echo $_POST['sid'] ?>" />
			<input type="text" class="form-text form-disabled" value="<?php echo $_POST['label']; ?>" disabled/><br />
			<input type="text" class="form-text form-disabled" value="<?php echo $slabel; ?>" disabled/><br />
			<select name="tid" class="form-text form-select">
			<?php
			foreach($templates as $t)
				echo "<option value=\"{$t[0]}\">{$t[1]}</option>";
			?>
			</select>
			<input type="submit" value="Add" class="form-button"/>
		<?php
		}
		echo "</form>";
		echo "</div>";
		
	}

	function manageSurround($db, $fm)
	{
		$addTemplate = false;
		$tname = "";
		$sid = 0;

		if(isset($_POST['sid']))
			$sid = $_POST['sid'];
		else
		if(isset($_GET['sid']))
			$sid = $_GET['sid'];

		$det = $db->sendQuery("SELECT * FROM surpool WHERE id='{$sid}';", false, false); 
		$sid = $det[0][0];
		$sname = $det[0][1];
		$sdir = SystemConfig::relativeAppPath("library/surrounds/$sname");
		$aflist = getAssetFiles($sdir, "", $fm);
	
		if(isset($_GET['op']) && $_GET['op'] == 1) {
			$addTemplate = true;
			$tname = $_GET['name'];
			if(!$db->sendQuery("SELECT id FROM surtemplate WHERE s_id='$sid' AND value='$tname';",false, false)) {
				$mid = $db->sendQuery("SELECT t_id FROM surtemplate WHERE s_id='$sid' ORDER BY t_id DESC LIMIT 1;", false, false);
				$mid = intVal($mid[0][0]);
				$mid++;
				$db->sendQuery("INSERT INTO surtemplate (s_id, t_id, value) VALUES ('$sid', '$mid','$tname');", false, false);
			}
		}
		else
		if(isset($_GET['op']) && $_GET['op'] == 2) {
			$addTemplate = true;
			$tid = $_GET['tid'];
			if($db->sendQuery("SELECT id FROM surtemplate WHERE s_id='$sid' AND t_id='$tid';",false, false))
				$db->sendQuery("DELETE FROM surtemplate WHERE s_id='$sid' AND t_id='$tid';", false, false);

		}
		else
		if(isset($_GET['op']) && $_GET['op'] == 3) {
			$path = $_GET['name'];
			if(!$db->sendQuery("SELECT id FROM surasset WHERE s_id='$sid' AND value='$path';",false, false)) {
				$a = explode("/", $path);
				$name = end($a);
				$type = explode(".", $name);
				$type = end($type);

				$sql = "INSERT INTO surasset (s_id, type, name, value) VALUES ";
				$sql .= "('$sid', '$type', '$name', '$path');";
				$db->sendQuery($sql);
			}
		}
		else
		if(isset($_GET['op']) && $_GET['op'] == 4) {
			$db->sendQuery("DELETE FROM surasset WHERE id='{$_GET['id']}';", false, false);
		}


		$alist = $db->sendQuery("SELECT type, name, value, id FROM surasset WHERE s_id='$sid';", false, false);
		if(!$alist)
			$alist = array();

		$tflist = $fm->listFiles($sdir);
		$tlist = $db->sendQuery("SELECT t_id, value FROM surtemplate WHERE s_id='$sid';", false, false);
		if(!$tlist)
			$tlist = array();

		echo "<b>Manage Surround</b><br />";
		echo "<div class=\"form-item\">";
		echo "<b>$sname</b> ($sid)";

		echo "<div class=\"form-item font-small\">";
		echo "<br /><b>Templates</b><br />";
		echo "<div class=\"panel-box form-item\">";
			echo "<table>";
			foreach($tflist as $t) {
				$id = "-";

				foreach($tlist as $td)
					if($t == $td[1])
						$id = $td[0];

				$a = explode('.', $t);
				$type = end($a);
				if($type != "php" && $type != "htm")
					continue;

				echo "<tr>";
					echo"<td>";
					if($id == "-")
						echo "<a class=\"switch-a\" href=\"index.php?tool=area&mode=mansur&op=1&name=$t&sid=$sid\">$t</a>";
					else
						echo $t;
					echo"</td>";
					echo"<td>$id</td>";
					echo "<td>";
						if($id != "-")
							echo "<a class=\"switch-a\" style=\"color: red;\" href=\"index.php?tool=area&mode=mansur&op=2&tid=$id&sid=$sid\">X</a>";
						else
							echo " ";
					echo "</td>";
				echo "</tr>";
			}
			echo "</table>";

		echo "</div>";
		echo "</div>";

		echo "<div class=\"form-item font-small\">";
		echo "<br /><b>Assets</b><br />";
		echo "<div class=\"panel-box form-item\">";
			echo "<table>";
			foreach($aflist as $a) {
				$loaded = false;
				$id = 0;

				foreach($alist as $ad)
					if($a[0] == $ad[2]) {
						$loaded = true;
						$id = $ad[3];
					}

				echo "<tr>";
					echo"<td>";
					if(!$loaded)
						echo "<a class=\"switch-a\" href=\"index.php?tool=area&mode=mansur&op=3&name={$a[0]}&sid=$sid\">{$a[2]}</a>";
					else
						echo $a[2];
					echo"</td>";
					echo"<td class=\"font-small\">({$a[0]})</td>";
					echo "<td>";
						if($loaded)
							echo "<a class=\"switch-a\" style=\"color: red;\" href=\"index.php?tool=area&mode=mansur&op=4&id=$id&sid=$sid\">X</a>";
						else
							echo " ";
					echo "</td>";
				echo "</tr>";
			}
			echo "</table>";

		echo "</div>";
		echo "</div>";
		echo "</div>";
	}

	function surroundRegister($db, $fm)
	{
		echo "<b>Surround Register</b><br />";
		$sdir = SystemConfig::relativeAppPath("library/surrounds");
		$flist = $fm->listDirectories($sdir);

		if(isset($_GET['op']) && $_GET['op'] == 1) {
			$surround = $_GET['sur'];
			if(!$db->sendQuery("SELECT id FROM surpool WHERE name='$surround';", false, false)) {
				echo "Adding surround";
				$db->sendQuery("INSERT INTO surpool (name) VALUES ('$surround');", false, false);
			}
		}

		$dlist = $db->sendQuery("SELECT * FROM surpool", false, false);
		if(!$dlist)
			$dlist = array();
		echo "<div class=\"form-item\">";
			echo "<ul>";
			echo "<table>";
			foreach($flist as $f) {
				$loaded = false;
				foreach($dlist as $d)
					if($f == $d[1])
						$loaded = true;
				echo "<tr>";
				if($loaded)
					echo "<td><li class=\"li-active\"><font style=\"color:grey\">$f</font></li></td>";
				else
					echo "<td><li class=\"li-inactive\"><a href=\"index.php?tool=area&mode=regsur&op=1&sur=$f\" class=\"switch-a\">$f</a></li></td>";
				echo "</trd>";

			}
			echo "</table>";
			echo "</ul>";
		echo "</div>";
		
	}

	function stats($db, $rman)
	{
		$tarea = $db->sendQuery("SELECT id FROM areapool",false, false);
		$tres = $rman->queryAssoc("Area();");
		$tres = sizeof($tres);
		$tarea = sizeof($tarea);
		$tsur = $db->sendQuery("SELECT id FROM surpool;", false, false);
		$tsur = sizeof($tsur);
		$ttemp = $db->sendQuery("SELECT id FROM surtemplate;", false, false);
		$ttemp = sizeof($ttemp);
		$tass = $db->sendQuery("SELECT id FROM surasset;", false, false);
		$tass = sizeof($tass);
		echo "<b>Overview</b><br />";
		echo "<div class=\"form-item\">";
		echo "Areas: $tarea<br />";
		echo "Resources: $tres";
		echo "</div>";
		echo "<div class=\"form-item\">";
		echo "Surrounds: $tsur<br />";
		echo "Templates: $ttemp<br />";
		echo "Assets: $tass<br />";
		echo "</div>";
	}

	function getAssetFiles($dir, $rel, $fm)
	{
		$list = $fm->listFiles($dir);
		$clist = array();
		foreach($list as $f) {
			$a = explode('.', $f);
			if($a[1] == 'js' || $a[1] == 'css') {

				if($rel == "")
					$val = "$f";
				else
					$val = "$rel/$f";

				$clist[] = array($val, $a[1], $f);
			}
		}

		$list = $fm->listDirectories($dir);
		foreach($list as $d) {
			$ndir = "";
			if($rel == "")
				$ndir = "$d";
			else
				$ndir = "$rel/$d";

			if(($ls = getAssetFiles("$dir/$d", "$ndir", $fm)) != null)
				$clist = array_merge($clist, $ls);
		}

		if(sizeof($clist) == 0)
			return null;

		return $clist;
	}
?>
