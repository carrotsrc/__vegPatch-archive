<?php
	error_reporting(E_ALL);
	ini_set('display_errors', '1');
	include($_SERVER["DOCUMENT_ROOT"]."/ksysconfig.php");
	if(!include(SystemConfig::appRootPath("system/helpers/kxml.php")))
		die("FUcked");

	$xml = new KXML();
	$xml->setTags(array("node","/node", "leaf"));
	$xml->setProperties(array("type", "pid", "ref", "rql", "cid"));
	$cml = "<node type=\"1\" type='2'><leaf pid=\"1\" cid=\"3\" rql=\"Instance()^User(\\'charlie\\');\"></node />";
	$xml->init($cml);
	while(($cmpt = $xml->getNextTag()) != null)
		echo $cmpt->printMe()."<br />";	
?>
