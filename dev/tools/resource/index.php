<?php
	if(!defined('_ROOT_TOOL'))
		die("Not logged in");
	include(SystemConfig::relativeAppPath("system/resource/resman.php"));
	include("lib.php");

	$fm = Koda::getFileManager();

	$rman = new ResMan($db);
	if(isset($_GET['mode']) && $_GET['mode'] == "newres") {
		ob_start();
		newResourcePanel($db, $rman);
		$panel = ob_get_contents();
		ob_end_clean();
	}
	else
	if(isset($_GET['mode']) && $_GET['mode'] == "newtype") {
		ob_start();
		newTypePanel($db, $rman);
		$panel = ob_get_contents();
		ob_end_clean();
	}
	else {
		ob_start();
		newResourcePanel($db, $rman);
		$panel = ob_get_contents();
		ob_end_clean();
	}
?>
<div id="kr-layout">
	<div class="tools">
		<div class="tool-panel" style="width: 120px">
		<b>Resources</b>
		<form method="post" action="index.php?tool=resource&mode=newres">
			<input type="submit" value="New Resource" class="form-button"/>
		</form>
		</div>
		<div class="tool-panel" style="width: 120px">
		<b>Types</b>
		<form method="post" action="index.php?tool=resource&mode=newtype">
			<input type="submit" value="New ResType" class="form-button"/>
		</form>
		</div>
	</div>

	<div class="panel">
		<?php echo $panel; ?>
	</div>
</div>
