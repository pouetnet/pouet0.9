<?
include('../include/auth.php');

$dbl = mysql_connect($db['host'], $db['user'], $db['password']);
if(!$dbl) {
	die('SQL error... sorry ! ^^; I\'m on it !');
}
mysql_select_db($db['database'],$dbl);

$r = mysql_query("select prods.id, prods.name, g1.name as g1name, g2.name as g2name from prods left join groups as g1 on g1.id=prods.group1 left join groups as g2 on g2.id=prods.group2 where download like '%".mysql_real_escape_string($_GET["filename"])."%'") or die(mysql_error());
while($o = mysql_fetch_object($r))
{
  $name = $o->name;
  if ($o->g1name)
    $name .= " by ".$o->g1name;
  if ($o->g2name)
    $name .= " and ".$o->g2name;

  printf("<a href='http://www.pouet.net/prod.php?which=%d'>%s</a>\n",$o->id,$name);
}
?>
