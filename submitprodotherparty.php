<?
require("include/top.php");

if (($SESSION_LEVEL=='administrator' || $SESSION_LEVEL=='moderator' || $SESSION_LEVEL=='gloperator')):

unset($submitok);
// check the submitted data
if($prod && $party)
{

  if($party==1024) {
    $errormessage[] = "you cant choose __no_party__ for prod other party association";
  }

  if(!$errormessage)
    $submitok=true;
}


// insert the submitted prod
if($submitok){
	  $query = "INSERT INTO prodotherparty SET ";
	  if($party) {
	    $query.= "party=".$party.", ";
	  }
	  if($pyear) {
	    $party_year=intval($pyear);
	    $query.= "party_year=".$party_year.", ";
	  }
	  if($prank) {
	    $query.= "party_place=".$prank.", ";
	  }
	  if($compo) {
	    $query.= "partycompo=\"".$compo."\", ";
	  }
	  $query.= "prod=".$prod;

	  mysql_query($query);
}

$result = mysql_query("DESC prods partycompo");
$row = mysql_fetch_row($result);
$compos = explode("'",$row[1]);

$result = mysql_query("SELECT * FROM parties ORDER BY name");
while($tmp=mysql_fetch_array($result)){
  $parties[]=$tmp;
}

?>
<br />
<form action="submitprodotherparty.php" method="post" enctype="multipart/form-data">
<table bgcolor="#000000" cellspacing="1" cellpadding="0"><tr><td>
<table bgcolor="#000000" cellspacing="1" cellpadding="2">
<? if($submitok): ?>
 <tr><th bgcolor="#224488">this prod other party has been added</th></tr>
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
  <th bgcolor="#224488">prod other party information</th>
 </tr>
 <tr>
  <td bgcolor="#446688">
   <table>
	<tr>
	 <td>prod:</td>
	 <td><input type="text" name="prod" value="<? print($prod); ?>"><br /></td>
	</tr>
	<tr>
	 <td>party:</td>
	 <td>
	  <select name="party">
    <option></option>
	  <?
		for($i=0;$i<count($parties);$i++) {
		  if($party==$parties[$i]["id"]) {
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
	  leave it blank if you don't know for sure what party it was released at.<br />
	  choose the __no_party__ option if you're _sure_ that it wasn't released at a party.<br />
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
   You need to be logged in to submit a prod :: <a href="account.php">register here</a><br />
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
