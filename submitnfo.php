<?
require("include/top.php");

unset($submitok);
// check the submitted data
if(is_uploaded_file($nfofile))
{

  // check user account
  if(!$_SESSION["SESSION"]||!$_SESSION["SCENEID"])
	$errormessage[]="you need to be logged in first.";
  // check if there is already an nfo file for thid prod
  if(file_exists("nfo/".$which.".nfo"))
  {
    if($SESSION_LEVEL=='administrator' || $SESSION_LEVEL=='moderator' || $SESSION_LEVEL=='gloperator')
    {
    	unlink("nfo/".$which.".nfo");
    	copy($nfofile,"nfo/".$which.".nfo");
  	unlink($nfofile);
  	$errormessage[]="there was already an infofile for this prod but since you're so strong and handsome i allowed you to caress my tail and replace it.";
    } else {
    $errormessage[]="there is already an nfo file for this prod";
    }
  }
  // check if this prod exists
  $query = "SELECT count(0) FROM prods WHERE id=".$which;
  $result = mysql_query($query);
  if(!mysql_result($result,0))
    $errormessage[] = "I can't find this prod o_O";
  // check the nfo file
  if(filesize($nfofile)>32768) {
    $errormessage[]="the size of the infofile must not be greater than 32Kb";
  }

  // if everything is ok
  if(!$errormessage)
    $submitok=true;
}

if(!$submitok&&file_exists($nfofile)){
  unlink($nfofile);
}

// move the submitted nfo to the appropriate location
if($submitok){
  copy($nfofile, "nfo/".$which.".nfo");
  unlink($nfofile);
  // reward the user
  $query ="INSERT INTO nfos SET ";
  $query.="prod=".$which.",";
  $query.="user=".$_SESSION["SCENEID_ID"].",";
  $query.="added=NOW()";
  mysql_query($query);
}

?>
<br>
<form action="submitnfo.php" method="post" enctype="multipart/form-data">
<INPUT TYPE="hidden" name="MAX_FILE_SIZE" value="65536">
<table bgcolor="#000000" cellspacing="1" cellpadding="0"><tr><td>
<table bgcolor="#000000" cellspacing="1" cellpadding="2">
<? if($submitok): ?>
 <tr><th bgcolor="#224488">the infofile has been added</th></tr>
 <tr>
  <td bgcolor="#446688" align="center">
   <!--feel free to add another one<br>-->
   <a href="nfo.php?which=<? print($which); ?>">see what you've done</a><br>
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
  <th bgcolor="#224488">prod information</th>
 </tr>
 <tr>
  <td bgcolor="#446688">
   <table>
    <tr>
	<? if($which):
	$query="SELECT name FROM prods WHERE id=".$which;
	$result=mysql_query($query);
	$prod_name=mysql_result($result,0);
	?>
	 <input type="hidden" name="which" value="<?=$which?>">
	 <td>prod:</td>
     <td valign="bottom">
	 <a href="prod.php?which=<?=$which?>"><?=$prod_name?></a>
     </td>
	<? else:
	// build the form
	$query="SELECT id,name FROM prods ORDER BY name";
	$result=mysql_query($query);
	$i=0;
	while($tmp=mysql_fetch_array($result)) {
	  if(!file_exists("nfo/".$tmp["id"].".nfo")) {
		$prods[$i]["id"]=$tmp["id"];
		$prods[$i]["name"]=$tmp["name"];
		$i++;
	  }
	}
	?>
	 <td>select the prod you want<br>to upload an infofile for:</td>
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
        <option value="<? print($prods[$i]["id"]); ?>"<? print($is_selected); ?>><? print($prods[$i]["name"]); ?></option>
       <? endfor; ?>
      </select>
     </td>
	 <? endif; ?>
    </tr>
    <tr>
     <td>infofile:</td>
     <td><input type="file" name="nfofile" size="60"></td>
    </tr>
    <tr>
     <td colspan="2" align="center">
      the infofile is usually a <b>.diz</b> or <b>.nfo</b> ascii textfile<br>
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
