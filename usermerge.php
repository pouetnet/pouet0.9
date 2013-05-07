<?
require("include/top.php");

if ($SESSION_LEVEL=='administrator' || $SESSION_LEVEL=='moderator'):

if ($id1 && $id2 && $action=='merge')
{
	$query="UPDATE logos set author1=".$id1." where author1=".$id2;
	$result=mysql_query($query);
	
	$query="UPDATE logos set author2=".$id1." where author2=".$id2;
	$result=mysql_query($query);
	
	$query="UPDATE prods set added=".$id1." where added=".$id2;
	$result=mysql_query($query);
	
	$query="UPDATE groups set added=".$id1." where added=".$id2;
	$result=mysql_query($query);
	
	$query="UPDATE parties set added=".$id1." where added=".$id2;
	$result=mysql_query($query);
	
	$query="UPDATE screenshots set user=".$id1." where user=".$id2;
	$result=mysql_query($query);
	
	$query="UPDATE nfos set user=".$id1." where user=".$id2;
	$result=mysql_query($query);
	
	$query="UPDATE comments set who=".$id1." where who=".$id2;
	$result=mysql_query($query);

	$query="UPDATE logos_votes set user=".$id1." where user=".$id2;
	$result=mysql_query($query);
	
	$query="UPDATE oneliner set who=".$id1." where who=".$id2;
	$result=mysql_query($query);
	
	$query="UPDATE bbs_posts set author=".$id1." where author=".$id2;
	$result=mysql_query($query);
	
	$query="UPDATE bbs_topics set userlastpost=".$id1." where userlastpost=".$id2;
	$result=mysql_query($query);
	
	$query="UPDATE bbs_topics set userfirstpost=".$id1." where userfirstpost=".$id2;
	$result=mysql_query($query);

	$query="UPDATE lists set adder=".$id1." where adder=".$id2;
	$result=mysql_query($query);
	
	$query="UPDATE lists set upkeeper=".$id1." where upkeeper=".$id2;
	$result=mysql_query($query);

	$query="UPDATE bbses set adder=".$id1." where adder=".$id2;
	$result=mysql_query($query);

	$query="UPDATE bbs_ads set adder=".$id1." where adder=".$id2;
	$result=mysql_query($query);
	
	$query = "DELETE FROM users WHERE id=".$id2." LIMIT 1";
	mysql_query($query);
	
	print("<br />user $id1 got all $id2 glops<br />$id2 got wasted<br />\n");
  logGloperatorAction("user_merge",$id1);
}

?>

<p>1ST NUMBER ACCOUNT WILL GET ALL GLOPS</p>
<p>2ND NUMBER ACCOUNT WILL LOOSE ALL AND GET DELETED</p>
<p>THIS IS THE ONE AND ONLY WARNING!!!</p>
<p>DOUBLE CHECK THE NUMBER!!!</p>

<form action="<?=basename($SCRIPT_FILENAME)?>" method="post">
user ID1
<input type="text" name="id1">
user ID2
<input type="text" name="id2">
<input type="submit" name="action" value="merge">
</form>

<? else: ?>

<p>dude, that's a dangerous page...</p>

<form action="login.php" method="post">
<table cellspacing="1" cellpadding="2" class="box">
 <tr bgcolor="#446688">
  <td nowrap align="center">
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
</form>

<? endif; ?>
<br />

<? require("include/bottom.php"); ?>
