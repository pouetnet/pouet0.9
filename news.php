<? require("include/top.php"); ?>
<?
$query="SELECT * FROM ojnews ORDER BY quand DESC";
$result=mysql_query($query);
while($tmp=mysql_fetch_assoc($result)) {
  $tmp["newstype"]="ojnews";
	$ojnews[]=$tmp;
}

$query="SELECT * FROM news ORDER BY quand DESC";
$result=mysql_query($query);
while($tmp=mysql_fetch_assoc($result)) {
  $tmp["newstype"]="pouetnews";
  $mynews[]=$tmp;
}

$oj=0;
$po=0;
for($i=0;$i<(count($ojnews)+count($mynews));$i++) {
  if($ojnews[$oj]["quand"]>$mynews[$po]["quand"]) {
    $ojnews[$oj]["content"]=stripslashes(urldecode($ojnews[$oj]["content"]));
    $ojnews[$oj]["title"]=stripslashes(urldecode($ojnews[$oj]["title"]));
    $ojnews[$oj]["authornick"]=stripslashes(urldecode($ojnews[$oj]["authornick"]));
    $ojnews[$oj]["authorgroup"]=stripslashes(urldecode($ojnews[$oj]["authorgroup"]));
    $news[]=$ojnews[$oj];
    $oj++;
  } else {
    $news[]=$mynews[$po];
    $po++;
  }
}

$query="SELECT id,nickname,avatar FROM users";
$result=mysql_query($query,$db);
while($tmp=mysql_fetch_array($result)) {
  $users[$tmp["id"]]["nickname"]=$tmp["nickname"];
  $users[$tmp["id"]]["avatar"]=$tmp["avatar"];
}
?>
<br>
<table width="50%"><tr>
<td width="50%" valign="top">
<? for($i=0;$i<count($news);$i++): ?>
<table cellspacing="1" cellpadding="2" class="box">
 <tr>
  <th>
   <? if($news[$i]["newstype"]=="ojnews"): ?>
    <img class="icon" src="gfx/titles/orange.gif" alt="ojuice news">
   <? else: ?>
    <img class="icon" src="gfx/titles/trompetted.gif" alt="pouët.net news">
   <? endif; ?>
   <? if($news[$i]["url"]): ?><a href="<?=$news[$i]["url"]?>"><? endif; ?>
   <?=$news[$i]["title"]?>
   <? if($news[$i]["url"]): ?></a><? endif; ?>
  </th>
 </tr>
 <tr>
  <td bgcolor="#446688" class="justify">
   <?=$news[$i]["content"]?>
  </td>
 </tr>
 <tr>
  <? if($news[$i]["newstype"]=="ojnews"): ?>
  <td bgcolor="#6688AA" colspan="2" align="right">
   added on the <? print(substr($news[$i]["quand"],0,10)." at ".substr($news[$i]["quand"],11,5)); ?> by <a href="http://www.ojuice.net/<?=$news[$i]["authorid"]?>/nick.htm"><b><?=$news[$i]["authornick"]?></b></a><? if($news[$i]["authorgroup"]): ?>::<?=$news[$i]["authorgroup"]?><? endif; ?>
  </td>
  <? else: ?>
  <td bgcolor="#6688AA" colspan="2" align="right">
   <table cellspacing="0" cellpadding="0">
    <tr>
     <td>added on the <? print(substr($news[$i]["quand"],0,10)." at ".substr($news[$i]["quand"],11,5)); ?> by <a href="user.php?who=<?=$news[$i]["who"]?>"><b><?=$users[$news[$i]["who"]]["nickname"]?></b></a></td>
     <td>&nbsp;<br></td>
     <td><a href="user.php?who=<?=$news[$i]["who"]?>"><img src="avatars/<?=$users[$news[$i]["who"]]["avatar"]?>" width="16" height="16" border="0" alt=""></a></td>
    </tr>
   </table>
  </td>
  <? endif; ?>
 </tr>
</table>
<br>
<? endfor; ?>
</td>
</tr></table>
<? require("include/bottom.php"); ?>
