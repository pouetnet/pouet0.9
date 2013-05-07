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
    $errormessage[]="there is no need to select the same group two times";
  if(!$group1&&($group2||$group3))
    $errormessage[]="choose the group in the first combo";
  // check the download url
  if(!$download)
    $errormessage[]="no download link for this prod ?!";
  else
  {
    $myurl=parse_url($download);
    if(($myurl["scheme"]!="http")&&($myurl["scheme"]!="ftp")&&($myurl["scheme"]!="https"))
      $errormessage[] = "only http/https and ftp protocols are supported for the download link";
    if(strlen($myurl["host"])==0)
      $errormessage[] = "missing hostname in the download link";
    if(strstr($myurl["path"],"incoming"))
      $errormessage[] = "the file you submitted is in an incoming path, try to find a real path";
    if( ((($myurl["port"])!=80) && (($myurl["port"])!=0)) && ((strlen($myurl["user"])>0) || (strlen($myurl["pass"])>0)) )
      $errormessage[] = "no private FTP please".$port;
  }
  // check the release date
  if((($rmonth)&&($ryear))&&(($rmonth>date('m'))&&($ryear>=date('Y')))) {
    $errormessage[]="you can't submit a prod released in the future, sorry =)";
  }
  // check the prod type
  if(!count($type)) {
    $errormessage[] = "you must select at least a type for this prod";
  }
  if(!count($platform)) {
    $errormessage[] = "you must select at least one platform";
  }
  // check the party input
  if(($pyear&&!$party)||($prank&&!$party))
    $errormessage[] = "please select either both party and a year, or neither!";

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
/*  $query.= "', ";
  $query.= "platform='";
  if(count($platform)>0) {
    $query.= $platform[0];
  }
  for($i=1;$i<count($platform);$i++) {
    $query.=",".$platform[$i];
  }*/
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
  if($invit) {
    $query.= "invitation=".$invit.", ";
  }
  if($boardID) {
    $query.= "boardID=".$boardID.", ";
  }
  if($piyear) {
    $invityear=intval($piyear);
    $query.= "invitationyear=".$invityear.", ";
  }
  if(isset($prank)) {
    $query.= "party_place=".$prank.", ";
  }
  $query.= "download='".$download."', ";
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
$result = mysql_query("DESC prods type");
$row = mysql_fetch_row($result);
$types = explode("'",$row[1]);
$result = mysql_query("DESC prods partycompo");
$row = mysql_fetch_row($result);
$compos = explode("'",$row[1]);
/*$result = mysql_query("DESC prods platform");
$row = mysql_fetch_row($result);
$platforms = explode("'",$row[1]);*/
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

<form action="editprod_light.php" method="get">
edit another prod:<br />
id:<input type="text" name="which" value="<?=$which?>" /><br />
<input type="image" src="gfx/submit.gif" style="border: 0px" />
</form>

<br />

<form action="editprod_light.php" method="post" name="editprod_light">
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
	 <td><input type="text" name="name" value="<? print(htmlentities($prod['name'])); ?>" size='50'><br /></td>
	</tr>
	<tr>
	 <td>group:</td>
	 <td><input type="text" name="group1" value="<? print($prod['group1']); ?>">
	 (<a href="javascript:popupGroupSelector('editprod_light','group1');">select</a>)</td>
	</tr>
	<tr>
	 <td>other group:</td>
	 <td><input type="text" name="group2" value="<? print($prod['group2']); ?>">
	 (<a href="javascript:popupGroupSelector('editprod_light','group2');">select</a>)</td>
	</tr>
	<tr>
	 <td>third group:</td>
	 <td><input type="text" name="group3" value="<? print($prod['group3']); ?>">
	 (<a href="javascript:popupGroupSelector('editprod_light','group3');">select</a>)</td>
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
	 <td>[<a href="submitdownloadlinks.php?prod=<? print($prod['id']); ?>">submit new download link</a>]<br /></td>
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
	  <select name="type[]" multiple="multiple" size="10">
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
	  <select name="platform[]" multiple="multiple" size="10">
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
	 <td>ad for BBS:</td>
	 <td><input type="text" name="boardID" value="<? print($prod['boardID']); ?>">
	 </td>
	</tr>
	<tr>
	 <td>invitation for party:</td>
	 <td><input type="text" name="invit" value="<? print($prod['invitation']); ?>">
	 (<a href="javascript:popupPartySelector('editprod_light','invit');">select</a>)</td>
	 </td>
	</tr>
	<tr>
	 <td>invitation party year:</td>
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
	 <td><input type="text" name="party" value="<? print($prod['party']); ?>">
	 (<a href="javascript:popupPartySelector('editprod_light','party');">select</a>)</td>
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
	   <option value='0'></option>
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

 <tr>
  <th bgcolor="#224488">prod affiliations</th>
 </tr>
 <tr bgcolor="#446688">
  <td>
  <b>derivative of:</b> [<a href='submitprodaffils.php?derivative=<?=$which?>'>add</a>]<br/>
<?
	$result=mysql_query("SELECT * from affiliatedprods where derivative=".$which);
	while ($o = mysql_fetch_object($result)) {
	  printf("%d (%s)",$o->original,$o->type);
	  printf(" [<a href='removeprodaffil.php?derivative=%d&original=%d'>remove</a>]",$o->derivative,$o->original);
	  printf(" [<a href='swapprodaffil.php?derivative=%d&original=%d'>swap</a>]",$o->derivative,$o->original);
	  printf("<br/>");
	}
?>
  </td>
 </tr>
 <tr bgcolor="#446688">
  <td>
  <b>original of:</b> [<a href='submitprodaffils.php?original=<?=$which?>'>add</a>]<br/>
<?
	$result=mysql_query("SELECT * from affiliatedprods where original=".$which);
	while ($o = mysql_fetch_object($result)) {
	  printf("%d (%s)",$o->derivative,$o->type);
	  printf(" [<a href='removeprodaffil.php?derivative=%d&original=%d'>remove</a>]",$o->derivative,$o->original);
	  printf(" [<a href='swapprodaffil.php?derivative=%d&original=%d'>swap</a>]",$o->derivative,$o->original);
	  printf("<br/>");
	}
?>
  </td>
 </tr>

 <tr>
  <th bgcolor="#224488">prod viewing tips / awards [<a href='submitsceneorgrecommended.php?which=<?=$which?>'>add</a>]</th>
 </tr>
 <tr bgcolor="#446688">
  <td>
<?
	$result=mysql_query("SELECT * from sceneorgrecommended where prodid=".$which);
	while ($o = mysql_fetch_object($result)) {
	  printf("type: <i>%s</i> - category: <i>%s</i> - [<a href='removesceneorgrecommended.php?which=%d'>remove</a>]<br/>",$o->type,$o->category,$o->id);
	}
?>
  </td>
 </tr>

 <tr>
  <th bgcolor="#224488">other parties [<a href='submitprodotherparty.php?prod=<?=$which?>'>add</a>]</th>
 </tr>
 <tr bgcolor="#446688">
  <td>
<?
	$result=mysql_query($query="SELECT prodotherparty.party, prodotherparty.party_place, prodotherparty.party_year, prodotherparty.partycompo, parties.name FROM prodotherparty LEFT JOIN parties ON parties.id=prodotherparty.party WHERE prod=".$which);
	while ($o = mysql_fetch_object($result)) {
	  printf("<a href='party.php?which=%d&when=%d'>%s %d</a>, %s [<a href='submitotherpartyinfo.php?which=%d&what=%d'>edit</a>]<br/>",$o->party,$o->party_year,$o->name,$o->party_year,$o->party_place,$which,$o->party);
	}
?>
  </td>
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
