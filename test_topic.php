<?
require("include/top.php");
require("include/libbb.php");

if($_SESSION["SESSION"]&&$_SESSION["SCENEID"])
{
	$query = "SELECT topicposts FROM users WHERE id='".$_SESSION["SCENEID_ID"]."'";
	$result = mysql_query($query);
	$posts_per_page=mysql_result($result,0);
}
else
{
	$posts_per_page = 25;
}
if ($com) $posts_per_page=$com; else $com=$posts_per_page;

$query='SELECT count(0) FROM bbs_posts WHERE topic='.$which;
$result=mysql_query($query);
$nb_posts=mysql_result($result,0);

$pages=ceil($nb_posts/$posts_per_page);

if(($page<=0)||(!$page)||($page>$pages)) {
  $page=$pages;
}


$query="SELECT topic FROM bbs_topics WHERE id=".$which;
$result=mysql_query($query);
$topictitle=htmlcleanonerow(mysql_result($result,0));

$query="SELECT bbs_posts.post,bbs_posts.author,bbs_posts.added,users.nickname,users.avatar,users.level FROM bbs_posts,users WHERE bbs_posts.author=users.id AND bbs_posts.topic=".$which." ORDER BY bbs_posts.added ASC";
$query.=" LIMIT ".(($page-1)*$posts_per_page).",".$posts_per_page;
$result=mysql_query($query);
while($tmp=mysql_fetch_assoc($result)) {
  $replies[]=$tmp;
}



//	place page & topic related infos into a global object so it can be used in displayPageLinks()
$GLOBALS['pageAndTopicInfos'] = array( 'page'=>$page, 'pages'=>$pages, 'which'=>$which, 'com'=>$com );


/*
 *	displayPageLinks
 *	add the TR with the links to the pages ( first/previous/next/last/dropdown )
 *
 *	added on 2007/04/14 by p01
 */
function displayPageLinks()
{
	//	make a local copy of the 'pageAndTopicInfos'
	foreach( $GLOBALS['pageAndTopicInfos'] as $key=>$val )
		$$key = $val;
?>
				<tr bgcolor="#224488">
					<td>
						<form action="">
							<input type="hidden" name="which" value="<?=$which?>">
							<input type="hidden" name="com" value="<?=$com?>">
							<table width="100%" cellspacing="0" cellpadding="0" border="0">
								<tr>
									<td width="20%"><br/>
<?
	if( $page>1 )
	{
?>
<?
		if( $page>1 && $pages>0 )
		{
			?><a href="?which=<?=$which?>&page=1"><img src="gfx/flecheg.gif" border="0"> <b>first page</b></a><br/><?
		}
		if( $page>1 )
		{
			?><a href="?which=<?=$which?>&page=<?=($page-1)?>"><img src="gfx/flecheg.gif" border="0"> <b>previous page</b></a><br/><?
		}
	}
?>
									<br/></td>
									<td width="40%" align="right" nowrap><font color="#9FCFFF"><b><? printf("go to page "); ?></b></font>
										<select name="page">
<?
	for( $i=1; $i<=$pages;$i++)
	{
?>
											<option value="<?=$i?>"<? if( $i==$page )echo ' selected'; ?>><?=$i?></option>
<?
	}
?>
										</select>
										<font color="#9FCFFF"><b><? printf("of ".($i-1)); ?></b></font>&nbsp</td><td width="20%" align="left"><input type="image" src="gfx/submit.gif" border="0"><br />
									</td>
									<td width="20%" align="right"><br/>
<?
	if( $page<$pages )
	{
			?><a href="?which=<?=$which?>"><b>last page <img src="gfx/fleched.gif" border="0"></b></a><br/><a href="?which=<?=$which?>&page=<?=($page+1)?>"><b>next page <img src="gfx/fleched.gif" border="0"></b></a><br/><?
	}
?><br/>
									</td>
								</tr>
							</table>
						</form>
					</td>
				</tr>
<?
}



?>
<br />
<table bgcolor="#000000" cellspacing="1" cellpadding="0" width="75%">
	<tr>
		<td>
			<table bgcolor="#000000" cellspacing="1" cellpadding="2" width="100%">
				<tr>
					<th><center><?=$topictitle?></center></th>
			    </tr>
			    <tr bgcolor="#446688"><td>&nbsp</td>
			    </tr>
<?

	displayPageLinks();

	//	display replies
	for( $i=0; $i<count($replies); $i++ )
	{
?>
			    <tr class="cite-<?=$replies[$i]["author"]?>"><td bgcolor="#446688">
    <?
	if( 	( ($replies[$i]["level"]=='fakeuser')		&& $user["topichidefakeuser"] )
		||	( ($replies[$i]["level"]=='pr0nstahr')		&& $user["topichidepornstar"] )
		||	( ($replies[$i]["level"]=='annoyinguser')	&& $user["topichideannoyinguser"] ) )
	{
			    print("i'm a ".$replies[$i]["level"]." so i got shamelessly censored, if you're registered you can uncensor my level's posts <a href=\"account2.php\">here</a>");
	}
    else
    {
    	print(parse_message($replies[$i]["post"]));
    }
?>
			    	</td>
			    </tr>
			    <tr class="cite-<?=$replies[$i]["author"]?>">
			    	<td bgcolor="#6688AA" align="right">
			    		<table cellspacing="0" cellpadding="0">
							<tr>
								<td>added on the <? print(substr($replies[$i]["added"],0,10)); ?> by <a href="user.php?who=<? print($replies[$i]["author"]); ?>"><? print($replies[$i]["nickname"]); ?></a></td>
								<td>&nbsp;<br /></td>
								<td><a href="user.php?who=<? print($replies[$i]["author"]); ?>"><img src="avatars/<? print($replies[$i]["avatar"]); ?>" width="16" height="16" border="0" alt="<? print($replies[$i]["nickname"]); ?>" title="<? print($replies[$i]["nickname"]); ?>"></a></td>
							</tr>
						</table>
					</td>
				</tr>
<?
	}

	displayPageLinks();

?>
			</table>
		</td>
	</tr>
</table>

<br />

<? if(session_is_registered("SESSION")): ?>
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
         <textarea cols="80" rows="10" name="message"><?=$message?></textarea><br />
         <div align="right"><a href="faq.php#BB Code"><b>BB Code</b></a> is allowed here</div>
        </td>
       </tr>
      </table>
     </td>
    </tr>
    <tr>
     <td bgcolor="#224488" align="right">
<?
  //if (($SESSION_LEVEL=='administrator' || $SESSION_LEVEL=='moderator' || $SESSION_LEVEL=='gloperator')) {
  if (1) {
?>
<script language="JavaScript" type="text/javascript">
<!--
  document.write('<input type="image" src="gfx/previewdxm.gif" onclick=\'return preview(this.form,"topic")\' border="0">');
//-->
</script>
<?
  }
?>
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
 <tr><th>post a new reply</th></tr>
 <tr bgcolor="#446688">
  <td nowrap align="center">
   You need to be logged in to post a new reply :: <a href="account.php">register here</a><br />
   <input type="text" name="login" value="SceneID" size="15" maxlength="16" onfocus="this.value=''">
   <input type="password" name="password" value="password" size="15" onfocus="this.value=''"><br />
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
<? endif; ?><br />

<? require("include/bottom.php"); ?>
