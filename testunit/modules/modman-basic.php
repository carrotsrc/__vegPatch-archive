<?php
	error_reporting(E_ALL);
	ini_set('display_errors', '1');
	
	require_once($_SERVER["DOCUMENT_ROOT"]."/ksysconfig.php");
	include("../../system/structure/blocks/schemablock.php");
	include("../../system/structure/module/modman.php");
	include("../../system/koda/koda.php");
	include("../../system/cblank.php");

	
	
	$obj = ModMan::getPanel("testpanel");
	
	if($obj == null)
		die("Failed");
	
	$obj->initialize();
?>

<html>
<head>
<title>ModMan-Basic Test</title>
</head>

<body>
<div style="width: 100%">
<?php
	echo $obj->getTemplate();
?>
</div>
</body>

</html>
