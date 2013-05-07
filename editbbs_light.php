<?
require("include/top.php");

if ($which && ($SESSION_LEVEL=='administrator' || $SESSION_LEVEL=='moderator' || $SESSION_LEVEL=='gloperator')):

// check the submitted data
unset($submitok);
if($name)
{
  // check user account
  if(!session_is_registered("SESSION"))
	$errormessage[]="you need to be logged in first.";
  if(($SESSION_LEVEL!='administrator')&&($SESSION_LEVEL!='moderator')&&($SESSION_LEVEL!='gloperator'))
	  $errormessage[]="you need to be a lobster god to edit a bbs.";
  // check prod id
  $result=mysql_query("SELECT count(0) FROM bbses WHERE id=$which");
  if(mysql_result($result,0)!=1)
    $errormessage[]="I can't find the bbs you are trying to modify";
  // check the release date
  if(($rmonth)&&(!$ryear)) {
    $errormessage[]="lobster no like month without year";
  }
  if((($rmonth)&&($ryear))&&(($rmonth>date('m'))&&($ryear>=date('Y')))) {
    $errormessage[]="you can't submit a prod released in the future, sorry =)";
  }
  // if everything is ok
  if(!$errormessage)
    $submitok=true;
}

// get list of all platforms
$query="select * from platforms order by name asc";
$result = mysql_query($query);
while($tmp = mysql_fetch_array($result)) {
  	 $platforms[]=$tmp;
}


// insert the submitted bbs
if($submitok){
  $query = "UPDATE bbses SET ";
  $query.= "name='".$name."', ";
  $query.= "sysop='".$sysop."', ";
  if(($rmonth)&&($ryear)) {
    $query.= "started='".$ryear."-".$rmonth."-15', ";
  } else {
  	if($ryear) {
  		$query.= "started='".$ryear."-00-15', ";
  	}
  }
  if(($rmonth2)&&($ryear2)) {
    $query.= "closed='".$ryear2."-".$rmonth2."-15', ";
  } else {
  	if($ryear2) {
  		$query.= "closed='".$ryear2."-00-15', ";
  	}
  }
  $query.= "phonenumber='".$phonenumber."', ";
  $query.= "telnetip='".$telnetip."' ";
  $query.= "WHERE id=$which LIMIT 1";
 //print($query);
  mysql_query($query);
  
  $query = "delete from bbses_platforms where bbses_platforms.bbs=".$which;
  mysql_query($query);
  //print(count($platform)."-".count($platforms));
  for($i=0;$i<count($platform);$i++) {
  	for($j=0;$j<count($platforms);$j++) {
  		//print("\n".$platform[$i]."-".$platforms[$j]["name"]);
  		if ($platform[$i]==$platforms[$j]["name"]):
  			$query="insert into bbses_platforms set bbses_platforms.bbs='".$which."', bbses_platforms.platform='".$platforms[$j]["id"]."'";
  			mysql_query($query);			
  			//print($query);
  		endif;
  	}
  }
  logGloperatorAction("bbs_edit",$which);
}

// get data to build the page
$result = mysql_query("SELECT * FROM bbses WHERE id=$which");
$bbs = mysql_fetch_assoc($result);
unset($platform);
$query="select platforms.name from bbses_platforms, platforms where bbses_platforms.bbs='".$which."' and platforms.id=bbses_platforms.platform";
$result = mysql_query($query);
while($tmp = mysql_fetch_array($result)) {
  	 $platform[]=$tmp;
}
$rmonth = sprintf('%d', substr($bbs['started'], 5, 2));
$ryear = substr($bbs['started'], 0, 4);
$rmonth2 = sprintf('%d', substr($bbs['closed'], 5, 2));
$ryear2 = substr($bbs['closed'], 0, 4);
?>

<br />

<form action="editbbs_light.php" method="get">
edit another bbs:<br />
id:<input type="text" name="which" value="<?=$which?>" /><br />
<input type="image" src="gfx/submit.gif" style="border: 0px" />
</form>

<br />

<form action="editbbs_light.php" method="post">
<input type="hidden" name="which" value="<?=$which?>">
<table bgcolor="#000000" cellspacing="1" cellpadding="0"><tr><td>
<table bgcolor="#000000" cellspacing="1" cellpadding="2">
<? if($submitok): ?>
 <tr><th bgcolor="#224488">this bbs has been modified</th></tr>
 <tr>
  <td bgcolor="#446688" align="center">
   feel free to edit it again<br />
   <a href="bbses.php?which=<? print($which); ?>">see what you've done</a><br />
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
  <th bgcolor="#224488">bbs information</th>
 </tr>
 <tr>
  <td bgcolor="#446688">
   <table>
	<tr>
	 <td>name:</td>
	 <td><input type="text" name="name" value="<? print($bbs['name']); ?>"><br ></td>
	</tr>
	<tr>
	 <td>sysop:</td>
	 <td><input type="text" name="sysop" value="<? print($bbs['sysop']); ?>"><br ></td>
	</tr>
	<tr>
	 <td>start date:</td>
	 <td>
	  <select name="rmonth">
	   <option></option>
	   <? for($i=1;$i<=12;$i++): ?>
	   <? ($rmonth==$i) ? $sel=" selected" : $sel=""; ?>
	   <option value="<? print($i); ?>" <? print($sel); ?>><? print($months[$i]); ?></option>
	   <? endfor; ?>
	  </select>
	  <select name="ryear">
	   <option></option>
	   <?
	   for($i=date("Y");$i>=1980;$i--) {
		 ($ryear==$i) ? $sel=" selected" : $sel="";
		 print("<option".$sel.">".$i."</option>\n");
	   }
	   ?>
	  </select>
	  <br />
	 </td>
	</tr>
	<tr>
	 <td>close date:</td>
	 <td>
	  <select name="rmonth2">
	   <option></option>
	   <? for($i=1;$i<=12;$i++): ?>
	   <? ($rmonth2==$i) ? $sel=" selected" : $sel=""; ?>
	   <option value="<? print($i); ?>" <? print($sel); ?>><? print($months[$i]); ?></option>
	   <? endfor; ?>
	  </select>
	  <select name="ryear2">
	   <option></option>
	   <?
	   for($i=date("Y");$i>=1980;$i--) {
		 ($ryear2==$i) ? $sel=" selected" : $sel="";
		 print("<option".$sel.">".$i."</option>\n");
	   }
	   ?>
	  </select>
	  <br />
	 </td>
	</tr>
	<tr>
	 <td></td>
	 <td>leave blank when in doubt.<br /></td>
	</tr>
	<tr>
	 <td>platforms:</td>
	 <td>
	  <select name="platform[]" multiple>
	  <?
		for($i=0;$i<count($platforms);$i++) {
		  $ok=0;
		  for($j=0;$j<count($platform);$j++) {
			//print($platforms[$i]["name"]."\n".$platform[$j]["name"]."\n");
			if($platforms[$i]["name"]==$platform[$j]["name"]) {
			  $ok++;
			}
		  }
		  if($ok) {
			$is_selected = " selected";
		  } else {
			$is_selected = "";
		  }
		  print("<option".$is_selected.">".$platforms[$i]["name"]."</option>\n");
		}
	  ?>
	  </select>
	 </td>
	</tr>
	<tr>
	 <td></td>
	 <td>platforms it spread, not ran on.<br /></td>
	</tr>
	<tr>
	 <td>phonenumber:</td>
	 <td><input type="text" name="phonenumber" value="<? print($bbs['phonenumber']); ?>"><br ></td>
	</tr>
	<tr>
	 <td colspan="2">follow standard: +countrycode-citycode-phonenumber eg: +7-095-391XXXX for a moscow bbs<br />
	 if it isnt demoscene related _DONT ADD IT_<br />
	 if it has illegal stuff on it and is still in service _DONT ADD IT_<br /> or atleast ask the sysop first, or dont state the actual numbers, be safe before sorry.<br />
	 on any cases unless you are/were the sysop, or have his/her permission, _dont disclose the last numbers_.<br /></td>
	</tr>
	<tr>
	 <td>telnet address:</td>
	 <td><input type="text" name="telnetip" value="<? print($bbs['telnetip']); ?>"><br ></td>
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

<? if(session_is_registered("SESSION")): ?>
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
