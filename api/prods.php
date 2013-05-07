<?php
header('Content-Type: text/plain; charset=utf-8');
include_once('../include/misc.php');
include_once('../include/auth.php');
conn_db();
$query="SELECT * FROM prods LEFT OUTER JOIN prods_platforms ON (prods.id = prods_platforms.prod)";
if ($_GET['latest']) {
	$query .= " WHERE quand > DATE_SUB(NOW(), INTERVAL 1 DAY)";
}
$result = mysql_query($query);
$row = mysql_fetch_array($result);
$bailout = 0;
while($bailout < 1000000) {
  $bailout++;
  echo $row['id'] . "\t" . iconv('iso-8859-1', 'utf-8', $row['name']) . "\t" . substr($row['date'], 0, 7) . "\t" . $row['type'] . "\t" . $row['group1'] . "\t" . $row['group2'] . "\t" . $row['group3'] . "\t" . $row['download'] . "\t" . $row['video'] . "\t" . $row['source'] . "\t" . $row['csdb'] . "\t" . $row['zxdemo'] . "\t" . $row['sceneorg'] . "\t" . $row['platform'];
  $last_id = $row['id'];
  while ($row = mysql_fetch_array($result)) {
    if ($row['id'] != $last_id) {
      break;
    }
    echo ',' . $row['platform'];
  }
  echo "\n";
  if (!$row) {
    break;
  }
}
?>