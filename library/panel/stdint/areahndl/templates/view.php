<?php
/* Copyright 2014, Charlie Fyvie-Gauld (Carrotsrg.org)
 *
 * This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/.
 */
?>
<b>Area Handler : <?php echo $vars->area[2]; ?></b><br />
<hr />
<div id="areahndl<?php echo $vars->_pnid; ?>-details" class="ah-details">
<b>Details</b><br />
<?php
echo "Name: ".$vars->area[2] . "<br />";
echo "Pool ID: ".$vars->area[0] . "<br />";
echo "Surround: ".$vars->area[3]. "<br />";
echo "Template: ".$vars->area[4]. "<br />";
?>
</div>
<div class="ah-template">
<b>Surround</b><br />
<?php
echo "Name: ". $vars->area[5] . "<br />";
echo "Markup: ". $vars->area[6] . "<br />";
?>
</div>
