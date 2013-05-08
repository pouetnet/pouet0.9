<?
require("include/top.php");

unset($submitok);
// check the submitted data
if($name)
{
  // check user account
  if(!$_SESSION["SESSION"]||!$_SESSION["SCENEID"])
	$errormessage[]="you need to be logged in first.";
  // check prod name (is commented couz there are dif prods with same name)
  // $result=mysql_query("SELECT count(0) FROM prods WHERE name='".$name."'");
  // if(mysql_result($result,0))
  //  $errormessage[]="a prod with the same name is already in the database";
  // check the chosen groups
  if(((($group1==$group2)||($group1==$group3))&&$group1)||(($group2==$group3)&&$group2))
    $errormessage[]="there is no need to select the same group two times";
  if(!$group1&&($group2||$group3))
    $errormessage[]="choose the group in the first combo";
  //if($_SESSION["SCENEID_ID"]==2229)
  //  $errormessage[]="let us know when you've fixed your script, it's adding dupes of groups and prods, read bbs topics 1024 and 4920 for more info";
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
    if(strstr($myurl["host"],"back2roots"))
      $errormessage[] = "back2roots does not allow download from outside, find another host please";
    if(strstr($myurl["host"],"intro-inferno"))
      $errormessage[] = "\"stop linking to intro-inferno, you turds :)\" /reed/";

    if(strstr($myurl["host"],"geocities"))
      $errormessage[] = "please get proper hosting (e.g. untergrund or scene.org) without traffic limits";

    $shithosts = array(
      "rapidshare",
      "depositfiles",
      "filefactory",
      "sendspace",
      "netload",
      "mediafire",
      "megashare",
      "uploading.com",
      "mirrorcreator",
      "multiupload",
      "speedyshare",
      "wetransfer",
      "dropbox"
    );
    foreach ($shithosts as $v)
      if(strstr($myurl["host"],$v))
        $errormessage[] = "seriously, get better hosting";
      
    if(strstr($myurl["host"],"youtube") || strstr($myurl["host"],"youtu.be"))
      $errormessage[] = "FUCK YOUTUBE - BINARY OR GTFO";

    if(strstr($myurl["path"],".txt"))
      $errormessage[] = "NO TEXTFILES.";

    if(strstr($myurl["host"],"untergrund.net"))
    {
      for ($x=1; $x<=5; $x++)
       if(strstr($myurl["host"],"ftp".$x.".untergrund.net"))
        $errormessage[] = "scamp says: link to ftp.untergrund.net not ftp".$x.".untergrund.net!!";
      if ($myurl["scheme"]=="http")
       $errormessage[] = "scamp says: no link to untergrund.net via http please!";
      if(strstr($myurl["host"],"www.untergrund.net"))
       $errormessage[] = "scamp says: godverdom!! link to ftp.untergrund.net instead!";
    }
    if(strstr($myurl["path"],"incoming"))
      $errormessage[] = "the file you submitted is in an incoming path, try to find a real path";
    if(strstr($myurl["host"],"scene.org") && strstr($myurl["query"],"incoming"))
      $errormessage[] = "the file you submitted is in an incoming path, try to find a real path";
    if( ((($myurl["port"])!=80) && (($myurl["port"])!=0)) && ((strlen($myurl["user"])>0) || (strlen($myurl["pass"])>0)) )
      $errormessage[] = "no private FTP please";
    if(!basename($myurl["path"]))
      $errormessage[] = "no file? no prod!";
      
  }
  // check the release date
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
  if(($party&&!$pyear)&&$party!=1024)
    $errormessage[] = "please select a party year";
  
  // check the screenshot
  if(is_uploaded_file($sshotfile)) {
    $fileinfo = GetImageSize($sshotfile);
    switch($fileinfo[2]) {
      case 1:$mytype=".gif";break;
      case 2:$mytype=".jpg";break;
      case 3:$mytype=".png";break;
      default: $errormessage[]="the screenshot is not a valid .gif/jpg or .png file"; break;
    }
    if($fileinfo[0]>400) {
      $errormessage[]="the width of the screenshot must not be greater than 400 pixels";
    }
    if($fileinfo[1]>300) {
      $errormessage[]="the height of the screenshot must not be greater than 300 pixels";
    }
    if(filesize($sshotfile)>65536) {
      $errormessage[]="the size of the screenshot must not be greater than 64Kb";
    }
  }
  // check the .nfo
  if(is_uploaded_file($nfofile)) {

    if(filesize($nfofile)>32768) {
      $errormessage[]="the size of the infofile must not be greater than 32Kb";
    }
  }
  
  // if everything is ok
  if(!$errormessage)
    $submitok=true;
}

// delete uploads if prod is wrong
if(!$submitok) {
  if(file_exists($sshotfile))
    unlink($sshotfile);
  if(file_exists($nfofile))
    unlink($nfofile);
}

// get list of all platforms
$query="select * from platforms order by name asc";
$result = mysql_query($query);
while($tmp = mysql_fetch_array($result)) {
  	 $platforms[]=$tmp;
}

// insert the submitted prod
if($submitok){
	//double insert check
	$query="SELECT name FROM prods ORDER BY quand DESC LIMIT 1";
	$result=mysql_query($query);
	$lastone=mysql_fetch_assoc($result);
	if($lastone["name"]!=$name)
	{
	  $query = "INSERT INTO prods SET ";
	  $query.= "name='".$name."', ";
	  if($group1) {
	    $query.= "group1=".((int)$group1).", ";
	  }
	  if($group2) {
	    $query.= "group2=".((int)$group2).", ";
	  }
	  if($group3) {
	    $query.= "group3=".((int)$group3).", ";
	  }
	  $query.= "download='".$download."', ";
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
/*	  $query.= "platform='";
	  if(count($platform)>0) {
	    $query.= $platform[0];
	  }
	  for($i=1;$i<count($platform);$i++) {
	    $query.=",".$platform[$i];
	  }
	  $query.= "', ";*/
	  if($invit) {
	    $query.= "invitation=".$invit.", ";
	  }
	  if($piyear) {
	    $invityear=intval($piyear);
	    $query.= "invitationyear=".(int)$invityear.", ";
	  }
	  if($party) {
	    $query.= "party=".(int)$party.", ";
	  }
	  if($pyear) {
	    $party_year=intval($pyear);
	    $query.= "party_year=".$party_year.", ";
	  }
	  if($prank) {
	    $query.= "party_place=".(int)$prank.", ";
	  }
	  if($compo) {
	    $query.= "partycompo=\"".$compo."\", ";
	  }
	  if ($csdb) $query.= "csdb=".((int)$csdb).", ";
	  if ($sceneorg) $query.= "sceneorg=".((int)$sceneorg).", ";
  	  if ($zxdemo) $query.= "zxdemo=".((int)$zxdemo).", ";
	  $query.= "added='".$_SESSION["SCENEID_ID"]."', ";
	  $query.= "quand=NOW()";
	  mysql_query($query) or die(mysql_error());
	  $lastid=mysql_insert_id();
	  if($sshotfile) {
	    copy($sshotfile, "screenshots/".$lastid.$mytype);
	    unlink($sshotfile);
	  // reward the user
		  $query ="INSERT INTO screenshots SET ";
		  $query.="prod=".$lastid.",";
		  $query.="user=".$_SESSION["SCENEID_ID"].",";
		  $query.="added=NOW()";
		  mysql_query($query);
	  }
	  if($nfofile) {
	    copy($nfofile, "nfo/".$lastid.".nfo");
	    unlink($nfofile);
		  // reward the user
		  $query ="INSERT INTO nfos SET ";
		  $query.="prod=".$lastid.",";
		  $query.="user=".$_SESSION["SCENEID_ID"].",";
		  $query.="added=NOW()";
		  mysql_query($query);
	  }
	  for($i=0;$i<count($platform);$i++) {
	  	for($j=0;$j<count($platforms);$j++) {
	  		if ($platform[$i]==$platforms[$j]["name"]):
	  			$query="insert into prods_platforms set prods_platforms.prod='".$lastid."', prods_platforms.platform='".$platforms[$j]["id"]."'";
	  			mysql_query($query);			
	  		endif;
	  	}
  	  }
	}
	// update prod cache
	create_cache_module("latest_demos", "SELECT prods.id,prods.name,prods.type,prods.group1,prods.group2,prods.group3,prods.added,users.nickname,users.avatar FROM prods LEFT JOIN users ON users.id=prods.added ORDER BY prods.quand DESC LIMIT 50",1);
	create_cache_module("latest_released_prods", "SELECT prods.id,prods.name,prods.type,prods.group1,prods.group2,prods.group3 FROM prods ORDER BY prods.date DESC,prods.quand DESC LIMIT 50",1);
	if ($party) create_cache_module("latest_released_parties", "select distinct parties.name, parties.id, prods.party_year, COUNT(prods.party) as prodcount from parties right join prods on prods.party=parties.id where parties.id!=1024 group by prods.party,prods.party_year order by prods.date desc, prods.id desc limit 50",0);
	
	//get data for table
	$query="select platforms.name from prods_platforms, platforms where prods_platforms.prods='".$lastid."' and platforms.id=prods_platforms.platform";
	$result = mysql_query($query);
	while($tmp = mysql_fetch_array($result)) {
	  	 $platform[]=$tmp;
	}
}

// get data to build the page
$result = mysql_query("SELECT id,name FROM groups ORDER BY name ASC");
while($tmp = mysql_fetch_row($result)) {
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
if ((int)($_SESSION["SCENEID_ID"])==3254) {
  echo "<br/>you're not allowed to submit prods right now. sorry. come back later.<br/><br/>";
} else {
?>
<br />
<form action="submitprod.php" method="post" enctype="multipart/form-data" name="submitprod">
<table bgcolor="#000000" cellspacing="1" cellpadding="0"><tr><td>
<table bgcolor="#000000" cellspacing="1" cellpadding="2">
<? if($submitok): ?>
 <tr><th bgcolor="#224488">this prod has been added</th></tr>
 <tr>
  <td bgcolor="#446688" align="center">
   feel free to add another one<br />
   <a href="prod.php?which=<? print($lastid); ?>">see what you've done</a><br />
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
	 <td><input type="text" name="name" value="<? print($name); ?>" size="50"><br ></td>
	</tr>

	<tr>
	 <td>group id:</td>
	 <td><input type="text" name="group1" value="<? print($group1); ?>" size="5">
<script language="JavaScript" type="text/javascript">
<!--
	 document.write("(<a href=\"javascript:popupGroupSelector('submitprod','group1');\">select</a>)");
//-->
</script>	 
	 </td>
	</tr>
	<tr>
	 <td>other group id:</td>
	 <td><input type="text" name="group2" value="<? print($group2); ?>" size="5">
<script language="JavaScript" type="text/javascript">
<!--
	 document.write("(<a href=\"javascript:popupGroupSelector('submitprod','group2');\">select</a>)");
//-->
</script>	 
	 </td>
	</tr>
	<tr>
	 <td>third group id:</td>
	 <td><input type="text" name="group3" value="<? print($group3); ?>" size="5">
<script language="JavaScript" type="text/javascript">
<!--
	 document.write("(<a href=\"javascript:popupGroupSelector('submitprod','group3');\">select</a>)");
//-->
</script>	 
	 </td>
	</tr>

	<tr>
	 <td><br /></td>
	 <td>
	  please make sure you enter correct id numbers for the groups..<br />
	  you can verify their id numbers on <a href="submitgroup.php" target=_blank>the same place where you submit new groups</a><br />
	  don't be lazy to double check them or the lobster will bite your nipples off! (\/) -_- (\/)<br />
	 </td>
	</tr>
	<tr>
	 <td>download url:</td>
	 <td><input type="text" name="download" value="<? print($download); ?>" size=60><br /></td>
	</tr>
	<tr>
	 <td><br /></td>
	 <td>
	  if there are extra download links you wanted to add, <a href="topic.php?which=1024">ask a gloperator</a><br />
	 </td>
	</tr>
	<tr>
	 <td><br /></td>
	 <td>
	  the download link <b>must point directly to the file</b>, or else it is considered invalid and risks being deleted.
	  (scene.org is an exception.)
	 </td>
	</tr>
	<tr>
	 <td>csdb id:</td>
	 <td><input type="text" name="csdb" value="<? print($csdb); ?>" size="5"><br /></td>
	</tr>
	<tr>
	 <td>sceneorg id:</td>
	 <td><input type="text" name="sceneorg" value="<? print($sceneorg); ?>" size="5"><br /></td>
	</tr>
	<tr>
	 <td>zxdemo id:</td>
	 <td><input type="text" name="zxdemo" value="<? print($zxdemo); ?>" size="5"><br /></td>
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
	 <td><br /></td>
	 <td>
	  if it's an artpack or musicdisk without GUI/viewer, _DON'T ADD IT_<br />
	  if it's not demoscene related, _DON'T ADD IT_<br />
	  if you have doubts about it beeing demoscene related, _DON'T ADD IT_<br />
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
	 <td>party:</td>
	 <td>
	  <select name="party">
    <option></option>
	  <?
		for($i=0;$i<count($parties);$i++) {
		  if($party==$parties[$i]["id"] || (!$party && $parties[$i]["id"]==1024)) {
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
	 <td><br /></td>
	 <td>
	  if you're sure it was released at a party but don't know which one choose the blank option instead of __no_party__<br />
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
	 <td><br /></td>
	 <td>
	  97: disqualified (delivered, maybe shown, disqualified, still released)<br />
	  98: not appliable (votedisks, invitations, wilds not in compos, musicdisks, diskmags, etc)<br />
	  99: not shown (delivered for compo, not shown, not disqualified, still released)<br />
	 </td>
	</tr>
	<tr>
	 <td>partycompo:</td>
	 <td>
	  <select name="compo">
	   <option></option>
	  <?
		for($i=1;$i<count($compos);$i+=2) {
		  if($compo==$compos[$i]) {
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
	<tr>
	 <td><br /></td>
	 <td>
	  if you're unsure of the correct compo, don't choose one!<br />
	  leave it for the experts, you won't loose a glop!<br />
	 </td>
	</tr>
	


	<tr>
	 <td>invitation for party/bbs:</td>
	 <td><input type="text" name="invit" value="<? print($invit); ?>" size="5"><br /></td>
	 </td>
	</tr>
	<tr>
	 <td><br /></td>
	 <td>
	  id number, not the name.<br />
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
	  <b>NOT</b> the year when it was released!
	 </td>
	</tr>
	
	
	<tr>
	 <td>infofile:</td>
	 <td><input type="file" name="nfofile" value="<? print($nfofile); ?>"size="50"><br /></td>
	</tr>
	<tr>
	 <td><br /></td>
	 <td>
	  upload the related <b>.diz</b> or <b>.nfo</b> with most usefull information<br />
	  size limit is 32kb<br />
	 </td>
	</tr>
	<tr>
	 <td>screenshot:</td>
	 <td><input type="file" name="sshotfile" value="<? print($sshotfile); ?>" size="50"><br /></td>
	</tr>
	<tr>
	 <td><br /></td>
	 <td>
	  <b>.gif</b> <b>.jpg</b> or <b>.png</b><br />
	  width and height limit are <b>400</b>x<b>300</b><br />
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
<?
}
require("include/bottom.php");
?>
