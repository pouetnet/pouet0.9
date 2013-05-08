<?
require("include/top.php");

unset($submitok);

if(!$which || !$what)
	$errormessage[] = "no prod from other party selected o_O";

// check the submitted data
if($party || $pyear || $prank || ($ryear))
{

  // check user account
  if(!isset($_SESSION['SESSION']))
	$errormessage[]="you need to be logged in first.";
  // check if this prod exists
  $query = "SELECT count(0) FROM prods WHERE id=".$which;
  $result = mysql_query($query);
  if(!mysql_result($result,0))
    $errormessage[] = "I can't find this prod o_O";

  // if everything is ok
  if(!$errormessage)
    $submitok=true;
}

// update the prod with the submitted info
if($submitok){
  $query = "UPDATE prodotherparty SET ";
  if($pyear) {
    $party_year=intval($pyear);
    $query.= "party_year=".$party_year.", ";
  } else {
    $query.= "party_year=NULL, ";
  }
  if($compo) {
    $query.= "partycompo=\"".$compo."\", ";
  } else {
    $query.= "partycompo=NULL, ";
  }
  if($prank) {
    $query.= "party_place=".$prank." ";
  } else {
    $query.= "party_place=NULL ";
  }
  $query.='WHERE prod='.$which.' and party='.$what.' LIMIT 1';
  mysql_query($query);
}

$result = mysql_query("DESC prodotherparty partycompo");
$row = mysql_fetch_row($result);
$compos = explode("'",$row[1]);

?>
<br>
<form action="submitotherpartyinfo.php" method="post">
<table bgcolor="#000000" cellspacing="1" cellpadding="0"><tr><td>
<table bgcolor="#000000" cellspacing="1" cellpadding="2">
<? if($submitok): ?>
 <tr><th bgcolor="#224488">the other party information has been added</th></tr>
 <tr>
  <td bgcolor="#446688" align="center">
   feel free to complete another one<br>
   <a href="prod.php?which=<? print($which); ?>">see what you've done</a><br>
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
<?if($which):?>
 <tr>
  <th bgcolor="#224488">prod information</th>
 </tr>
 <tr>
  <td bgcolor="#446688">
   <table>
	<?
	$yayz = false;
  if (($SESSION_LEVEL=='administrator' || $SESSION_LEVEL=='moderator' || $SESSION_LEVEL=='gloperator'))
    $yayz = true;

	// build the form
	$query='SELECT prod,party,party_year,party_place,partycompo FROM prodotherparty WHERE prod='.$which.' AND party='.$what;
	$result=mysql_query($query);
	$prod = mysql_fetch_assoc($result);
	$party = $prod['party'];
	if(isset($prod['party_year']))
	{
		$pyear = $prod['party_year'];
	}
	//print("->".$prod['party_year']."<->".$pyear."<-");
	$prank = $prod['party_place'];
	$compo = $prod['partycompo'];
	?>
    <tr>
     <td>the prod:</td>
     <td valign="bottom">
	  <input type="hidden" name="which" value="<?=$which?>">
	  <input type="hidden" name="what" value="<?=$what?>">
	  <a href="prod.php?which=<?=$which?>"><b><?=$which?></b></a> from
	  <a href="party.php?which=<?=$what?>"><b><?=$what?></b></a>
     </td>
    </tr>

	<tr>
	 <td>party year:</td>
	 <td>
	 <? if(!$yayz && $pyear): ?>
	 <input type="hidden" name="pyear" value="<?=$pyear?>">
	 <b><?=$pyear?></b>
	 <? else: ?>
	  <select name="pyear">
	   <option></option>
	   <?
	   for($i=date("Y");$i>=1980;$i--) {
		 ($pyear==$i) ? $sel=" selected" : $sel="";
		 print("<option".$sel.">".$i."</option>\n");
	   }
	   ?>
	  </select>
	  <br>
	  <? endif; ?>
	 </td>
	</tr>
	<tr>
	 <td>party rank:</td>
	 <td>
	 <? if(!$yayz && $prank): ?>
 	<input type="hidden" name="prank" value="<?=$prank?>">
	 <b><?=$prank?></b>
	 <? else: ?>
	  <select name="prank">
	   <option></option>
	   <?
	   for($i=1;$i<=99;$i++) {
		 ($prank==$i) ? $sel=" selected" : $sel="";
		 print("<option".$sel.">".$i."</option>\n");
	   }
	   ?>
	  </select>
	  <br>
	  </td></tr>
	  <tr><td></td><td>
	  97: disqualified (delivered, maybe shown, disqualified, still released)<br>
	  98: not appliable (votedisks, invitations, wilds not in compos, musicdisks, diskmags, etc)<br>
	  99: not shown (delivered for compo, not shown, not disqualified, still released)<br>
	  <? endif; ?>
	 </td>
	</tr>

	<tr>
	 <td>party compo:</td>
	 <td>
	 <? if(!$yayz && $compo): ?>
	 <input type="hidden" name="compo" value="<?=$compo?>">
	 <b><?=$compo?></b>
	 <? else: ?>
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
	  </tr>
	  <tr><td></td><td>
	  leave it blank if you don't know for sure what compo you should choose.<br>
	  <? endif; ?>
	 </td>
	</tr>

   </table>
  </td>
 </tr>
 <tr>
  <td bgcolor="#224488" align="right"><input type="image" src="gfx/submit.gif" style="border: 0px"></td>
 </tr>
<? endif; ?>
</table>
</td></tr></table>
</form>
<br />
<? require("include/bottom.php"); ?>
