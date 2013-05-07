<?
require("include/top.php");

unset($submitok);

if(!$which)
	$errormessage[] = "no prod selected o_O";

// check the submitted data
if($party || $pyear || $prank || ($ryear))
{

  // check user account
  if(!session_is_registered("SESSION"))
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
  $query = "UPDATE prods SET ";
  if(($rmonth)&&($ryear)) {
    $query.= "date='".$ryear."-".$rmonth."-15', ";
  } else {
  	if($ryear) {
  		$query.= "date='".$ryear."-00-15', ";
  	}
  }
  if($party) {
    $query.= "party=".$party.", ";
  } else {
    $query.= "party=0, ";
  }
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
  $query.='WHERE id='.$which.' LIMIT 1';
  mysql_query($query);
}

$result = mysql_query("DESC prods partycompo");
$row = mysql_fetch_row($result);
$compos = explode("'",$row[1]);

?>
<br>
<form action="submitpartyinfo.php" method="post">
<table bgcolor="#000000" cellspacing="1" cellpadding="0"><tr><td>
<table bgcolor="#000000" cellspacing="1" cellpadding="2">
<? if($submitok): ?>
 <tr><th bgcolor="#224488">the party information has been added</th></tr>
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
	// build the form
	$query='SELECT name,date,party,party_year,party_place,partycompo FROM prods WHERE id='.$which;
	$result=mysql_query($query);
	$prod = mysql_fetch_assoc($result);
	$rmonth = sprintf('%d', substr($prod['date'], 5, 2));
	$ryear = substr($prod['date'], 0, 4);
	$party = $prod['party'];
	if(isset($prod['party_year']))
	{
		$pyear = $prod['party_year'];
	}
	$prank = $prod['party_place'];
	$compo = $prod['partycompo'];
	?>
    <tr>
     <td>the prod:</td>
     <td valign="bottom">
	  <input type="hidden" name="which" value="<?=$which?>">
	  <a href="prod.php?which=<?=$which?>"><b><?=$prod['name']?></b></a>
     </td>
    </tr>
	<? // endif; ?>
<? 
$result = mysql_query("SELECT * FROM parties ORDER BY name");
while($tmp=mysql_fetch_array($result)){
  $parties[]=$tmp;
} 
?>
	<tr>
	<td>release date:</td>
	<td>
         <? if(($rmonth)&&($ryear)):
          switch($rmonth) {
            case "01": $rmonth="January"; break;
            case "02": $rmonth="February"; break;
            case "03": $rmonth="March"; break;
            case "04": $rmonth="April"; break;
            case "05": $rmonth="May"; break;
            case "06": $rmonth="June"; break;
            case "07": $rmonth="July"; break;
            case "08": $rmonth="August"; break;
            case "09": $rmonth="September"; break;
            case "10": $rmonth="October"; break;
            case "11": $rmonth="November"; break;
            case "12": $rmonth="December"; break;
          }
         ?>
         <input type="hidden" name="date" value="<? print($rmonth." ".$ryear); ?>">
         <b><? print($rmonth." ".$ryear); ?></b>
         <? else: ?>
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
	 <? endif; ?>
	</td>
        </tr>
      	<tr>
	 <td>party:</td>
	 <td>
	 <? if($party):
	 $query = 'SELECT name FROM parties WHERE id='.$party;
	 $result = mysql_query($query);
	 $party_name = mysql_result($result,0);
	 ?>
	 <input type="hidden" name="party" value="<?=$party?>">
	 <b><a href="party.php?which=<?=$party?>&when=<?=$pyear?>"><?=$party_name?></a></b>
	 <? else: ?>
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
	  </tr>
	  <tr><td></td><td>
	  leave it blank if you don't know for sure what party it was released at.<br>
	  choose __no_party__ option if you're _sure_ that it wasn't released at a party.<br>
	  <? endif; ?>
	 </td>
	</tr>
	
	<tr>
	 <td>party year:</td>
	 <td>
	 <? if($pyear): ?>
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
	 <? if($prank): ?>
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
	 <? if($compo): ?>
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
	  choose "no compo" option only if you're _sure_ that it wasn't released under a compo.<br>
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
