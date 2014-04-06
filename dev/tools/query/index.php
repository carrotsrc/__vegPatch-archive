<?php
	if(!include($_SERVER["DOCUMENT_ROOT"]."/ksysconfig.php"))
			die("Setup Problem: Cannot locate SystemConfig.");
	
	include(SystemConfig::relativeAppPath("system/koda/koda.php"));
	include(SystemConfig::relativeAppPath("system/resource/resman.php"));
	include("lib.php");

	$db = Koda::getDatabaseConnection('mysql');
	$db->connect(SystemConfig::$dbcUsername, SystemConfig::$dbcPassword);
	$db->selectDatabase(SystemConfig::$dbcDatabase);
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
<html>
	<head>
		<title>VegPatch Query</title>
		<link type="text/css" rel="stylesheet" href="template.css" />
	</head>

<body>
<div id="header">
<div id="vp-title">
	Run Query
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
			<b>Tool Panel</b>
			</div>
		</div>

			<div class="panel">
				<?php echo $panel; ?>
			</div>
		</div>
</div>

</body>
</html>
