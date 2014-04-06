<?php
/* (C)opyright 2014, Carrotsrc.org
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
<div style="border: 2px dashed orange; padding: 10px; border-radius: 5px; margin-bottom: 30px;">
<b>Send a problem/suggestion</b>
<div class="vform-item">
<form method="post" action="<?php echo $vars->_fallback->submit; ?>">
Give a one line overview here:<br />
<input type="text" class="vform-text vfont-large vform-item" name="vtus" style="width: 50%;" /><br /><br />

If you need to give more details, add them here:<br />
<textarea class="vform-text vform-item" name="vtub" rows="7" cols="55" style="width: 100%;"></textarea>
<input type="submit" value="send report" class="vform-button vfont-large vform-item"/>
</div>
</div>
<div class="vpatch-table" style="width: 95%;">
<b style="font-size: large;">Latest</b>
	<div class="content" style="">
	<table style="width: 100%">
	<tr class="title">
	<td style="width: 60%; text-align: left;">subject</td>
	<td>user</td>
	<td>status</td>
	<td>posted</td>
	</tr>
	<?php
		if($vars->tickets != null) {
			foreach($vars->tickets as $k => $t) {
				$crow = "content-row";
				if($k%2 != 0)
					$crow .= " content-alt";

				echo "<tr class=\"$crow\">";
				
				echo "<td style=\"font-size: small; text-align: left;\" >";
					echo "<a href=\"{$vars->_fallback->focus}&vtui={$t[0]}\" class=\"light\">";
					echo $t[2];
					echo "</a>";
				echo "</td>";
				
				echo "<td style=\"font-size: small;\">";
					echo $t[8];
				echo "</td>";

				echo "<td style=\"font-size: small;\">";
					switch($t[6]) {
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
				echo "</td>";
				
				echo "<td style=\"font-size: small;\">";
					echo $t[7];
				echo "</td>";

				echo "</tr>";
			}
		}
		else {
			echo "<tr class=\"content-row\"><td colspan=\"5\">No tickets</td></tr>";
		}
	?>
	</table>
	</div>
</div>
</div>
