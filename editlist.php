<?
require("include/top.php");

if ($which && ($SESSION_LEVEL=='administrator' || $SESSION_LEVEL=='moderator' || $SESSION_LEVEL=='gloperator')):

// check the submitted data
unset($submitok);
if($name)
{
  // check user account
  if(!isset($_SESSION['SESSION']))
	$errormessage[]="you need to be logged in first.";
  if(($SESSION_LEVEL!='administrator')&&($SESSION_LEVEL!='moderator')&&($SESSION_LEVEL!='gloperator'))
	  $errormessage[]="you need to be a lobster god to edit a bbs.";
  // check prod id
  $result=mysql_query("SELECT count(0) FROM lists WHERE id=$which");
  if(mysql_result($result,0)!=1)
    $errormessage[]="I can't find the list you are trying to modify";

  // if everything is ok
  if(!$errormessage)
    $submitok=true;
}

// insert the submitted bbs
if($submitok){
  $query = "UPDATE lists SET ";
  $query.= "lists.name='".$name."', ";
  $query.= "lists.desc='".$desc."', ";
  $query.= "upkeeper='".$upkeeper."' ";
  $query.= "WHERE id=$which LIMIT 1";
 //print($query);
  mysql_query($query);
  logGloperatorAction("list_edit",$which);
}

// get data to build the page
$result = mysql_query("SELECT * FROM lists WHERE id=$which");
$lists = mysql_fetch_assoc($result);
?>

<br />

<form action="editlist.php" method="get">
edit another list:<br />
id:<input type="text" name="which" value="<?=$which?>" /><br />
<input type="image" src="gfx/submit.gif" style="border: 0px" />
</form>

<br />

<form action="editlist.php" method="post">
<input type="hidden" name="which" value="<?=$which?>">
<table bgcolor="#000000" cellspacing="1" cellpadding="0"><tr><td>
<table bgcolor="#000000" cellspacing="1" cellpadding="2">
<? if($submitok): ?>
 <tr><th bgcolor="#224488">this list has been modified</th></tr>
 <tr>
  <td bgcolor="#446688" align="center">
   feel free to edit it again<br />
   <a href="lists.php?which=<? print($which); ?>">see what you've done</a><br />
  </td>
 </tr>
<? endif; ?>
<? if($errormessage): ?>
 <tr><th bgcolor="#224488">errors found</th></tr>
 <? for($i=0;$i<count($errormessage);$i++): ?>
  <? if($i%2): ?>
   <tr><td bgcolor="#557799">&nbsp;<b>- <font color="#FF8888"><? print($errormessage[$i]); ?></font></b></td></tr>
  <? else: ?>
   <tr><td bgcolor="#446688">&nbsp;<b>- <font color="#FF8888"><? print($errormessage[$i]); ?></font></b></td></tr>
  <? endif; ?>
 <? endfor; ?>
<? endif; ?>
 <tr>
  <th bgcolor="#224488">list information</th>
 </tr>
 <tr>
  <td bgcolor="#446688">
   <table>
	<tr>
	 <td>name:</td>
	 <td><input type="text" name="name" value="<? print($lists['name']); ?>"><br ></td>
	</tr>
	<tr>
	 <td>desc:</td>
	 <td><input type="text" name="desc" value="<? print($lists['desc']); ?>"><br ></td>
	</tr>
	<tr>
	 <td>upkeeper:</td>
	 <td><input type="text" name="upkeeper" value="<? print($lists['upkeeper']); ?>"><br ></td>
	</tr>
	</table>
  </td>
 </tr>
 <tr>
  <td bgcolor="#224488" align="right"><input type="image" src="gfx/submit.gif" style="border: 0px"></td>
 </tr>
</table>
</td></tr></table>
</form>
<br />
<? else : ?>

<? if(isset($_SESSION['SESSION'])): ?>
<p>the lobster says "NO ENTRANCE!"</p>
<p></p>
<p>you need god priveligies to access this place.</p>
<p>it's also possible that you forgot a ?which=xxx</p>
<? else: ?>
<br />
<table width="20%"><tr><td>
<form action="login.php" method="post">
<table cellspacing="1" cellpadding="2" class="box">
 <tr><th>login</th></tr>
 <tr bgcolor="#446688">
  <td nowrap align="center">
   You need to be logged in to edit a prod :: <a href="account.php">register here</a><br />
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
</td></tr></table>
<br />
<? endif; ?>


<? endif; ?>

<? require("include/bottom.php"); ?>
