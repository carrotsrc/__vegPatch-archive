<pre>
<?php
/*function iexp($base, $exp)
{
	$result = 1;
	while($exp)
	{
		if($exp&1)
			$result *= $base;
		$exp>>=1;
		$base *= $base;
	}

	return $result;
}
$area = 3;
$layout = 4;
$instance = 1;

$context = 0;
$anchor = 0;
$id = 0;
$a1 = array(array(3, 2, 1, 4, 5));
$a1[] = array(3,3,1,16,4);
$a1[] = array(3,4,1,1,2);
print_r($a1);
$stime = microtime(true);
for($i = 0; $i < 50000; $i++)
{
	foreach($a1 as $a) {
		if($a[0] == $area && $a[1] == $layout && $a[2] == $instance) {
			$context = $a[3];
			$anchor = $a[4];
			break;
		}
	}
}
$etime = microtime(true);
echo $etime-$stime."<br />";
echo "$context/$anchor<br />";

$width = floor((PHP_INT_SIZE<<3)/3);
$swidth = (PHP_INT_SIZE<<2);
$mask = pow(2, $width)-1;
$a2 = array();
foreach($a1 as $a) {
	$field = $a[0];
	$field <<= $width;
	$field ^=$a[1];
	$field <<= $width;
	$field ^=$a[2];
	if($a[2] == $instance)
		$id = $field;

	$ca = ($a[3]<<$swidth);
	$ca ^= $a[4];
	$a2[$field] = $ca;
}

print_r($a2);

$width = (PHP_INT_SIZE<<2);
$mask = pow(2, $width)-1;
$stime = microtime(true);
for($i = 0; $i < 50000; $i++)
{
$width = (PHP_INT_SIZE<<2);
$context = ($a2[$id]&($mask<<$width))>>$width;
$anchor = $a2[$id]&$mask;
}
$etime = microtime(true);
echo $etime-$stime."<br />";
echo "$context/$anchor<br />";

$exp = $width;
$base = 2;
$result = 1;
$stime = microtime(true);
for($i = 0; $i < 50000; $i++)
	$result = iexp(2,$width);

$etime = microtime(true);
echo "<br />$result<br />";
echo $etime-$stime."<br />";


$stime = microtime(true);
for($i = 0; $i < 50000; $i++)
	$result = pow(2,$width);
$etime = microtime(true);
echo "$result<br />";
echo $etime-$stime."<br />";
*/
$a = "Hello";
$b = "Helloworld";
$k=0;
$stime = microtime(true);
for($i = 0; $i < 50000; $i++)
	if($a == $b)
		$k++;
$etime = microtime(true);
echo $etime-$stime."<br />";
echo "$k<br />";
$stime = microtime(true);
for($i = 0; $i < 50000; $i++)
	if(!$a&$b)
		$k++;
$etime = microtime(true);
echo $etime-$stime."<br />";
echo "$k<br />";
$stime = microtime(true);
?>
</pre>
