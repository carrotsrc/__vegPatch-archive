<?php
	echo "<b>Request Loopback</b><br />";
	if(sizeof($_GET) > 0) { 
		foreach($_GET as $k => $v) {
			echo "$k => $v<br />";
		}
	}
	else {
		echo "\$_GET is null<br />";
	}

	if(sizeof($_POST) > 0) {
		foreach($_POST as $k => $v) {
			echo "$k => $v<br />";
		}
	}
	else {
		echo "\$_POST is null";
	}
?>
