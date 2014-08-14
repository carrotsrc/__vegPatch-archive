<?php

		if(isset($_POST['wcfg']) && $_POST['wcfg'] == 1) {
			unset($_POST['wcfg']);
			$type = ResCast::cast("Component");

			$cid = $_POST['cid'];
			unset($_POST['cid']);

			$inst = $_POST['inst'];
			unset($_POST['inst']);

			setWidgetConfig($type, $cid, $inst, $_POST, $db);
		}
		echo "<b>Component Manager</b><br /><br />";
		$details = $db->sendQuery("SELECT * FROM modreg WHERE id='$id' && space='{$_GET['nspace']}';", false, false);
		if(!$details) {
			echo "No Details";
			return;
		}

		$details = $details[0];
		echo "<b>{$details['module_name']}</b> ({$details['id']})<br />";

		$rq = "Component('{$details['id']}');";
		$res = $rman->queryAssoc($rq);
		if(!$res) {
			echo "<div class=\"form-item\">\n";
			echo "<form method=\"post\" action=\"index.php?tool=module&mode=cmptman&nspace={$_GET['nspace']}\">";
				echo "<input name=\"cid\" type=\"hidden\" value=\"$id\" class=\"form-button\"/>\n";
				echo "<input name=\"name\" type=\"hidden\" value=\"{$details['module_name']}\" class=\"form-button\"/>\n";
				echo "<input name=\"op\" type=\"hidden\" value=\"13\" class=\"form-button\"/>\n";
				echo "<input type=\"submit\" value=\"Register Resource\" class=\"form-button\"/>\n";
			echo "</form>";
			echo "</div>\n";
		}
		else {
			echo "<div class=\"form-item font-small\">Component( {$res[0]['id']} ) =&gt; {$details['id']}</div>";
			$rq = "Instance(){r,l}<Component('{$details['id']}');";
			$res = $rman->queryAssoc($rq);

			echo "<div class=\"form-item\">";
			echo "Instances";
			echo "<div class=\"panel-box\">";
			if($res != false) {
				echo "<table>\n";
				foreach($res as $r) {
					// // // //$c = $rman->getResourceFromId($r[0]);
					echo "<tr>";
					echo "<td style=\"text-align: right;\"><a href=\"index.php?tool=module&mode=cmptman&op=12&id={$r['id']}&cid={$id}&nspace={$_GET['nspace']}\" class=\"switch-a\">{$r['label']}</a></td>";
					echo "<td>=&gt; {$r['ref']}</td>";
					echo "<td class=\"font-small\">({$r['id']})</td>";
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
					if($r['id'] == $_GET['id'])
						$sel = $rman->getResourceFromId($r['id']);


				echo "<div class=\"form-item\" style=\"\">";
					echo "Edit Instance<br />";
					echo "<form method=\"post\" action=\"index.php?tool=module&mode=cmptman&nspace={$_GET['nspace']}\">";

					echo "<div class=\"form-item\">";
						echo "<font class=\"font-small\"><b>Label</b><br /></font>";
						echo "<input name=\"label\" type=\"text\" class=\"form-text\" style=\"margin-top: 0px;\" value=\"{$sel['label']}\"/>";
					echo "</div>";

					echo "<div class=\"form-item\">";
						echo "<font class=\"font-small\"><b>Ref</b><br /></font>";
						echo "<input name=\"ref\" type=\"text\" class=\"form-text\" style=\"margin-top: 0px; width: 35px;\" value=\"{$sel['handler']}\" />";
						echo "<input name=\"op\" type=\"hidden\" value=\"12\" />";
						echo "<input name=\"cid\" type=\"hidden\" value=\"$id\" />";
						echo "<input name=\"id\" type=\"hidden\" value=\"{$sel['id']}\" />";
						echo "<input name=\"submit\" type=\"submit\"  value=\"Modify\" style=\"margin-top: 0px; margin-left: 15px;\" class=\"form-button\" />";
						echo "<a href=\"index.php?tool=module&mode=cmptman&cid={$id}&nspace={$_GET['nspace']}\" class=\"switch-a\" style=\"margin-left: 15px\">cancel</a>";
					echo "</div>";

					echo "</form>";
				echo "</div>";
				echo "</div>";

				$cmpt = ModMan::getComponent($details['id'], 0, $db);
				if($cmpt == null) {
					echo "</div>";
					return;
				}
				$cfg = null;
				$clist = null;
				if(($clist = $cmpt->getConfigList()) == null) {
					echo "</div>";
					return;
				}

				$cfg = loadWidgetConfig($details['id'], $sel['handler'], $db);
				if($cfg == null)
					$cfg = array();
				echo "<br /><br /><br /><div class=\"form-item\" style=\"float: left; margin-left: 10px; color: #808080;\">";
				echo "<form action=\"\" method=\"post\">";
				echo "<b>Widget Configs</b><br />";
				echo "<div class=\"form-item\">";
				echo "<table>";
				foreach($clist as $c) {
					echo "<tr>";
					echo "<td style=\"color: #808080;\">$c</td>";
					echo "<td>";
					echo "<input class=\"form-text\" type=\"text\" name=\"$c\" max-width=\"8\" value=\"";
					foreach($cfg as $v) {
						if($v[1] == $c) {
							echo $v[2];
						}
					}
					echo "\" />";
					echo "</td>";
					echo "</tr>";
				}
				echo "</table>";
				echo "<input type=\"hidden\" name=\"cid\" value=\"{$details['id']}\" />";
				echo "<input type=\"hidden\" name=\"inst\" value=\"{$sel['handler']}\" />";
				echo "<input name=\"wcfg\" type=\"hidden\" value=\"1\" />";
				echo "<input type=\"submit\" value=\"Update Configs\" class=\"form-button\" />";
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
						echo "<font class=\"font-small\"><b>Instance Parameters</b><br /></font>";
						echo "<input name=\"params\" type=\"text\" class=\"form-text\" style=\"margin-top: 0px;\" autocomplete=off/>";
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
?>
