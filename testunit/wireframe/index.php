<html>
<head>
<title>Wireframe test</title>
<link rel="stylesheet" type="text/css" href="wireframe.css">
</head>
<body>
<?php
	error_reporting(E_ALL);
	ini_set('display_errors', '1');
	require($_SERVER["DOCUMENT_ROOT"]."/ksysconfig.php");

	if(!include(SystemConfig::appRootPath("system/structure/blocks/schemablock.php")))
		die("FUcked");
	if(!include(SystemConfig::appRootPath("system/dbacc.php")))
		die("FUcked");
	if(!include(SystemConfig::appRootPath("system/cblank.php")))
		die("FUcked");
	if(!include(SystemConfig::appRootPath("system/structure/module/modman.php")))
		die("FUcked");
	if(!include(SystemConfig::appRootPath("system/helpers/kxml.php")))
		die("FUcked");
	if(!include(SystemConfig::appRootPath("system/layout/wireframegenerator.php")))
		die("FUcked");

	$cml = "<node type=\"1\"><leaf type=\"1\" pid=\"ritcast\"/><leaf type=\"0\" pid=\"ritcast\"  /><leaf type=\"1\" pid=\"ritcast\" /></node><node type=\"1\"></node>";
	$wgen = new WireframeGenerator();
	$wireframe = $wgen->processCML($cml);

	$header = $wireframe->getHeader();
	$html = "";
	foreach($header as $panel)
	{
		$panelId = $panel->getPanelId();
		if($panelId == "")
			continue;

		$pObj = ModMan::getPanel($panelId);
		$pObj->initialize();

		$panel->setPanel($pObj);
	}
	echo $wireframe->generateHTML();
?>

</body>
</html>
