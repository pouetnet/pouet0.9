<?
require("inc/top.php");

/*
$query="SELECT MAX(views) FROM prods";
$result=mysql_query($query,$db);
$maxviews=mysql_result($result,0);
$query="SELECT id,name,groups,views FROM prods WHERE (views>=(".floor(($maxviews*7)/100).")) ORDER BY name ASC";

$query="SELECT id,type,name,groups,when FROM prods ORDER BY views DESC LIMIT 5";
$result=mysql_query($query,$db);
while($tmp=mysql_fetch_array($result)) {
  $prods[]=$tmp;
}
*/

$query="SELECT id,type,name,group1,group2,quand FROM prods ORDER BY quand DESC LIMIT 15";
$result=mysql_query($query,$db);
while($tmp=mysql_fetch_array($result)) {
  $prods[]=$tmp;
}

for($i=0;$i<count($prods);$i++) {
	$query="SELECT name FROM groups WHERE id=".$prods[$i]["group1"];
	$result=mysql_query($query,$db);
	if(mysql_num_rows($result)) {
		$prods[$i]["group"]=mysql_result($result,0);
	}
	$query="SELECT name FROM groups WHERE id=".$prods[$i]["group2"];
	$result=mysql_query($query,$db);
	if(mysql_num_rows($result)) {
		$prods[$i]["group"].=" and ".mysql_result($result,0);
	}
}

for($i=0;$i<count($prods);$i++) {
  if($i<5) {
	$prodtype="most";
  } else {
	$prodtype="latest";
  }
  print($prods[$i]["id"]."\r\n".$prods[$i]["type"]."\r\n".$prods[$i]["name"]."\r\n".$prods[$i]["group"]."\r\n".$prods[$i]["quand"]."\r\n".$prodtype."\r\n");
}

require("inc/bottom.php");
?>
