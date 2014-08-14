<?php
/* Copyright 2014, Charlie Fyvie-Gauld (Carrotsrg.org)
 *
 * This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/.
 */
	
	if(!defined('_ROOT_TOOL'))
		die("Not logged in");

	$flag = KS_MOD;
	include(SystemConfig::relativeAppPath("system/resource/resman.php"));
	include(SystemConfig::relativeAppPath("system/structure/uiblock.php"));
	include(SystemConfig::relativeAppPath("system/dbacc.php"));
	include(SystemConfig::relativeAppPath("system/module/modman.php"));
	include(SystemConfig::relativeAppPath("system/managers.php"));
	include(SystemConfig::relativeAppPath("system/libload.php"));
	include('lib.php');

	$rman = new ResMan($db);
	Managers::setResourceManager($rman);

	$lduld = null;
	$panel = "";

	$regcmpt = null;
	$cid = null;


	$pcollection = $fm->listDirectories(SystemConfig::relativeAppPath("library/panel"));
	$cdir = $fm->listDirectories(SystemConfig::relativeAppPath("library/components"));
	$ccollection = null;
	$ncollection = null;
	$regpnl = null;
	$pid = null;
	if(isset($_GET['collection']))
		$ccollection = $_GET['collection'];

	if(isset($_GET['ncollection']))
		$ncollection = $_GET['ncollection'];

	// organise component registry
	if(isset($_GET['mode']) && $_GET['mode'] == 'cmptreg' && $ncollection != null) {

		if(isset($_GET['op']) && $_GET['op'] == 1)
			registerComponent($_GET['id'], $db);

		$mlib = SystemConfig::relativeAppPath("library/components/$ncollection");
		$dirs = $fm->listDirectories($mlib);

		$sql = "SELECT id, module_name FROM modreg WHERE module_type='0' AND space='$ncollection';";
		$regcmpt = $db->sendQuery($sql);
		if($regcmpt != false) {

			$lduld = array();

			foreach($dirs as $cmpt) {
				$loaded = false;
				foreach($regcmpt as $ld) {
					if($cmpt == $ld['module_name'])
						$loaded = $ld;
				}
				if($loaded !== false)
					$lduld[] = array($loaded['id'], $loaded['module_name'], 1);
				else
					$lduld[] = array(0, $cmpt, 0);
			}
		}
		else {
			$lduld = array();

			foreach($dirs as $cmpt)
				$lduld[] = array(0, $cmpt, 0);
		}
	}
	else
	if(isset($_GET['mode']) && $_GET['mode'] == 'cmptman') {

		if(isset($_POST['op']) && $_POST['op'] == 12) {
			modifyInstance($_POST['id'], $_POST['label'], $_POST['ref'], $db);
			unset($_POST['op']);
			unset($_POST['ref']);
			unset($_POST['label']);
			unset($_POST['id']);
		}
		else
		if(isset($_POST['op']) && $_POST['op'] == 11) {
			$instparam = null;
			if(isset($_POST['params']) && $_POST['params'] != "")
				$instparam = $_POST['params'];
			addInstance($_POST['cid'], $_POST['label'], $_POST['ref'], $db, $rman, $instparam);
			unset($_POST['op']);
			unset($_POST['ref']);
			unset($_POST['label']);
		}
		else
		if(isset($_POST['op']) && $_POST['op'] == 13) {
			registerResource($_POST['cid'], $_POST['name'], $rman);
			unset($_POST['op']);
			unset($_POST['name']);
			unset($_POST['label']);
		}

		if(isset($_POST['cid']))
			$cid = $_POST['cid'];
		else
		if(isset($_GET['cid']))
			$cid = $_GET['cid'];
		else
			$cid = null;
	
		ob_start();
		manageComponent($cid, $db, $rman);
		$panel = ob_get_contents();
		ob_end_clean();
	}
	else
	if(isset($_GET['mode']) && $_GET['mode'] == 'panelreg') {

		if(isset($_GET['op']) && $_GET['op'] == 1) {
			registerPanel($_GET['id'], $ccollection, $db);
		}
		$contents = $fm->listDirectories(SystemConfig::relativeAppPath("library/panel/$ccollection"));
		ob_start();
		panelReg($contents, $ccollection, $db, $rman);
		$panel = ob_get_contents();
		ob_end_clean();
	}
	else
	if(isset($_GET['mode']) && $_GET['mode'] == 'panelman') {

		$pid = $_GET['pid'];
		if(isset($_GET['op']) && $_GET['op'] == 1) {
			registerPanelResource($pid, $_GET['name'], $rman);
		}
		ob_start();
		managePanel($pid, $ccollection, $db, $rman);
		$panel = ob_get_contents();
		ob_end_clean();
	}
	else {
		// load up basic stats
		ob_start();
		stats($db, $rman);
		$panel = ob_get_contents();
		ob_end_clean();
	}

	if($ncollection != null) {
		$sql = "SELECT id, module_name FROM modreg WHERE module_type='0' AND space='$ncollection';";
		$regcmpt = $db->sendQuery($sql, false, false);
		if(!$regcmpt)
			$regcmpt = array();
	}
	else
		$regcmp = array();

	if($ccollection != null)
		$regpnl = $db->sendQuery("SELECT id, module_name FROM modreg WHERE module_type='1' AND space='$ccollection';", false, false);
?>
<div id="kr-layout">
	<div class="tools">
		<div class="tool-panel">
		<b>Components</b>

		<form method="get" action="index.php">
			<input type="hidden" name="tool" value="module" />
			<input type="hidden" name="mode" value="cmptreg" />
			<select class="form-text form-select" name="ncollection">
				<?php 
					foreach($cdir as $c) {
						if($ncollection != null && $ncollection == $c)
							echo "<option value=\"{$c}\" selected>{$c}</option>";
						else
							echo "<option value=\"{$c}\">{$c}</option>";
					}
				?>
			</select>
			<input type="submit" value="Load Collection" class="form-button"/>
		</form>
		<?php
			if($ncollection != null) {
		?>
		<form method="post" action="index.php?tool=module&mode=cmptreg&ncollection=<?php echo $ncollection; ?>">
			<input type="submit" value="Component Registry" class="form-button"/>
		</form>
		<form method="post" action="index.php?tool=module&mode=cmptman&ncollection=<?php echo $ncollection; ?>">
			<select class="form-text form-select" name="cid">
				<?php 
					foreach($regcmpt as $c) {
						if($cid != null && $cid == $c['id'])
							echo "<option value=\"{$c['id']}\" selected>{$c['module_name']}</option>";
						else
							echo "<option value=\"{$c['id']}\">{$c['module_name']}</option>";
					}
				?>
			</select>
			<input type="submit" value="Manage Component" class="form-button"/>
		</form>
		<?php
			}
		?>
		</div>



		<div class="tool-panel">
		<b>Panels</b>
		<form method="get" action="index.php">
			<input type="hidden" name="tool" value="module" />
			<select class="form-text form-select" name="collection">
				<?php 
					foreach($pcollection as $c) {
						if($ccollection != null && $ccollection == $c)
							echo "<option value=\"{$c}\" selected>{$c}</option>";
						else
							echo "<option value=\"{$c}\">{$c}</option>";
					}
				?>
			</select>
			<?php 
			echo"<input type=\"hidden\" name=\"mode\" value=\"panelreg\" />";
			?>
			<input type="submit" value="Load Collection" class="form-button"/>
		</form>

		<?php if($ccollection != null) { ?>
		<form method="get" action="index.php">
			<input type="hidden" name="tool" value="module" />
			<input type="hidden" name="mode" value="panelreg" />
			<input type="hidden" name="collection" value="<?php echo $ccollection; ?>" />
			<input type="submit" value="Panel Registry" class="form-button" />
		</form>

		<form method="get" action="index.php">
			<input type="hidden" name="tool" value="module" />
			<input type="hidden" name="mode" value="panelman" />
			<input type="hidden" name="collection" value="<?php echo $ccollection; ?>" />
			<select class="form-text form-select" name="pid">
				<?php 
					foreach($regpnl as $c) {
						if($pid != null && $pid == $c['id'])
							echo "<option value=\"{$c['id']}\" selected>{$c['module_name']}</option>";
						else
							echo "<option value=\"{$c['id']}\">{$c['module_name']}</option>";
					}
				?>
			</select>
			<input type="submit" value="Manage Panel" class="form-button" />
		</form>
		<?php } ?>
		</div>


	</div>
	<div class="panel">
		<?php
			if(isset($_GET['mode']) && $_GET['mode'] == 'cmptreg')
				cmptregPanel($lduld);
			else
				echo $panel;
		?>
	</div>
</div>
