<?
require("include/top.php");

$r = mysql_query("select * from users where nickname=''") or die(mysql_error());
while ($o = mysql_fetch_object($r))
{
  $data = unserialize($o->sceneIDData);
  if($data["nickname"])
  {
    mysql_query("update users set nickname='".mysql_real_escape_string($data["nickname"])."' where id=".$o->id) or die(mysql_error());
    echo $o->id;
  }
  else if($data["login"])
  {
    mysql_query("update users set nickname='".mysql_real_escape_string($data["login"])."' where id=".$o->id) or die(mysql_error());
    echo $o->id;
  }
}
require("include/bottom.php");

?>
