<?
include('../include/auth.php');

$dbl = mysql_connect($db['host'], $db['user'], $db['password']);
if(!$dbl) {
	die('SQL error... sorry ! ^^; I\'m on it !');
}
mysql_select_db($db['database'],$dbl);

header("Content-type: text/xml");
echo "<"."?xml version='1.0' encoding='UTF-8'?".">\n";

$query = "SELECT p.name, p.id, plt.name as platform, g1.name AS groupname, pa.name AS partyname FROM prods p ".
         " LEFT JOIN groups AS g1 ON p.group1=g1.id ".
         " LEFT JOIN groups AS g2 ON p.group2=g2.id ".
         " LEFT JOIN groups AS g3 ON p.group3=g3.id ".
         " LEFT JOIN parties AS pa ON p.party=pa.id ".
         " LEFT JOIN prods_platforms AS pp ON pp.prod=p.id ".
         " LEFT JOIN platforms AS plt ON plt.id=pp.platform ";
if ($_GET["id"]) {
  $id = (int)$_GET["id"];
  $query .= " WHERE p.id=".$id;
  printf("<feed pouetid='%d'>\n",$id);
} else {
  $pouetgroup = $_GET["pouetgroup"];
  $pouetname = $_GET["pouetname"];
  $query .= " WHERE ";
  if (trim($pouetgroup)) {
    $query .= "(g1.name LIKE '%".$pouetgroup."%' OR ";
    $query .= " g2.name LIKE '%".$pouetgroup."%' OR ";
    $query .= " g3.name LIKE '%".$pouetgroup."%') AND ";
  }
  $query .= " p.name LIKE '%".$pouetname."%' order by rank";
  printf("<feed pouetgroup='%s' pouetname='%s'>\n",$pouetgroup,$pouetname);
}
if ($_GET["pixturhack"]) {
  $query =  "select name,id,prodz.* from groups ";
  $query .= "left join (select sum(voteup) as t_up,sum(votedown) as t_down,group1,count(*) as nprods from prods group by group1) as prodz on prodz.group1 = groups.id ";
  $query .= "order by t_up desc limit 1000";

}
$r = mysql_query($query) or die(mysql_error());

$n = 1;
while ($o = mysql_fetch_object($r)) {
  if ($_GET["pixturhack"]) {
    printf(" <group rank='%d' groupid='%d'>\n",$n++,$o->id);
    printf("  <name>%s</name>\n",utf8_encode(htmlspecialchars($o->name)));
    printf("  <thumbsup>%d</thumbsup>\n",utf8_encode(htmlspecialchars($o->t_up)));
    printf("  <thumbsdown>%d</thumbsdown>\n",utf8_encode(htmlspecialchars($o->t_down)));
    printf("  <prods>%d</prods>\n",utf8_encode(htmlspecialchars($o->nprods)));
    printf(" </group>\n");
    continue;
  }
  printf(" <prod>\n");
  printf("  <name>%s</name>\n",utf8_encode(htmlspecialchars($o->name)));
  printf("  <group>%s</group>\n",utf8_encode(htmlspecialchars($o->groupname)));
  printf("  <video>%s</video>\n",utf8_encode(htmlspecialchars($o->video)));
  printf("  <partyname>%s</partyname>\n",utf8_encode(htmlspecialchars($o->partyname)));
  printf("  <platform>%s</platform>\n",utf8_encode(htmlspecialchars($o->platform)));
  printf("  <id>%s</id>\n",utf8_encode(htmlspecialchars($o->id)));
/*
    $name = $r["name"];
    $groupname = $r["groupname"];
    $link = $r["video"];
    $party = $r["partyname"]." ".$year;
    $platform = $r["platform"];
    $pouetID = $r["id"];
*/
  printf(" </prod>\n");
}
printf(" </feed>\n");
?>
