
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
<style>
	a {
		color: #7D9E05;
	}

	a:hover {
		color: #9DBF23;
	}

	.package-versions {
		border-width: 0px;
		border-spacing: 0px;
	}

	.package-versions td {
		padding: 5px;
		color: #808080;
	}

	.package-versions .top {
		background-color: #DFFFCF;
	}

	.package-desc {
		font-size: 18px;
	}

	.collection-list {
		font-size: 18px;
	}

	.package-list {
		font-size: 18px;
	}

	.package-list td {
		font-size: 18px;
		color: #808080;
		padding: 5px;
	}

</style>
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
