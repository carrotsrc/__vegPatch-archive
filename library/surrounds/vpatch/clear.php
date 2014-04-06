<?php
/* (C)opyright 2014, Carrotsrc.org
 *
 * This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/.
 */
 ?>
<html>
<head>
<title><?php echo $vars->app->title; ?> Root</title>
<?php
	foreach($vars->assets['css'] as $a)
		echo "$a\n";
	foreach($vars->assets['js'] as $a)
		echo "$a\n";
?>
<script>
	window.onload = function () {
<?php
	if(!$vars->nodym)
		echo $vars->onload;
?>
	}
</script>
</head>

<body>
<div id="kr-header" class="solidbg2">
	<div id="kr-version">
		VegPatch v1.0	
	</div>

	<div id="kr-title">
		Kura Root
	</div>

</div>

<div id="kr-layout-container">
<?php
	echo $vars->app->layout;
?>
</div>
</body>

