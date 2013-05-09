<?php
header('Content-Type: text/plain; charset=utf-8');
include_once('../include/misc.php');
include_once('../include/auth.php');
conn_db();
$query="SELECT * FROM downloadlinks";
$result = mysql_query($query);
while($row = mysql_fetch_array($result)) {
  echo $row['prod'] . "\t" . $row['type'] . "\t" . $row['link'] . "\n";
}
?>
