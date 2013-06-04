<?
//ob_start();
session_start();
//var_dump($_SESSION);
if (!($_SESSION["SESSION_LEVEL"]=='administrator' || $_SESSION["SESSION_LEVEL"]=='moderator' || $_SESSION["SESSION_LEVEL"]=='gloperator'))
  die("OMG");
include_once("../include/top.php");
//echo str_replace("include","../include",ob_get_clean());

$ban_id = (int) $_POST["ban_id"];
$ip = mysql_real_escape_string($_GET["ip"]);
$user = (int) $_GET["user"];

if ($_SESSION["SESSION_LEVEL"]=='administrator' && $ban_id) {
  $sql = sprintf("update users set level='banned' where id=%d", $ban_id);
  echo $sql;
  mysql_query($sql);

}
?>
<table bgcolor="#000000" cellspacing="1" cellpadding="3" border="0">
<?
if ($ip) {
$sql = "SELECT id, level, nickname, avatar, lastip, lasthost, glops FROM users where lastip='".$ip."'";
$r = mysql_query($sql);
while ($o = mysql_fetch_object($r)) {
?>
 <tr class="bg<?=(($n++)&1)+1?>">
  <td><a href="../user.php?who=<?=$o->id?>"><img border='0' src="../avatars/<?=$o->avatar?>"></a></td>
  <td><a href="../user.php?who=<?=$o->id?>"><?=$o->nickname?></a></td>
  <td><a href="ip.php?user=<?=$o->id?>"><?=$o->level?></a></td>
  <td><?=$o->glops?> glops</td>
  <td><?=$o->lasthost?> (<?=$o->lastip?>)</td>
 </tr>
<?
}
} else if ($user) {
$sql = "SELECT id, level, nickname, avatar, lastip, lasthost, glops, lastlogin FROM users where id='".$user."'";
$r = mysql_query($sql);
while ($o = mysql_fetch_object($r)) {
?>
 <tr class="bg<?=(($n++)&1)+1?>">
  <td><a href="../user.php?who=<?=$o->id?>"><img border='0' src="../avatars/<?=$o->avatar?>"></a></td>
  <td><a href="../user.php?who=<?=$o->id?>"><?=$o->nickname?></a></td>
  <td><?=$o->level?></td>
  <td><?=$o->glops?> glops</td>
  <td><?=$o->lastlogin?></td>
  <td><?=$o->lasthost?> (<a href='ip.php?ip=<?=$o->lastip?>'><?=$o->lastip?></a>) (<a href='http://www.geoiptool.com/en/?IP=<?=$o->lastip?>'>geoip</a>)</td>
<?
if ($SESSION_LEVEL=='administrator') {
?>
  <td>
    <form action='ip.php?user=<?=$user?>' method='post' onsubmit='return confirm("are you sure you want to ban <?=$o->nickname?>?")'>
     <input type='hidden' name='ban_id' value='<?=$o->id?>'/>
     <input type='submit' value='ban'/>
    </form>
  </td>
<?
}
?>
 </tr>
<?
}
} else {
$sql = 'SELECT lastip, lasthost, count( * ) AS c, group_concat( nickname ) AS nix FROM users GROUP BY lastip HAVING c > 1 ORDER BY c DESC, lasthost';
$r = mysql_query($sql) or die(mysql_error());
while ($o = mysql_fetch_object($r)) {
  if (!$o->lastip) continue;
?>
 <tr class="bg<?=(($n++)&1)+1?>">
  <td><a href="ip.php?ip=<?=$o->lastip?>"><?=$o->lasthost?> (<?=$o->lastip?>)</a></td>
  <td><?=$o->c?></td>
  <td><?=$o->nix?></td>
 </tr>
<?
}
}

?>
</table>
<? include_once("../include/bottom.php"); ?>
