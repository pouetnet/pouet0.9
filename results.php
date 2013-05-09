<?
require("include/top.php");

$query ="SELECT parties.name FROM parties where parties.id=".$which." LIMIT 1";
$result=mysql_query($query);
$party = mysql_fetch_array($result);

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
	    <font size="+1"><? print(stripslashes($party["name"])." ".$when." results"); ?></font>
      </b>
	   </td>
    </tr>
    <tr bgcolor="#446688">
     <td align="center">
      <table><tr><td>
	   <? if($f=='none'): ?>
	   <pre><? readfile('results/'.$which.'_'.$when.'.txt'); ?></pre>
	   <? else: ?>
	   <img src="ascii.php?results=<?=$which?>&amp;when=<?=$when?>&amp;f=<?=$f?>"><br>
	   <? endif; ?>
      </td></tr></table>
     </td>
    </tr>
    <tr>
     <td bgcolor="#6688AA" align="center">
     <? $txt=array("dos 80*25","dos 80*50","rez's ascii","amiga medres","amiga hires");
     print("<b>[ "); ?>
     <a href="results.php?which=<?=$which?>&amp;when=<?=$when?>&amp;f=none">html</a>
     <? for($i=0;$i<=count($txt)-1;$i++): ?>
       | <a href="results.php?which=<?=$which?>&amp;when=<?=$when?>&amp;f=<?=$i+1?>"><?=$txt[$i]?></a>
       <? endfor;
      print(" ]</b>"); ?>
     </td>
    </tr>
    <tr>
     <td bgcolor="#224488" align="right">
<?
if($SESSION_LEVEL=='administrator' || $SESSION_LEVEL=='moderator' || $SESSION_LEVEL=='gloperator') {
?>
<b>[ <a href="submitpartyresults.php?which=<?=$which?>&amp;when=<?=$when?>">admin: upload new res</a> ]</b>
<b>[ <a href="results/<?=$which?>_<?=$when?>.txt">admin: download res</a> ]</b>
<?
}
?>
       <b>[ <a href="party.php?which=<? print($which); ?>&amp;when=<? print($when); ?>">back to the party releases</a> ]</b><br>
     </td>
    </tr>
   </table>
  </td>
 </tr>
</table>
</td></tr></table>
<br>
<? require("include/bottom.php"); ?>
