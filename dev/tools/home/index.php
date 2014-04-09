<?php
	if(!defined('_ROOT_TOOL'))
		die("Not logged in");

	include(SystemConfig::relativeAppPath("system/resource/resman.php"));
	
	$fm = Koda::getFileManager();
	$rman = new ResMan($db);
	$panel = "";

?>
	<div id="kr-layout">
		<div class="tools">
		</div>
		<div class="tool-icon">
		<a href="?tool=area"><img src="area/icon.png" /><br />Area Tool</a>
		</div<
	</div>
