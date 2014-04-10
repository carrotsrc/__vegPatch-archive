<?php
	if(!defined('_ROOT_TOOL'))
		die("Not logged in");
	
	include(SystemConfig::relativeAppPath("system/resource/resman.php"));
	include(SystemConfig::relativeAppPath("system/managers.php"));
	include("lib.php");

	$fm = Koda::getFileManager();
	$panel = "";

	$rman = new ResMan($db);
	Managers::setResourceManager($rman);

	if(isset($_GET['mode']) && $_GET['mode'] == "root") {
		ob_start();
		strapRootPanel($fm, $db, $rman);
		$panel = ob_get_contents();
		ob_end_clean();
	}
	else
	if(isset($_GET['mode']) && $_GET['mode'] == "system") {
		ob_start();
		strapSystemPanel($fm, $db, $rman);
		$panel = ob_get_contents();
		ob_end_clean();
	} else {
		ob_start();
		strapRootPanel($fm, $db, $rman);
		$panel = ob_get_contents();
		ob_end_clean();
	}
?>
<div id="kr-layout">
	<div class="tools">
		<div class="tool-panel">
		<b>Straps</b>
		<form method="post" action="index.php?tool=strap&mode=root">
			<input type="submit" value="Root System" class="form-button" style="width: 120px;" />
		</form>
		<form method="post" action="index.php?tool=strap&mode=system">
			<input type="submit" value="Other Systems" class="form-button" style="width: 120px; margin-top: -20px;" />
		</form>
		</div>
	</div>

	<div class="panel">
		<?php echo $panel; ?>
	</div>
</div>
