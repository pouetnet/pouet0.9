<?php
header('Content-Type: text/plain; charset=utf-8');
include_once('../include/misc.php');
include_once('../include/auth.php');
conn_db();
$query="SELECT * from partylinks";
$result = mysql_query($query);
while($row = mysql_fetch_array($result)) {
  echo $row['id'] . "\t" . $row['party'] . "\t" . $row['year'] . "\t" . $row['csdb'] . "\t" . $row['zxdemo'] . "\t" . $row['slengpung'] . "\n";
}
?>
