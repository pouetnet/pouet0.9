<?php
header('Content-Type: text/plain; charset=utf-8');
include_once('../include/misc.php');
include_once('../include/auth.php');
conn_db();
$query="SELECT screenshots.* FROM screenshots INNER JOIN prods ON (screenshots.prod = prods.id)";
if ($_GET['latest']) {
	$query .= " WHERE screenshots.added > DATE_SUB(NOW(), INTERVAL 1 DAY)";
}
$result = mysql_query($query);
while($row = mysql_fetch_array($result)) {
  if(file_exists("../screenshots/".$row["prod"].".jpg")) {
    $format = "jpg";
  } elseif(file_exists("../screenshots/".$row["prod"].".gif")) {
    $format = "gif";
  } elseif(file_exists("../screenshots/".$row["prod"].".png")) {
    $format = "png";
  } else {
    $format = "!!!";
  }
  echo $row['id'] . "\t" . $row['prod'] . "\t" . $format . "\n";
}
?>
