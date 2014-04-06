<?php
	error_reporting(E_ALL);
	ini_set('display_errors', '1');
	
	require_once("../../system/koda/koda.php");
	
	echo "getting FileManager from koda...";
	$fileManager = Koda::getFileManager();
	echo "done<br />";

	echo "Getting directory listings...<br />";
	$list = $fileManager->listDirectory("../");
	foreach($list as $file)
		echo $file . "<br />";
	echo "done<br />";
		
	echo "Getting file listings...<br />";
	$list = $fileManager->listFiles("../");
	foreach($list as $file)
		echo $file . "<br />";
	echo "done<br />";
	
	echo "Getting directory listings...<br />";
	$list = $fileManager->listDirectories("../");
	foreach($list as $file)
		echo $file . "<br />";
	echo "done<br />";	
	
	echo "Loading File...";
	$file = $fileManager->openFile("test.tst", "r");
	
	if($file == null)
		die("OOPS");
		
	echo "Content: " . $file->read() . "<br />";	
	echo "done";


?>
