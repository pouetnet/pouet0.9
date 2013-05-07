<?
require("include/top.php");

unset($submitok);
// check the submitted data
if($sceneorgflag)
{
  // check user account
  if(!$_SESSION["SESSION"]||!$_SESSION["SCENEID"])
	$errormessage[]="you need to be logged in first.";
  // check if this prod exists
  $query = "SELECT count(0) FROM prods WHERE id=".$which;
  $result = mysql_query($query);
  if(!mysql_result($result,0))
    $errormessage[] = "The lobster ate this prod for lunch (\/) o_O (\/) *clack clack clack*";
  // check if sceneorg for this prod already exists
  $query = "SELECT sceneorg FROM prods WHERE id=".$which;
  $result = mysql_query($query);
  if(mysql_result($result,0)>0)
    $errormessage[] = "sceneorg for this prod already exists (\/) O_o (\/)";

  // if everything is ok
  if(!$errormessage)
    $submitok=true;
}

if($submitok){
  $query ="update prods set sceneorg=".((int)$sceneorgflag)." where id=".$which;
  mysql_query($query);
}

?>
<br>
<form action="submitprodsceneorg.php" method="post" enctype="multipart/form-data">
<INPUT TYPE="hidden" name="MAX_FILE_SIZE" value="65536">
<table bgcolor="#000000" cellspacing="1" cellpadding="0"><tr><td>
<table bgcolor="#000000" cellspacing="1" cellpadding="2">
<? if($submitok): ?>
 <tr><th bgcolor="#224488">sceneorg id for prod submited</th></tr>
 <tr>
  <td bgcolor="#446688" align="center">
   <!--feel free to add another one<br>-->
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
	$query="SELECT id,name FROM prods where sceneorg=0 and platform='Commodore 64' ORDER BY name";
	$result=mysql_query($query);
	while($tmp=mysql_fetch_array($result)) {
		$prods[]=$tmp;
	}
	?>
	 <td>select the prod you want<br>to add sceneorg id for:</td>
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
     <td>sceneorg id:</td>
     <td><input type="text" name="sceneorgflag" value="<? print($sceneorgflag); ?>"><br></td>
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
