
<?php
/* Copyright 2014, Charlie Fyvie-Gauld (Carrotsrg.org)
 *
 * This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/.
 */
	
	if(!defined('_ROOT_TOOL'))
		die("Not logged in");
?>
<?php
	$repo = "http://repo.localhost";
	include(SystemConfig::relativeAppPath("system/helpers/vpxml.php"));
	include("lib.php");
	$fm = Koda::getFileManager($repo);

	ob_start();
	browse_repo($repo);
	$panel = ob_get_contents();
	ob_end_clean();
?>
<div id="kr-layout">
	<div class="tools">
		<div class="tool-panel">
		<b>Straps</b>
		<form method="post" action="index.php?tool=strap&mode=root">
			<input type="submit" value="Root System" class="form-button" />
		</form>
		<form method="post" action="index.php?tool=strap&mode=system">
			<input type="submit" value="Other Systems" class="form-button" />
		</form>
		</div>
	</div>

	<div class="panel">
		<?php echo $panel; ?>
	</div>
</div>
