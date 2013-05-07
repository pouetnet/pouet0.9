<?
require("include/top.php");

function goodfleche($wanted,$current) {
  if($wanted==$current) {
    $fleche="fleche1a";
  } else {
    $fleche="fleche1b";
  }
  return $fleche;
}

function htmlcleanonerow($inhtml){
  $inhtml= str_replace( "<", "&" . "lt;", $inhtml);
  $inhtml= str_replace( ">", "&" . "gt;", $inhtml);
  $inhtml= str_replace( "\"", "&" . "quot;", $inhtml);
  $inhtml= str_replace( "\n", " ", $inhtml);
  return $inhtml;
}

$topics_per_page=$user["bbsbbstopics"];

if(($page<=0)||(!$page)) {
  $page=1;
}

$query="SELECT count(0) FROM bbs_topics";
if ($_GET["categoryfilter"])
  $query.= sprintf(" where bbs_topics.category = %d",$_GET["categoryfilter"]);
$result=mysql_query($query);
$nb_topics=mysql_result($result,0);

$query="SELECT bbs_topics.id, bbs_topics.topic, bbs_topics.firstpost, bbs_topics.lastpost, bbs_topics.userfirstpost, bbs_topics.userlastpost, bbs_topics.count, bbs_topics.category, users1.nickname as nickname_1, users1.avatar as avatar_1, users2.nickname as nickname_2, users2.avatar as avatar_2 FROM bbs_topics LEFT JOIN users as users2 on users2.id=bbs_topics.userfirstpost LEFT JOIN users as users1 on users1.id=bbs_topics.userlastpost";
if ($_GET["categoryfilter"])
  $query.= sprintf(" where bbs_topics.category = %d",$_GET["categoryfilter"]);
switch($order) {
  case "userfirstpost": $query.=" ORDER BY nickname_2, avatar_2, bbs_topics.lastpost DESC"; break;
  case "firstpost": $query.=" ORDER BY firstpost DESC"; break;
  case "userlastpost": $query.=" ORDER BY nickname_1, avatar_1, bbs_topics.lastpost DESC"; break;
  case "lastpost": $query.=" ORDER BY bbs_topics.lastpost DESC"; break;
  case "count": $query.=" ORDER BY bbs_topics.count DESC"; break;
  case "topic": $query.=" ORDER BY bbs_topics.topic"; break;
  case "category": $query.=" ORDER BY bbs_topics.category"; break;
  default: $query.=" ORDER BY bbs_topics.lastpost DESC"; break;
}
  
$query.=" LIMIT ".(($page-1)*$topics_per_page).",".$topics_per_page;
$result=mysql_query($query);
while($tmp=mysql_fetch_assoc($result)) {
  $topics[]=$tmp;
}

$sortlink ="bbs.php?";
if ($_GET["categoryfilter"])
  $sortlink ="categoryfilter=".$_GET["categoryfilter"]."&amp;";
$sortlink.="order="; 

$pagelink = "bbs.php?order=".$order;
if ($_GET["categoryfilter"])
  $pagelink .= "&categoryfilter=".$_GET["categoryfilter"]."";

$row = 6;
if (canSeeBBSCategories()) {
  $row = 7;
}

?>
<br />
<table bgcolor="#000000" cellspacing="1" cellpadding="0">
 <tr>
  <td>
   <table bgcolor="#000000" cellspacing="1" cellpadding="2">
    <tr>
     <td bgcolor="#224488" colspan="<?=$row?>" align="center">
       <b>the oldskool pouët.net bbs</b><br />
         </td>
    </tr>
    <tr>
    <th bgcolor="#224488">
      <table><tr>
       <td>
        <a href="<? print($sortlink); ?>firstpost"><img src="gfx/<? print(goodfleche("firstpost",$order)); ?>.gif" width="13" height="12" border="0"></a><br />
       </td>
       <td>
        <a href="<? print($sortlink); ?>firstpost"><b>started</b></a>
       </td>
      </tr></table>
     </th>
     <th bgcolor="#224488">
      <table><tr>
       <td>
        <a href="<? print($sortlink); ?>userfirstpost"><img src="gfx/<? print(goodfleche("userfirstpost",$order)); ?>.gif" width="13" height="12" border="0"></a><br />
       </td>
       <td>
        <a href="<? print($sortlink); ?>userfirstpost"><b>by</b></a>
       </td>
      </tr></table>
     </th>
<?
if (canSeeBBSCategories()) {
?>
     <th bgcolor="#224488">
      <table><tr>
       <td>
        <a href="<? print($sortlink); ?>category"><img src="gfx/<? print(goodfleche("category",$order)); ?>.gif" width="13" height="12" border="0"></a><br />
       </td>
       <td>
        <a href="<? print($sortlink); ?>category"><b>category</b></a>
       </td>
      </tr></table>
     </th>
<?
}
?>     
     <th bgcolor="#224488">
      <table><tr>
       <td>
        <a href="<? print($sortlink); ?>topic"><img src="gfx/<? print(goodfleche("topic",$order)); ?>.gif" width="13" height="12" border="0"></a><br />
       </td>
       <td>
        <a href="<? print($sortlink); ?>topic"><b>bbs topic</b></a>
       </td>
      </tr></table>
     </th>
     <th bgcolor="#224488">
      <table><tr>
       <td>
        <a href="<? print($sortlink); ?>count"><img src="gfx/<? print(goodfleche("count",$order)); ?>.gif" width="13" height="12" border="0"></a><br />
       </td>
       <td>
        <a href="<? print($sortlink); ?>count"><b>replies</b></a>
       </td>
      </tr></table>
     </th>
     <th bgcolor="#224488">
      <table><tr>
       <td>
        <a href="<? print($sortlink); ?>lastpost"><img src="gfx/<? print(goodfleche("lastpost",$order)); ?>.gif" width="13" height="12" border="0"></a><br />
       </td>
       <td>
        <a href="<? print($sortlink); ?>lastpost"><b>last post</b></a>
       </td>
      </tr></table>
     </th>
     <th bgcolor="#224488">
      <table><tr>
       <td>
        <a href="<? print($sortlink); ?>userlastpost"><img src="gfx/<? print(goodfleche("userlastpost",$order)); ?>.gif" width="13" height="12" border="0"></a><br />
       </td>
       <td>
        <a href="<? print($sortlink); ?>userlastpost"><b>by</b></a>
       </td>
      </tr></table>
     </th>
    </tr>
    <? for($i=0;$i<count($topics);$i++): ?>
    <? $tdcolor=($i%2)?"#557799":"#446688"; ?>
    <tr class="cite-<?=$topics[$i]["userfirstpost"]?>">
     <td bgcolor="<?=$tdcolor?>">
      <?=substr($topics[$i]["firstpost"],0,10)?><br />
     </td>
     <td bgcolor="<?=$tdcolor?>">
      <table cellspacing="0" cellpadding="0">
       <tr>
        <td>
         <a href="user.php?who=<?=$topics[$i]["userfirstpost"]?>">
          <img src="avatars/<?=$topics[$i]["avatar_2"]?>" width="16" height="16" border="0" title="<?=$topics[$i]["nickname_2"]?>"><br />
         </a>
        </td>
        <td>
         <img src="gfx/z.gif" width="3" height="1"><br />
        </td>
        <td>
         <a href="user.php?who=<?=$topics[$i]["userfirstpost"]?>">
          <?=$topics[$i]["nickname_2"]?><br />
         </a>
        </td>
       </tr>
      </table>
     </td>
     <?
if (canSeeBBSCategories()) {
?>
     <td bgcolor="<?=$tdcolor?>">
      <?=$thread_categories[$topics[$i]["category"]]?>
<?
if (canEditBBSCategories()) {
?>
      [<a href="edit_topic_category.php?which=<?=$topics[$i]["id"]?>">edit</a>]
<?
}
?>     
     </td>
<?
}
?>     

     <td bgcolor="<?=$tdcolor?>">
      <a href="topic.php?which=<?=$topics[$i]["id"]?>"><b>
<? if (strlen($topics[$i]["topic"])>70) print(htmlcleanonerow(substr($topics[$i]["topic"],0,70)."...")); else print(htmlcleanonerow($topics[$i]["topic"])); ?></b></a><br />
     </td>
     <td bgcolor="<?=$tdcolor?>" align="right">
      <?=$topics[$i]["count"]?><br />
     </td>
     <td bgcolor="<?=$tdcolor?>">
      <?=$topics[$i]["lastpost"]?><br />
     </td>
     <td bgcolor="<?=$tdcolor?>">
      <table cellspacing="0" cellpadding="0">
       <tr>
        <td>
         <a href="user.php?who=<?=$topics[$i]["userlastpost"]?>">
          <img src="avatars/<?=$topics[$i]["avatar_1"]?>" width="16" height="16" border="0" title="<?=$topics[$i]["nickname_1"]?>"><br />
         </a>
        </td>
        <td>
         <img src="gfx/z.gif" width="3" height="1"><br />
        </td>
        <td>
         <a href="user.php?who=<?=$topics[$i]["userlastpost"]?>">
          <?=$topics[$i]["nickname_1"]?><br />
         </a>
        </td>
       </tr>
      </table>
     </td>
     
    </tr>
    <? endfor; ?>
    
    </tr>
	<tr bgcolor="#224488">
 	 <td colspan="<?=$row?>">
      <table width="100%" cellspacing="0" cellpadding="0" border="0">
       <tr>
       <? if($page>=2): ?>
        <td>
         <a href="<?=($pagelink)?>&page=<?=($page-1)?>">
          <img src="gfx/flecheg.gif" border="0"><br />
         </a>
        </td>
        <td>&nbsp;</td>
        <td nowrap>
         <a href="<?=($pagelink)?>&page=<?=($page-1)?>">
          <b>previous page</b><br />
         </a>
        </td>
       <? endif; ?>
        <form action="bbs.php">
        <td width="50%" align="right">
        <input type="hidden" name="order" value="<? print($order); ?>">
<?if($_GET["categoryfilter"]){?>        <input type="hidden" name="categoryfilter" value="<? print($_GET["categoryfilter"]); ?>"><?}?>
        <select name="page">
        <? for($i=1;($i-1)<=($nb_topics/$topics_per_page);$i++): ?>
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
       <? if(($page*$topics_per_page)<=$nb_topics): ?>
        <td nowrap>
         <a href="<?=($pagelink)?>&page=<?=($page+1)?>">
          <b>next page</b><br />
         </a>
        </td>
        <td>&nbsp;</td>
        <td>
         <a href="<?=($pagelink)?>&page=<?=($page+1)?>">
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

<table bgcolor="#000000" cellspacing="1" cellpadding="0" width="50%">
 <tr>
  <td>
   <table bgcolor="#000000" cellspacing="1" cellpadding="2">
    <tr>
     <th bgcolor="#224488" colspan="4">
      Disclaimer<br />
     </th>
    </tr>
    <tr>
     <td bgcolor="#446688" colspan="4" align="left">
      The oldskool pouët BBS is not the demoscene, please visit a <a href="http://www.demoparty.net/">demoparty</a>. This is merely a forum for sceners, with obviously too much free time, to release their frustrations and request salted communitary feedback, as opposed to polluting the prod comments section.<br />
      <br />
      New users: please take your vaccines before entering, don't expect instant praise, and try not to feel easily insulted.<br />
      Old users: please don't feed the trolls. Reoccuring annoying behavior will result in a heavily subjective ban. Just try not to piss off 90% of our active users with childish and/or idiotic behaviour and you should be safe.<br />
      <br />
      <a href="http://i.imgur.com/QBxVc.jpg">You have been warned.</a><br />
      <br />
      Also, <b>copycat threads are the equivalent of putting glow and ribbons in your demo.</b><br />
     </td>
    </tr>
   </table>
  </td>
 </tr>
</table>
<br />

<? if($_SESSION["SCENEID_ID"]): ?>
<form action="add.php" method="post">
<input type="hidden" name="type" value="topic">
<table bgcolor="#000000" cellspacing="1" cellpadding="0">
 <tr>
  <td>
   <table bgcolor="#000000" cellspacing="1" cellpadding="2">
    <tr>
     <th bgcolor="#224488" colspan="4">
      post a new topic<br />
     </th>
    </tr>
    <tr>
     <td bgcolor="#446688" colspan="4" align="center">
      <table>
       <tr>
        <td>
         topic:<br />
         <input type="text" name="topic" size="80" value="<?=$topic?>"><br />
<?
if (canSeeBBSCategories()) {
?>
         category:<br />
        <select name='category'>
        <?
        foreach ($thread_categories as $k=>$v)
          printf("<option value='%d'%s>%s</option>\n",$k,0==$k?' selected="selected"':"",htmlspecialchars($v));
        ?>
        </select> 
        <br />        
<?
}
?>            
         message:<br />
         <textarea cols="80" rows="10" name="message"><?=$message?></textarea><br />
         <div align="right"><a href="faq.php#BB Code"><b>BB Code</b></a> is allowed here</div>
        </td>
       </tr>
      </table>
     </td>
    </tr>
    <tr>
     <td bgcolor="#224488" colspan="4" align="right">
<script language="JavaScript" type="text/javascript">
<!--
  document.write('<input type="image" src="gfx/previewdxm.gif" onclick=\'return preview(this.form,"topic")\' border="0">');
//-->
</script>
      <input type="image" src="gfx/submit.gif" border="0"><br />
     </th>
    </tr>
   </table>
  </td>
 </tr>
</table>
</form>
<? else: ?>
<form action="login.php" method="post">
<table bgcolor="#000000" cellspacing="1" cellpadding="0">
 <tr>
  <td>
<table bgcolor="#000000" cellspacing="1" cellpadding="2">
 <tr><th>post a new topic</th></tr>
 <tr bgcolor="#446688">
  <td nowrap align="center">
   You need to be logged in to post a new topic :: <a href="account.php">register here</a><br />
   <input type="text" name="login" value="SceneID" size="15" maxlength="16" onfocus="this.value=''">
   <input type="password" name="password" value="password" size="15" onfocus="javascript:if(this.value=='password') this.value='';"><br />
  </td>
 </tr>
 <tr>
  <td bgcolor="#6688AA" align="right">
   <input type="image" src="gfx/submit.gif">
  </td>
 </tr>
</table>
  </td>
 </tr>
</table>
</form>
<? endif; ?>
<br />
<? require("include/bottom.php"); ?>
