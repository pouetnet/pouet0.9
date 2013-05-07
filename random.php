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
	  <th colspan="3" style="text-align:center">hidden gem</th>
	</tr>
<?

$sql  = " SELECT *,prods.id as id, ".
" g1.name as groupn1,g1.acronym as groupacron1, ".
" g2.name as groupn2,g2.acronym as groupacron2, ".
" g3.name as groupn3,g3.acronym as groupacron3, ".
" prods.name as name, ".
" platforms.name as platform ".
" FROM prods ".
" JOIN prods_platforms JOIN platforms ".
" LEFT JOIN groups AS g1 ON prods.group1 = g1.id".
" LEFT JOIN groups AS g2 ON prods.group2 = g2.id".
" LEFT JOIN groups AS g3 ON prods.group3 = g3.id".
" WHERE prods_platforms.prod=prods.id ".
" AND prods_platforms.platform=platforms.id ".
" AND prods.voteavg > 0.99 ".
" ORDER BY RAND() LIMIT 1";

flush();

$r = mysql_query($sql) or die(mysql_error());
$n = 0;
$y = date("Y")+1;
$c = "";
while($o = mysql_fetch_object($r)) {
  $year = date("Y",strtotime($o->date));
  if ($year < $y || $c != $o->category) {
    $y = $year;
    $c = $o->category;
  }
?>
<tr class="bg<?=((($n++)&1)+1)?>">
<td>
  <?
//$o->sotype=="awardwinner"?" style='font-weight:bold;'":""
$typ = explode(",", $o->type);
foreach($typ as $p) {
  ?><a href="prodlist.php?type[]=<?=$p?>"><img src="gfx/types/<?=$types[$p]?>" border="0" title="<?=$p?>" alt="<?=$p?>"></a><?
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

	<tr>
	  <th colspan="3" style="text-align:center">unvoted</th>
	</tr>
<?

$sql  = " SELECT *,prods.id as id, ".
" g1.name as groupn1,g1.acronym as groupacron1, ".
" g2.name as groupn2,g2.acronym as groupacron2, ".
" g3.name as groupn3,g3.acronym as groupacron3, ".
" prods.name as name, ".
" platforms.name as platform ".
" FROM prods ".
" JOIN prods_platforms JOIN platforms ".
" LEFT JOIN groups AS g1 ON prods.group1 = g1.id".
" LEFT JOIN groups AS g2 ON prods.group2 = g2.id".
" LEFT JOIN groups AS g3 ON prods.group3 = g3.id".
" WHERE prods_platforms.prod=prods.id ".
" AND prods.voteup=0 ".
" AND prods.votepig=0 ".
" AND prods.votedown=0 ".
" AND prods_platforms.platform=platforms.id ".
" ORDER BY RAND() LIMIT 1";

flush();

$r = mysql_query($sql) or die(mysql_error());
$n = 0;
$y = date("Y")+1;
$c = "";
while($o = mysql_fetch_object($r)) {
  $year = date("Y",strtotime($o->date));
  if ($year < $y || $c != $o->category) {
    $y = $year;
    $c = $o->category;
  }
?>
<tr class="bg<?=((($n++)&1)+1)?>">
<td>
  <?
//$o->sotype=="awardwinner"?" style='font-weight:bold;'":""
$typ = explode(",", $o->type);
foreach($typ as $p) {
  ?><a href="prodlist.php?type[]=<?=$p?>"><img src="gfx/types/<?=$types[$p]?>" border="0" title="<?=$p?>" alt="<?=$p?>"></a><?
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


$notypes = array("32b", "64b", "128b", "512b", "1k", "8k", "16k", "32k", "80k", "96k", "100k", "128k", "256k", "artpack", "fastdemo", "report", "votedisk");
foreach ($types as $key => $value) {
	
	if (!in_array($key, $notypes)) {
?>
	<tr>
	  <th colspan="3" style="text-align:center"><? print($key); ?></th>
	</tr>

<?
$sql  = " SELECT *,prods.id as id, ".
" g1.name as groupn1,g1.acronym as groupacron1, ".
" g2.name as groupn2,g2.acronym as groupacron2, ".
" g3.name as groupn3,g3.acronym as groupacron3, ".
" prods.name as name, ".
" platforms.name as platform ".
" FROM prods ".
" JOIN prods_platforms JOIN platforms ".
" LEFT JOIN groups AS g1 ON prods.group1 = g1.id".
" LEFT JOIN groups AS g2 ON prods.group2 = g2.id".
" LEFT JOIN groups AS g3 ON prods.group3 = g3.id".
" WHERE prods_platforms.prod=prods.id ".
" AND prods.type like '".$key."' ".
" AND prods_platforms.platform=platforms.id ".
" ORDER BY RAND() LIMIT 1";

flush();

$r = mysql_query($sql) or die(mysql_error());
$n = 0;
$y = date("Y")+1;
$c = "";
while($o = mysql_fetch_object($r)) {
  $year = date("Y",strtotime($o->date));
  if ($year < $y || $c != $o->category) {
    $y = $year;
    $c = $o->category;
  }
?>
<tr class="bg<?=((($n++)&1)+1)?>">
<td>
  <?
//$o->sotype=="awardwinner"?" style='font-weight:bold;'":""
$typ = explode(",", $o->type);
foreach($typ as $p) {
  ?><a href="prodlist.php?type[]=<?=$p?>"><img src="gfx/types/<?=$types[$p]?>" border="0" title="<?=$p?>" alt="<?=$p?>"></a><?
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
}
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
