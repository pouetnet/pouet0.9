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
	  <th colspan="3" style="text-align:center">scene.org awards</th>
	</tr>
<?

$sql  = " SELECT *,prods.id as id, ".
" g1.name as groupn1,g1.acronym as groupacron1, ".
" g2.name as groupn2,g2.acronym as groupacron2, ".
" g3.name as groupn3,g3.acronym as groupacron3, ".
" prods.name as name, ".
" GROUP_CONCAT(platforms.name) as platform, sceneorgrecommended.type as sotype ".
" FROM sceneorgrecommended,prods ".
" JOIN prods_platforms JOIN platforms ".
" LEFT JOIN groups AS g1 ON prods.group1 = g1.id".
" LEFT JOIN groups AS g2 ON prods.group2 = g2.id".
" LEFT JOIN groups AS g3 ON prods.group3 = g3.id".
" WHERE sceneorgrecommended.prodid = prods.id ".
" AND sceneorgrecommended.type != 'viewingtip'".
" AND prods_platforms.prod=prods.id ".
" AND prods_platforms.platform=platforms.id ".
" GROUP BY sceneorgrecommended.id ".
" ORDER BY date_format(prods.date,'%Y') DESC,sceneorgrecommended.category,sceneorgrecommended.type";

flush();

$r = mysql_query($sql) or die(mysql_error());
$n = 0;
$y = date("Y")+1;
$c = "";
while($o = mysql_fetch_object($r)) {
  $year = date("Y",strtotime($o->date));
  if ($year < $y || $c != $o->category) {
    if ($year < $y) {
?>
<tr>
  <th colspan="3"><a name='<?=$year?>'></a><big><?=$year?></big></th>
</tr>
<?
    }
    $y = $year;
    $c = $o->category;
?>
<tr>
  <th colspan="3"><a name='<?=$year.str_replace(" ","",$o->category)?>'></a><?=$o->category?> </th>
</tr>
<?
  }
?>
<tr class="bg<?=((($n++)&1)+1)?>">
<td>
  <img src="gfx/sceneorg/<?=$o->sotype?>.gif" alt="conehead!"/>
<?
//$o->sotype=="awardwinner"?" style='font-weight:bold;'":""
$typ = explode(",", $o->type);
foreach($typ as $p) {
  ?><a href="prodlist.php?type[]=<?=$p?>"><img src="gfx/types/<?=$types[$p]?>" border="0" title="<?=$p?>"></a><?
}	
?>
  <a href="prod.php?which=<?=$o->id?>"><?=$o->name?></a>
</td>
<td>
<?
$a = array();
if ($o->group1) $a[] = sprintf("<a href='groups.php?which=%d'>%s</a>",$o->group1,$o->groupn1);
if ($o->group2) $a[] = sprintf("<a href='groups.php?which=%d'>%s</a>",$o->group2,$o->groupn2);
if ($o->group3) $a[] = sprintf("<a href='groups.php?which=%d'>%s</a>",$o->group3,$o->groupn3);
echo implode(" :: ",$a);
?>
</td>
<td>
<?
$platforms = explode(",", $o->platform);
foreach($platforms as $p) {
  ?><a href="prodlist.php?platform[]=<?=$p?>"><img src="gfx/os/<?=$os[$p]?>" border="0" title="<?=$p?>"></a><?
}	
?>
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

<br />

<table><tr>
<td valign="top" align="center">
<table bgcolor="#000000" cellspacing="1" cellpadding="0">
 <tr>
  <td>
<table bgcolor="#000000" cellspacing="1" cellpadding="2" margin="5">
<tr>
  <th colspan="3" style="text-align:center">scene.org viewing tips</th>
</tr>
<?

$sql  = " SELECT *,prods.id as id, ".
" g1.name as groupn1,g1.acronym as groupacron1, ".
" g2.name as groupn2,g2.acronym as groupacron2, ".
" g3.name as groupn3,g3.acronym as groupacron3, ".
" prods.name as name, ".
" GROUP_CONCAT(platforms.name) as platform ".
" FROM sceneorgrecommended,prods ".
" JOIN prods_platforms JOIN platforms ".
" LEFT JOIN groups AS g1 ON prods.group1 = g1.id".
" LEFT JOIN groups AS g2 ON prods.group2 = g2.id".
" LEFT JOIN groups AS g3 ON prods.group3 = g3.id".
" WHERE sceneorgrecommended.prodid = prods.id ".
" AND sceneorgrecommended.type = 'viewingtip'".
" AND prods_platforms.prod=prods.id ".
" AND prods_platforms.platform=platforms.id ".
" GROUP BY sceneorgrecommended.id ".
" ORDER BY date_format(prods.date,'%Y') DESC";

flush();

$r = mysql_query($sql) or die(mysql_error());
$n = 0;
$y = date("Y")+1;
while($o = mysql_fetch_object($r)) {
  $year = date("Y",strtotime($o->date));
  if ($year > 2001) continue;
  if ($year < $y) {
    $y = $year;
?>
<tr>
  <th colspan="3"><a name='<?=$year?>'><?=$year?></th>
</tr>
<?
  }
?>
<tr class="bg<?=((($n++)&1)+1)?>">
<td>
<?
$typ = explode(",", $o->type);
foreach($typ as $p) {
  ?><a href="prodlist.php?type[]=<?=$p?>"><img src="gfx/types/<?=$types[$p]?>" border="0" title="<?=$p?>"></a><?
}	
?>
<a href="prod.php?which=<?=$o->id?>"><?=$o->name?></a></td>
<td>
<?
$a = array();
if ($o->group1) $a[] = sprintf("<a href='groups.php?which=%d'>%s</a>",$o->group1,$o->groupn1);
if ($o->group2) $a[] = sprintf("<a href='groups.php?which=%d'>%s</a>",$o->group2,$o->groupn2);
if ($o->group3) $a[] = sprintf("<a href='groups.php?which=%d'>%s</a>",$o->group3,$o->groupn3);
echo implode(" :: ",$a);
?>
</td>
<td>
<?
$platforms = explode(",", $o->platform);
foreach($platforms as $p) {
  ?><a href="prodlist.php?platform[]=<?=$p?>"><img src="gfx/os/<?=$os[$p]?>" border="0" title="<?=$p?>"></a><?
}	
?>
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
<br />


<? require("include/bottom.php"); ?>
