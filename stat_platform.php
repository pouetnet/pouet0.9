<?

require("include/top.php");

//$result = mysql_query("DESC prods platform");
//$row = mysql_fetch_row($result);
//$platforms = explode("'",$row[1]);

//$query="select platforms.name, platforms.icon from platforms order by platforms.name asc";
$sql = 'SELECT count(*) as c,platforms.name as name,platforms.icon as icon FROM prods_platforms right JOIN platforms on platforms.id = prods_platforms.platform GROUP by prods_platforms.platform order by c DESC';
$r = mysql_query($sql);
?>

<br />
<table width="30%">
<tr><td>
<table cellspacing="1" cellpadding="2" class="box">
 <tr><th colspan="3"><center>platform icon check</center></th></tr>

<?
$n = 1;
while ($o = mysql_fetch_object($r)) {
?>
 <tr bgcolor="#557799">
  <td><?=$n++?>.</td>
  <td><img src="gfx/os/<?=$o->icon?>" border="0" > <a href='http://www.pouet.net/prodlist.php?platform[]=<?=rawurlencode($o->name)?>'><?=$o->name?></a><br /></td>
  <td><?=$o->c?></td>
   </tr>
<? } ?>
</table>
<br />
</td>
</tr>
</table>
<? require("include/bottom.php"); ?>
