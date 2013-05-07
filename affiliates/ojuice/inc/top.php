<?php
require_once("../../../include/auth.php");
$dbinfo=$db;
$db=mysql_connect($dbinfo['host'],$dbinfo['user'], $dbinfo['password']);
mysql_select_db($dbinfo['database'],$db);
?>
