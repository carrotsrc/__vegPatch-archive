<?php
	error_reporting(E_ALL);
	ini_set('display_errors', '1');
	
	require_once($_SERVER["DOCUMENT_ROOT"]."/ksysconfig.php");
	include("../../system/layout/layoutgenerator.php");
	include("../../system/structure/blocks/schemablock.php");
	include("../../system/structure/module/modman.php");
	include("../../system/koda/koda.php");
	include("../../system/cblank.php");
	
	$gen = new LayoutGenerator();
	$layout = $gen->generateLayout("
	c{
		r{
			p(testpanel, 4){ 
				!w5;
			}
		}
		
		r{}		
		r{}
	}
	
	c{
		r{}
		r{}
	}
	c{

	}");
	$layout->arrangePlugs();						     
	
	echo $layout->generateHTML();

?>
