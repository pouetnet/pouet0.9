<?php

require_once("../include/auth.php");
$dbl = mysql_connect($db['host'], $db['user'], $db['password']);
mysql_select_db($db['database'],$dbl);

$d = dir("../gfx/logos");
$entry = $d->read();
$entry = $d->read();

while (false !== ($entry = $d->read())) {
	$query = "SELECT count(0) FROM logos WHERE file = '$entry'";
	$result = mysql_query($query);
	if(!mysql_result($result,0))
		print("$entry<br>\n");
}
$d->close()

?>
end!
