<?php
	if(!defined('_ROOT_TOOL'))
		die("Not logged in");
	
	include(SystemConfig::relativeAppPath("system/resource/resman.php"));
	include("lib.php");

	$fm = Koda::getFileManager();

	$rman = new ResMan($db);
	$mlist = $fm->listDirectories("../");
	$panel = "";
	if(isset($_GET['mode']) && $_GET['mode'] == "rql" ||
	!isset($_GET['mode'])) {
		$prq = $crq = null;

		if(isset($_POST['query']))
			$prq = $_POST['query'];

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

