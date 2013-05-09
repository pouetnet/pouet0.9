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
if($dir)
{

  // check user account
  if(!$_SESSION["SESSION"]||!$_SESSION["SCENEID"])
	$errormessage[]="you need to be logged in first.";

  // check if there already exists a download directory
  $query = "SELECT download FROM partylinks WHERE party=".$which." and year=".$when;
  $result = mysql_query($query);
  if(mysql_result($result,0))
    $errormessage[] = "download dir for this party year already exists";

  // if everything is ok
  if(!$errormessage)
    $submitok=true;
}

// submit the info
if($submitok){
  $query = "SELECT id FROM partylinks WHERE party=".$which." and year=".$when;
  $result = mysql_query($query);
  $thisid = mysql_result($result,0);
  if($thisid)
  {
	$query ="UPDATE partylinks SET download='".$dir."' where id=".$thisid;
  	mysql_query($query);
  }
  else
  {
	$query ="INSERT INTO partylinks SET party=".$which.", year=".$when.", download='".$dir."'";
	mysql_query($query);
  }	
}

?>
<br>
<form action="submitpartylinks.php" method="post" enctype="multipart/form-data">
<INPUT TYPE="hidden" name="MAX_FILE_SIZE" value="65536">
<table bgcolor="#000000" cellspacing="1" cellpadding="0"><tr><td>
<table bgcolor="#000000" cellspacing="1" cellpadding="2">
<? if($submitok): ?>
 <tr><th bgcolor="#224488">the download link has been added</th></tr>
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
  <th bgcolor="#224488">party information</th>
 </tr>
 <tr>
  <td bgcolor="#446688">
   <table>
    <tr>
	<? if($which&&$when):
	$query="SELECT name FROM parties WHERE id=".$which;
	$result=mysql_query($query);
	$party_name=mysql_result($result,0);
	?>
	 <input type="hidden" name="which" value="<?=$which?>">
	 <input type="hidden" name="when" value="<?=$when?>">
	 <td>party:</td>
     <td valign="bottom">
	 <a href="party.php?which=<?=$which?>&when=<? print($when); ?>"><?=$party_name?>&nbsp;<?=$when?></a>
     </td>
	<? else:
	// build the form
	$query="select distinct prods.party_year,parties.id,parties.name from parties,prods left join partylinks on partylinks.party=prods.party and partylinks.year=prods.party_year where parties.id=prods.party and partylinks.download is NULL order by parties.name";
	$result=mysql_query($query);
	$i=0;
	while($tmp=mysql_fetch_array($result)) {
	  //if(!file_exists("results/".$tmp["id"]."_".sprintf("%02d",$tmp["party_year"]).".txt")) {
		$prods[$i]["id"]=$tmp["id"];
		$prods[$i]["name"]=$tmp["name"];
		$prods[$i]["party_year"]=$tmp["party_year"];
		$i++;
	  //}
	}
	?>
	 <td>select the party edition</td>
     <td valign="bottom">
      <select name="whichwhen">
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
     <td>url:</td>
     <td><input type="text" name="dir" value="<? print($dir); ?>" size="60"><br></td>
    </tr>
    <tr>
     <td colspan="2" align="center">
      only submit a directory when it's on a permanent location and contains a great majority of the releases<br>
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
