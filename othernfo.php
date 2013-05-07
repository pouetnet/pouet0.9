<?
require("include/top.php");

$query ="SELECT * FROM othernfos WHERE id=".$which;
$result = mysql_query($query);
$prod = mysql_fetch_array($result);

if ($prod["type"]=="group") $query="SELECT name FROM groups WHERE id=".$prod["refid"];
 else $query="SELECT name FROM bbses WHERE id=".$prod["refid"];
$result=mysql_query($query);
$tmp=mysql_fetch_array($result);
$prod["name"]=$tmp["name"];

if(!$f) $f=1;

?>
<br />
<table><tr><td>
<table bgcolor="#000000" cellspacing="1" cellpadding="0" border="0" width="100%">
 <tr>
  <td>
   <table bgcolor="#000000" cellspacing="1" cellpadding="2" border="0" width="100%">
    <tr>
	   <td bgcolor="#224488">
      <b>
	    <font size="+1"><? print(stripslashes($prod["name"])); ?></font>
      </b>
	   </td>
    </tr>
    <tr bgcolor="#446688">
     <td align="center">
      <table><tr><td>
	   <? if($f=='none'): ?>
	   <pre><? readfile('othernfo/'.$which.'.nfo'); ?></pre>
	   <? else: ?>
	   <img src="ascii.php?othernfo=<?=$which?>&amp;f=<?=$f?>"><br />
	   <? endif; ?>
      </td></tr></table>
     </td>
    </tr>
    <tr>
     <td bgcolor="#6688AA" align="center">
     <? $txt=array("dos 80*25","dos 80*50","rez's ascii","amiga medres","amiga hires");
     print("<b>[ "); ?>
     <a href="othernfo.php?which=<?=$which?>&amp;f=none">html</a> 
     <? for($i=0;$i<=count($txt)-1;$i++): ?>
       | <a href="othernfo.php?which=<?=$which?>&amp;f=<?=$i+1?>"><?=$txt[$i]?></a>
       <? endfor; 
      print(" ]</b>"); ?>
     </td>
    </tr>
    <tr>
     <td bgcolor="#224488" align="right">
     <? if ($prod["type"]=="group"): ?> <b>[ <a href="group.php?which=<? print($prod["refid"]); ?>">back to the group</a> ]</b><br />
      <? else: ?> <b>[ <a href="bbses.php?which=<? print($prod["refid"]); ?>">back to the bbs</a> ]</b><br />
     <? endif; ?>
     </td>
    </tr>
   </table>
  </td>
 </tr>
</table>
</td></tr></table>
<br />
<? require("include/bottom.php"); ?>
