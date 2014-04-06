<?php
	error_reporting(E_ALL);
	ini_set('display_errors', '1');
	
	include("../../system/cblank.php");
	
	$cBlank = new CBlank();
	$cBlank->testProperty = "This is a test property";
	
	if(isset($cBlank->testProperty))
		echo $cBlank->testProperty;

?>
