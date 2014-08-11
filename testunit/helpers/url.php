<?php
	include("../../system/helpers/url.php");
	echo url_modify_params(array('foo' => 13, 'bar'=>null, 'foobar'=>"cryptic"));
?>
