<?php
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
				if($t == $td['value'])
					$id = $td['t_id'];

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
		if($aflist) foreach($aflist as $a) {
			$loaded = false;
			$id = 0;
			foreach($alist as $ad)
				if($a[0] == $ad['value']) {
					$loaded = true;
					$id = $ad['id'];
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
?>
