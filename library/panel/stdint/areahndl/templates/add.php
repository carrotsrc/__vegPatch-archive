<?php
/* Copyright 2014, Charlie Fyvie-Gauld (Carrotsrg.org)
 *
 * This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/.
 */
?>
<b>Area Handler : Add Area</b><br />
<hr />
<form method="post" action="<?php echo $vars->_fallback->next; ?>">
<?php
echo "Name: <input type=\"text\" name=\"siahl\" id=\"areahndl{$vars->_pnid}-name\" ";
	if($vars->name != null)
		echo "value=\"{$vars->name}\" ";
	echo "/><br />\n";

echo "Surround: ";
	if($vars->surrounds != null) {
	echo "<select name=\"siahs\" id=\"{$vars->_pmod}-srnd\">\n";
	foreach($vars->surrounds as $item)
		echo "<option value=\"{$item[0]}\">{$item[1]}</option>\n";
	echo "</select>\n";
	} else {
		echo "<input type=\"hidden\" name=\"siahs\" value=\"{$vars->sid}\" />\n";
		echo "$vars->sid\n";
	}

echo "<br />Template: ";
if($vars->templates == null)
	echo "<select name=\"siaht\" id=\"areahndl{$vars->_pnid}-tmpl\" disabled/><br />\n";
else {
	echo "<select name=\"siaht\" id=\"areahndl{$vars->_pnid}-tmpl\">\n";
	foreach($vars->templates as $item)
		echo "<option value=\"{$item[0]}\">{$item[1]}</option>\n";
	echo "</select>\n";
}
echo "<input type=\"submit\" value=\"next\" style=\"float: right\">\n";
echo "<div style=\"float: right;\"><a href=\"{$vars->_fallback->cancel}\">cancel</a></div>\n";
?>
</form>

