<?php
	if(!include($_SERVER["DOCUMENT_ROOT"]."/ksysconfig.php"))
			die("Setup Problem: Cannot locate SystemConfig.");
	
	include(SystemConfig::relativeAppPath("system/koda/koda.php"));
	include(SystemConfig::relativeAppPath("system/resource/resman.php"));
	include(SystemConfig::relativeAppPath("system/structure/blocks/schemablock.php"));
	include(SystemConfig::relativeAppPath("system/dbacc.php"));
	include(SystemConfig::relativeAppPath("system/structure/module/modman.php"));
	include('lib.php');
	$db = Koda::getDatabaseConnection('mysql');
	$fm = Koda::getFileManager();
	$db->connect(SystemConfig::$dbcUsername, SystemConfig::$dbcPassword);
	$db->selectDatabase(SystemConfig::$dbcDatabase);

	$rman = new ResMan($db);

	$lduld = null;
	$panel = "";

	$regcmpt = null;
	$cid = null;


	$pspace = $fm->listDirectories(SystemConfig::relativeAppPath("library/panel"));
	$cdir = $fm->listDirectories(SystemConfig::relativeAppPath("library/components"));
	$cspace = null;
	$nspace = null;
	$regpnl = null;
	$pid = null;
	if(isset($_GET['space']))
		$cspace = $_GET['space'];

	if(isset($_GET['nspace']))
		$nspace = $_GET['nspace'];

	// organise component registry
	if(isset($_GET['mode']) && $_GET['mode'] == 'cmptreg' && $nspace != null) {

		if(isset($_GET['op']) && $_GET['op'] == 1)
			registerComponent($_GET['id'], $db);

		$mlib = SystemConfig::relativeAppPath("library/components/$nspace");
		$dirs = $fm->listDirectories($mlib);

		$sql = "SELECT id, module_name FROM modreg WHERE module_type='0' AND space='$nspace';";
		$regcmpt = $db->sendQuery($sql, false, false);

		if($regcmpt != false) {

			$lduld = array();

			foreach($dirs as $cmpt) {
				$loaded = false;
				foreach($regcmpt as $ld) {
					if($cmpt == $ld[1])
						$loaded = $ld;
				}
				if($loaded !== false)
					$lduld[] = array($loaded[0], $loaded[1], 1);
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
			addInstance($_POST['cid'], $_POST['label'], $_POST['ref'], $db, $rman);
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
			registerPanel($_GET['id'], $cspace, $db);
		}
		$contents = $fm->listDirectories(SystemConfig::relativeAppPath("library/panel/$cspace"));
		ob_start();
		panelReg($contents, $cspace, $db, $rman);
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
		managePanel($pid, $cspace, $db, $rman);
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

	if($nspace != null) {
		$sql = "SELECT id, module_name FROM modreg WHERE module_type='0' AND space='$nspace';";
		$regcmpt = $db->sendQuery($sql, false, false);
		if(!$regcmpt)
			$regcmpt = array();
	}
	else
		$regcmp = array();

	if($cspace != null)
		$regpnl = $db->sendQuery("SELECT id, module_name FROM modreg WHERE module_type='1' AND space='$cspace';", false, false);
	$mlist = $fm->listDirectories("../");
?>
<html>
	<head>
		<title>VegPatch Module Manager</title>
		<link type="text/css" rel="stylesheet" href="template.css" />
	</head>

<body>
<div id="header">
<div id="vp-title">
	Module Manager
</div>

<div id="vp-version">
	SuperRoot VegPatch v0.1
</div>
</div>

<div id="link-bar">
<?php
	echo "| ";
	foreach($mlist as $d) {
		if($d == 'tool-template')
			continue;

		echo "<a href=\"../$d\">$d</a>";
		echo " | ";
	}
?>
</div>
<div id="kr-layout-column">
	<div id="kr-layout">
		<div class="tools">
			<div class="tool-panel">
			<b>Components</b>

			<form method="get" action="index.php">
				<input type="hidden" name="mode" value="cmptreg" />
				<select class="form-text form-select" style="width: 150px;" name="nspace">
					<?php 
						foreach($cdir as $c) {
							if($nspace != null && $nspace == $c)
								echo "<option value=\"{$c}\" selected>{$c}</option>";
							else
								echo "<option value=\"{$c}\">{$c}</option>";
						}
					?>
				</select><br />
				<input type="submit" value="Load Space" class="form-button"/>
			</form>
			<?php
				if($nspace != null) {
			?>
			<form method="post" action="index.php?mode=cmptreg&nspace=<?php echo $nspace; ?>">
				<input type="submit" value="Component Registry" class="form-button"/>
			</form>
			<form method="post" action="index.php?mode=cmptman&nspace=<?php echo $nspace; ?>" style="margin-top: -5px">
				<select class="form-text form-select" name="cid">
					<?php 
						foreach($regcmpt as $c) {
							if($cid != null && $cid == $c[0])
								echo "<option value=\"{$c[0]}\" selected>{$c[1]}</option>";
							else
								echo "<option value=\"{$c[0]}\">{$c[1]}</option>";
						}
					?>
				</select><br />
				<input type="submit" value="Manage Component" class="form-button"/>
			</form>
			<?php
				}
			?>
			</div>



			<div class="tool-panel">
			<b>Panels</b>
			<form method="get" action="index.php">
				<select class="form-text form-select" style="width: 150px;" name="space">
					<?php 
						foreach($pspace as $c) {
							if($cspace != null && $cspace == $c)
								echo "<option value=\"{$c}\" selected>{$c}</option>";
							else
								echo "<option value=\"{$c}\">{$c}</option>";
						}
					?>
				</select><br />
				<?php 
				echo"<input type=\"hidden\" name=\"mode\" value=\"panelreg\" />";
				?>
				<input type="submit" value="Load Space" class="form-button"/>
			</form>

			<?php if($cspace != null) { ?>
			<form method="get" action="index.php">
				<input type="hidden" name="mode" value="panelreg" />
				<input type="hidden" name="space" value="<?php echo $cspace; ?>" />
				<input type="submit" value="Panel Registry" class="form-button" />
			</form>

			<form method="get" action="index.php">
				<input type="hidden" name="mode" value="panelman" />
				<input type="hidden" name="space" value="<?php echo $cspace; ?>" />
				<select class="form-text form-select" style="width: 150px;" name="pid">
					<?php 
						foreach($regpnl as $c) {
							if($pid != null && $pid == $c[0])
								echo "<option value=\"{$c[0]}\" selected>{$c[1]}</option>";
							else
								echo "<option value=\"{$c[0]}\">{$c[1]}</option>";
						}
					?>
				</select><br />
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
</div>

</body>
</html>
