<?php
	if(isset($_GET['reqjs'])) {
		if(!include($_SERVER["DOCUMENT_ROOT"]."/ksysconfig.php"))
				die("Setup Problem: Cannot locate SystemConfig.");
		include(SystemConfig::relativeAppPath("system/koda/koda.php"));
		$jspath = SystemConfig::relativeAppPath("library/share/kglob/gscripts.js");
		if(!file_exists($jspath))
			die();

		$fm = Koda::getFileManager();
		$file = $fm->openFile($jspath, 'r');
		if($file == null)
			die();

		$content = $file->read();
		$content = str_replace("__URL_AJAX_REQ__", $_SERVER['HTTP_HOST']."/testunit/js/vplib/loopback.php", $content);
		die($content);
	}
?>
<html>
<head>
<title>Javascript Libraries - VPLib</title>
<script type="text/javascript" src="?reqjs=1"></script>
<script type="text/javascript" src="vplib.js"></script>
</head>

<body>
	<div id="vplib-load">
	</div>

	<div id="vplib-alias">
	</div>
</body>

</html>
