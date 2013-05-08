<?
require("include/top.php");

if ( ($which&&$when) && ($SESSION_LEVEL=='administrator' || $SESSION_LEVEL=='moderator' || $SESSION_LEVEL=='gloperator')):

// extend years to 4 digits
$when = intval($when);
if ($when < 50) {
	$when += 2000;
} elseif ($when < 100) {
	$when += 1900;
}

// check the submitted data
unset($submitok);
if($which&&$when&&$id)
{
  // check user account
  if(!isset($_SESSION['SESSION']))
	$errormessage[]="you need to be logged in first.";
  if(($SESSION_LEVEL!='administrator')&&($SESSION_LEVEL!='moderator')&&($SESSION_LEVEL!='gloperator'))
	  $errormessage[]="you need to be a lobster god to edit a prod.";
  // check prod id
  $result=mysql_query("SELECT count(0) FROM partylinks WHERE party=$which AND year=$when");
  if(mysql_result($result,0)!=1)
    $errormessage[]="doesnt seem to exist....";
  // check the download url
  if($download)
  {
    $myurl=parse_url($download);
    if(($myurl["scheme"]!="http")&&($myurl["scheme"]!="ftp"))
      $errormessage[] = "only http and ftp protocols are supported for the download link";
    if(strlen($myurl["host"])==0)
      $errormessage[] = "missing hostname in the download link";
    if(strstr($myurl["path"],"incoming"))
      $errormessage[] = "the file you submitted is in an incoming path, try to find a real path";
    if( ((($myurl["port"])!=80) && (($myurl["port"])!=0)) && ((strlen($myurl["user"])>0) || (strlen($myurl["pass"])>0)) )
      $errormessage[] = "no private FTP please".$port;
  }

  // if everything is ok
  if(!$errormessage)
    $submitok=true;
}

// insert the submitted prod
if($submitok){
  $query = "UPDATE partylinks SET ";
  $query.= "download='".$download."', ";
  $query.= "csdb=".((int)$csdb).", ";
  $query.= "slengpung=".((int)$slengpung).", ";
  $query.= "zxdemo=".((int)$zxdemo).", ";
  $query.= "artcity='".($artcity)."' ";
  $query.= "WHERE party=".$which." AND year=".$when." LIMIT 1";
  mysql_query($query);
  logGloperatorAction("partylinks_edit",$which);
}

// get data to build the page
$result=mysql_query("SELECT * FROM partylinks WHERE party=$which AND year=$when");
$links = mysql_fetch_assoc($result);
?>

<br />

<form action="editpartylinks.php" method="get">
edit another partylinks:<br />
which: (party id) <input type="text" name="which" value="<?=$which?>" /><br />
when: (party year) <input type="text" name="when" value="<?=$when?>" /><br />
<input type="image" src="gfx/submit.gif" style="border: 0px" />
</form>

<br>

<form action="editpartylinks.php" method="post">
<input type="hidden" name="which" value="<?=$which?>">
<input type="hidden" name="when" value="<?=$when?>">
<input type="hidden" name="id" value="<?=$links['id']?>">
<table bgcolor="#000000" cellspacing="1" cellpadding="0"><tr><td>
<table bgcolor="#000000" cellspacing="1" cellpadding="2">
<? if($submitok): ?>
 <tr><th bgcolor="#224488">this partylinks has been modified</th></tr>
 <tr>
  <td bgcolor="#446688" align="center">
   feel free to edit it again<br>
   <a href="party.php?which=<? print($which); ?>&amp;when=<? print($when); ?>">see what you've done</a><br>
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
  <th bgcolor="#224488">partylinks information</th>
 </tr>
 <tr>
  <td bgcolor="#446688">
   <table>
<? /*	<tr>
	 <td>id:</td>
	 <td><? print("id: ".$links['id']); ?><br></td>
	</tr> */ ?>
	<tr>
	 <td>party:</td>
	 <td><? print("party: ".$links['party']); ?><br></td>
	</tr>
	<tr>
	 <td>year:</td>
	 <td><? print("year: ".$links['year']); ?><br></td>
	</tr>
	<tr>
	 <td>download url:</td>
	 <td><input type="text" name="download" value="<? print($links['download']); ?>" size=100><br></td>
	</tr>
	<tr>
	 <td>csdb id:</td>
	 <td><input type="text" name="csdb" value="<? print($links['csdb']); ?>"><br></td>
	</tr>
	<tr>
	 <td>zxdemo id:</td>
	 <td><input type="text" name="zxdemo" value="<? print($links['zxdemo']); ?>"><br></td>
	</tr>
	<tr>
	 <td>slengpung id:</td>
	 <td><input type="text" name="slengpung" value="<? print($links['slengpung']); ?>"><br></td>
	</tr>
	<tr>
	 <td>artcity tags:</td>
	 <td><input type="text" name="artcity" value="<? print($links['artcity']); ?>"><br></td>
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
<p>it's also possible that you forgot a ?which=xxx</p>
<? else: ?>
<br>
<table width="20%"><tr><td>
<form action="login.php" method="post">
<table cellspacing="1" cellpadding="2" class="box">
 <tr><th>login</th></tr>
 <tr bgcolor="#446688">
  <td nowrap align="center">
   You need to be logged in to edit a prod :: <a href="account.php">register here</a><br>
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
<br>
<? endif; ?>


<? endif; ?>

<? require("include/bottom.php"); ?>
