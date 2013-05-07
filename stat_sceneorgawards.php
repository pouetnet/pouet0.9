<?

require("include/top.php");

$sqlz["scene.org nominations by group"] =
  "SELECT count(*) as c, groups.name as text from sceneorgrecommended ".
  " left join prods on prods.id=sceneorgrecommended.prodid ".
  " left join groups on prods.group1=groups.id ".
  " where sceneorgrecommended.type='awardwinner' or sceneorgrecommended.type='awardnominee' group by prods.group1 order by c desc";

$sqlz["scene.org awards by group"] =
  "SELECT count(*) as c, groups.name as text from sceneorgrecommended ".
  " left join prods on prods.id=sceneorgrecommended.prodid ".
  " left join groups on prods.group1=groups.id ".
  " where sceneorgrecommended.type='awardwinner' group by prods.group1 order by c desc";

$sqlz["scene.org nominations by party"] =
  "SELECT count(*) as c, parties.name as text from sceneorgrecommended ".
  " left join prods on prods.id=sceneorgrecommended.prodid ".
  " left join parties on prods.party=parties.id ".
  " where sceneorgrecommended.type='awardwinner' or sceneorgrecommended.type='awardnominee' group by prods.party order by c desc";

foreach ($sqlz as $desc=>$sql) {
  $r = mysql_query($sql) or die(mysql_error());
  ?>
  
  <br />
  <table width="30%">
  <tr><td>
  <table cellspacing="1" cellpadding="2" class="box">
   <tr><th colspan="2"><center><?=$desc?></center></th></tr>
  <?
  $n = 1;
  while ($o = mysql_fetch_object($r)) {
  ?>
   <tr bgcolor="#557799">
    <td><?=$o->text?></td>
    <td><?=$o->c?></td>
   </tr>
  <? } ?>
  </table>
  </td>
  </tr>
  </table>
  <?
  }
?>


<? require("include/bottom.php"); ?>
