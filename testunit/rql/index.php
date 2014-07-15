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
		echo "<b style=\"color:#5F8F11;\">".str_replace("<", "&lt;", $rql) ."</b><br />";
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
	parse("[entity]<Group();");
	parse("[entity]{l}<Group('G1');");
	echo "<hr style=\"margin-bottom: 30px; border-color: #d8d8d8;\" />";
	parse("(User(){l}<Group('G1'))<(Area()<Area('AP'));");
	parse("(User(){l}<Group('G1'):leader)<(Area()<Area('AP'));");
	parse("(User(){l}<Group('G1'):leader)<(Area()<Area('AP')):admin;");
	echo "<hr style=\"margin-bottom: 30px; border-color: #d8d8d8;\" />";
	parse("(%User(){l}<Group('G1'))<(Area()<Area('AP'));");
	$pmua = $pmu = memory_get_peak_usage();

	if($pmu > 1*pow(10,6))
		$pmu = $pmu/pow(10,6) . " MB";
	else
	if($pmu > 1000)
		$pmu = $pmu/1000 . " KB";
	else
		$pmu .= " B";

	echo "<b style=\"font-size: small;\">PMU:</b><br />$pmu<br />$pmua bytes";
?>
</body>
</html>
