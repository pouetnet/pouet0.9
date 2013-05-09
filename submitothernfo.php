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
  if(file_exists("othernfo/".$which.".nfo"))
  {
    if($SESSION_LEVEL=='administrator' || $SESSION_LEVEL=='moderator' || $SESSION_LEVEL=='gloperator')
    {
    	unlink("othernfo/".$which.".nfo");
    	copy($nfofile,"othernfo/".$which.".nfo");
  	unlink($nfofile);
  	$errormessage[]="there was already an infofile for this prod but since you're so strong and handsome i allowed you to caress my tail and replace it.";
    } else {
    $errormessage[]="there is already an nfo file for this prod";
    }
  }
  // check the nfo file
  if(filesize($nfofile)>65536) {
    $errormessage[]="the size of the infofile must not be greater than 64Kb";
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
  // reward the user
  $query ="INSERT INTO othernfos SET ";
  $query.="refid=".$refid.",";
  $query.="type='".$type."',";
  $query.="adder=".$_SESSION["SCENEID_ID"].",";
  $query.="added=NOW()";
  //print("->".$query."<-");
  mysql_query($query);
  $lastid=mysql_insert_id();
  copy($nfofile, "othernfo/".$lastid.".nfo");
  unlink($nfofile);
  $which=$lastid;
}

// get data to build the page
$result = mysql_query("DESC othernfos type");
$row = mysql_fetch_row($result);
$types = explode("'",$row[1]);

?>
<br>
<form action="submitothernfo.php" method="post" enctype="multipart/form-data">
<INPUT TYPE="hidden" name="MAX_FILE_SIZE" value="65536">
<table bgcolor="#000000" cellspacing="1" cellpadding="0"><tr><td>
<table bgcolor="#000000" cellspacing="1" cellpadding="2">
<? if($submitok): ?>
 <tr><th bgcolor="#224488">the infofile has been added</th></tr>
 <tr>
  <td bgcolor="#446688" align="center">
   <!--feel free to add another one<br>-->
   <a href="othernfo.php?which=<? print($which); ?>">see what you've done</a><br>
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
  <th bgcolor="#224488">other infofiles</th>
 </tr>
 <tr>
  <td bgcolor="#446688">
   <table>
    	<tr>
	 <td>ref id:</td>
	 <td><input type="text" name="refid" value="<? print($refid); ?>"><br /></td>
	</tr>
	<tr>
	 <td>type:</td>
	 <td>
	  <select name="type">
		<option value="0"></option>
	   <?
		for($i=1;$i<count($types);$i+=2) {
		  print("<option value=\"".$types[$i]."\"");
		  if ($type==$types[$i]) print(" selected");
		  print(">".strtolower($types[$i])."</option>\n");
		}
	   ?>
	  </select>
	 </td>
	</tr>
    <tr>
     <td>infofile:</td>
     <td><input type="file" name="nfofile" size="70"></td>
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
