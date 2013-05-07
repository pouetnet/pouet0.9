<?php
header('Content-Type: text/plain; charset=utf-8');
include_once('../include/misc.php');
include_once('../include/auth.php');
conn_db();
$query="SELECT * FROM parties";
if ($_GET['latest']) {
	$query .= " WHERE quand > DATE_SUB(NOW(), INTERVAL 1 DAY)";
}
$result = mysql_query($query);
while($row = mysql_fetch_array($result)) {
  echo $row['id'] . "\t" . iconv('iso-8859-1', 'utf-8', $row['name']) . "\t" . $row['web'] . "\n";
}
?>