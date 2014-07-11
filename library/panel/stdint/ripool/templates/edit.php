<?php
/* Copyright 2014, Charlie Fyvie-Gauld (Carrotsrg.org)
 *
 * This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/.
 */
 ?>
<font style="font-size: small">Resource Pool</font>
<div id="<?php echo $vars->_pmod ?>">
View Resource<br />
<?php 
	if($vars->rpe > 0) {
		echo "<form name=\"{$vars->_pmod}-editform\" action=\"{$vars->_fallback->action}\" method=\"post\">\n";
		echo "<input type=\"hidden\" value=\"{$vars->res['id']}\" name=\"id\" />\n";
	}
?>
<div class="kr-form-group">
	<div class="xsmallfont">
	<?php
		if($vars->rpe == 0)
			echo $vars->res['stype']."<br />";
		else {
			echo "<select name=\"type\" id=\"{$vars->_pmod}-edittype\" class=\"xsmallfont\">\n";
			foreach($vars->resCast as $r)
				if($r[0] == $vars->res['type'])
					echo "<option value=\"{$r[0]}\" selected>{$r[1]}</option>\n";
				else
					echo "<option value=\"{$r[0]}\">{$r[1]}</option>\n";
			echo "</select>\n";

		}
	?>
	</div>
	<div>(
	<?php
		if($vars->rpe == 0)
			echo $vars->res['label'];
		else 
			echo "<input name=\"label\" type=\"text\" id=\"{$vars->_pmod}-editname\" style=\"width: 120px\"  value=\"{$vars->res['label']}\" />\n";
	?>)
	</div>
</div>

<?php
	if($vars->rpe == 0) {
		echo "<a href=\"{$vars->_fallback->back}\">Back</a> ";
		echo "<a href=\"{$vars->_fallback->edit}\">Edit</a>";
	}
	else {
		echo "<a href=\"{$vars->_fallback->cancel}\">Cancel</a>";
		echo "<input type=\"submit\" value=\"edit\" />";
	}

	if($vars->rpe > 0) {
		echo "</form>";
		echo "<form action=\"{$vars->_fallback->remove}\" method=\"post\">";
		echo "<input type=\"hidden\" value=\"{$vars->res['id']}\" name=\"id\" />\n";
		echo "<input type=\"submit\" value=\"remove\" />";
		echo "</form>";
	}

?>
</div>
