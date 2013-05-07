<?
include('../include/auth.php');

$dbl = mysql_connect($db['host'], $db['user'], $db['password']);
if(!$dbl) {
	die('SQL error... sorry ! ^^; I\'m on it !');
}
mysql_select_db($db['database'],$dbl);

header("Content-type: text/xml");
echo "<"."?xml version='1.0' encoding='UTF-8'?".">\n";

$query = 
  "SELECT prods.id as id, prods.name, parties.name as partyname, downloadlinks.link, prods.partycompo, prods.party_place, g1.name as g1name, g2.name as g2name, g3.name as g3name ".
  " FROM downloadlinks ".
  " LEFT JOIN prods on prods.id = downloadlinks.prod ".
  " LEFT JOIN parties on parties.id = prods.party ".
  " LEFT JOIN groups as g1 on g1.id = prods.group1 ".
  " LEFT JOIN groups as g2 on g2.id = prods.group2 ".
  " LEFT JOIN groups as g3 on g3.id = prods.group3 ".
  " WHERE downloadlinks.type = 'youtube'";
printf("<feed>\n");

$r = mysql_query($query) or die(mysql_error());
$n = 1;
while ($o = mysql_fetch_object($r)) {
  printf(" <prod>\n");
  printf("  <id>%d</id>\n",$o->id);
  printf("  <name>%s</name>\n",utf8_encode(htmlspecialchars($o->name)));
  if ($o->g1name)
    printf("  <group1>%s</group1>\n",utf8_encode(htmlspecialchars($o->g1name)));
  if ($o->g2name)
    printf("  <group2>%s</group2>\n",utf8_encode(htmlspecialchars($o->g2name)));
  if ($o->g3name)
    printf("  <group3>%s</group3>\n",utf8_encode(htmlspecialchars($o->g3name)));
  if ($o->partyname)
  {
    printf("  <party>%s</party>\n",utf8_encode(htmlspecialchars($o->partyname)));
    printf("  <compo>%s</compo>\n",utf8_encode(htmlspecialchars($o->partycompo)));
    printf("  <rank>%s</rank>\n",utf8_encode(htmlspecialchars($o->party_place)));
  }
  printf("  <youtube>%s</youtube>\n",utf8_encode(htmlspecialchars($o->link)));
  printf(" </prod>\n");
  flush();
}
printf("</feed>\n");
?>  
