<?php
	if(!defined('_ROOT_TOOL'))
		die("Not logged in");

	$repo = $_GET['repo'];
	$collection = $_GET['collection'];
	$package = $_GET['package'];
	$archive = $_GET['archive'];
	$redir = "index.php?tool=repo&collection=$collection&package=$package&code=";
	$url = "$repo/repo/$collection/$package/$archive";
	$fn = SystemConfig::relativeAppPath("system/tmp/$archive");
	if(file_put_contents($fn, fopen($url, 'r')) === false) {
		header("refresh:2;url=$url");
		echo "Failed to pull archive from repository. Going back to repo browser";
		exit;
	}
?>
<div id="kr-layout">
<div class="panel">
<?php
	echo "<h2>Pulling $package archive</h2>";
	$arc = new PharData($fn);
	echo "Extracting archive...";
	if($arc->extractTo(SystemConfig::relativeAppPath("library"), null, true) === false) {
		echo "Failed";
		echo "<p><a href=\"$redir\">Go back</a>";
	}

	echo "OK"
?>
</div>
</div>
