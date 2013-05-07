<?
require("include/top.php");

if($whichwhen)
{
	list($which,$when) = explode("|",$whichwhen);
}

$when = intval($when);
if ($when < 50) {
	$when += 2000;
} elseif ($when < 100) {
	$when += 1900;
}
unset($submitok);
// check the submitted data
if($slengpungflag)
{
  // check user account
  if(!$_SESSION["SESSION"]||!$_SESSION["SCENEID"])
	$errormessage[]="you need to be logged in first.";
  // check if this event exists
  $query = "SELECT count(0) FROM prods WHERE party=".$which." AND party_year=".$when;
  $result = mysql_query($query);
  if(!mysql_result($result,0))
    $errormessage[] = "Try with an event that actually took place will yah? (\/) o_O (\/) *clack*";
  // check if slengpung for this prod already exists
  $query = "SELECT slengpung FROM partylinks WHERE party=".$which." and year=".$when;
  $result = mysql_query($query);
  if(mysql_result($result,0)>0)
    $errormessage[] = "slengpung for this event already exists! (\/) O_o (\/)";

  // if everything is ok
  if(!$errormessage)
    $submitok=true;
}

if($submitok){
  $query = "SELECT id FROM partylinks WHERE party=".$which." and year=".$when;
  $result = mysql_query($query);
  $thisid = mysql_result($result,0);
  if($thisid)
  {
  	$query ="UPDATE partylinks SET slengpung='".$slengpungflag."' where id=".$thisid;
  	mysql_query($query);
  }
  else
  {
	$query ="INSERT INTO partylinks SET party=".$which.", year=".$when.", slengpung='".$slengpungflag."'";
  	mysql_query($query);
  }
}

?>
<br>
<form action="submitpartyslengpung.php" method="post" enctype="multipart/form-data">
<INPUT TYPE="hidden" name="MAX_FILE_SIZE" value="65536">
<table bgcolor="#000000" cellspacing="1" cellpadding="0"><tr><td>
<table bgcolor="#000000" cellspacing="1" cellpadding="2">
<? if($submitok): ?>
 <tr><th bgcolor="#224488">slengpung id for event submited</th></tr>
 <tr>
  <td bgcolor="#446688" align="center">
   <!--feel free to add another one<br>-->
   <a href="party.php?which=<? print($which); ?>&when=<? print($when); ?>">see what you've done</a><br>
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
<? if(!$submitok): ?>
 <tr>
  <th bgcolor="#224488">event information</th>
 </tr>
 <tr>
  <td bgcolor="#446688">
   <table>
    <tr>
	<? if($which):
	$query="SELECT name FROM parties WHERE id=".$which;
	$result=mysql_query($query);
	$party_name=mysql_result($result,0);
	?>
	 <input type="hidden" name="which" value="<?=$which?>">
	 <input type="hidden" name="when" value="<?=$when?>">
	 <td>prod:</td>
     <td valign="bottom">
	 <a href="party.php?which=<?=$which?>&when=<? print($when); ?>"><?=$party_name?>&nbsp;<?=$when?></a>
     </td>
	<? else:
	// build the form
	$query="select distinct prods.party_year,parties.name from parties left join prods on parties.id = prods.party left join partylinks on (partylinks.party=parties.id and partylinks.year=prods.party_year) where (partylinks.slengpung=0 or partylinks.slengpung is null) and parties.id!=1024 order by parties.name";
	$result=mysql_query($query);
	while($tmp=mysql_fetch_array($result)) {
		$prods[]=$tmp;
	}
	?>
	 <td>select the event you want<br>to add slengpung id for:</td>
     <td valign="bottom">
      <select name="which">
       <? for($i=1;$i<count($prods);$i++): ?>
       <?
       if($which==$prods[$i]["id"]) {
         $is_selected = " selected";
       } else {
         $is_selected = "";
       }
       ?>
        <option value="<? print($prods[$i]["id"]."|".$prods[$i]["party_year"]); ?>"<? print($is_selected); ?>><? print($prods[$i]["name"]." ".sprintf("%02d",$prods[$i]["party_year"])); ?></option>
       <? endfor; ?>
      </select>
     </td>
	 <? endif; ?>
    </tr>
    <tr>
     <td>slengpung id:</td>
     <td><input type="text" name="slengpungflag" value="<? print($slengpungflag); ?>"><br></td>
    </tr>
    <tr>
     <td></td>
     <td>use id 12 if there is no slengpung page for this event,<br />will make the +slengpung on party.php disapear</td>
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
