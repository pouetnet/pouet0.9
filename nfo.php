<?
require("include/top.php");

$query ="SELECT prods.name,prods.group1,prods.group2,prods.group3,users.nickname,users.avatar ";
$query.="FROM prods,users ";
$query.="WHERE prods.id=".$which." AND users.id=prods.added";
$result = mysql_query($query);
$prod = mysql_fetch_array($result);

if ($prod["group1"]):
	$gquery="select name,acronym from groups where id='".$prod["group1"]."'";
	$gresult=mysql_query($gquery);
	while($gtmp = mysql_fetch_array($gresult)) {
	  $prod["groupname1"]=$gtmp["name"];
	  $prod["groupacron1"]=$gtmp["acronym"];
	 }
endif;
if ($prod["group2"]):
	$gquery="select name,acronym from groups where id='".$prod["group2"]."'";
	$gresult=mysql_query($gquery);
	while($gtmp = mysql_fetch_array($gresult)) {
	  $prod["groupname2"]=$gtmp["name"];
	  $prod["groupacron2"]=$gtmp["acronym"];
	 }
endif;
if ($prod["group3"]):
	$gquery="select name,acronym from groups where id='".$prod["group3"]."'";
	$gresult=mysql_query($gquery);
	while($gtmp = mysql_fetch_array($gresult)) {
	  $prod["groupname3"]=$gtmp["name"];
	  $prod["groupacron3"]=$gtmp["acronym"];
	 }
endif;

if (strlen($prod["groupname1"].$prod["groupname2"].$prod["groupname3"])>27):
	if (strlen($prod["groupname1"])>10 && $prod["groupacron1"]) $prod["groupname1"]=$prod["groupacron1"];
	if (strlen($prod["groupname2"])>10 && $prod["groupacron2"]) $prod["groupname2"]=$prod["groupacron2"];
	if (strlen($prod["groupname3"])>10 && $prod["groupacron3"]) $prod["groupname3"]=$prod["groupacron3"];
endif;

if(!$f) $f=1;

?>
<br>
<table><tr><td>
<table bgcolor="#000000" cellspacing="1" cellpadding="0" border="0" width="100%">
 <tr>
  <td>
   <table bgcolor="#000000" cellspacing="1" cellpadding="2" border="0" width="100%">
    <tr>
	   <td bgcolor="#224488">
      <b>
	    <font size="+1"><? print(stripslashes($prod["name"])); ?></font>
	    <? if($prod["groupname1"]): ?>
	    by
	    <a href="groups.php?which=<? print($prod["group1"]); ?>"><? print($prod["groupname1"]); ?></a>
	    <? endif; ?>
	    <? if($prod["groupname2"]): ?>
	    &amp;
	    <a href="groups.php?which=<? print($prod["group2"]); ?>"><? print($prod["groupname2"]); ?></a>
	    <? endif; ?>
   	    <? if($prod["groupname3"]): ?>
	    &amp;
	    <a href="groups.php?which=<? print($prod["group3"]); ?>"><? print($prod["groupname3"]); ?></a>
	    <? endif; ?>

      </b>
	   </td>
    </tr>
    <tr bgcolor="#446688">
     <td align="center">
      <table><tr><td>
	   <? if($f=='none'): ?>
	   <pre><?= htmlentities(file_get_contents('nfo/'.$which.'.nfo')); ?></pre>
	   <? else: ?>
	   <img src="ascii.php?nfo=<?=$which?>&amp;f=<?=$f?>"><br>
	   <? endif; ?>
      </td></tr></table>
     </td>
    </tr>
    <tr>
     <td bgcolor="#6688AA" align="center">
     <? $txt=array("dos 80*25","dos 80*50","rez's ascii","amiga medres","amiga hires");
     print("<b>[ "); ?>
     <a href="nfo.php?which=<?=$which?>&amp;f=none">html</a> 
     <? for($i=0;$i<=count($txt)-1;$i++): ?>
       | <a href="nfo.php?which=<?=$which?>&amp;f=<?=$i+1?>"><?=$txt[$i]?></a>
       <? endfor; 
      print(" ]</b>"); ?>
     </td>
    </tr>
    <tr>
     <td bgcolor="#224488" align="right">
<?    
if($SESSION_LEVEL=='administrator' || $SESSION_LEVEL=='moderator' || $SESSION_LEVEL=='gloperator') {
?>
<b>[ <a href="submitnfo.php?which=<?=$which?>">admin: upload new nfo</a> ]</b>
<b>[ <a href="nfo/<?=$which?>.nfo">admin: download nfo</a> ]</b>
<?
}
?>
       <b>[ <a href="prod.php?which=<? print($which); ?>">back to the prod</a> ]</b><br>
     </td>
    </tr>
   </table>
  </td>
 </tr>
</table>
</td></tr></table>
<br>
<? require("include/bottom.php"); ?>
