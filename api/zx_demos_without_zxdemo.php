<?php
header('Content-Type: text/plain; charset=utf-8');
include_once('../include/misc.php');
include_once('../include/auth.php');
conn_db();
$query="SELECT id, name, download FROM prods INNER JOIN prods_platforms ON ( prods.id = prods_platforms.prod AND prods_platforms.platform = 82 ) WHERE ( zxdemo = 0 OR zxdemo IS NULL )";
$result = mysql_query($query);
while($row = mysql_fetch_array($result)) {
  echo $row['id'] . '|' . $row['name'] . '|' . $row['download'] . "\n";
}
?>
