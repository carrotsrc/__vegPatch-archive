<?php
/* Copyright 2014, Charlie Fyvie-Gauld (Carrotsrg.org)
 *
 * This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/.
 */
 ?>
<div class="manager-title">
	Problems &amp; Suggestions
</div>

<div class="manager-area">
<?php
	echo "<div style=\"color: orange; font-size: large; font-weight: bold; margin-top: 5px;\">";
	echo "<font style=\"color: #808080; margin-right: 10px;\">#{$vars->details[0]} </font>";
	echo $vars->details[2];
	echo "</div>";

	echo "<div style=\"margin-top: 5px;\">";
	echo "<form method=\"post\" action=\"{$vars->_fallback->update}\">";
	echo "Status: <font style=\"color: #5084A7;\">";
		switch($vars->details[6]) {
		case 0:
			echo "unread";
		break;

		case 1:
			echo "open";
		break;

		case 2:
			echo "work in progress";
		break;

		case 3:
			echo "on hold";
		break;

		case 4:
			echo "cancelled";
		break;

		case 5:
			echo "resolved";
		break;
		}
	echo "</font>";
	echo " / ";
	echo "<select class=\"vform-text vform-select\" name=\"vtms\">";
		echo "<option value=\"0\">unread</option>";
		echo "<option value=\"1\">open</option>";
		echo "<option value=\"2\">work in progress</option>";
		echo "<option value=\"3\">on hold</option>";
		echo "<option value=\"4\">cancelled</option>";
		echo "<option value=\"5\">resolved</option>";
	echo "</select> ";
	echo "<input type=\"hidden\" value=\"{$vars->details[0]}\" name=\"vtmi\" />";
	echo "<input class=\"vform-button\" type=\"submit\" value=\"update\" />";
	echo "</form>";
	echo "</div>";

	echo "<div style=\"padding: 15px; margin-top: 10px; border-bottom: 1px solid #D8D8D8;\">";
		$content = stripslashes($vars->details[3]);
		$content = str_replace("\n", "<br />", $content);
		echo $content;
	echo "</div>";

	echo "<div style=\"padding-left: 30px; margin-top: 10px;\">";
		if($vars->replies == null) {
			echo " - No replies - ";
		}
		else {
			foreach($vars->replies as $r) {
				echo "<div style=\"margin-top: 15px;\">";
				
				$content = stripslashes($r[3]);
				$content = str_replace("\n", "<br />", $content);
				echo $content;
				echo "<div style=\"font-size: small; color: #5084A7; margin-top: 5px; padding-left: 10px;\">";
				echo $r[6];
				echo "<font style=\"color: #808080;\"> ";
				echo $r[5];
				echo "</font>";
				echo "</div>";

				echo "</div>";
			}
		}
	echo "</div>";

	echo "<div style=\"margin-top: 30px;\">";
		echo "<form action=\"{$vars->_fallback->reply}\" method=\"post\">";
			echo "<input type=\"hidden\" name=\"vtmi\" value=\"{$vars->details[0]}\" />";
			echo "<textarea name=\"vtmb\" class=\"vform-text\" rows=\"7\" cols=\"55\"></textarea><br />";
			echo "<input type=\"submit\" value=\"comment\" class=\"vform-button vform-item\" />";
		echo "</form>";
	echo "</div>";

	echo "<a href=\"{$vars->_fallback->focus}\">&lt;&lt; Back</a>";
	echo "<div>";
	echo "</div>";
//	var_dump($vars->details);

?>
</div>
