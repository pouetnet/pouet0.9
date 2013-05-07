<?
session_start();
if (!($SESSION_LEVEL=='administrator' || $SESSION_LEVEL=='moderator' || $SESSION_LEVEL=='gloperator'))
  die("OMG");

require("../include/top.php");
require("../include/awardscategories.inc.php");

$year = $sceneorgyear;

?>
<style type="text/css">
.view {
  background: black;
  border-collapse: separate;
  border-spacing: 1px;
  border: 1px solid black;
  margin: 10px;
}
.view td {
  margin: 0px;
  padding: 2px;
}
.view img {
  vertical-align: text-top;
  margin-right: 2px;
}
.view th {
  font-size: 120%;
  padding: 5px;
  text-align: center;
}
#container {
  width: 750px;
  margin: 0px auto;
}
</style>

<div id='container'>
<?
$f = file("sql.csv");
$votes = array();
foreach($f as $v) {$a=explode(";",$v);$votes[$a[0]]=trim($a[1]);}
//var_dump($votes);
$r = mysql_query("select * from users where id in (".implode(",",array_keys($votes)).")");
$users=array();
while($o=mysql_fetch_object($r))$users[$o->id]=$o;

$r = mysql_query("select * from prods where id in (".implode(",",$votes).")");
$prods=array();
while($o=mysql_fetch_object($r))$prods[$o->id]=$o;

ksort($votes);

printf("<table class='view'>\n");
printf("<tr>\n");
printf("  <th>user</th>\n");
printf("  <th>last login/access</th>\n");
printf("  <th>gl&ouml;ps</th>\n");
printf("  <th>vote</th>\n");
printf("</tr>\n");
foreach($votes as $user=>$prod)
{
  printf("<tr class='bg%d'>\n",($n++&1) + 1);
  if ($users[$user])
  {
    printf("<td><img src='../avatars/%s' width='16'/><a href='../user.php?who=%d'>%s</a></td>\n",$users[$user]->avatar,$user,$users[$user]->nickname);
    printf("<td>%s (%d days)</td>\n",$users[$user]->lastlogin,(time()-strtotime($users[$user]->lastlogin))/(60*60*24));
    printf("<td>%d gl&ouml;ps</td>\n",$users[$user]->glops);
  }
  else
    printf("<td colspan='3'>non-pouet user (#%d)</td>\n",$user);
  printf("<td><a href='../prod.php?which=%d'>%s</a></td>\n",$prod,$prods[$prod]->name);
  printf("</tr>\n");
}
printf("</table>\n");
?>
</div>
<?
require("../include/bottom.php");
?>
