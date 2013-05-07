<?
//ob_start();
session_start();
if (!($SESSION_LEVEL=='administrator' || $SESSION_LEVEL=='moderator' || $SESSION_LEVEL=='gloperator'))
  die("OMG");
include_once("../include/top.php");

$r = mysql_query(sprintf("select * from gloperator_log left join users on users.id = gloperator_log.gloperatorid order by date desc limit %d",$_GET["limit"]?$_GET["limit"]:50));

?>
<table bgcolor="#000000" cellspacing="1" cellpadding="3" border="0" style='margin:10px;'>
<?
while($o = mysql_fetch_object($r)) {
?>
 <tr class="bg<?=(($n++)&1)+1?>">
  <td><?=$o->date?></td>
  <td><a href="../user.php?who=<?=$o->gloperatorid?>"><img border='0' src="../avatars/<?=$o->avatar?>"></a></td>
  <td><a href="../user.php?who=<?=$o->gloperatorid?>"><?=$o->nickname?></a></td>
  <td><?=$o->action?></td>
  <td><?
  switch($o->action) {
    case "topic_edit_category":
      printf("topic: <a href='../topic.php?which=%d'>%d</a>",$o->itemid,$o->itemid);
      break;
    case "prod_edit":
      printf("prod: <a href='../prod.php?which=%d'>%d</a>",$o->itemid,$o->itemid);
      break;
    default:
      printf("%d",$o->itemid);
      break;
  }
  ?></td>
 </tr>
<?
}
?>
</table>
<?


include_once("../include/bottom.php"); ?>
