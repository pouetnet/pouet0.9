<?
require("include/top.php");

$linebypage=25;

$query="SELECT count(*) FROM oneliner";
$result=mysql_query($query);
$nbmsg=mysql_result($result,0);

$allpages = (int)ceil($nbmsg/(float)$linebypage);
if(!$page)
	$page=$allpages;

debuglog($nbmsg."/".$linebypage."=".($nbmsg/(float)$linebypage));

$query="SELECT oneliner.who,oneliner.message,users.avatar,users.nickname FROM oneliner LEFT JOIN users ON oneliner.who=users.id ORDER BY oneliner.quand ASC LIMIT ".(($page-1)*$linebypage).",".$linebypage;
debuglog($query);
$result=mysql_query($query);
while($tmp=mysql_fetch_array($result)) {
	$onelines[]=$tmp;
}
?>
<br />
<table bgcolor="#000000" cellspacing="1" cellpadding="0" border="0" width="75%">
 <tr>
  <td>
   <table bgcolor="#000000" cellspacing="1" cellpadding="2" border="0" width="100%">
    <tr>
     <td bgcolor="#224488" colspan="2">
      <table border="0" cellspacing="0" cellpadding="0"><tr><td>
       <img src="gfx/titles/talk.gif" width="16" height="16" border="0" title="talk"><br />
      </td><td>&nbsp;</td><td>
       <b>the so complete pouët.net oneliner</b><br />
      </td></tr></table>
     </td>
    </tr>
    <? for($i=0;$i<count($onelines);$i++): ?>
    <tr class="cite-<?=$onelines[$i]["who"]?>">
    <?
     if($i%2) {
       print("<td bgcolor=\"#557799\">");
     } else {
       print("<td bgcolor=\"#446688\">");
     }
    ?>
      <table cellspacing="0" cellpadding="0"><tr><td>
       <a href="user.php?who=<? (print($onelines[$i]["who"])); ?>"><img src="avatars/<? print($onelines[$i]["avatar"]); ?>" width="16" height="16" border="0" title="<? htmlspecialchars(print($onelines[$i]["nickname"])); ?>"></a><br />
      </td>
      <td>&nbsp;</td>
      <td>
       <? print(str_replace("\'", "'", htmlentities( stripslashes($onelines[$i]["message"]) ))); ?><br />
      </td></tr></table>
     </td>
    </tr>
    <? endfor; ?>

    <tr bgcolor="#224488">
     <td>
      <table width="100%" cellspacing="0" cellpadding="0" border="0">
       <tr>
       <? if($page>=2): ?>
        <td>
         <a href="oneliner.php?page=<?=($page-1)?>">
          <img src="gfx/flecheg.gif" border="0"><br />
         </a>
        </td>
        <td>&nbsp;</td>
        <td nowrap>
         <a href="oneliner.php?page=<?=($page-1)?>">
          <b>previous page</b><br />
         </a>
        </td>
       <? endif; ?>
        <form action="oneliner.php">
        <td width="50%" align="right">
        <select name="page">
        <? for($i=1;$i<=$allpages;$i++): ?>
        <? if($i==$page): ?>
        <option value="<? print($i); ?>" selected><? print($i); ?></option>
        <? else: ?>
        <option value="<? print($i); ?>"><? print($i); ?></option>
        <? endif; ?>
        <? endfor; ?>
        </select><br />
        </td>
        <td>&nbsp;</td>
        <td width="50%">
        <input type="image" src="gfx/submit.gif" border="0"><br />
        </td>
        </form>
       <? if($page<$allpages): ?>
        <td nowrap>
         <a href="oneliner.php?page=<?=($page+1)?>">
          <b>next page</b><br />
         </a>
        </td>
        <td>&nbsp;</td>
        <td>
         <a href="oneliner.php?page=<?=($page+1)?>">
          <img src="gfx/fleched.gif" border="0"><br />
         </a>
        </td>
       <? endif; ?>
       </tr>
      </table>
     </td>
    </tr>

   </table>
  </td>
 </tr>
</table>
<br />
<? require("include/bottom.php"); ?>
