<?
include_once("include/misc.php");
conn_db();

if (!$_GET["days"] || !$_GET["platforms"] || !$_GET["types"]) {
  
$typeslist = array();
$result = mysql_query("DESC prods type");
$row = mysql_fetch_row($result);
preg_match_all("/'(.*?)'/",$row[1],$typeslist);

$platforms = array();
$query="select * from platforms order by name asc";
$result = mysql_query($query);
while($tmp = mysql_fetch_array($result)) {
  	 $platforms[]=$tmp;
}
  
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
	"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
<body>

<h1>Number of days to go back</h1>
<form action="painfeed.php" method="get">
<input name="days" value="365"/>

<h1>Prod types</h1>
<select name="types[]" multiple="multiple" size="10">
<?
foreach($typeslist[1] as $t) printf(" <option value='%s'>%s</option>\n",$t,$t);
?>
</select>

<h1>Prod platforms</h1>
<select name="platforms[]" multiple="multiple" size="10">
<?
foreach($platforms as $t) printf(" <option value='%s'>%s</option>\n",$t["id"],$t["name"]);
?>
</select>

<input type="submit"/>

</form>
</body>
</html>
<?
  exit();
}

header("Content-type: application/xml");

echo "<"."?xml version=\"1.0\" encoding=\"UTF-8\"?".">";

$days = )int)$_GET["days"];
$days = min($days,3*365);

$platforms = implode(",",$_GET["platforms"]);

$t = array();
foreach($_GET["types"] as $t2) $t[] = "'".mysql_real_escape_string($t2)."'";
$types = implode(",",$t);

//$sql = "select * from platforms";
//$r = mysql_query($sql) or die(mysql_error()." - ".$sql);

$sql = "select prods.id,prods.name,g1.name as g1,g2.name as g2,g3.name as g3 from prods ".
" LEFT JOIN groups AS g1 ON prods.group1 = g1.id".
" LEFT JOIN groups AS g2 ON prods.group2 = g2.id".
" LEFT JOIN groups AS g3 ON prods.group3 = g3.id".
" JOIN prods_platforms ON prods.id = prods_platforms.prod ".
" WHERE (UNIX_TIMESTAMP()-UNIX_TIMESTAMP(prods.date))<=".$days."*60*60*24 ".
" AND prods_platforms.platform IN (".mysql_real_escape_string($platforms).") ".
" AND FIND_IN_SET(".$types.",prods.type) ".
" GROUP BY prods.id ORDER BY prods.name";

$r = mysql_query($sql) or die(mysql_error()." - ".$sql);
?>
<prods>
<?
while($o = mysql_fetch_object($r)) {
  $g = array();
  if ($o->g1) $g[] = $o->g1;
  if ($o->g2) $g[] = $o->g2;
  if ($o->g3) $g[] = $o->g3;
  $groups = implode(", ",$g);
  
  $s = sprintf("select platforms.name from prods_platforms, platforms where prod = %d and platforms.id = prods_platforms.platform",$o->id);
  $r2 = mysql_query($s);
  $plat = array();
  while($o2 = mysql_fetch_object($r2))
    $plat[] = $o2->name;
    
  echo "  <prod>\n";
  printf("    <name>%s</name>\n",utf8_encode(htmlspecialchars($o->name)) );
  if ($groups)
    printf("    <groups>%s</groups>\n",utf8_encode(htmlspecialchars($groups)) );
  printf("    <platform>%s</platform>\n",utf8_encode(htmlspecialchars(implode(", ",$plat))) );
  echo "  </prod>\n";
}
?>
</prods>
