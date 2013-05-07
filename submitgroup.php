<?
require("include/top.php");

unset($submitok);
// check the submitted data
if($name)
{
  // check user account
  if(!$_SESSION["SESSION"]||!$_SESSION["SCENEID"])
	$errormessage[]="you need to be logged in first.";
  // check the group name
  $result=mysql_query("SELECT count(0) FROM groups WHERE name='".$name."'");
  if(mysql_result($result,0))
    $errormessage[]="a group with the same name is already in the database";
  // check the group website
  if($website)
  {
    $myurl=parse_url($website);
    if($myurl["scheme"]!="http")
      $errormessage[] = "only the http protocol is supported for the group website";
    if(strlen($myurl["host"])==0)
      $errormessage[] = "missing hostname in the download link";
  }
  // if everything is ok
  if(!$errormessage)
    $submitok=true;
}

// insert the submitted group
if($submitok){
  $query = "INSERT INTO groups SET ";
  $query.= "name='".$name."', ";
  $query.= "web='".$website."', ";
  $query.= "acronym='".$acronym."', ";
  if ($csdb) $query.= "csdb=".((int)$csdb).", ";
  if ($zxdemo) $query.= "zxdemo=".((int)$zxdemo).", ";
  $query.= "added='".$_SESSION["SCENEID_ID"]."', ";
  $query.= "quand=NOW()";
  mysql_query($query);
  $lastid=mysql_insert_id();
}

// build the form
$query="SELECT id,name FROM groups ORDER BY name";
$result=mysql_query($query);
while($tmp=mysql_fetch_array($result)) {
	$groups[]=$tmp;
}
?>
<br>
<form action="submitgroup.php" method="post">
<table bgcolor="#000000" cellspacing="1" cellpadding="0"><tr><td>
<table bgcolor="#000000" cellspacing="1" cellpadding="2">
<? if($submitok): ?>
 <tr><th bgcolor="#224488">this group has been added</th></tr>
 <tr>
  <td bgcolor="#446688" align="center">
   feel free to add another one<br>
   <a href="groups.php?which=<? print($lastid); ?>">see what you've done</a><br>
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
  <th bgcolor="#224488">group information</th>
 </tr>
 <tr>
  <td bgcolor="#446688">
   <table>
    <tr>
	 <td>check if the group isn't<br>already in this combobox:</td>
	 <td valign="bottom">
      <select>
       <? for($i=1;$i<count($groups);$i++): ?>
        <option><? print($groups[$i]["name"].' &lt;id:'.$groups[$i]["id"].'>'); ?></option>
       <? endfor; ?>
      </select>
	 </td>
	</tr>
	<tr>
	 <td>name:</td>
	 <td><input type="text" name="name" value="<? print($name); ?>" size="50"><br></td>
	</tr>
	<tr>
	 <td>acronym:</td>
	 <td><input type="text" name="acronym" value="<? print($acronym); ?>"><br></td>
	</tr>
	<tr>
	 <td>website:</td>
	 <td><input type="text" name="website" value="<? print($website); ?>" size="50"><br></td>
	</tr>
	<tr>
	 <td>csdb id:</td>
	 <td><input type="text" name="csdb" value="<? print($csdb); ?>" size="5"><br></td>
	</tr>
	<tr>
	 <td>zxdemo id:</td>
	 <td><input type="text" name="zxdemo" value="<? print($zxdemo); ?>" size="5"><br></td>
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
