<?
require("include/top.php");

if ($which && ($SESSION_LEVEL=='administrator' || $SESSION_LEVEL=='moderator' || $SESSION_LEVEL=='gloperator')):

// check the submitted data
unset($submitok);
if($type && $link)
{
  // check user account
  if(!isset($_SESSION['SESSION']))
	$errormessage[]="you need to be logged in first.";
  if(($SESSION_LEVEL!='administrator')&&($SESSION_LEVEL!='moderator')&&($SESSION_LEVEL!='gloperator'))
	  $errormessage[]="you need to be a lobster god to edit a bbs.";
  // check prod id
  $result=mysql_query("SELECT count(0) FROM downloadlinks WHERE id=$which");
  if(mysql_result($result,0)!=1)
    $errormessage[]="I can't find the downloadlink you are trying to modify";

  // if everything is ok
  if(!$errormessage)
    $submitok=true;
}

// insert the submitted download link
if($submitok){
  $query = "UPDATE downloadlinks SET ";
  $query.= "downloadlinks.type='".$type."', ";
  $query.= "downloadlinks.link='".$link."' ";
  $query.= "WHERE downloadlinks.id=$which LIMIT 1";
 //print($query);
  mysql_query($query);
  logGloperatorAction("downloadlinks_edit",$which);
}

// get data to build the page
$result = mysql_query("SELECT * FROM downloadlinks WHERE id=$which");
$dl = mysql_fetch_assoc($result);
?>

<br />

<form action="editdownloadlink.php" method="get">
edit another downloadlink:<br />
id:<input type="text" name="which" value="<?=$which?>" /><br />
<input type="image" src="gfx/submit.gif" style="border: 0px" />
</form>

<br />

<form action="editdownloadlink.php" method="post">
<input type="hidden" name="which" value="<?=$which?>">
<table bgcolor="#000000" cellspacing="1" cellpadding="0"><tr><td>
<table bgcolor="#000000" cellspacing="1" cellpadding="2">
<? if($submitok): ?>
 <tr><th bgcolor="#224488">this downloadlink has been modified</th></tr>
 <tr>
  <td bgcolor="#446688" align="center">
   feel free to edit it again<br />
   <a href="prod.php?which=<? print($dl["prod"]); ?>">see what you've done</a><br />
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
  <th bgcolor="#224488">download link information</th>
 </tr>
 <tr>
  <td bgcolor="#446688">
   <table>
	<tr>
	 <td>type of link:</td>
	 <td><input type="text" name="type" value="<? print($dl['type']); ?>"><br ></td>
	</tr>
	<tr>
	 <td colspan="2">follow the standards bitch<br />
	 used so far are disk2, disk3, disk4, disk5, video, source.<br />
	 also acceptable: OSX port, Windows port, Amiga port, etc. <br />
	 fix, party version, soundtrack, youtube, video hires, video lowres, video (.mov), video (.wmv)<br />
	 </td>
	</tr>
	<tr>
	 <td>url:</td>
	 <td><input type="text" name="link" value="<? print($dl['link']); ?>" size="110"><br ></td>
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
   You need to be logged in to edit a download link :: <a href="account.php">register here</a><br />
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
