<?php
/* Copyright 2014, Charlie Fyvie-Gauld (Carrotsrg.org)
 *
 * This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/.
 */
 ?>
<font style="font-size: small;">Resource Typecast</font>
<div id="<?php echo $vars->_pmod ?>">
	<table>
	<?php
		if(isset($vars->resCast)) 
			foreach($vars->resCast as $row) {
				echo "<tr><td class=\"ressmall restype\" style=\"text-align: right;\">{$row[3]}(</td>";
				echo "<td class=\"resname\" style=\"text-align: center;\">{$row[1]}</td>";
				echo "<td class=\"ressmall restype\">)</td></tr>\n";
			}
	?>
	</table>
</div>
