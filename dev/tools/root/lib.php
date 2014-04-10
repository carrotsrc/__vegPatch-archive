<?php
	function rootConfig($db)
	{
		if(isset($_POST['op']) && $_POST['op'] == 1) {
			$val = "";
			$sz = sizeof($_POST)-1;
			foreach($_POST as $k => $p) {
				if($k == 'op')
					continue;

				$val .= "$k='$p'";
				if($sz-- > 1)
					$val .= ", ";
			}

			$sql = "UPDATE rootconfig SET $val;";
			$db->sendQuery($sql);
		}
		echo "<b>Root Configuration</b>";
		$config = $db->sendQuery("SELECT * FROM rootconfig;", false, true);
		if(!$config) {
			$config = array(
				"title" => "",
				"assetrequest" => "",
				"ajaxrequest" => "",
				"submitrequest" => "",
				"boilerplate" => "",
				"libshare" => "",
				"globalasset" => "",
				"assetgrouping" => ""
				);
		}
		else
			$config = $config[0];

		echo "<form method=\"post\" action=\"index.php?tool=root&mode=config\">";
		echo "<table>";
		foreach($config as $k => $v) {
			echo "<tr>";
			echo "<td style=\"color: grey; text-align: right;\">$k</td><td> <input class=\"form-text margin-top: 0px; margin-left: 5px;\" name=\"$k\" value=\"$v\" autocomplete=\"off\"/></td>";
			echo "</div>";
			echo "</tr>";
		}
		echo "</table>";
		echo "<input type=\"hidden\" name=\"op\" value=\"1\">";
		echo "<input class=\"form-button float-r\" type=\"submit\" value=\"Update\">";
		echo "</form>";
	}

	function rootAssets($db, $fm)
	{
		if(isset($_GET['op']) && $_GET['op'] == 1) {
			$path = $_GET['name'];
			if(!$db->sendQuery("SELECT id FROM rootasset WHERE value='$path';",false, false)) {
				$a = explode("/", $path);
				$name = end($a);
				$type = explode(".", $name);
				$type = end($type);

				$sql = "INSERT INTO rootasset (type, name, value) VALUES ";
				$sql .= "('$type', '$name', '$path');";
				$db->sendQuery($sql);
			}
		}
		else
		if(isset($_GET['op']) && $_GET['op'] == 2) {
			$db->sendQuery("DELETE FROM rootasset WHERE id='{$_GET['id']}';", false, false);
		}

		$gpath = $db->sendQuery("SELECT globalasset FROM rootconfig", false, false);
		$gpath = $gpath[0][0];
		$path = SystemConfig::relativeAppPath("library/{$gpath}");
		$aflist = getAssetFiles($path, "", $fm);
		if($aflist == null)
			$aflist = array();
		$alist = $db->sendQuery("SELECT * FROM rootasset;", false, false);
		if($alist == false)
			$alist = array();

		echo "<b>Root Assets</b><br />";
		echo "<div class=\"form-item\">";
			echo "<b class=\"font-small\">$gpath</b>";
			echo "<div class=\"panel-box\">";
				echo "<table>";
				foreach($aflist as $f) {
					echo "<tr>";

					$loaded = false;
					$id = 0;
					
					foreach($alist as $a) {
						if($a[3] == $f[0]) {
							$loaded = true;
							$id = $a[0];
						}
					}

						if($loaded) {
							echo "<td>{$f[2]}</td>";
							echo "<td>/{$f[0]}</td>";
							echo "<td>";
							echo "<a href=\"index.php?tool=root&mode=asset&op=2&id=$id\" class=\"switch-a\" style=\"color:red;\">X</a>";
							echo "</td>";
						}
						else {
							echo "<td><a href=\"index.php?tool=root&mode=asset&op=1&name={$f[0]}\" class=\"switch-a\">{$f[2]}</a></td>";
							echo "<td>/{$f[0]}</td>";
							echo "<td> </td>";
						}
					echo "</tr>";
				}
// check to see if entry exists
				foreach($alist as $a) {
					$exists = false;

					foreach($aflist as $f) {
						if($f[0] == $a[3])
							$exists = true;
					}

					if($exists)
						continue;

					$id = $a[0];
					echo "<tr>";
					echo "<td>{$a[2]}</td>";
					echo "<td>???</td>";
					echo "<td>";
					echo "<a href=\"index.php?tool=root&mode=asset&op=2&id=$id\" class=\"switch-a\" style=\"color:red;\">X</a>";
					echo "</td>";
					echo "</tr>";
				}
				echo "</table>";
				echo "";
			echo "</div>";
		echo "</div>";
	}

	function getAssetFiles($dir, $rel, $fm)
	{
		$list = $fm->listFiles($dir);
		$clist = array();
		foreach($list as $f) {
			$a = explode('.', $f);
			if(!isset($a[1]))
				continue;

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
