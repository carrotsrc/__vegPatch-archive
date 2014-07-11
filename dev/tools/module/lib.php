<?php
/* Copyright 2014, Charlie Fyvie-Gauld (Carrotsrg.org)
 *
 * This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/.
 */
 	function loadWidgetConfig($cid, $ref, $db)
	{
		$sql = "SELECT `widget_cfgreg`.`id`, `widget_cfgreg`.`config`, `widget_cfgreg`.`value` FROM `widget_cfgreg` JOIN `rescast` ON `widget_cfgreg`.`type` = `rescast`.`id` ";
		$sql .= "WHERE `rescast`.`type`='Component' AND `widget_cfgreg`.`inst`='{$ref}' ";
		$sql .= "AND `widget_cfgreg`.`cid`='{$cid}'";
		$r = $db->sendQuery($sql, false, false);
		if(!$r)
			return null;

		return $r;
	}

	function setWidgetConfig($type, $cid, $ref, $configs, $db)
	{
		$vals = loadWidgetConfig($cid, $ref, $db);
		// delete values first
		if($vals != null) {
			$q = array();
			foreach($configs as $k => $v) {
				if($v == "") {
					foreach($vals as $r) {
						if($r[1] == $k) {
							$q[] = $r[0];
							unset($configs[$k]);
						}
					}
				}
			}


			if(($sz = sizeof($q)) > 0) {
				$sz--;
				$sql = "DELETE FROM `widget_cfgreg` WHERE ";
				foreach($q as $id) {
					$sql .= "`id`='$id'";
					if($sz-- > 0)
						$sql .= " OR ";
				}
				$db->sendQuery($sql);
			}
		}

		// update values next
		if($vals != null) {
			$q = array();
			foreach($configs as $k => $v) {
				foreach($vals as $r)
					if($r[1] == $k)
						$q[] = $r[1];
			}

			if(($sz = sizeof($q)) > 0) {
				foreach($q as $id) {
					$sql = "UPDATE `widget_cfgreg` SET ";
					$sql .= "`value`='{$configs[$id]}' ";
					$sql .= "WHERE `config`='{$id}' AND ";
					$sql .= "`cid`='$cid' AND `inst`='$ref';";
					unset($configs[$id]);
					$db->sendQuery($sql);
				}
			}
		}

		// insert any values left
		foreach($configs as $cfg => $value) {
			$sql = "INSERT INTO `widget_cfgreg` (`type`, `cid`, `inst`, `config`, `value`) VALUES ";
			$sql .= "('{$type['id']}', '$cid', '$ref', '$cfg', '$value');";
			//echo "<pre>$sql</pre>";
			$r = $db->sendQuery($sql);
		}
	}

	function cmptregPanel($data)
	{
		echo "<b>Component Registry</b><br />\n";
		if($data == null) {
			echo "Failed to load registry";
			return;
		}
		echo $_GET['nspace']."/<br />";
		echo "<table>";
		echo "<ul>";
		foreach($data as $cmpt) {
			echo "<tr><td>";
			if($cmpt[2] == 1)
				echo "<li style=\"color: #B3DD63;\">";
			else
				echo "<li style=\"color: grey;\">";
			
			if($cmpt[2] == 0)
				echo "<a class=\"switch-a\" href=\"index.php?tool=module&mode=cmptreg&op=1&id={$cmpt[1]}&nspace={$_GET['nspace']}\" style=\"color: grey;\">{$cmpt[1]}</a>";
			else {
				echo "<font style=\"color: grey;\">{$cmpt[1]}</font></td>";
				echo "<td style=\"padding-left: 10px; font-size: x-small;\"><font style=\"color: grey;\">{$cmpt[0]}</font>";
			}

			echo "</li>";
			echo "</td></tr>";
		}
		echo "</ul>";
		echo "</table>";
	}

	function registerComponent($name, $db)
	{
		if($db->sendQuery("SELECT id FROM modreg WHERE module_name='$name';", false, false) != false)
			return;

		$sql = "INSERT INTO modreg ";
		$sql .= "(module_type, module_name, space, active, version) ";
		$sql .= "VALUES ";
		$sql .= "(0, '$name', '{$_GET['nspace']}', 1, '1.0')";
		$db->sendQuery($sql);
	}

	function manageComponent($id, $db, $rman)
	{
		include("panels/manageComponent.php");
	}

	function modifyInstance($rid, $label, $ref, $db)
	{
		$sql = "UPDATE respool SET label='$label', handler_ref='$ref' WHERE id='$rid';";
		$db->sendQuery($sql);
	}

	function addInstance($cid, $label, $ref, $db, $rman, $params = null)
	{
		$cmpt = ModMan::getComponent($cid, 0, $db);

		if($params != null) {
			$atoms = explode(";", $params);
			$params = array();
			foreach($atoms as $a) {
				$na = explode("=", $a);
				if(sizeof($na) > 1)
					$params[$na[0]] = $na[1];
			}
		}
		$nref = $cmpt->createInstance($params);
		if($nref != 0)
			$ref = $nref;

		$crid = $rman->queryAssoc("Component('$cid');");
		$crid = $crid[0][0];

		$rid = $rman->addResource('Instance', $ref, $label);
		if(!$rid)
			return;

		$rman->createRelationship($crid, $rid);
	}

	function registerResource($id, $name, $rman)
	{
		$rman->addResource('Component', $id, $name);
	}

	function panelReg($dir, $space, $db) 
	{
		$sql = "SELECT id, module_name FROM modreg WHERE module_type='1' AND space='$space'";
		$reg = $db->sendQuery($sql, false, false);
		if(!$reg)
			$reg = array();


		$lduld = array();

		foreach($dir as $p) {
			$loaded = false;
			foreach($reg as $ld) {
				if($p == $ld[1])
					$loaded = $ld;
			}
			if($loaded !== false)
				$lduld[] = array($loaded[0], $loaded[1], 1);
			else
				$lduld[] = array(0, $p, 0);
		}

		echo "<b>Panel Registry</b><br />";
		echo "<div class=\"form-item\">";
			echo "$space/";
			echo "<table>";
			echo "<ul>";
			foreach($lduld as $cmpt) {
				echo "<tr><td>";
				if($cmpt[2] == 1)
					echo "<li style=\"color: #B3DD63;\">";
				else
					echo "<li style=\"color: grey;\">";
				
				if($cmpt[2] == 0)
					echo "<a class=\"switch-a\" href=\"index.php?tool=module&mode=panelreg&space=$space&op=1&id={$cmpt[1]}\" style=\"color: grey;\">{$cmpt[1]}</a>";
				else {
					echo "<font style=\"color: grey;\">{$cmpt[1]}</font></td>";
					echo "<td style=\"padding-left: 10px; font-size: x-small;\"><font style=\"color: grey;\">{$cmpt[0]}</font>";
				}

				echo "</li>";
				echo "</td></tr>";
			}
			echo "</ul>";
			echo "</table>";
		echo "</div>";
	}

	function registerPanel($name, $space, $db)
	{
		if($db->sendQuery("SELECT id FROM modreg WHERE module_name='$name';", false, false) != false)
			return;

		$sql = "INSERT INTO modreg ";
		$sql .= "(module_type, module_name, space, active, version) ";
		$sql .= "VALUES ";
		$sql .= "(1, \"$name\", \"$space\", 1, \"1.0\")";
		$db->sendQuery($sql);
	}

	function registerPanelResource($id, $name, $rman)
	{
		if($rman->queryAssoc("Panel('$id');") != false)
			return;

		$rman->addResource('Panel', $id, $name);
	}

	function managePanel($id, $space, $db, $rman)
	{
		include("panels/managePanel.php");
	}

	function stats($db, $rman)
	{
		$tcmpt = $db->sendQuery("SELECT id FROM modreg WHERE module_type='0';", false, false);
		if(!$tcmpt)
			$tcmpt = 0;
		else
			$tcmpt = sizeof($tcmpt);

		$tpanel = $db->sendQuery("SELECT id FROM modreg WHERE module_type='1';", false, false);
		if(!$tpanel)
			$tpanel = 0;
		else
			$tpanel = sizeof($tpanel);

		$tinst = 0;
		if($tcmpt > 0) {
			$tinst = $rman->queryAssoc("Instance()<Component();");
			if(!$tinst)
				$inst = 0;
			else
				$tinst = sizeof($tinst);
		}

		$tt = $tcmpt + $tpanel + $tinst;
		echo "<b>Overview</b><br />";

		echo "<div class=\"form-item\">";
		echo "Components: $tcmpt<br />";
		echo "Instances: $tinst";
		echo "</div>";

		echo "<div class=\"form-item\">";
		echo "Panels: $tpanel<br />";
		echo "</div>";

		echo "<div class=\"form-item\">";
		echo "Total Resources: $tt<br />";
		echo "</div>";
	}
?>
