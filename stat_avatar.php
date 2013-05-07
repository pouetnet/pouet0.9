<?
require("include/top.php");
?>
<table bgcolor="#000000" cellspacing="1" cellpadding="3" border="0">
<?
if ($_GET["avatar"]) {
$sql = "SELECT avatar, id, nickname FROM users WHERE BINARY avatar = '".$_GET["avatar"]."'";
$r = mysql_query($sql);
while ($o = mysql_fetch_object($r)) {
?>
 <tr class="bg<?=(($n++)&1)+1?>">
  <td><img src="avatars/<?=$o->avatar?>"></td>
  <td><a href="user.php?who=<?=$o->id?>"><?=$o->nickname?></a></td>
 </tr>
<?
}
} else {
$sql = "SELECT avatar, count(  *  )  AS c FROM users GROUP BY BINARY avatar ORDER BY c DESC limit 50";
$r = mysql_query($sql);
while ($o = mysql_fetch_object($r)) {
?>
 <tr class="bg<?=(($n++)&1)+1?>">
  <td><?=$n?>.</td>
  <td><a href="stat_avatar.php?avatar=<?=$o->avatar?>"><img src="avatars/<?=$o->avatar?>" border="0"></a></td>
  <td><?DoBar($o->c,false);?></td>
 </tr>
<?
}
}

?>
</table>
<? require("include/bottom.php"); ?>
