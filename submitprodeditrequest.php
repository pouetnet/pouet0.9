<?
require("include/top.php");
if (($SESSION_LEVEL=='administrator' || $SESSION_LEVEL=='moderator' || $SESSION_LEVEL=='gloperator')) {

$fields = array(
  "name" => "name",
  "group1" => "group 1",
  "group2" => "group 2",
  "group3" => "group 3",
  "download" => "primary download link",
  "type" => "type(s)",
  "party" => "release party",
  "party_year" => "release party year",
  "party_place" => "release party ranking",
);  

$pid = $_GET["which"] ? $_GET["which"] : $_POST["prodid"];
if ($pid) {
  $sql = sprintf("select * from prods where id=%d",$pid);
  $r = mysql_query($sql);
  $o = mysql_fetch_object($r);
  if (!$o) $pid = 0;
}
?>
<br>
<form action="submitprodeditrequest.php" method="post" enctype="multipart/form-data">

<table bgcolor="#000000" cellspacing="1" cellpadding="2">
<tr>
<th>submit prod edit request</th>
</tr>
<tr>
<td class="bg1">
<?
$showsubmit = 1;
if (!$pid) {
?>
please enter the prod id you wish to have edited:
<input type="text" name="prodid"/>
<?
} else if (!$_POST["field"]) {
?>
<input type="hidden" name="prodid" value="<?=((int)$pid)?>"/>
please select the field/parameter you wish to have edited for <b><?=$o->name?></b>:
  
<select name="field">
<? foreach($fields as $k=>$v) {?>
  <option value="<?=$k?>"><?=$v?></option>
<? } ?>
</select>
<?
} else if (!$_POST["newvalue"]) {
  $f = $_POST["field"];
?>
<input type="hidden" name="prodid" value="<?=((int)$pid)?>"/>
<input type="hidden" name="field" value="<?=($_POST["field"])?>"/>
please enter the correct value for <b><?=$fields[$_POST["field"]]?></b> for <b><?=$o->name?></b>:<br/>
<input type="text" name="newvalue" value="<?=htmlentities($o->$f)?>" size="80"/>
<?
} else {
  $sql = sprintf("insert into editrequests set prodid='%d', field='%s', newvalue='%s', userid='%d', datetime='%s'",
    $pid,$_POST["field"],$_POST["newvalue"],$_SESSION["SCENEID_ID"],date("Y-m-d H:i:s"));
  $r = mysql_query($sql);
  
  echo "thanks for your correction. it will soon be reviewed by someone worthy enough to fuck up the site.";
}
?>
</td>
</tr>
<tr>
<td class="bg2">
<? if ($showsubmit) { ?>
<input type="image" src="gfx/submit.gif" border="0">
<? } else { ?>
<a href="prod.php?which=<?=$pid?>">go back!</a>
<? } ?>
</td>
</tr>
</table>
</form>
<br />
<?
} else {
  print("not now susan, my head hurts.");
}
require("include/bottom.php");
?>
