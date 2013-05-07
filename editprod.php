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
	  $errormessage[]="you need to be a lobster god to edit a prod.";
  // check prod id
  $result=mysql_query("SELECT count(0) FROM prods WHERE id=$which");
  if(mysql_result($result,0)!=1)
    $errormessage[]="I can't find the prod you are trying to modify";
  // check the chosen groups
  if(((($group1==$group2)||($group1==$group3))&&$group1)||(($group2==$group3)&&$group2))
    $errormessage[]="there is no need to select the same group twice";
  if(!$group1&&($group2||$group3))
    $errormessage[]="choose the group in the first combo";
  // check the download url
  if(!$download)
    $errormessage[]="no download link for this prod ?!";
  else
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
  // check the release date
  if(($rmonth)&&(!$ryear)) {
    $errormessage[]="lobster no like month without year";
  }
  if((($rmonth)&&($ryear))&&(($rmonth>date('m'))&&($ryear>=date('Y')))) {
    $errormessage[]="you can't submit a prod released in the future, sorry =)";
  }
  // check the prod type
  if(!count($type)) {
    $errormessage[] = "you must select at least one type for this prod";
  }
  if(!count($platform)) {
    $errormessage[] = "you must select at least one platform";
  }
  // check the party input
  if(($pyear&&!$party)||($prank&&!$party))
    $errormessage[] = "please select a party";

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


// insert the submitted prod
if($submitok){
  $query = "UPDATE prods SET ";
  $query.= "name='".$name."', ";
  if($group1) {
    $query.= "group1=".$group1.", ";
  } else {
	$query.= "group1=NULL, ";
  }
  if($group2) {
    $query.= "group2=".$group2.", ";
  } else {
	$query.= "group2=NULL, ";
  }
  if($group3) {
    $query.= "group3=".$group3.", ";
  } else {
	$query.= "group3=NULL, ";
  }
  if(($rmonth)&&($ryear)) {
    $query.= "date='".$ryear."-".$rmonth."-15', ";
  } else {
  	if($ryear) {
  		$query.= "date='".$ryear."-00-15', ";
  	}
  }
  $query.= "type='";
  if(count($type)>0) {
    $query.= $type[0];
  }
  for($i=1;$i<count($type);$i++) {
    $query.=",".$type[$i];
  }
  $query.= "', ";
  $query.= "partycompo='".$compo."', ";
  if($party && $pyear) {
    $query.= "party=".$party.", ";
    $party_year=intval($pyear);
    $query.= "party_year=".$party_year.", ";
  } else if($party==1024) {
    $query.= "party=1024, ";
  } 
  if(!$party && !$pyear) {
    $query.= "party=0, ";
    $query.= "party_year=0, ";
  }
  if($invitation) {
    $query.= "invitation=".$invitation.", ";
  }
  if($piyear) {
    $invitationyear=intval($piyear);
    $query.= "invitationyear=".$invitationyear.", ";
  }
  if($prank) {
    $query.= "party_place=".$prank.", ";
  }
  $query.= "download='".$download."', ";
  /*$query.= "download2='".$download2."', ";
  $query.= "download3='".$download3."', ";
  $query.= "download4='".$download4."', ";
  $query.= "download5='".$download5."', ";
  $query.= "video='".$video."', ";
  $query.= "source='".$source."', ";*/
  $query.= "csdb=".((int)$csdb).", ";
  $query.= "sceneorg=".((int)$sceneorg).", ";
  $query.= "zxdemo=".((int)$zxdemo)." ";
  $query.= "WHERE id=$which LIMIT 1";
  mysql_query($query);
  
  $query = "delete from prods_platforms where prods_platforms.prod=".$which;
  mysql_query($query);
  for($i=0;$i<count($platform);$i++) {
  	for($j=0;$j<count($platforms);$j++) {
  		if ($platform[$i]==$platforms[$j]["name"]):
  			$query="insert into prods_platforms set prods_platforms.prod='".$which."', prods_platforms.platform='".$platforms[$j]["id"]."'";
  			mysql_query($query);			
  		endif;
  	}
  }
 
  logGloperatorAction("prod_edit",$which);
}

// get data to build the page

$result = mysql_query("SELECT * FROM prods WHERE id=$which");
$prod = mysql_fetch_assoc($result);

$result = mysql_query("SELECT id,link,type FROM downloadlinks WHERE downloadlinks.prod=$which ORDER BY downloadlinks.type");
while($tmp = mysql_fetch_array($result)) {
  $dl[] = $tmp;
}

$result = mysql_query("SELECT id,name FROM groups ORDER BY name ASC");
while($tmp = mysql_fetch_array($result)) {
  $groups[] = $tmp;
}
$result = mysql_query("DESC prods type");
$row = mysql_fetch_row($result);
$types = explode("'",$row[1]);
//$result = mysql_query("DESC prods platform");
//$row = mysql_fetch_row($result);
//$platforms = explode("'",$row[1]);
$result = mysql_query("DESC prods partycompo");
$row = mysql_fetch_row($result);
$compos = explode("'",$row[1]);
$result = mysql_query("SELECT * FROM parties ORDER BY name");
while($tmp=mysql_fetch_array($result)){
  $parties[]=$tmp;
}
$rmonth = sprintf('%d', substr($prod['date'], 5, 2));
$ryear = substr($prod['date'], 0, 4);
$type = explode(',', $prod['type']);
//$platform = explode(',', $prod['platform']);
$query="select platforms.name from prods_platforms, platforms where prods_platforms.prod='".$which."' and platforms.id=prods_platforms.platform";
$result = mysql_query($query);
while($tmp = mysql_fetch_array($result)) {
  	 $platform[]=$tmp;
}

if(isset($prod['party_year']))
{
	$pyear = $prod['party_year'];
}
if(isset($prod['invitationyear']))
{
	$piyear = $prod['invitationyear'];
}
$prank=$prod['party_place'];
?>

<br />

<form action="editprod.php" method="get">
edit another prod:<br />
id:<input type="text" name="which" value="<?=$which?>" /><br />
<input type="image" src="gfx/submit.gif" style="border: 0px" />
</form>

<br />

<form action="editprod.php" method="post">
<input type="hidden" name="which" value="<?=$which?>">
<table bgcolor="#000000" cellspacing="1" cellpadding="0"><tr><td>
<table bgcolor="#000000" cellspacing="1" cellpadding="2">
<? if($submitok): ?>
 <tr><th bgcolor="#224488">this prod has been modified</th></tr>
 <tr>
  <td bgcolor="#446688" align="center">
   feel free to edit it again<br />
   <a href="prod.php?which=<? print($which); ?>">see what you've done</a><br />
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
  <th bgcolor="#224488">prod information</th>
 </tr>
 <tr>
  <td bgcolor="#446688">
   <table>
	<tr>
	 <td>name:</td>
	 <td><input type="text" name="name" value="<? print(htmlspecialchars($prod['name'])); ?>"><br /></td>
	</tr>
	<tr>
	 <td>group:</td>
	 <td>
	  <select name="group1">
		<option value="0"></option>
	   <?
		for($i=0;$i<count($groups);$i++) {
		  if($prod['group1']==$groups[$i][0]) {
			$is_selected = " selected";
		  } else {
			$is_selected = "";
		  }
		  print("<option value=\"".$groups[$i][0]."\"".$is_selected.">".strtolower($groups[$i][1])."</option>\n");
		}
	   ?>
	  </select>
	 </td>
	</tr>
	<tr>
	 <td>other group:</td>
	 <td>
	  <select name="group2">
		<option value="0"></option>
	   <?
		for($i=0;$i<count($groups);$i++) {
		  if($prod['group2']==$groups[$i][0]) {
			$is_selected = " selected";
		  } else {
			$is_selected = "";
		  }
		  print("<option value=\"".$groups[$i][0]."\"".$is_selected.">".strtolower($groups[$i][1])."</option>\n");
		}
	   ?>
	  </select>
	 </td>
	</tr>
	<tr>
	 <td>third group:</td>
	 <td>
	  <select name="group3">
		<option value="0"></option>
	   <?
		for($i=0;$i<count($groups);$i++) {
		  if($prod['group3']==$groups[$i][0]) {
			$is_selected = " selected";
		  } else {
			$is_selected = "";
		  }
		  print("<option value=\"".$groups[$i][0]."\"".$is_selected.">".strtolower($groups[$i][1])."</option>\n");
		}
	   ?>
	  </select>
	 </td>
	</tr>
	<tr>
	 <td>download url:</td>
	 <td><input type="text" name="download" value="<? print($prod['download']); ?>" size=60><br /></td>
	</tr>
	<? for($i=0;$i<count($dl);$i++): ?>
		<tr>
		 <td><? print($dl[$i]['type']); ?> [<a href="removedownloadlink.php?id=<? print($dl[$i]['id']); ?>&amp;action=delete">kill</a>]<br /></td>
		 <td><? print($dl[$i]['link']); ?> [<a href="editdownloadlink.php?which=<? print($dl[$i]['id']); ?>">edit</a>]<br /></td>
		</tr>
	<? endfor; ?>
	<tr>
	 <td><br /></td>
	 <td>[<a href="submitdownloadlinks.php?which=<? print($prod['id']); ?>">submit new download link</a>]<br /></td>
	</tr>
	<tr>
	 <td>csdb id:</td>
	 <td><input type="text" name="csdb" value="<? print($prod['csdb']); ?>"><br /></td>
	</tr>
	<tr>
	 <td>sceneorg id:</td>
	 <td><input type="text" name="sceneorg" value="<? print($prod['sceneorg']); ?>"><br /></td>
	</tr>
	<tr>
	 <td>zxdemo id:</td>
	 <td><input type="text" name="zxdemo" value="<? print($prod['zxdemo']); ?>"><br /></td>
	</tr>
	<tr>
	 <td>release date:</td>
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
	   for($i=date("Y");$i>=1978;$i--) {
		 ($ryear==$i) ? $sel=" selected" : $sel="";
		 print("<option".$sel.">".$i."</option>\n");
	   }
	   ?>
	  </select>
	  <br />
	 </td>
	</tr>
	<tr>
	 <td>type:</td>
	 <td>
	 <select name="type[]" multiple>
	  <?
		for($i=1;$i<count($types);$i+=2) {
		  $ok=0;
		  for($j=0;$j<count($type);$j++) {
			//print($types[$i]."\n".$type[$j]."\n");
			if($types[$i]==$type[$j]) {
			  $ok++;
			}
		  }
		  if($ok) {
			$is_selected = " selected";
		  } else {
			$is_selected = "";
		  }
		  print("<option".$is_selected.">".$types[$i]."</option>\n");
		}
	  ?>
	  </select>
	 </td>
	</tr>
	<tr>
	 <td>platform:</td>
	 <td>
	  <select name="platform[]" multiple>
	  <?
		for($i=0;$i<count($platforms);$i++) {
		  $ok=0;
		  for($j=0;$j<count($platform);$j++) {
			//print($platforms[$i]."\n".$platform[$j]."\n");
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
	 <td>invitation for party:</td>
	 <td>
	  <select name="invitation">
    <option></option>
	  <?
		for($i=0;$i<count($parties);$i++) {
		  if($prod['invitation']==$parties[$i]["id"]) {
			$is_selected = " selected";
		  } else {
			$is_selected = "";
		  }
		  print("<option value=\"".$parties[$i]["id"]."\"".$is_selected.">".$parties[$i]["name"]."</option>\n");
		}
	  ?>
	  </select>
	 </td>
	</tr>
	<tr>
	 <td>party invitation year:</td>
	 <td>
	  <select name="piyear">
	   <option></option>
	   <?
	   for($i=date("Y")+1;$i>=1980;$i--) {
		 ($piyear==$i) ? $sel=" selected" : $sel="";
		 print("<option".$sel.">".$i."</option>\n");
	   }
	   ?>
	  </select>
	  <br />
	 </td>
	</tr>
	
	
	<tr>
	 <td>party:</td>
	 <td>
	  <select name="party">
    <option></option>
	  <?
		for($i=0;$i<count($parties);$i++) {
		  if($prod['party']==$parties[$i]["id"]) {
			$is_selected = " selected";
		  } else {
			$is_selected = "";
		  }
		  print("<option value=\"".$parties[$i]["id"]."\"".$is_selected.">".$parties[$i]["name"]."</option>\n");
		}
	  ?>
	  </select>
	 </td>
	</tr>
	<tr>
	 <td>party year:</td>
	 <td>
	  <select name="pyear">
	   <option></option>
	   <?
	   for($i=date("Y");$i>=1980;$i--) {
		 ($pyear==$i) ? $sel=" selected" : $sel="";
		 print("<option".$sel.">".$i."</option>\n");
	   }
	   ?>
	  </select>
	  <br />
	 </td>
	</tr>
	<tr>
	 <td>party rank:</td>
	 <td>
	  <select name="prank">
	   <option></option>
	   <?
	   for($i=1;$i<=99;$i++) {
		 ($prank==$i) ? $sel=" selected" : $sel="";
		 print("<option".$sel.">".$i."</option>\n");
	   }
	   ?>
	  </select>
	  <br />
	 </td>
	</tr>
	<tr>
	 <td>party compo:</td>
	 <td>
	<select name="compo">
    	<option></option>
	  <?
		for($i=1;$i<count($compos);$i+=2) {
		  if($prod['partycompo']==$compos[$i]) {
			$is_selected = " selected";
		  } else {
			$is_selected = "";
		  }
		  print("<option".$is_selected.">".$compos[$i]."</option>\n");
		}
	  ?>
	  </select>
	 </td>
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
