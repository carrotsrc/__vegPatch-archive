<?php
	echo "<b>Request Loopback</b><br />";
	foreach($_GET as $k => $v) {
		echo "$k => $v<br />";
	}

	if(sizeof($_POST) > 0) {
		foreach($_POST as $k => $v) {
			echo "$k => $v<br />";
		}
	}
?>
