<?
include_once("include/misc.php");
include_once("include/auth.php");

conn_db();

$r = mysql_query("select name,type,party_year from prods where name like '".mysql_real_escape_string($_GET["what"])."%' order by views desc limit 10");
$res[0] = $_GET["what"];
while($o = mysql_fetch_object($r))
{
  $res[1][] = $o->name;
  $res[2][] = $o->type.($o->party_year?", ".$o->party_year:"");
}
echo json_encode($res);
?>
