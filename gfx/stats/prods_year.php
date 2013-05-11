<?
include('../../include/auth.php');
// opening DB
$dbl = mysql_connect($db['host'], $db['user'], $db['password']);
if(!$dbl) {
	die('SQL error... sorry ! ^^; I\'m on it !');
}
mysql_select_db($db['database'],$dbl);
// querying DB
unset($stats);
$query = 'SELECT count(0) as nb,YEARWEEK(quand) as date_added FROM prods GROUP BY date_added ASC';
$result = mysql_query($query);
while($row = mysql_fetch_array($result))
	$stats[] = $row[0];
// closing DB
if (isset($dbl)) {
	mysql_close($dbl);
}

header ("Content-type: image/png");
$im = @imagecreate (600, 100)
    or die ("Cannot Initialize new GD image stream");
$background_color = imagecolorallocate ($im, 58, 110, 165);
imagecolortransparent ($im, $background_color);
// $text_color = imagecolorallocate ($im, 255, 255, 255);
// imagestring ($im, 1, 5, 5,  "A Simple Text String", $text_color);
$rectangle_color1 = imagecolorallocate ($im, 80, 112, 144);
$rectangle_color2 = imagecolorallocate ($im, 64, 96, 128);
$i = 0;
/*
for($i=count($stats);$i>(count($stats)-150);$i--)
{
	$s = $stats[$i];
	if($i%2)
		$current_color = $rectangle_color1;
	else
		$current_color = $rectangle_color2;
	imagefilledrectangle($im, $i*4, 100-round($s*100/max($stats)), ($i+1)*4, 100, $current_color);
	$i++;
}
*/
//$yop = ($i*4).','.$s.','.(($i+1)*4).',0';
// imagestring ($im, 1, 5, 20,  $yop , $text_color);
imagepng ($im);
?>
