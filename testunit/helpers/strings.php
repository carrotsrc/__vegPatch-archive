<?php
	include("../../system/helpers/strings.php");
	$mysql = $xml = $clean = "";
	if(isset($_POST['string'])) {
		$mysql = string_prepare_mysql($_POST['string']);
		$xml = string_prepare_xml($_POST['string']);
		$clean = string_clean_escapes($_POST['string']);
	}
?>
<form method="post">
	<input type="text" name="string" />
	<input type="submit" value="test" />
</form>

<?php
echo "<p>mysql:<br /><tt>$mysql</tt></p>";
echo "\n";
echo "<p>xml:<br /><tt>$xml</tt></p>";
echo "\n";
echo "<p>cleaned:<br /><tt>$clean</tt></p>";

$alpha = array();

for($i = 1; $i < 27; $i++) {
	$c = array(
		'item' => "item id for $i",
		'value' => "random $i");

	$alpha[] = array(
			'id' => $i,
			'alpha' => chr($i+0x60),
			'value' => $c
			);
}

json_encode_array($alpha);
?>
