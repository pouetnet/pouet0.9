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

if ($_POST["requestid"]) {
  if ($_POST["approve"]) {
    $sql = sprintf("select * from editrequests where id=%d",$_POST["requestid"]);
    $r = mysql_query($sql);
    $o = mysql_fetch_object($r);

    $sql = sprintf("update prods set %s = '%s' where id=%d",
      $o->field,addslashes($o->newvalue),$o->prodid);
    $r = mysql_query($sql);
    
    $sql = sprintf("update editrequests set approved = 1, gloperatorid = %d where id=%d",
      $_SESSION["SCENEID_ID"],$_POST["requestid"]);
    mysql_query($sql);
  }
  if ($_POST["decline"]) {
    $sql = sprintf("update editrequests set approved = -1, gloperatorid = %d where id=%d",
      $_SESSION["SCENEID_ID"],$_POST["requestid"]);
    mysql_query($sql);
  }
}
?>
<br>
<form action="processeditrequest.php" method="post" enctype="multipart/form-data">

<table bgcolor="#000000" cellspacing="1" cellpadding="2">
<tr>
<th colspan="6">process prod edit requests</th>
</tr>
<tr>
<th>requested by</th>
<th>prod</th>
<th>field</th>
<th>from</th>
<th>to</th>
<th>action</th>
</tr>
<?
$sql =  "select *,prods.name as prodname,editrequests.id as id ";
$sql .= " from editrequests";
$sql .= " join users join prods ";
$sql .= " where editrequests.userid = users.id and approved = 0 and prods.id=editrequests.prodid";
$sql .= " order by datetime desc ";

$r = mysql_query($sql);
$n = 0;
while ($o = mysql_fetch_object($r)) {
  $f = $o->field;
?>
<tr class="bg<?=(($n++&1)+1)?>">
<td><a href="user.php?who=<?=$o->userid?>"><img src="avatars/<?=$o->avatar?>" border="0"> <?=$o->nickname?></a></td>
<td><a href="prod.php?which=<?=$o->prodid?>"><?=$o->prodname?></a></td>
<td><?=$fields[$o->field]?></td>
<td><?=$o->$f?></td>
<td><?=$o->newvalue?></td>
<td>
<form action="processeditrequest.php" method="post">
<input name="requestid" type="hidden" value="<?=$o->id?>">
<input name="approve" type="submit" value="approve!">
<input name="decline" type="submit" value="decline!">
</form>
</td>
</tr>
<?
}
?>
</table>
</form>
<br />
<?
} else {
  print("not now susan, my head hurts.");
}
require("include/bottom.php");
?>
