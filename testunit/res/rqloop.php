<?php
	error_reporting(E_ALL);
	ini_set('display_errors', '1');
	require_once("../../system/koda/koda.php");
	require_once("../../system/resource/resman.php");
	require_once("../../system/dbacc.php");
	require_once("../../system/managers.php");
	
	$connection = Koda::getDatabaseConnection("mysql");
	$connection->connect("root", "lopana75");
	$connection->selectDatabase("kura");
		
	$resman = new ResMan($connection);
	Managers::setResourceManager($resman);
	if(!isset($_GET['q']))
		die();
		
	$query = str_replace("_AND_","&",$_GET['q']); 
	
	if($_GET['r'] == 1)
	{
		$sql = $resman->convertRQL($query);
		$feedback = $sql;
		$feedback .= "\n";
		$result = $connection->sendQuery($sql, false, false);
		
		if(!$result)
			$feedback .= "-1";
		else
			foreach($result as $row)
				$feedback .= $row[0] . " ";
	}
	else
		$feedback = $resman->convertRQL($query);

	if(isset($_GET['ref']) && $_GET['ref'] == 1) {
		$ls = array();
		foreach($result as $row)
			$ls[] = $row[0];

		$v = $resman->getlsHandlerRef($ls);
		var_dump($v);

	}
	
	echo $feedback;
?>
