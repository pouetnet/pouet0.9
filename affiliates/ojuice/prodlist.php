<?
require("inc/top.php");

/*
$query="SELECT MAX(views) FROM prods";
$result=mysql_query($query,$db);
$maxviews=mysql_result($result,0);
$query="SELECT id,name,groups,views FROM prods WHERE (views>=(".floor(($maxviews*7)/100).")) ORDER BY name ASC";
*/
$query="SELECT id,name,group1,group2,views,quand FROM prods ORDER BY name ASC";
$result=mysql_query($query,$db);
while($tmp=mysql_fetch_array($result)) {
  $prods[]=$tmp;
}

$query="SELECT id,name FROM  groups";
$result=mysql_query($query,$db);
while($tmp=mysql_fetch_array($result)) {
  $groups[$tmp["id"]]=$tmp["name"];
}

for($i=0;$i<count($prods);$i++) {
  if($prods[$i]["group2"]) {
    print($prods[$i]["id"]."\r\n".$prods[$i]["name"]."\r\n".$groups[$prods[$i]["group1"]]." and ".$groups[$prods[$i]["group2"]]."\r\n".$prods[$i]["quand"]."\r\n");
  } else {
    print($prods[$i]["id"]."\r\n".$prods[$i]["name"]."\r\n".$groups[$prods[$i]["group1"]]."\r\n".$prods[$i]["quand"]."\r\n");
  }
}

require("inc/bottom.php");
?>