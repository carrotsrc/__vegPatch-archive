<?php
/* Copyright 2014, Charlie Fyvie-Gauld (Carrotsrg.org)
 *
 * This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/.
 */
 ?>
<font style="font-size: small;">Resource Pool</font>
<div id="ripool<?php echo $vars->_pnid ?>">
<table id="ripool<?php echo $vars->_pnid ?>-table" style="width: 100%;">
	<?php
		if(isset($vars->resPool)) {
			$index = 0;
			foreach($vars->resPool as $key => $row) {
				echo "<tr><td class=\"ressmall restype\">";
				echo "{$row[5]}(";
				echo "</td><td class=\"resname\">";
				echo "<a id=\"{$vars->_pmod}-el$key\" href=\"".$vars->_fallback->modify."&stdint-rpi=".$row[0]."\">";
				echo "{$row[3]}</a>";
				echo "</td><td class=\"ressmall\">)</td></tr>\n";
			}
		}
	?>
</table>
</div>
<a id="ripool<?php echo $vars->_pnid ?>-next" href="<?php echo $vars->_fallback->next ?>" class="rfloat">&gt;</a>
<a id="ripool<?php echo $vars->_pnid ?>-prev" href="<?php echo $vars->_fallback->prev ?>" class="lfloat">&lt;</a><br />
<a id="ripool<?php echo $vars->_pnid ?>-addnew" href="<?php echo $vars->_fallback->addnew ?>" class="lfloat">Add New</a><br />
<div id="ripool<?php echo $vars->_pnid ?>-log">
</div>
