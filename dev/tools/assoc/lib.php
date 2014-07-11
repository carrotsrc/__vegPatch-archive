<?php
/* Copyright 2014, Charlie Fyvie-Gauld (Carrotsrg.org)
 *
 * This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/.
 */
	
	function relationshipPanel($prq, $crq, $rman, $db)
	{
		$etype = $db->sendQuery("SELECT * FROM edgetype;", false, false);
		echo "<b>New Relationship</b><br />";
		echo "<div style=\"float: left; width: 350px;\" class=\"font-small form-item\">";
			echo "<b>Parent</b>";
			echo "<form action=\"index.php?tool=assoc&mode=newrel\" method=\"post\">";
				echo "<input autocomplete=\"off\" type=\"text\" class=\"form-text\" name=\"prq\" ";
					if($prq != null)
						echo "value=\"$prq\" ";
				echo "/>";

				if($crq != null)
					echo "<input type=\"hidden\" name=\"crq\" value=\"$crq\" />";
				echo "<br /><input type=\"submit\" class=\"form-button\" value=\"query\">";
			echo "</form>";
		echo "</div>";

		echo "<div style=\"float: left; width: 350px; margin-left: 10px;\" class=\"font-small form-item\">";
			echo "<b>Child</b>";
			echo "<form action=\"index.php?tool=assoc&mode=newrel\" method=\"post\">";
				echo "<input autocomplete=\"off\" type=\"text\" class=\"form-text\" name=\"crq\" ";
					if($crq != null)
						echo "value=\"$crq\" ";
				echo "/>";

				if($prq != null)
					echo "<input type=\"hidden\" name=\"prq\" value=\"$prq\" />";

				echo "<br /><input type=\"submit\" class=\"form-button\" value=\"query\">";
			echo "</form>";
		echo "</div>";
		echo "<br />";

		$pr = $cr = null;
		if($prq != null && $prq != "")
			$pr = $rman->queryAssoc($prq);


		if($crq != null && $crq != "")
			$cr = $rman->queryAssoc($crq);

		echo "<form name=\"final-relationship\" method=\"post\" action=\"index.php?tool=assoc&mode=newrel\" >";
		echo "<div style=\"float: left; width: 350px;\" class=\"font-small form-item\">";
		echo "<div class=\"panel-box\" style=\"height: 250px\">";
			if($pr != null || $pr != false) {
				echo "<table>";
				foreach($pr as $r) {
					$res = $rman->getResourceFromId($r[0]);
					echo "<tr>";
						echo "<td>";
						echo "<input type=\"checkbox\" name=\"p-{$res['id']}\">";
						echo "</td>";

						echo "<td>";
						echo $res['id'];
						echo "</td>";
						echo "<td style=\"text-align: center;\">";
						echo $res['label'];
						echo "</td>";
						echo "<td>";
						echo "=> ".$res['handler'];
						echo "</td>";
					echo "</tr>";
				}
				echo "</table>";
			}
			else
				echo "No query sent";
		echo "</div>";
		echo "</div>";
		
		echo "<div style=\"float: left; width: 350px; margin-left: 10px;\" class=\"font-small form-item\">";
		echo "<div class=\"panel-box\" style=\"height: 250px\">";
			if($cr != null || $cr != false) {
				echo "<table>";
				foreach($cr as $r) {
					$res = $rman->getResourceFromId($r[0]);
					echo "<tr>";
						echo "<td>";
						echo "<input type=\"checkbox\" name=\"c-{$res['id']}\">";
						echo "</td>";
						echo "<td>";
						echo $res['id'];
						echo "</td>";
						echo "<td style=\"text-align: center;\">";
						echo $res['label'];
						echo "</td>";
						echo "<td>";
						echo "=> ".$res['handler'];
						echo "</td>";
					echo "</tr>";
				}
				echo "</table>";
			}
			else
				echo "No query sent";
		echo "</div>";
		echo "</div><br />";
		echo "<input type=\"hidden\" name=\"op\" value=\"1\">";
		echo "<input type=\"hidden\" name=\"tool\" value=\"assoc\">";
		echo "<select class=\"form-text form-select float-l\" name=\"edge\">";
			echo "<option value=\"0\">Normal Edge</option>";
			foreach($etype as $edge) {
				$t = ResCast::cast($edge[1]);
				echo "<option value=\"{$edge[0]}\">{$t['type']}(): {$edge[2]}</option>";
			}
		echo "</select>";
		echo "<input type=\"submit\" style=\"margin-left: 6px;\" class=\"form-button float-r\" value=\"Create Relationship\">";
		echo "</form>";
		echo "<form method=\"post\" action=\"index.php?tool=assoc&mode=newrel\">";
		echo "<input type=\"submit\"  class=\"form-button float-r\" value=\"clear\">";
		echo "</form>";
	}

	function createRelationship($edge, $rman)
	{
		$p = array();
		$c = array();
		foreach($_POST as $k => $item) {
			if($k == "op" || $k == "edge")
				continue;

			$a = explode("-", $k);
			if($a[0] == 'p')
				$p[] = $a[1];
			else
			if($a[0] == 'c')
				$c[] = $a[1];

		}

		if(sizeof($p) == 0 || sizeof($c) == 0)
			return;

		foreach($p as $rp) {
			foreach($c as $rc) {
				$rman->createRelationship($rp, $rc, $edge);
			}
		}
	}

	function managePanel($prq, $rman, $db)
	{
			echo "<b>Manage Relationship</b><br />";
			$res = null;

			if($prq != null && $prq != "")
				$res = $rman->queryAssoc($prq, true);
				
			echo "<div class=\"form-item\" style=\"overflow: auto; padding: 0px; width: auto; min-width: 600px;\">";
				echo "<b>Query</b>";
				echo "<form action=\"index.php?tool=assoc&mode=manrel\" method=\"post\">";
					echo "<input type=\"text\" style=\"width: 100%;\" class=\"form-text\" name=\"query\" ";
						if($prq != null)
							echo "value=\"$prq\" ";
					echo "autocomplete=\"off\"/>";

					echo "<br /><input type=\"submit\" class=\"form-button float-r\" value=\"query\">";
				echo "</form><br />";

				echo "<form action=\"index.php?tool=assoc&mode=manrel\" method=\"post\">";
				echo "<div class=\"form-item panel-box\"  style=\"height: 250px\">";
					if($res != null || $res != false) {
						echo "<table>";
						foreach($res as $r) {
							$rid = $r[0];
							$rp = $rman->getResourceFromId($r[1]);
							$rc = $rman->getResourceFromId($r[2]);
							$tp = ResCast::cast($rp['type']);
							$tc = ResCast::cast($rc['type']);
							$edge = null;
							if($r[3] > 0)
								$edge = $db->sendQuery("SELECT label FROM edgetype WHERE id='{$r[3]}';", false, false);

							echo "<tr>";
								echo "<td>";
								echo "<input type=\"checkbox\" name=\"{$rid}\">";
								echo "</td>";
								echo "<td>";
								echo "{$tp['type']}( '{$rp['label']}' )";
								echo "</td>";
								echo "<td>&gt;</td>";
								echo "<td style=\"text-align: center;\">";
								echo "{$tc['type']}( '{$rc['label']}' )";
								echo "</td>";
								echo "<td>";
									if($edge == null)
										echo ":normal";
									else
										echo ":{$edge[0][0]}";
								echo "</td>";
							echo "</tr>";
						}
						echo "</table>";
					}
					else
					if($res === false)
						echo "No results";
					else
						echo "No query sent";
				echo "</div>";
			echo "</div>";
				echo "<input type=\"hidden\" name=\"op\" value=\"2\">";
				echo "<input type=\"hidden\" name=\"query\" value=\"$prq\">";
				echo "<input type=\"hidden\" name=\"tool\" value=\"assoc\">";
				echo "<input type=\"submit\" value=\"remove\" style=\"margin-top: -10;\"class=\"form-button\" />";
			echo "</form>";
			echo "<form method=\"post\" action=\"index.php?tool=assoc&mode=manrel\">";
				echo "<input type=\"submit\" value=\"clear\" style=\"margin-top: -21;\" class=\"form-button float-r\" />";
			echo "</form>";
	}

	function removeRelationships($rman)
	{
		foreach($_POST as $k => $v) {
			if($k == 'op' || $k == "query")
				continue;

			$rman->removeRelationshipWithId($k);
		}
	}

	function edgePanel($rman, $db)
	{
		$types = $rman->getResCast();

		$mod = false;
		$det = null;
		if(isset($_GET['op']) && $_GET['op'] == 2) {
			$mod = $_GET['id'];
			$det = $db->sendQuery("SELECT * FROM edgetype WHERE id='$mod';", false, false);
			$det = $det[0];
		}
		else
		if(isset($_POST['op']) && $_POST['op'] == 1) {
			addEdge($_POST['type'], $_POST['label'], $db);
		}
		else
		if(isset($_POST['op']) && $_POST['op'] == 3) {
			modifyEdge($_POST['id'], $_POST['type'], $_POST['label'], $db);
		}

		$edges = $db->sendQuery("SELECT * FROM edgetype;", false, false);
		echo "<b>Edge Manager</b>";
		echo "<div class=\"panel-box form-item\" style=\"height: auto;\">";
			echo "<table>";
			foreach($edges as $e) {
				echo "<tr>";
				echo "<td>";
				$o = ResCast::cast($e[1]);

				echo "<a class=\"switch-a\" href=\"index.php?tool=assoc&mode=edge&op=2&id={$e[0]}\">{$e[2]}</a>";
				echo "</td>";

				echo "<td>";
				echo ":{$o['type']}()";
				echo "</td>";

			}
			echo "</table>";
		echo "</div>";

		echo "<hr />";
		if($det == null)
			echo "New Edge<br />";
		else
			echo "Modify Edge<br />";
		echo "<form method=\"post\" action=\"index.php?tool=assoc&mode=edge\">";
		echo "<div class=\"form-item font-small\">";
		echo "<b>Type</b><br />";
		echo "<select name=\"type\" class=\"form-text form-select\" style=\"margin-top: 0px;\">";
			foreach($types as $t) {
				if($det != null && $t['id'] == $det[1])
					echo "<option value=\"{$t['id']}\" selected>{$t['type']}</option>";
				else
					echo "<option value=\"{$t['id']}\">{$t['type']}</option>";
			}
		echo "</select>";
		echo "</div>";

		echo "<div class=\"form-item font-small\">";
		echo "<b>Label</b><br />";
		if($det == null)
			echo "<input name=\"label\" value=\"\" type=\"text\" class=\"form-text\" style=\"margin-top: 0px;\" autocomplete=\"off\">";
		else
			echo "<input name=\"label\" type=\"text\" class=\"form-text\" style=\"margin-top: 0px;\" autocomplete=\"off\" value=\"{$det[2]}\">";

		if(!$mod)
			echo "<input type=\"hidden\" name=\"op\" value=\"1\">";
		else {
			echo "<input type=\"hidden\" name=\"id\" value=\"$mod\">";
			echo "<input type=\"hidden\" name=\"op\" value=\"3\">";
		}
		echo "<input type=\"hidden\" name=\"tool\" value=\"assoc\">";

		if($det == null)
			echo "<br /><input type=\"submit\" class=\"form-button\" value=\"Add\">";
		else
			echo "<br /><input type=\"submit\" class=\"form-button\" value=\"Modify\">";
		echo "</div>";
		echo "</form>";

		if($det != null) {
			echo "<form method=\"post\" action=\"index.php?tool=assoc&mode=edge\">";
			echo "<input type=\"submit\" class=\"form-button\" style=\"margin-top: -20px;\" value=\"cancel\">";
			echo "</form>";
		}
	}

	function addEdge($type, $label, $db)
	{
		$sql = "SELECT id FROM edgetype WHERE rtype=\"$type\" AND label=\"$label\";";
		if($db->sendQuery($sql) != false)
			return;

		$sql = "INSERT INTO edgetype (rtype, label) VALUES (\"$type\", \"$label\");";
		$db->sendQuery($sql);
	}

	function modifyEdge($id, $type, $label, $db)
	{
		$sql = "UPDATE edgetype SET rtype='$type', label='$label' WHERE id='$id';";
		$db->sendQuery($sql);
	}
?>
