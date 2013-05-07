<?
require("include/top.php");
require("include/libbb.php");

if($_SESSION["SESSION"]&&$_SESSION["SCENEID"])
{
//	$query = "SELECT topicposts FROM usersettings WHERE id='".$_SESSION["SCENEID_ID"]."'";
//	$result = mysql_query($query);
	$posts_per_page=$user["topicposts"];//mysql_result($result,0);
}
else
{
	$posts_per_page = 25;
}
if ($com) $posts_per_page=$com; else $com=$posts_per_page;

$which = (int)$which;

/*
if ($which == 6618)
  die("jesus christ people, okay, I GET IT - now stop acting like complete retards.");

if ($which == 7465) {
  header("Location: http://www.scene.org/demoscene.php");
  exit();
}  
*/
$query='SELECT count(0) FROM bbs_posts WHERE topic='.(int)$which;
$result=mysql_query($query);
$nb_posts=mysql_result($result,0);
/*
if ($which == 2735 && $nb_posts >= 32767)
  die("okay, the thread reached 0x8000 posts, which means it ran it's course. now go make a demo about it. (a demo, NOT a new thread.)");
*/
if(($page<=0)||(!$page)) {
  $page=ceil($nb_posts/$posts_per_page);
}

$query="SELECT topic,category,closed FROM bbs_topics WHERE id=".$which;
$result=mysql_query($query);
$topic = mysql_fetch_object($result);
$topictitle=htmlcleanonerow($topic->topic);

$query="SELECT bbs_posts.id,bbs_posts.post,bbs_posts.author,bbs_posts.added,users.nickname,users.avatar,users.level FROM bbs_posts,users WHERE bbs_posts.author=users.id AND bbs_posts.topic=".$which." ORDER BY bbs_posts.added ASC";
$query.=" LIMIT ".(($page-1)*$posts_per_page).",".$posts_per_page;
$result=mysql_query($query);
while($tmp=mysql_fetch_assoc($result)) {
  $replies[]=$tmp;
}
?>
<br />
<table bgcolor="#000000" cellspacing="1" cellpadding="0" width="75%" style="max-width:75%">
 <tr>
  <td>
   <table bgcolor="#000000" cellspacing="1" cellpadding="2" width="100%" class='bbstable'>
    <tr>
     <th><center>
	<?=$topictitle?>
	</center></th>
    </tr>
    <tr bgcolor="#446688"><td align="center"><b>category:</b> <?=$thread_categories[$topic->category]?>
<?
if (canEditBBSCategories()) {
?>
      [<a href="edit_topic_category.php?which=<?=$which?>">edit</a>]
<?
}
?>     
    </td>
    </tr>
    <tr bgcolor="#224488"><td>
    <form action="topic.php">
     <input type="hidden" name="which" value="<?=$which?>">
      <table width="100%" cellspacing="0" cellpadding="0" border="0">
       <tr>
       <? if($page>=2): ?>
        <td>
         <a href="topic.php?which=<?=$which?>&page=<?=($page-1)?>">
          <img src="gfx/flecheg.gif" border="0"><br />
         </a>
        </td>
        <td>&nbsp;</td>
        <td nowrap>
         <a href="topic.php?which=<?=$which?>&page=<?=($page-1)?>">
          <b>previous page</b><br />
         </a>
        </td>
       <? endif; ?>
        <td width="50%" align="right">
        <font color="#9FCFFF"><b><? print(" go to page "); ?></b></font>
	<select name="page">
        <? for($i=1;($i-1)<=(($nb_posts-1)/$posts_per_page);$i++): ?>
        <? if($i==$page): ?>
        <option value="<? print($i); ?>" selected><? print($i); ?></option>
        <? else: ?>
        <option value="<? print($i); ?>"><? print($i); ?></option>
        <? endif; ?>
        <? endfor; ?>
        </select>
       <font color="#9FCFFF"><b><? printf("of ".($i-1)); ?></b></font>
        &nbsp</td>
	<td width="50%">
	<input type="image" src="gfx/submit.gif" border="0"><br />
        </td>
       <? if(($page*$posts_per_page)<=($nb_posts-1)): ?>
        <td nowrap>
         <a href="topic.php?which=<?=$which?>&page=<?=($page+1)?>">
          <b>next page</b><br />
         </a>
        </td>
        <td>&nbsp;</td>
        <td>
         <a href="topic.php?which=<?=$which?>&page=<?=($page+1)?>">
          <img src="gfx/fleched.gif" border="0"><br />
         </a>
        </td>
       <? endif; ?>
       </tr>
      </table>
      </form>
         </td>
        </tr>


    <? for($i=0;$i<count($replies);$i++): ?>
    <tr class="cite-<?=$replies[$i]["author"]?>"><td bgcolor="#446688" id="c<?=$replies[$i]["id"]?>">
    <? if ($i == count($replies) - 1) printf("<a name='lastpost'></a>"); ?>
    <? 
    if (
         ( ($replies[$i]["level"]=='fakeuser') && $user["topichidefakeuser"] ) ||
         ( ($replies[$i]["level"]=='pr0nstahr') && $user["topichidepornstar"] ) ||
         ( ($replies[$i]["level"]=='annoyinguser') && $user["topichideannoyinguser"] )
       )
       {
        print("i'm a ".$replies[$i]["level"]." so i got shamelessly censored, if you're registered you can uncensor my level's posts <a href=\"account2.php\">here</a>");
       }
       else {
         print(parse_message($replies[$i]["post"]));
       }
    ?>
    </td></tr>
    <tr class="cite-<?=$replies[$i]["author"]?>">
     <td bgcolor="#6688AA" align="right">
      <table cellspacing="0" cellpadding="0">
       <tr>
        <td>added on the <a href="#c<?=$replies[$i]["id"]?>"><? print(($replies[$i]["added"])); ?></a> by <a href="user.php?who=<? print($replies[$i]["author"]); ?>"><? print($replies[$i]["nickname"]); ?></a></td>
        <td>&nbsp;<br /></td>
        <td><a href="user.php?who=<? print($replies[$i]["author"]); ?>"><img<?=($_GET["youcantbeserious"]?" width='100' height='100'":" width='16' height='16'")?> src="avatars/<? print($replies[$i]["avatar"]); ?>" border="0" alt="<? print($replies[$i]["nickname"]); ?>" title="<? print($replies[$i]["nickname"]); ?>"></a></td>
       </tr>
      </table>
     </td>
    </tr>
    <? endfor; ?>
	<tr bgcolor="#224488">
 	 <td>
    <form action="topic.php">
     <input type="hidden" name="which" value="<?=$which?>">
     <input type="hidden" name="com" value="<?=$com?>">
      <table width="100%" cellspacing="0" cellpadding="0" border="0">
       <tr>
       <? if($page>=2): ?>
        <td>
         <a href="topic.php?which=<?=$which?>&page=<?=($page-1)?>">
          <img src="gfx/flecheg.gif" border="0"><br />
         </a>
        </td>
        <td>&nbsp;</td>
        <td nowrap>
         <a href="topic.php?which=<?=$which?>&page=<?=($page-1)?>">
          <b>previous page</b><br />
         </a>
        </td>
       <? endif; ?>
		<input type="hidden" name="which" value="<?=$which?>">
		<input type="hidden" name="com" value="<?=$com?>">
        <td width="50%" align="right">
        <font color="#9FCFFF"><b><? printf("go to page "); ?></b></font>
        <select name="page">
        <? for($i=1;($i-1)<=(($nb_posts-1)/$posts_per_page);$i++): ?>
        <? if($i==$page): ?>
        <option value="<? print($i); ?>" selected><? print($i); ?></option>
        <? else: ?>
        <option value="<? print($i); ?>"><? print($i); ?></option>
        <? endif; ?>
        <? endfor; ?>
        </select>
       <font color="#9FCFFF"><b><? printf("of ".($i-1)); ?></b></font>
        &nbsp</td>
        <td width="50%">
        <input type="image" src="gfx/submit.gif" border="0"><br />
        </td>
       <? if(($page*$posts_per_page)<=($nb_posts-1)): ?>
        <td nowrap>
         <a href="topic.php?which=<?=$which?>&page=<?=($page+1)?>">
          <b>next page</b><br />
         </a>
        </td>
        <td>&nbsp;</td>
        <td>
         <a href="topic.php?which=<?=$which?>&page=<?=($page+1)?>">
          <img src="gfx/fleched.gif" border="0"><br />
         </a>
        </td>
       <? endif; ?>
       </tr>
      </table>
      </form>
	 </td>
	</tr>
   </table>
  </td>
 </tr>
</table>

<br />

<?
if($_SESSION["SCENEID_ID"])
{

if ($which==1024) 
  $message = "please always use [url=http://en.wikipedia.org/wiki/BBCode]bbcode[/url] in this thread when refering to broken things.";
  
if ($topic->closed)
{
?>
<table bgcolor="#000000" cellspacing="1" cellpadding="2" style="border:1px solid black" width="50%">
 <tr>
  <th>thread closed</th>
 </tr>
 <tr>
 <td class='bg1'>
  this thread now officially wants YOU to go make a demo about it instead. please comply.
 </td>
 </tr>
</table>
<?
}
else
{
?>
<form action="add.php" method="post">
<input type="hidden" name="which" value="<?=$which?>">
<input type="hidden" name="type" value="post">
<table bgcolor="#000000" cellspacing="1" cellpadding="0">
 <tr>
  <td>
   <table bgcolor="#000000" cellspacing="1" cellpadding="2">
    <tr>
     <th bgcolor="#224488">
      post a new reply<br />
     </th>
    </tr>
    <tr>
     <td bgcolor="#446688" align="center">
      <table>
       <tr>
        <td>
         message:<br />
         <textarea cols="80" rows="10" name="message"><?=htmlspecialchars($message)?></textarea><br />
         <div align="right"><a href="faq.php#BB Code"><b>BB Code</b></a> is allowed here</div>
        </td>
       </tr>
      </table>
     </td>
    </tr>
    <tr>
     <td bgcolor="#224488" align="right">
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
<?
}
} else {
?>
<form action="login.php" method="post">
<table bgcolor="#000000" cellspacing="1" cellpadding="0">
 <tr>
  <td>
<table bgcolor="#000000" cellspacing="1" cellpadding="2">
 <tr><th>post a new reply</th></tr>
 <tr bgcolor="#446688">
  <td nowrap align="center">
   You need to be logged in to post a new reply :: <a href="account.php">register here</a><br />
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
<? 
}
?><br />

<? require("include/bottom.php"); ?>
