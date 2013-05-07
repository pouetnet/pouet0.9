<?
require("include/top.php");

if (!canEditBBSCategories()) die("AEGRHGAKEAHG!N!!!!!!!");

if($_POST) {
  $submitok = mysql_query(sprintf("update bbs_topics set category=%d where id=%d",$_POST["category"],$_POST["which"]));
  logGloperatorAction("topic_edit_category",$_POST["which"]);
}

$r = mysql_query(sprintf("select * from bbs_topics where id=%d",$_REQUEST["which"]));
$o = mysql_fetch_object($r);
?>

<br />

<form action="edit_topic_category.php?which=<?=$which?>" method="post" name="editprod_light">
<input type="hidden" name="which" value="<?=$which?>">
<table bgcolor="#000000" cellspacing="1" cellpadding="0"><tr><td>
<table bgcolor="#000000" cellspacing="1" cellpadding="2">
<? if($submitok): ?>
 <tr><th bgcolor="#224488">this prod has been modified</th></tr>
 <tr>
  <td bgcolor="#446688" align="center">
   feel free to edit it again<br />
   <a href="topic.php?which=<? print($which); ?>">see what you've done</a><br />
  </td>
 </tr>
<? endif; ?>
<? if($errormessage): ?>
 <tr><th bgcolor="#224488">errors found</th></tr>
 <? for($i=0;$i<count($errormessage);$i++): ?>
  <? if($i%2): ?>
   <tr><td bgcolor="#557799">&nbsp;<b>- <font color="#FF8888"><? print($errormessage[$i]); ?></font></b></td></tr>
  <? else: ?>
   <tr><td bgcolor="#446688">&nbsp;<b>- <font color="#FF8888"><? print($errormessage[$i]); ?></font></b></td></tr>
  <? endif; ?>
 <? endfor; ?>
<? endif; ?>
 <tr>
  <th bgcolor="#224488">topic information</th>
 </tr>
 <tr>
  <td bgcolor="#446688">
  <div style='margin:10px;'>thread:</div>
  <b><?=htmlspecialchars($o->topic)?></b>
  <div style='margin:10px;'>category:</div>
  <select name='category'>
  <?
  foreach ($thread_categories as $k=>$v)
    printf("<option value='%d'%s>%s</option>\n",$k,$o->category==$k?' selected="selected"':"",htmlspecialchars($v));
  ?>
  </select>
  </td>
 </tr>
 <tr>
  <td bgcolor="#224488" align="right"><input type="image" src="gfx/submit.gif" style="border: 0px"></td>
 </tr>
</table>



</td></tr></table>
</form>
<? require("include/bottom.php"); ?>
