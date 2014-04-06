<?php
	error_reporting(E_ALL);
	ini_set('display_errors', '1');
	
	require_once($_SERVER["DOCUMENT_ROOT"]."/ksysconfig.php");
	include("../../system/managers.php");
	include("../../system/dbacc.php");
	include("../../system/structure/blocks/schemablock.php");
	include("../../system/structure/module/modman.php");
	include("../../system/koda/koda.php");
	include("../../system/resource/resman.php");
	include("../../system/cblank.php");

	echo "getting connection from Koda...";
	$connection = Koda::getDatabaseConnection("mysql");
	echo "done<br />";
	
	echo "connection status...";
	if(!$connection->connect("root", "lopana75"))
		die("failed");
	else
		echo "connected!<br />";
	
	echo "selecting database 'kura'...";
	if(!$connection->selectDatabase("kura"))
		die("failed");
	else
		echo "done!";
		
	echo "<br />";

	Managers::setResourceManager(new ResMan($connection));
	$obj = ModMan::getComponent("rminterface",1);
	if($obj == null)
		die("Failed");

	if(!$obj->initialize())
		die("Failed to init component");
	
?>

<html>
<head>
<title>ModMan-Basic Test</title>
</head>

<body>
<div style="width: 100%">
<?php
	echo $obj->run();
?>
</div>
</body>

</html>

