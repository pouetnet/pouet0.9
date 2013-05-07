<?
require("include/top.php");

$query="SELECT type,img,url,alt FROM buttons WHERE dead = 0 ORDER BY type,RAND()";
$result=mysql_query($query);
while($tmp=mysql_fetch_array($result)) {
  $buttons[]=$tmp;
}
?>
<br>

<table bgcolor="#000000" cellspacing="1" cellpadding="2" width="50%">
<? for($i=0;$i<count($buttons);$i++): ?>
<? if($buttons[$i]["type"]!=$buttons[$i-1]["type"]): ?>
<tr>
<th bgcolor="#224488"><?=$buttons[$i]["type"]?></th>
</tr>
<tr>
<td bgcolor="#446688">
<? endif; ?>
<div style="margin:3px; float:left; width:88px; height:31px; overflow:hidden;"><a href="<?=$buttons[$i]["url"]?>"><img src="gfx/buttons/<?=$buttons[$i]["img"]?>" border="0" alt="<?=$buttons[$i]["alt"]?>"></a></div>
<? if($buttons[$i]["type"]!=$buttons[$i+1]["type"]): ?>
</td>
</tr>
<? endif; ?>
<? endfor; ?>
</table>
<br>
<? require("include/bottom.php"); ?>
