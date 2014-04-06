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
	$panel = "";

	if(isset($_GET['mode']) && $_GET['mode'] == "newrel" || !isset($_GET['mode'])) {
		$prq = $crq = null;

		if(isset($_POST['prq']))
			$prq = $_POST['prq'];

		if(isset($_POST['crq']))
			$crq = $_POST['crq'];
		
		if(isset($_POST['op']) && $_POST['op'] == 1) {
			createRelationship($_POST['edge'], $rman);
		}
		ob_start();
		relationshipPanel($prq, $crq, $rman, $db);
		$panel = ob_get_contents();
		ob_end_clean();
	}
	else
	if(isset($_GET['mode']) && $_GET['mode'] == "manrel") {
		$prq = $crq = null;

		if(isset($_POST['query']))
			$prq = $_POST['query'];

		if(isset($_POST['op']) && $_POST['op'] == 1) {
		//	createRelationship($_POST['edge'], $rman);
		}
		else
		if(isset($_POST['op']) && $_POST['op'] == 2) {
			removeRelationships($rman);
		}
		ob_start();
		managePanel($prq, $rman, $db);
		$panel = ob_get_contents();
		ob_end_clean();
	}
	else
	if(isset($_GET['mode']) && $_GET['mode'] == "edge") {

		ob_start();
		edgePanel($rman, $db);
		$panel = ob_get_contents();
		ob_end_clean();
	}
	$mlist = $fm->listDirectories("../");
?>
<html>
	<head>
		<title>VegPatch Associations</title>
		<link type="text/css" rel="stylesheet" href="template.css" />
	</head>

<body>
<div id="header">
<div id="vp-title">
	Asssociations
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
			<b>Relationships</b>
			<form method="post" action="index.php?mode=newrel">
				<input type="submit" class="form-button" value="Create Assoc" />
			</form>
			<form method="post" action="index.php?mode=manrel">
				<input type="submit" style="margin-top: -20px;" class="form-button" value="Manage Assoc" />
			</form>
			<form method="post" action="index.php?mode=edge">
				<input type="submit" style="margin-top: -8px;" class="form-button" value="Manage Edges" />
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
