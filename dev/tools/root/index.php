<?php
/* (C)opyright 2014, Carrotsrc.org
 *
 * This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/.
 */
	
	if(!defined('_ROOT_TOOL'))
		die("Not logged in");
	
	include(SystemConfig::relativeAppPath("system/resource/resman.php"));
	include("lib.php");
	$panel = "";
	$rman = new ResMan($db);
	if(isset($_GET['mode']) && $_GET['mode'] == 'config') {
		ob_start();
		rootConfig($db);
		$panel = ob_get_contents();
		ob_end_clean();
	}
	else
	if(isset($_GET['mode']) && $_GET['mode'] == 'asset') {
		ob_start();
		rootAssets($db, $fm);
		$panel = ob_get_contents();
		ob_end_clean();
	}
	else {
		ob_start();
		rootConfig($db);
		$panel = ob_get_contents();
		ob_end_clean();
	}
?>
<div id="kr-layout">
	<div class="tools">
		<div class="tool-panel">
		<b>Toolkit</b>
		<form method="post" action="index.php?tool=root&mode=config">
			<input type="submit" value="Root Config" class="form-button" style="width: 100px;" />
		</form>
		<form method="post" action="index.php?tool=root&mode=asset" style="margin-top: -20px;">
			<input type="submit" value="Root Assets" class="form-button" style="width: 100px;" />
		</form>
		</div>
	</div>

	<div class="panel">
		<?php echo $panel; ?>
	</div>
</div>
