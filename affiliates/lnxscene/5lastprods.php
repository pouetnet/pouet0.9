<?
//if($REMOTE_ADDR!="212.11.11.55") die("access not allowed");

print("<?\n\n");

require_once("../../include/auth.php");
$dbinfo=$db;
$db=mysql_connect($dbinfo['host'],$dbinfo['user'], $dbinfo['password']);
mysql_select_db($dbinfo['database'],$db);

$query="SELECT id,type,name,group1,group2,quand FROM prods ORDER BY quand DESC LIMIT 5";
$result=mysql_query($query,$db);
while($tmp=mysql_fetch_array($result)) {
  $prods[]=$tmp;
}

for($i=0;$i<count($prods);$i++) {
	$query="SELECT name FROM groups WHERE id=".$prods[$i]["group1"];
	$result=mysql_query($query,$db);
	if(mysql_num_rows($result)) {
		$prods[$i]["groupname1"]=mysql_result($result,0);
	}
	$query="SELECT name FROM groups WHERE id=".$prods[$i]["group2"];
	$result=mysql_query($query,$db);
	if(mysql_num_rows($result)) {
		$prods[$i]["groupname2"].=mysql_result($result,0);
	}
}

for($i=0;$i<count($prods);$i++) {
  print("\$newprods[".$i."][\"id\"]=".$prods[$i]["id"].";\n");
  print("\$newprods[".$i."][\"name\"]=\"".$prods[$i]["name"]."\";\n");
  print("\$newprods[".$i."][\"type\"]=\"".$prods[$i]["type"]."\";\n");
  print("\$newprods[".$i."][\"group1\"]=\"".$prods[$i]["groupname1"]."\";\n");
  print("\$newprods[".$i."][\"group2\"]=\"".$prods[$i]["groupname2"]."\";\n");
  print("\$newprods[".$i."][\"date\"]=\"".$prods[$i]["quand"]."\";\n");
  print("\n");
}

mysql_close($db);
print("?>\n");
?>
