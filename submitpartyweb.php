<?
require("include/top.php");

unset($submitok);
// check the submitted data
if($web)
{
  // check user account
  if(!$_SESSION["SESSION"]||!$_SESSION["SCENEID"])
	$errormessage[]="you need to be logged in first.";
  // check if this party exists
  $query = "SELECT count(0) FROM parties WHERE id=".$which;
  $result = mysql_query($query);
  if(!mysql_result($result,0))
    $errormessage[] = "Lobster has eaten this party. Find it's dupe instead will yah? (\/) o_O (\/) *clack*";
  // check if it already has web url..
  $query = "SELECT web FROM parties WHERE id=".$which;
  $result = mysql_query($query);
  if(mysql_result($result,0))
    $errormessage[] = "This party already has a webpage associated. Ask a pouet admin to change the url if thats what you were trying to do.";
  // if everything is ok
  if(!$errormessage)
    $submitok=true;
}


if($submitok){
  $query ="UPDATE parties SET web='".$web."' WHERE id=".$which;
  mysql_query($query);
}

?>
<br>
<form action="submitpartyweb.php" method="post" enctype="multipart/form-data">
<table bgcolor="#000000" cellspacing="1" cellpadding="0"><tr><td>
<table bgcolor="#000000" cellspacing="1" cellpadding="2">
<? if($submitok): ?>
 <tr><th bgcolor="#224488">the party web has been added</th></tr>
 <tr>
  <td bgcolor="#446688" align="center">
   <!--feel free to add another one<br>-->
   <a href="party.php?which=<? print($which); ?>">see what you've done</a><br>
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
	<? if($which):
	$query="SELECT name FROM parties WHERE id=".$which;
	$result=mysql_query($query);
	$party_name=mysql_result($result,0);
	?>
	 <input type="hidden" name="which" value="<?=$which?>">
	 <input type="hidden" name="when" value="<?=$when?>">
	 <td>party:</td>
     <td valign="bottom">
	 <a href="party.php?which=<?=$which?>"><?=$party_name?></a>
     </td>
	<? else:
	// build the form
	$query="SELECT id,name,web FROM parties ORDER BY name";
	$result=mysql_query($query);
	$i=0;
	while($tmp=mysql_fetch_array($result)) {
	  if(!$tmp["web"]) {
		$parties[$i]["id"]=$tmp["id"];
		$parties[$i]["name"]=$tmp["name"];
		$i++;
	  }
	}
	?>
	 <td>select the party you want<br>to update a web url for:</td>
     <td valign="bottom">
      <select name="which">
       <? for($i=1;$i<count($parties);$i++): ?>
       <?
       if($which==$parties[$i]["id"]) {
         $is_selected = " selected";
       } else {
         $is_selected = "";
       }
       ?>
        <option value="<? print($parties[$i]["id"]); ?>"<? print($is_selected); ?>><? print($parties[$i]["name"]); ?></option>
       <? endfor; ?>
      </select>
     </td>
	 <? endif; ?>
    </tr>
    <tr>
     <td>web url:</td>
     <td><input type="text" name="web" value="<? print($web); ?>" size="60"><br></td>
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
