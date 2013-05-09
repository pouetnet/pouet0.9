<?
require("include/top.php");

unset($submitok);
// check the submitted data
//if($sshotfile&&($sshotfile!="none"))
if(is_uploaded_file($sshotfile))
{
  // check user account
  if(!$_SESSION["SESSION"]||!$_SESSION["SCENEID"])
	$errormessage[]="you need to be logged in first.";

  // check if this prod exists
  $query = "SELECT count(0) FROM prods WHERE id=".$which;
  $result = mysql_query($query);
  if(!mysql_result($result,0))
    $errormessage[] = "I can't find this prod o_O";

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

  // check if there is already a sshot for this prod
  if(file_exists("screenshots/".$which.".gif")||file_exists("screenshots/".$which.".jpg")||file_exists("screenshots/".$which.".png"))
  {
    if($SESSION_LEVEL=='administrator' || $SESSION_LEVEL=='moderator' || $SESSION_LEVEL=='gloperator')
    {
    	unlink("screenshots/".$which.".jpg");
    	unlink("screenshots/".$which.".gif");
    	unlink("screenshots/".$which.".png");
    	copy($sshotfile,"screenshots/".$which.$mytype);
  	unlink($sshotfile);
  	$errormessage[]="there was already a screenshot for this prod but since you're so leet and sexy i allowed you to lick my claw and replace it.";
    } else {
    	$errormessage[]="there is already a screenshot for this prod";
    }
  }

  // if everything is ok
  if(!$errormessage)
    $submitok=true;
}

// move the submitted sshot to the appropriate location
if(!$submitok&&file_exists($sshotfile)){
  unlink($sshotfile);
//  move_uploaded_file($sshotfile,"screenshots/".$which.$mytype);
//  copy($sshotfile, "screenshots/".$which.$mytype);
//  unlink($sshotfile);
}

if($submitok) {
  copy($sshotfile,"screenshots/".$which.$mytype);
  unlink($sshotfile);
  // reward the user
  $query ="INSERT INTO screenshots SET ";
  $query.="prod=".$which.",";
  $query.="user=".$_SESSION["SCENEID_ID"].",";
  $query.="added=NOW()";
  mysql_query($query);
}

?>
<br>
<form action="submitsshot.php" method="post" enctype="multipart/form-data">
<table bgcolor="#000000" cellspacing="1" cellpadding="0"><tr><td>
<table bgcolor="#000000" cellspacing="1" cellpadding="2">
<? if($submitok): ?>
 <tr><th bgcolor="#224488">the screenshot has been added</th></tr>
 <tr>
  <td bgcolor="#446688" align="center">
   feel free to add another one<br>
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
	  if(!file_exists("screenshots/".$tmp["id"].".gif")&&!file_exists("screenshots/".$tmp["id"].".jpg")&&!file_exists("screenshots/".$tmp["id"].".png")) {
		$prods[$i]["id"]=$tmp["id"];
		$prods[$i]["name"]=$tmp["name"];
		$i++;
	  }
	}
	?>
	<td>select the prod you want<br>to upload a screenshot for:</td>
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
     <td>screenshot:</td>
     <td><input type="file" name="sshotfile" size="60"></td>
    </tr>
    <tr>
     <td colspan="2" align="center">
      <b>.gif</b> <b>.jpg</b> or <b>.png</b> width and height limit are <b>400</b>x<b>300</b><br>
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
<? require("include/bottom.php"); ?>
