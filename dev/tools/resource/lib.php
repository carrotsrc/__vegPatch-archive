<?php
/* Copyright 2014, Charlie Fyvie-Gauld (Carrotsrg.org)
 *
 * This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/.
 */
	
	function newResourcePanel($db, $rman)
	{
		if(isset($_POST['op']) && $_POST['op'] == 1) {
			$tl = ResCast::cast($_POST['type']);
			$tl = $tl['type'];
			if(!$rman->queryAssoc("$tl('{$_POST['label']}');")) {
				if($_POST['ref'] == "")
					$_POST['ref'] = "0";
				$rman->addResource($tl, $_POST['ref'], $_POST['label']);
			}
		}
		$types = $rman->getResCast();
		echo "<b>New Resource</b><br />";
		echo "<div class=\"font-item\">";
			echo "<form method=\"post\" action=\"index.php?tool=resource&mode=newres\">";
				echo "<select name=\"type\" class=\"form-text form-select\">";
					foreach($types as $t)
						echo "<option value=\"{$t['id']}\">{$t['type']}</option>";
				echo "</select>";
				echo " ( '<input class=\"form-text\" type=\"text\" name=\"label\" />' )";
				echo " =&gt; <input class=\"form-text\" style=\"width: 30px\" type=\"text\" name=\"ref\" /><br />";
				echo "<input type=\"hidden\" name=\"op\" value=\"1\">";
				echo "<input type=\"submit\" value=\"Add Resource\" class=\"form-button float-r\">";
			echo "</form>";
		echo "</div>";
	}

	function newTypePanel($db, $rman)
	{
		$bases = $db->sendQuery("SELECT id, label FROM resbase ORDER BY id;");
		if(isset($_POST['op']) && $_POST['op'] == 1) {
			$base = $_POST['base'];
			$type = $_POST['label'];
			$ref = $_POST['ref'];
			if($ref == "")
				$ref = 0;

			if(!$db->sendQuery("SELECT id FROM rescast WHERE type='$type';"))
				$db->sendQuery("INSERT INTO rescast (type, handler, base) VALUES ('$type', '$ref', '$base');");

		}
		echo "<b>New Type</b><br />";
		echo "<div class=\"font-item\">";
			echo "<form method=\"post\" action=\"index.php?tool=resource&mode=newtype\">";
				echo "<select name=\"base\" class=\"form-text form-select\">";
					foreach($bases as $b)
						echo "<option value=\"{$b['id']}\">{$b['label']}</option>";
				echo "</select>";
				echo " ( '<input class=\"form-text\" type=\"text\" name=\"label\" />' )";
				echo " =&gt; <input class=\"form-text\" style=\"width: 30px\" type=\"text\" name=\"ref\" /><br />";
				echo "<input type=\"hidden\" name=\"op\" value=\"1\">";
				echo "<input type=\"submit\" value=\"Add Type\" class=\"form-button float-r\">";
			echo "</form>";
		echo "</div>";
	}
?>
