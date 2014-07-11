<?php
/* Copyright 2014, Charlie Fyvie-Gauld (Carrotsrg.org)
 *
 * This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/.
 */
	
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
		echo "<b>Component Manager</b><br /><br />";
		$details = $db->sendQuery("SELECT * FROM modreg WHERE id='$id' && space='{$_GET['nspace']}';", false, false);
		if(!$details) {
			echo "No Details";
			return;
		}
		$details = $details[0];
		echo "<b>{$details[2]}</b> ({$details[0]})<br />";

		$rq = "Component('{$details[0]}');";
		$res = $rman->queryAssoc($rq);
		if(!$res) {
			echo "<div class=\"form-item\">\n";
			echo "<form method=\"post\" action=\"index.php?tool=module&mode=cmptman&nspace={$_GET['nspace']}\">";
				echo "<input name=\"cid\" type=\"hidden\" value=\"$id\" class=\"form-button\"/>\n";
				echo "<input name=\"name\" type=\"hidden\" value=\"{$details[2]}\" class=\"form-button\"/>\n";
				echo "<input name=\"op\" type=\"hidden\" value=\"13\" class=\"form-button\"/>\n";
				echo "<input type=\"submit\" value=\"Register Resource\" class=\"form-button\"/>\n";
			echo "</form>";
			echo "</div>\n";
		}
		else {
			echo "<div class=\"form-item font-small\">Component( {$res[0][0]} )</div>";
			$rq = "Instance()<Component('{$details[0]}');";
			$res = $rman->queryAssoc($rq);

			echo "<div class=\"form-item\">";
			echo "Instances";
			echo "<div class=\"panel-box\">";
			if($res != false) {
				echo "<table>\n";
				foreach($res as $r) {
					$c = $rman->getResourceFromId($r[0]);
					echo "<tr>";
					echo "<td style=\"text-align: right;\"><a href=\"index.php?tool=module&mode=cmptman&op=12&id={$c['id']}&cid={$id}&nspace={$_GET['nspace']}\" class=\"switch-a\">{$c['label']}</a></td>";
					echo "<td>=&gt; {$c['handler']}</td>";
					echo "<td class=\"font-small\">({$c['id']})</td>";
					echo "</tr>";
				}
				echo "</table>";
			}
			else
				echo "No Instances";
			echo "</div>";
			echo "</div>";
	
			echo "<hr />";
			if(isset($_GET['op']) && $_GET['op'] == 12) {
				$sel = null;
				foreach($res as $r)
					if($r[0] == $_GET['id'])
						$sel = $rman->getResourceFromId($r[0]);



				echo "<div class=\"form-item\">";
					echo "Edit Instance<br />";
					echo "<form method=\"post\" action=\"index.php?tool=module&mode=cmptman&nspace={$_GET['nspace']}\">";

					echo "<div class=\"form-item\">";
						echo "<font class=\"font-small\"><b>Label</b><br /></font>";
						echo "<input name=\"label\" type=\"text\" class=\"form-text\" style=\"margin-top: 0px;\" value=\"{$sel['label']}\"/>";
					echo "</div>";

					echo "<div class=\"form-item\">";
						echo "<font class=\"font-small\"><b>Ref</b><br /></font>";
						echo "<input name=\"ref\" type=\"text\" class=\"form-text\" style=\"margin-top: 0px; width: 35px;\" value=\"{$sel['handler']}\" />";
						echo "<input name=\"op\" type=\"hidden\" value=\"12\"/>";
						echo "<input name=\"cid\" type=\"hidden\" value=\"$id\"/>";
						echo "<input name=\"id\" type=\"hidden\" value=\"{$sel['id']}\"/>";
						echo "<input name=\"submit\" type=\"submit\"  value=\"Modify\" style=\"margin-top: 0px; margin-left: 15px;\" class=\"form-button\" />";
						echo "<a href=\"index.php?tool=module&mode=cmptman&cid={$id}&nspace={$_GET['nspace']}\" class=\"switch-a\" style=\"margin-left: 15px\">cancel</a>";
					echo "</div>";

					echo "</form>";
				echo "</div>";
			}
			else {
				echo "<div class=\"form-item\">";
					echo "New Instance<br />";
					echo "<form method=\"post\" action=\"index.php?tool=module&mode=cmptman&nspace={$_GET['nspace']}\">";

					echo "<div class=\"form-item\">";
						echo "<font class=\"font-small\"><b>Label</b><br /></font>";
						echo "<input name=\"label\" type=\"text\" class=\"form-text\" style=\"margin-top: 0px\" />";
					echo "</div>";

					echo "<div class=\"form-item\">";
						echo "<font class=\"font-small\"><b>Ref</b><br /></font>";
						echo "<input name=\"ref\" type=\"text\" class=\"form-text\" style=\"margin-top: 0px; width: 35px;\" />";
						echo "<input name=\"op\" type=\"hidden\" value=\"11\"/>";
						echo "<input name=\"cid\" type=\"hidden\" value=\"$id\"/>";
						echo "<input name=\"submit\" type=\"submit\"  value=\"Add Instance\" style=\"margin-top: 0px; margin-left: 15px;\" class=\"form-button\" />";
					echo "</div>";

					echo "</form>";
				echo "</div>";
			}
		}

	}

	function modifyInstance($rid, $label, $ref, $db)
	{
		$sql = "UPDATE respool SET label='$label', handler_ref='$ref' WHERE id='$rid';";
		$db->sendQuery($sql);
	}

	function addInstance($cid, $label, $ref, $db, $rman)
	{
		$cmpt = ModMan::getComponent($cid, 0, $db);
		$nref = $cmpt->createInstance();
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

		echo "<b>Panel Manager</b><br /><br />";
		$details = $db->sendQuery("SELECT * FROM modreg WHERE id='$id'", false, false);
		if(!$details) {
			echo "No details";
			return;
		}
		$details = $details[0];
		
		echo "<div class=\"form-item\">";
			echo "$space/";
		echo "</div>";

		echo "<div class=\"form-item\">";
			echo "<b>{$details[2]}</b> ({$details[0]})";
		echo "</div>";

		$res = $rman->queryAssoc("Panel('$id');");
		if(!$res) {
			echo "<div class=\"form-item\">";
			echo "<form method=\"get\" action=\"index.php\">";
				echo "<input name=\"tool\" type=\"hidden\" value=\"module\"/>";
				echo "<input name=\"mode\" type=\"hidden\" value=\"panelman\"/>";
				echo "<input name=\"op\" type=\"hidden\" value=\"1\"/>";
				echo "<input name=\"space\" type=\"hidden\" value=\"$space\"/>";
				echo "<input name=\"pid\" type=\"hidden\" value=\"$id\"/>";
				echo "<input name=\"name\" type=\"hidden\" value=\"{$details[2]}\"/>";
				echo "<input type=\"submit\" class=\"form-button\" value=\"Register Resource\">";
			echo "</form>";
			echo "</div>";
			return;
		}

		echo "<div class=\"form-item font-small\">";
			echo "Panel( {$res[0][0]} )";
		echo "</div>";
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
