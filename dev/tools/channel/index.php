<?php
	if(!include($_SERVER["DOCUMENT_ROOT"]."/ksysconfig.php"))
			die("Setup Problem: Cannot locate SystemConfig.");
	
	include(SystemConfig::relativeAppPath("system/koda/koda.php"));
	include(SystemConfig::relativeAppPath("system/resource/resman.php"));
	include(SystemConfig::relativeAppPath("system/debugmicro.php"));
	include("lib.php");

	$db = Koda::getDatabaseConnection('mysql');
	$fm = Koda::getFileManager();
	$db->connect(SystemConfig::$dbcUsername, SystemConfig::$dbcPassword);
	$db->selectDatabase(SystemConfig::$dbcDatabase);
	$dbm = new DebugMicro();

	$rman = new ResMan($db);
	$cplugin = null;
	$cchan = null;
	$panel = null;

	if(isset($_GET['cplugin']))
		$cplugin = $_GET['cplugin'];

	if(isset($_GET['cchan']))
		$cchan = $_GET['cchan'];

	$plugins = $fm->listDirectories(SystemConfig::relativeAppPath("library/plugins"));
	if(isset($_GET['mode']) && $_GET['mode'] == "plugin") {
		if(isset($_GET['op']) && $_GET['op'] == 1)
			registerPlugin($cplugin, $rman);
		else
		if(isset($_GET['op']) && $_GET['op'] == 2)
			newInstance($cplugin, $db, $rman);

		if($cplugin != null) {
			ob_start();
			pluginPanel($cplugin, $rman);
			$panel = ob_get_contents();
			ob_end_clean();
		}
	}
	else
	if(isset($_GET['mode']) && $_GET['mode'] == "channel") {
		if(isset($_GET['op']) && $_GET['op'] == 1) {
			$crud = false;
			if(isset($_GET['crud'])) {
				$crud = true;
			}

			registerChannel($_GET['cchan'], $db, $crud, $rman);
		}
		else
		if(isset($_GET['op']) && $_GET['op'] == 3)
			addInstanceToChannel($_GET['inst'], $db, $rman);
		else
		if(isset($_GET['op']) && $_GET['op'] == 5)
			moveInstanceUp($_GET['nid'], $db, $rman);
		else
		if(isset($_GET['op']) && $_GET['op'] == 6)
			moveInstanceDown($_GET['nid'], $db, $rman);
		else
		if(isset($_GET['op']) && $_GET['op'] == 7)
			deleteInstanceFromChannel($_GET['nid'], $db, $rman);

		if($cchan != null) {
			ob_start();
			manageChannel($cchan, $db, $rman, $plugins);
			$panel = ob_get_contents();
			ob_end_clean();
		}
	}
	else
	if(isset($_GET['mode']) && $_GET['mode'] == "nchan") {
		// changed to nchan because newchan would
		// take infinity to load for some reason
		if(isset($_POST['op']) && $_POST['op'] == 1) {
			$newChannel = $_POST['label'];
			addNewChannel($newChannel, $db);
		}
		ob_start();
		newChannelPanel();
		$panel = ob_get_contents();
		ob_end_clean();
	}
	else {
		ob_start();
		stats($db, $rman);
		$panel = ob_get_contents();
		ob_end_clean();
	}
	// get all the plugins
	$channels = getChannels($db);
	$mlist = $fm->listDirectories("../");
	$plugins = $fm->listDirectories(SystemConfig::relativeAppPath("library/plugins"));
?>
<html>
	<head>
		<title>VegPatch Channel Manager</title>
		<link type="text/css" rel="stylesheet" href="template.css" />
	</head>

<body>
<div id="header">
<div id="vp-title">
	Channel Manager
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
			<b>Plugins</b>
			<form method="get" action="index.php" style="margin-top: -5px">
				<input type="hidden" name="mode" value="plugin"/>
				<select class="form-text form-select" name="cplugin" style="width: 150px;">
					<?php 
						foreach($plugins as $p) {
							if($cplugin != null && $cplugin == $p)
								echo "<option value=\"$p\" selected>{$p}</option>";
							else
								echo "<option value=\"{$p}\">{$p}</option>";
						}
					?>
				</select><br />
				<input type="submit" value="Manage Plugin" class="form-button"/>
			</form>
			</div>

			<div class="tool-panel">
			<b>Channels</b>
			<form method="post" action="index.php?mode=nchan" style="margin-top: -5px">
				<input type="submit" value="New Channel" class="form-button"/>
			</form>
			<form method="get" action="index.php" style="margin-top: -5px">
				<input type="hidden" name="mode" value="channel"/>
				<select class="form-text form-select" name="cchan" style="width: 150px;">
					<?php 
						foreach($channels as $c) {
							if($cchan != null && $cchan == $c[0])
								echo "<option value=\"{$c[0]}\" selected>{$c[2]}</option>";
							else
								echo "<option value=\"{$c[0]}\">{$c[2]}</option>";
						}
					?>
				</select><br />
				<input type="submit" value="Manage Channel" class="form-button"/>
			</form>
			</div>
		</div>

			<div class="panel">
				<?php echo $panel; ?>
			</div>
		</div>
</div>
</body>
</html>
