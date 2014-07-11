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
	
	$fm = Koda::getFileManager();
	$rman = new ResMan($db);
	$panel = "";

	if(isset($_GET['mode']) && $_GET['mode'] == "manarea") {
		ob_start();
		areaManagerPanel($db, $rman);
		$panel = ob_get_contents();
		ob_end_clean();
	}
	else
	if(isset($_GET['mode']) && $_GET['mode'] == "newarea") {
		ob_start();
		newAreaPanel($db);
		$panel = ob_get_contents();
		ob_end_clean();
	}
	else
	if(isset($_GET['mode']) && $_GET['mode'] == "mansur") {
		ob_start();
		manageSurround($db, $fm);
		$panel = ob_get_contents();
		ob_end_clean();
	}
	else
	if(isset($_GET['mode']) && $_GET['mode'] == "regsur") {
		ob_start();
		surroundRegister($db, $fm);
		$panel = ob_get_contents();
		ob_end_clean();
	}
	else {
		ob_start();
		stats($db, $rman);
		$panel = ob_get_contents();
		ob_end_clean();
	}
	$areas = $db->sendQuery("SELECT * FROM areapool;", false, false);
	$surrounds = $db->sendQuery("SELECT * FROM surpool;", false, false);
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
						echo "<option value=\"{$a[0]}\">{$a[2]}</option>";
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
						echo "<option value=\"{$s[0]}\">{$s[1]}</option>";
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
