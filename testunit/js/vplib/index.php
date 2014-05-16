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
		$url = "http://".$_SERVER['HTTP_HOST']."/testunit/js/vplib/loopback.php"; 
		$content = str_replace("__URL_AJAX_REQ__", $url, $content);
		die($content);
	}
?>
<html>
<head>
<title>Javascript Libraries - VPLib</title>
<script type="text/javascript" src="?reqjs=1"></script>
<script type="text/javascript" src="vplib.js"></script>
<style>
	.feedback {
		margin-top: 15px;
		color: #808080;
	}

	.pass {
		color: #57AF15;
	}

	.fail {
		color: #BF3D23;
	}
</style>
</head>

<body>
	<div id="vplib-load" class="feedback">
	</div>

	<div id="vplib-alias" class="feedback">
	</div>

	<div id="vplib-requestGet" class="feedback">
	</div>

	<div id="vplib-multiRequestGet" class="feedback">
	</div>

	<div id="vplib-requestPost" class="feedback">
	</div>
</body>

</html>
