<?php
header('Content-Type: text/plain; charset=utf-8');
include_once('../include/misc.php');
include_once('../include/auth.php');
conn_db();
$query="SELECT level FROM users WHERE id=" . intval($_GET['id']);
$result = mysql_query($query);
if ($row = mysql_fetch_array($result)) {
  echo $row['level'];
} else {
  echo 'none';
}
?>