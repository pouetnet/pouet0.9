<?
require("include/top.php");

if (($SESSION_LEVEL=='administrator' || $SESSION_LEVEL=='moderator' || $SESSION_LEVEL=='gloperator')):

unset($submitok);
// check the submitted data
if($prod && $type && $link)
{
  if(!$type)
    $errormessage[]="having a type would be nice..";

  if(!$link)
    $errormessage[]="no download link ?!";
  else
  {
    $myurl=parse_url($link);
    if(($myurl["scheme"]!="http")&&($myurl["scheme"]!="ftp")&&($myurl["scheme"]!="https"))
      $errormessage[] = "only http/https and ftp protocols are supported for the download link";
    if(strlen($myurl["host"])==0)
      $errormessage[] = "missing hostname in the download link";
    if(strstr($myurl["path"],"incoming"))
      $errormessage[] = "the file you submitted is in an incoming path, try to find a real path";
  }

  if(!$errormessage)
    $submitok=true;
}


// insert the submitted download link
if($submitok){
	  $query = "INSERT INTO downloadlinks SET ";
	  $query.= "downloadlinks.prod='".$prod."', ";
	  $query.= "downloadlinks.type='".$type."', ";
    	  $query.= "downloadlinks.link='".$link."'";
    	  //print($query);
	  mysql_query($query);
}

?>
<br />
<form action="submitdownloadlinks.php" method="post" enctype="multipart/form-data">
<table bgcolor="#000000" cellspacing="1" cellpadding="0"><tr><td>
<table bgcolor="#000000" cellspacing="1" cellpadding="2">
<? if($submitok): ?>
 <tr><th bgcolor="#224488">download link has been added</th></tr>
 <tr>
  <td bgcolor="#446688" align="center">
   feel free to add another one<br />
   <a href="prod.php?which=<? print($prod); ?>">see what you've done</a><br />
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
	 <td>prod id:</td>
	 <td><input type="text" name="prod" value="<? print($prod); ?>"><br /></td>
	</tr>
	<tr>
	 <td>type of link:</td>
	 <td><input type="text" name="type" value="<? print($type); ?>"><br /></td>
	</tr>
	<tr>
	 <td colspan="2">follow the standards bitch<br />
	 used so far are disk2, disk3, disk4, disk5, video, source<br />
	 also acceptable: OSX port, Windows port, Win32 port, Amiga port, etc.<br />
	 along with fix, party version, soundtrack, youtube, video hires, video lowres, video (.mov), video (.wmv)<br />
	 </td>
	</tr>
	<tr>
	 <td>url link:</td>
	 <td><input type="text" name="link" value="<? print($link); ?>" size="110"><br /></td>
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

<? else : ?>

<? if(isset($_SESSION['SESSION'])): ?>
<p>the lobster says "NO ENTRANCE!"</p>
<p></p>
<p>you need god priveligies to access this place.</p>
<? else: ?>
<br />
<table width="20%"><tr><td>
<form action="login.php" method="post">
<table cellspacing="1" cellpadding="2" class="box">
 <tr><th>login</th></tr>
 <tr bgcolor="#446688">
  <td nowrap align="center">
   You need to be logged in to submit a download link :: <a href="account.php">register here</a><br />
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
<? endif; ?>

<? endif; ?>
<br />

<? require("include/bottom.php"); ?>
