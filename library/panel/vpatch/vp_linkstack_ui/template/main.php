<?php
/* (C)opyright 2014, Carrotsrc.org
 *
 * This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/.
 */
 ?>
<div style="color: #508da7" class="vfont-xx-large">
</div>
<div style="margin-top: 15px; overflow: auto; width: 220px; text-align: center; float: left;">
<div>
	<a class="a-light vfont-xx-large" href="<?php echo $vars->_fallback->clear; ?>loc=ss436-students&kmodule=1">
	<img src="<?php echo $vars->mediag; ?>/mortarboard.png" /><br />
	Academia</a>
</div>
<div style="color: #808080;" class="vfont-small vform-item-spacer">
Study and activity area for module
<?php
	if($vars->access['root'] || $vars->access['cadmin'])
		echo "<br /><a href=\"{$vars->_fallback->clear}&loc=ss436-staff&kmodule=1\">[ staff ]</a>";
?>
</div>
</div>

<div style="margin-top: 15px; overflow: auto; width: 220px; text-align: center; float: left;">
<div>
	<a target="_BLANK" class="a-light vfont-xx-large" style="" href="<?php echo $vars->_fallback->clear; ?>&loc=mentors">
	<img src="<?php echo $vars->mediag; ?>/mentor.png" /><br />
	Mentors</a>
</div>
<div style="color: #808080;" class="vfont-small vform-item-spacer">
Mentor Area
</div>
</div>




<div style="margin-top: 15px; overflow: auto; width: 220px; text-align: center; float: left;">
<div>
	<a target="_BLANK" class="a-light vfont-xx-large" href="<?php echo $vars->_fallback->clear; ?>&loc=profiles">
	<img src="<?php echo $vars->mediag; ?>/smiley.png" /><br />
	Profile</a>
</div>
<div style="color: #808080;" class="vfont-small vform-item-spacer">
View profiles
</div>
</div>




<?php
	if($vars->access['root']) {
?>
<div style="margin-top: 15px; overflow: auto; width: 220px; text-align: center; float: left;">
<div>
	<a target="_BLANK" class="a-system vfont-xx-large" style="color: #808080;" href="<?php echo $vars->_fallback->clear; ?>&loc=sys">
	<img src="<?php echo $vars->mediag; ?>/system.png" /><br />
	System</a>
</div>
<div style="color: #808080;" class="vfont-small vform-item-spacer">
Site maintainence
</div>
</div>
<?php } ?>
