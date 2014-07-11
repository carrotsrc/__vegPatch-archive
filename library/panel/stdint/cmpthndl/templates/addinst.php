<?php
/* Copyright 2014, Charlie Fyvie-Gauld (Carrotsrg.org)
 *
 * This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/.
 */
?>
<b>Component Handler : New Instance</b><hr />
<?php
	echo "<form id=\"{$vars->_pmod}-submit\" method=\"post\" action=\"{$vars->_fallback->submit}\">";
	echo "Type: ";
	echo "<select name=\"sichi\" id=\"{$vars->_pmod}-srnd\">\n";
	foreach($vars->types as $item)
		echo "<option value=\"{$item[0]}\">{$item[1]}</option>\n";
	echo "</select>\n<br /><br />";

	echo "Label: \n";
	echo "<input type=\"text\" name=\"sichl\" id=\"{$vars->_pmod}-label\">\n";
?>
<input type="submit" value="Add" />
</font>
