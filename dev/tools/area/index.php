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
	include("lib.php");
	$rman = new ResMan($db);
	$panel = "";
	ob_start();
	if(isset($_GET['mode']) && $_GET['mode'] == "manarea") {
		areaManagerPanel($db, $rman);
	}
	else
	if(isset($_GET['mode']) && $_GET['mode'] == "newarea") {
		newAreaPanel($db);
	}
	else
	if(isset($_GET['mode']) && $_GET['mode'] == "mansur") {
		manageSurround($db, $fm);
	}
	else
	if(isset($_GET['mode']) && $_GET['mode'] == "regsur") {
		surroundRegister($db, $fm);
	}
	else {
		stats($db, $rman);
	}
	$panel = ob_get_contents();
	ob_end_clean();
	$areas = $db->sendQuery("SELECT * FROM areapool;");
	$surrounds = $db->sendQuery("SELECT * FROM surpool;");
	$mlist = $fm->listDirectories("../");
?>
	<div id="kr-layout">
		<div class="tools">
			<div class="tool-panel">
			<b>Areas</b>
			<form action="index.php?tool=area&mode=newarea" method="post">
				<input type="submit" value="New Area" class="form-button" />
			</form>
			<form action="index.php?tool=area&mode=manarea" method="post">
				<select name="aid" class="form-text form-select">
					<?php
					foreach($areas as $a)
						echo "<option value=\"{$a['id']}\">{$a['name']}</option>";
					?>
				</select>
				<input type="submit" value="Manage Area" class="form-button" />
			</form>
			</div>

			<div class="tool-panel">
			<b>Surrounds</b>
			<form action="index.php?tool=area&mode=regsur" method="post">
				<input type="submit" value="Surround Register" class="form-button" />
			</form>
			<form action="index.php?tool=area&mode=mansur" method="post">
				<select name="sid" class="form-text form-select">
					<?php
					foreach($surrounds as $s)
						echo "<option value=\"{$s['id']}\">{$s['name']}</option>";
					?>
				</select>
				<input type="submit" value="Manage Surround" class="form-button" />
			</form>
			</div>
		</div>

			<div class="panel">
				<?php echo $panel; ?>
			</div>
		</div>
