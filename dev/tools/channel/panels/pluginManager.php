<?php
	if(isset($_POST['wcfg']) && $_POST['wcfg'] == 1) {
		unset($_POST['wcfg']);
		$type = ResCast::cast("Plugin");

		$cid = $_POST['cid'];
		unset($_POST['cid']);

		$inst = $_POST['inst'];
		unset($_POST['inst']);

		setWidgetConfig($type, $cid, $inst, $_POST, $db);
	}

	$sel = null;

	echo "<b>Plugin Manager</b><br />";
	$rid = pluginResource($plugin, $rman);
	echo "<div class=\"form-item\" style=\"margin-top: 10px;\">";
	echo "<b>$plugin</b>";
	if($rid ==  null) {
		echo "<form name=\"reg-plugin\">\n";
			echo "<input type=\"hidden\" name=\"tool\" value=\"channel\" />\n";
			echo "<input type=\"hidden\" name=\"op\" value=\"1\" />\n";
			echo "<input type=\"hidden\" name=\"cplugin\" value=\"$plugin\" />\n";
			echo "<input type=\"hidden\" name=\"mode\" value=\"plugin\" />\n";
			echo "<input type=\"submit\" class=\"form-button\" value=\"Register Resource\" />\n";
		echo "</form>";
		echo "</div>";
		return;

	}
	echo "<br ><font class=\"font-small\">Plugin($rid)</font>\n";

//		$rid = pluginResource($plugin, $rman);
	$ls = pluginInstances($rid, $rman);
	echo "<div class=\"panel-box form-item\">";
		if($ls != null){
			echo "<table>\n";
			foreach($ls as $p) {
				echo "<tr>";
				echo "<td style=\"text-align: right;\"><a href=\"index.php?tool=channel&mode=plugin&op=3&id={$p['id']}&cplugin={$plugin}&cinst={$p['handler']}\" class=\"switch-a\">{$p['label']}</a></td>";
				if(isset($_GET['cinst']) && $p['handler'] == $_GET['cinst'])
					$sel = $p;
				echo "<td>=&gt; {$p['handler']}</td>";
				echo "<td class=\"font-small\">({$p['id']})</td>";
				echo "</tr>";
			}
			echo "</table>";
		}
		else
			echo "No instances";
	echo "</div>";

	if(isset($_GET['op']) && ($_GET['op'] == 3 || $_GET['op'] == 4) && $sel != null) {
		include(SystemConfig::relativeAppPath("system/plugin/pluginman.php"));
		$pluginman = new PluginMan($db);
		echo "<hr />";
		echo "Edit Instance<br />";
		echo "<form name=\"ninst\" method=\"post\" class=\"font-small form-item\" action=\"index.php?tool=channel&id={$_GET['id']}&mode=plugin&cplugin=$plugin&op=4&cinst={$_GET['cinst']}\">";
		echo "<b>Label</b><br />";
		echo "<input type=\"text\" name=\"label\" class=\"form-text font-small\" value=\"{$sel['label']}\" style=\"margin-top: 0px; margin-bottom: 7px;\" /><br />";
		echo "<b>Ref</b><br />";
		echo "<input type=\"text\" name=\"handler_ref\" class=\"form-text font-small\" value=\"{$sel['handler']}\" style=\"margin-top: 0px; width: 40px;\"/>";
		echo "<input type=\"submit\" class=\"form-button font-small\" value=\"Modify\" style=\"margin-left: 10px; margin-top:0px;\" />";
		echo "<a href=\"index.php?tool=channel&mode=plugin&cplugin={$_GET['cplugin']}\" class=\"switch-a\" style=\"margin-left: 15px\">cancel</a>";
		echo "</form>";
		echo "</div>";

		$res = $rman->getResourceFromId($rid);
		$plugin = $pluginman->getPlugin($res['handler'], $_GET['cinst']);
		if($plugin == null) {
			echo "</div>";
			return;
		}
		$cfg = null;
		$clist = null;
		if(($clist = $plugin->getConfigList()) == null) {
			echo "</div>";
			return;
		}
		echo "</div>";

		$cfg = loadWidgetConfig($res['handler'], $_GET['cinst'], $db);
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
		echo "<input type=\"hidden\" name=\"cid\" value=\"{$res['handler']}\" />";
		echo "<input type=\"hidden\" name=\"inst\" value=\"{$_GET['cinst']}\" />";
		echo "<input name=\"wcfg\" type=\"hidden\" value=\"1\" />";
		echo "<input type=\"submit\" value=\"Update Configs\" class=\"form-button\" />";
		echo "</div>";

		echo "</form>";
		echo "</div>";

	}
	else {
		echo "<hr />";
		echo "New Instance<br />";
		echo "<form name=\"ninst\" method=\"post\" class=\"font-small form-item\" action=\"index.php?tool=channel&mode=plugin&cplugin=$plugin&op=2\">";
		echo "<b>Label</b><br />";
		echo "<input type=\"text\" name=\"label\" class=\"form-text font-small\" style=\"margin-top: 0px; margin-bottom: 7px;\" /><br />";
		echo "<b>Ref</b><br />";
		echo "<input type=\"text\" name=\"ref\" class=\"form-text font-small\" style=\"margin-top: 0px; width: 40px;\"/>";
		echo "<input type=\"submit\" class=\"form-button font-small\" value=\"Add\" style=\"margin-left: 10px; margin-top:0px;\" />";
		echo "</form>";
		echo "</div>";
	}

?>
