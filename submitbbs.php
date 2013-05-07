<?
require("include/top.php");

unset($submitok);
// check the submitted data
if($name)
{
  // check user account
  if(!$_SESSION["SESSION"]||!$_SESSION["SCENEID"])
	$errormessage[]="you need to be logged in first.";
  // check the release date
  if(($rmonth)&&(!$ryear)) {
    $errormessage[]="lobster no like month without year";
  }
  if(!count($platform)) {
    $errormessage[] = "you must select at least one platform";
  }
  // if everything is ok
  if(!$errormessage)
    $submitok=true;
}

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
	  $query = "INSERT INTO bbses SET ";
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
/*	  $query.= "platforms='";
	  if(count($platform)>0) {
	    $query.= $platform[0];
	  }
	  for($i=1;$i<count($platform);$i++) {
	    $query.=",".$platform[$i];
	  }
	  $query.= "', ";*/
	  if( $phonenumber) $query.= "phonenumber='".$phonenumber."', ";
	  if ($telnetip) $query.= "telnetip='".$telnetip."', ";
	  $query.= "adder='".$_SESSION["SCENEID_ID"]."', ";
	  $query.= "added=NOW()";
	  mysql_query($query);
	  $lastid=mysql_insert_id();
	  
	  for($i=0;$i<count($platform);$i++) {
	  	for($j=0;$j<count($platforms);$j++) {
	  		if ($platform[$i]==$platforms[$j]["name"]):
	  			$query="insert into bbses_platforms set bbses_platforms.bbs='".$lastid."', bbses_platforms.platform='".$platforms[$j]["id"]."'";
	  			mysql_query($query);			
	  		endif;
	  	}
  	  }
	}

	$query="select platforms.name from bbses_platforms, platforms where bbses_platforms.bbs='".$lastid."' and platforms.id=bbses_platforms.platform";
	$result = mysql_query($query);
	while($tmp = mysql_fetch_array($result)) {
	  	 $platform[]=$tmp;
	}
}



?>
<br />
<form action="submitbbs.php" method="post" enctype="multipart/form-data">
<table bgcolor="#000000" cellspacing="1" cellpadding="0"><tr><td>
<table bgcolor="#000000" cellspacing="1" cellpadding="2">
<? if($submitok): ?>
 <tr><th bgcolor="#224488">this bbs has been added</th></tr>
 <tr>
  <td bgcolor="#446688" align="center">
   feel free to add another one<br />
   <a href="bbses.php?which=<? print($lastid); ?>">see what you've done</a><br />
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
	 <td><input type="text" name="name" value="<? print($name); ?>"><br ></td>
	</tr>
	<tr>
	 <td>sysop:</td>
	 <td><input type="text" name="sysop" value="<? print($sysop); ?>"><br ></td>
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
			//print($platforms[$i]["name"]."\n".$platform[$j]."\n");
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
	 <td><input type="text" name="phonenumber" value="<? print($phonenumber); ?>"><br /></td>
	</tr>
	<tr>
	 <td colspan="2">follow standard: +countrycode-citycode-phonenumber eg: +7-095-391XXXX for a moscow bbs<br />
	 if it isnt demoscene related _DONT ADD IT_<br />
	 if it has illegal stuff on it and is still in service _DONT ADD IT_<br /> or atleast ask the sysop first, or dont state the actual numbers, be safe before sorry.<br />
	 on any cases unless you are/were the sysop, or have his/her permission, _dont disclose the last numbers_.<br /></td>
	</tr>
	<tr>
	 <td>telnet address:</td>
	 <td><input type="text" name="telnetip" value="<? print($telnetip); ?>"><br /></td>
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
<? require("include/bottom.php"); ?>
