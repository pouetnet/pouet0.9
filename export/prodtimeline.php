<?
include('../include/auth.php');

$dbl = mysql_connect($db['host'], $db['user'], $db['password']);
if(!$dbl) {
	die('SQL error... sorry ! ^^; I\'m on it !');
}
mysql_select_db($db['database'],$dbl);

header("Content-type: text/xml");
echo "<"."?xml version='1.0' encoding='UTF-8'?".">\n";

$query = "SELECT p.name, p.date, g1.name AS groupname FROM prods p ".
         " LEFT JOIN groups AS g1 ON p.group1=g1.id ".
         //" LEFT JOIN groups AS g2 ON p.group2=g2.id ".
         //" LEFT JOIN groups AS g3 ON p.group3=g3.id ".
         //" LEFT JOIN parties AS pa ON p.party=pa.id ".
         " where p.date IS NOT NULL ".
         " ORDER BY p.date";
printf("<feed>\n");

$r = mysql_query($query) or die(mysql_error());
$n = 1;
while ($o = mysql_fetch_object($r)) {
  printf(" <prod>\n");
  printf("  <name>%s</name>\n",utf8_encode(htmlspecialchars($o->name)));
  printf("  <group>%s</group>\n",utf8_encode(htmlspecialchars($o->groupname)));
  printf("  <date>%s</date>\n",utf8_encode(htmlspecialchars($o->date)));
/*
    $name = $r["name"];
    $groupname = $r["groupname"];
    $link = $r["video"];
    $party = $r["partyname"]." ".$year;
    $platform = $r["platform"];
    $pouetID = $r["id"];
*/
  printf(" </prod>\n");
  flush();
}
printf(" </feed>\n");
?>
