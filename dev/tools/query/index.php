<?php
/* Copyright 2014, Charlie Fyvie-Gauld (Carrotsrg.org)
 *
 * This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/.
 */
	
	if(!defined('_ROOT_TOOL'))
		die("Not logged in");
	
	include(SystemConfig::relativeAppPath("system/resource/resman.php"));
	include(SystemConfig::relativeAppPath("system/helpers/strings.php"));
	include("lib.php");

	$rman = new ResMan($db);
	$mlist = $fm->listDirectories("../");
	$panel = "";
	if(isset($_GET['mode']) && $_GET['mode'] == "rql" ||
	!isset($_GET['mode'])) {
		$prq = $crq = null;

		if(isset($_POST['query']))
			$prq = string_clean_escapes($_POST['query']);

		ob_start();
		rqlPanel($prq, $rman, $db);
		$panel = ob_get_contents();
		ob_end_clean();
	}
?>
	<div id="kr-layout">
		<div class="tools">
			<div class="tool-panel">
			<b>Tool Panel</b>
			</div>
		</div>

		<div class="panel">
			<?php echo $panel; ?>
		</div>
	</div>

