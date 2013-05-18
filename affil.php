<?
require("include/top.php");

?>
<br />

<table><tr>
<td valign="top" align="center">
<table bgcolor="#000000" cellspacing="1" cellpadding="0">
 <tr>
  <td>
	<table bgcolor="#000000" cellspacing="1" cellpadding="2" margin="5">
	<tr>
	 <th>original</th>
	 <th>-&gt;</th>
	 <th>derivative</th>
	</tr>
<?

$sql  = " SELECT *,".
" p1.id as p1id, ".
" p1.name as p1name, ".
" p1.type as p1type, ".
" p2.id as p2id, ".
" p2.name as p2name, ".
" p2.type as p2type, ".
" affiliatedprods.type as type ".
" from affiliatedprods ".
" left join prods as p1 on p1.id = affiliatedprods.original ".
" left join prods as p2 on p2.id = affiliatedprods.derivative ".
"";

flush();

$r = mysql_query($sql) or die(mysql_error());
$n = 0;
$y = date("Y")+1;
$c = "";
while($o = mysql_fetch_object($r)) {
?>
<tr class="bg<?=((($n++)&1)+1)?>">
<td>
<?
$typ = explode(",", $o->p1type);
foreach($typ as $p) {
  ?><a href="prodlist.php?type[]=<?=$p?>"><img src="gfx/types/<?=$types[$p]?>" border="0" title="<?=$p?>"></a><?
}
?>
  <a href="prod.php?which=<?=$o->p1id?>"><?=$o->p1name?></a>
</td>
<td>
<?=$o->type?>
</td>
<td>
<?
$typ = explode(",", $o->p2type);
foreach($typ as $p) {
  ?><a href="prodlist.php?type[]=<?=$p?>"><img src="gfx/types/<?=$types[$p]?>" border="0" title="<?=$p?>"></a><?
}
?>
  <a href="prod.php?which=<?=$o->p2id?>"><?=$o->p2name?></a>
</td>
</tr>
<?
}
?>
</table>
</td>
</tr>
</table>
</td>
</tr>
</table>

<? require("include/bottom.php"); ?>
