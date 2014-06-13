<?php
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
			echo "Panel( {$res[0][0]} ) =&gt; $id";
		echo "</div>";
?>
