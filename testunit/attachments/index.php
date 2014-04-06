<?php
	if(!include($_SERVER["DOCUMENT_ROOT"]."/ksysconfig.php"))
		die("Setup Problem: Cannot locate SystemConfig.");

	include_once(SystemConfig::appRootPath("system/koda/koda.php"));
	include_once(SystemConfig::appRootPath("system/dbacc.php"));
	include_once(SystemConfig::appRootPath("library/lib/kura/attachmentLibrary.php"));
	$db = Koda::getDatabaseConnection('mysql');
	$db->connect(SystemConfig::$dbcUsername, SystemConfig::$dbcPassword);
	$db->selectDatabase(SystemConfig::$dbcDatabase);
	$alib = new attachmentLibrary($db);

	function gtype($rl)
	{
		global $alib;
		$type = $alib->getType($rl, 1);
		if($type === 0)
			$type = "external";
		$type = $type[1];
	
		echo "<p>rloc: $rl<br />type: $type</p>";
	}

	echo "<b>Control</b>";
	gtype("http://www.nowhere.url/hello.asd");
	gtype("http://www.nowhere.url/hellojpg");
	gtype("http://www.youtube.com/hellojpg");
	gtype("http://www.youtubecom/watch?v=Uuwye");
	gtype("/usr/share/video/hellompg");
	gtype("http://www.nowhere.url/sub/articlepdf");

	echo "<b>Images</b>";
	gtype("http://www.nowhere.url/hello.jpg");
	gtype("http://www.nowhere.url/hello.jpeg");
	gtype("http://www.nowhere.url/hello.png");
	gtype("/usr/share/image/hello.png");

	echo "<b>Videos</b>";
	gtype("https://www.youtube.com/watch?v=I6dhE5hsR");
	gtype("/usr/share/video/hello.mpg");
	gtype("/usr/share/video/hello.mpeg");
	gtype("/usr/share/video/hello.mov");

	echo "<b>Documents</b>";
	gtype("http://www.nowhere.url/sub/article.pdf");
	gtype("/usr/share/docs/hello.doc");

	echo "<b>Website</b>";
	gtype("http://www.nowhere.url/sub/site.htm");
	gtype("http://www.nowhere.url/sub/site.html");
	if(isset($_GET['full']) && $_GET['full'] == 1) {
		echo "<b>Database Insert</b>";
		$url = "http://www.youtube.com/watch?v=27373728";
		$id = $alib->generateAttachment($url);
		echo "<p>rloc: $url<br />ref: ";
		var_dump($id);
	}

?>
