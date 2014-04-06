<?php
error_reporting(E_ALL);
ini_set('display_errors', '1');
	require_once($_SERVER["DOCUMENT_ROOT"]."/ksysconfig.php");
	include("../../system/cblank.php");
	include("../../system/structure/blocks/schemablock.php");
	include("../../system/structure/module/modman.php");
	include("../../system/layout/layoutgenerator.php");
	
	$gen = new LayoutGenerator();
	$layout = $gen->generateLayout("
	c{
		r{
			p(testpanel, 5){!w8;!h2;}
		}
		r{}
		r{}

	}
	
	c{
		r{}
		r{}
	}
	c{
		r{}
		r{}
		r{}
	}");
	
$layout = $gen->generateLayout("
	r{
		c{
			p(testpanel, 8){!h8;}	
		}
		c{}
	}
	r{
		c{
						
		}
		c{
			p(testpanel, 4){!w10;}
		}
	}
	");
	$layout->arrangeBlocks();
?>

<html>
<head>
<title>Layout Scratch</title>
</head>

<body>
<div style="width: 70%; height: 70%; border: 1px green dashed; padding: 5px;">
	<?php echo $layout->generateHTML(); ?>
</div>

</body>
</html>
