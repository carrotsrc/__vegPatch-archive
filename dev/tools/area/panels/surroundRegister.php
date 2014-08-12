<?php
	echo "<b>Surround Register</b><br />";
	$sdir = SystemConfig::relativeAppPath("library/surrounds");
	$flist = $fm->listDirectories($sdir);

	if(isset($_GET['op']) && $_GET['op'] == 1) {
		$surround = $_GET['sur'];
		if(!$db->sendQuery("SELECT id FROM surpool WHERE name='$surround';")) {
			$db->sendQuery("INSERT INTO surpool (name) VALUES ('$surround');");
		}
	}

	$dlist = $db->sendQuery("SELECT * FROM surpool");
	if(!$dlist)
		$dlist = array();
	echo "<div class=\"form-item\">";
		echo "<ul>";
		echo "<table>";
		foreach($flist as $f) {
			$loaded = false;
			foreach($dlist as $d)
				if($f == $d['name'])
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
?>
