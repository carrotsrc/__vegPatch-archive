<?php
	if(!defined('_ROOT_TOOL'))
		die("Not logged in");

	$repo = $_GET['repo'];
	$collection = $_GET['collection'];
	$package = $_GET['package'];
	$archive = $_GET['archive'];
	$redir = "index.php?tool=repo&collection=$collection&package=$package";
	$url = "$repo/repo/$collection/$package/$archive";
	$fn = SystemConfig::relativeAppPath("system/tmp/$archive");
	ob_start();
	echo "Pulling $package archive...";
	if(file_put_contents($fn, fopen($url, 'r')) === false) {
		echo "Failed";
		header("refresh:3;url=$redir");
		echo "<p><h3 style=\"color: red;\">Error</h3>An error occurred pulling the archive for <strong>$package</strong> from the repository<br />Redirecting to repository browser in 3 seconds<br /><a href=\"$redir\">Or click here</a></p>";
	} else {
		echo "OK<br />";
		$arc = new PharData($fn);
		echo "Extracting archive...";
		$success = true;
		try {
			if($arc->extractTo(SystemConfig::relativeAppPath("library"), null, true) === false)
				$success = false;
		} catch(Exception $e) {
			$success = false;
		}
		header("refresh:5;url=$redir");
		if($success) {
			echo "OK";
			echo "<p><h3 style=\"color: #7D9E05;\">Success</h3>Successfully unpacked archive <strong>$archive</strong> into library/<br />Redirecting to repository browser in 5 seconds<br /><a href=\"$redir\">Or click here</a></p>";
		}
		else {
			echo "Failed";
			echo "<p><h3 style=\"color: red;\">Error</h3>An error occurred extracting <strong>$archive</strong> into library/<br />Redirecting to repository browser in 5 seconds<br /><a href=\"$redir\">Or click here</a></p>";
		}

		unlink($fn);

	}
	$html = ob_get_contents();
	ob_clean();
?>
<style>
	a {
		color: #7D9E05;
	}

	a:hover {
		color: #9DBF23;
	}
</style>
<div id="kr-layout">
<div class="panel">
<?php
	echo $html;
?>
</div>
</div>

