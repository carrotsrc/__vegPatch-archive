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
		$det = $db->sendQuery("SELECT name FROM areapool WHERE id='$aid'");
		if(!$det)
			return;
		$rman->addResource('Area', $aid, $det[0]['name']);
	}

	function areaManagerPanel($db, $rman)
	{
		$aid = null;
		$editMode = false;
		$surrounds = null;
		$templates = null;

		if(isset($_POST['aid']))
			$aid = $_POST['aid'];

		if(isset($_POST['op'])) {
			if($_POST['op'] == 1) {
				registerAreaResource($aid, $db, $rman);
			}
			else
			if($_POST['op'] == 2 || $_POST['op'] == 3) {
				$editMode = true;
				$surrounds = $db->sendQuery("SELECT id, name FROM surpool");
				if($_POST['op'] == 3)
					$templates = $db->sendQuery("SELECT t_id, value FROM surtemplate WHERE s_id='{$_POST['sid']}'");
			}
			else
			if($_POST['op'] == 4) {
				$sql = "UPDATE areapool SET ";
				$sql .= "name='{$_POST['label']}', ";
				$sql .= "s_id='{$_POST['sid']}', ";
				$sql .= "st_id='{$_POST['tid']}' ";
				$sql .= "WHERE id='{$_POST['aid']}'";

				$db->sendQuery($sql);

			}
		}

		$res = $rman->queryAssoc("Area('$aid');");

		$det = $db->sendQuery("SELECT * FROM areapool WHERE id='$aid'");
		$det = $det[0];
		include('panels/manageArea.php');
	}

	function newAreaPanel($db)
	{
		$templates = $surrounds = null;
		if(isset($_POST['op'])){
			if($_POST['op'] == 1)
				$templates = $db->sendQuery("SELECT t_id, value FROM surtemplate WHERE s_id='{$_POST['sid']}';", false, false);
			else
			if($_POST['op'] == 2) {
				$sql = "INSERT INTO areapool ";
				$sql .= "(name, s_id, st_id) VALUES ";
				$sql .= "('{$_POST['label']}','{$_POST['sid']}','{$_POST['tid']}');";
				$db->sendQuery($sql, false, false);
				unset($_POST['op']);
			}
		}
		
		$surrounds = $db->sendQuery("SELECT id, name FROM surpool;");
		if(!$surrounds)
			return;

		include('panels/newArea.php');
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

		$det = $db->sendQuery("SELECT * FROM surpool WHERE id='{$sid}';"); 
		$sid = $det[0]['id'];
		$sname = $det[0]['name'];
		$sdir = SystemConfig::relativeAppPath("library/surrounds/$sname");
		$aflist = getAssetFiles($sdir, "", $fm);
	
		if(isset($_GET['op']) && $_GET['op'] == 1) {
			$addTemplate = true;
			$tname = $_GET['name'];
			if(!$db->sendQuery("SELECT id FROM surtemplate WHERE s_id='$sid' AND value='$tname';",false, false)) {
				$mid = $db->sendQuery("SELECT t_id FROM surtemplate WHERE s_id='$sid' ORDER BY t_id DESC LIMIT 1;", false, false);
				$mid = intVal($mid[0]['t_id']);
				$mid++;
				$db->sendQuery("INSERT INTO surtemplate (s_id, t_id, value) VALUES ('$sid', '$mid','$tname');", false, false);
			}
		}
		else
		if(isset($_GET['op']) && $_GET['op'] == 2) {
			$addTemplate = true;
			$tid = $_GET['tid'];
			if($db->sendQuery("SELECT id FROM surtemplate WHERE s_id='$sid' AND t_id='$tid';"))
				$db->sendQuery("DELETE FROM surtemplate WHERE s_id='$sid' AND t_id='$tid';");

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


		$alist = $db->sendQuery("SELECT type, name, value, id FROM surasset WHERE s_id='$sid';");
		if(!$alist)
			$alist = array();

		$tflist = $fm->listFiles($sdir);
		$tlist = $db->sendQuery("SELECT t_id, value FROM surtemplate WHERE s_id='$sid';");
		if(!$tlist)
			$tlist = array();


		include('panels/manageSurround.php');
	}

	function surroundRegister($db, $fm)
	{
		include('panels/surroundRegister.php');
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
