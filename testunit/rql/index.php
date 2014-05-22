<?php
		if(!include($_SERVER["DOCUMENT_ROOT"]."/ksysconfig.php"))
				die("Setup Problem: Cannot locate SystemConfig.");
		include(SystemConfig::relativeAppPath("system/resource/qparse.php"));
		global $parser;
		$parser = new QParse();
?>
<html>
<head>
<title>Resource System - RQL Parse</title>
</head>
<body>
<?php
	function parse($rql)
	{
		global $parser;
		echo "<div style=\"margin-bottom: 30px\">";
		echo "<b style=\"color:#5F8F11;\">".str_replace("<", "&lt;", $rql) ."</b>";
		$cpart = $parser->parse($rql);
		echo "<pre style=\"color: #808080;\">";
		print_r($cpart);
		echo "</pre>";
		echo "<div style=\"color: #808080;\">";
		echo str_replace("<", "&lt;", $parser->generate($cpart));
		echo "</div>";

		echo "</div>";
	}

	parse("Area();");
	parse("Area(23);");
	parse("Area('2');");
	parse("Area('foo');");
	echo "<hr style=\"margin-bottom: 30px; border-color: #d8d8d8;\" />";
	parse("User()<Area();");
?>
</body>
</html>
