<?php
	if(!include($_SERVER["DOCUMENT_ROOT"]."/ksysconfig.php"))
		die("Setup Problem: Cannot locate SystemConfig.");

	include_once(SystemConfig::appRootPath("system/resource/qparse.php"));
	$qp = new QParse();
	$q = $qp->parse("[expression]<User('1');");

	echo $qp->generate($q);

	$qp = new QParse();
	$q = $qp->parse("User('1','2','5');");

	echo "<pre>";
	print_r($q);
	echo "</pre>";
	echo $qp->generate($q);
	
	$q = $qp->parse("(User()<Course('1'))<(Instance()<Component('2'));");

	echo "<pre>";
	print_r($q);
	echo "</pre>";
	echo $qp->generate($q);
	$q = $qp->parse("User('charlie'){ref}<Course('1'):admin;");
	echo "<pre>";
	print_r($q);
	echo "</pre>";
	echo $qp->generate($q);

	$q = $qp->parse("Comment()<(User('charlie')<Course('1'));");
	echo "<pre>";
	print_r($q);
	echo "</pre>";
	echo $qp->generate($q);

	$q = $qp->parse("[space]{l}<Course('1'));");
	echo "<pre>";
	print_r($q);
	echo "</pre>";
	echo $qp->generate($q);

	$q = $qp->parse("User()>(Comment()<(Module('1',3)<Course('4')));");
	echo "<pre>";
	print_r($q);
	echo "</pre>";
	echo $qp->generate($q);

?>
