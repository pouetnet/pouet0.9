<?
require("include/top.php");

if($_SESSION["SESSION"]&&$_SESSION["SCENEID"]):

unset($submitok);
// check the submitted data
if($itemid)
{

	$query = "SELECT upkeeper FROM lists where id='".$which."'";
  	$result=mysql_query($query);
  	$upkeeper=mysql_result($result,0);

  if($_SESSION["SCENEID_ID"]!=$upkeeper && !($SESSION_LEVEL=='administrator' || $SESSION_LEVEL=='moderator' || $SESSION_LEVEL=='gloperator')) {
    $errormessage[] = "you're not the upkeeper of this list sonnyboy";
  }

  if(!$type) {
    $errormessage[] = "you must select a type";
  }

  if(!$errormessage)
    $submitok=true;
}


// insert the submitted prod
if($submitok){
	  $query = "INSERT INTO listitems SET ";
	  $query.= "listitems.list='".$which."', ";
	  $query.= "listitems.itemid='".$itemid."', ";
    	  $query.= "listitems.type='".$type."'";
//    	  print($query);
	  mysql_query($query);
}

// get data to build the page
$result = mysql_query("DESC listitems type");
$row = mysql_fetch_row($result);
$types = explode("'",$row[1]);

?>
<br />
<form action="submitlistitem.php" method="post" enctype="multipart/form-data">
<table bgcolor="#000000" cellspacing="1" cellpadding="0"><tr><td>
<table bgcolor="#000000" cellspacing="1" cellpadding="2">
<? if($submitok): ?>
 <tr><th bgcolor="#224488">this listitem has been added</th></tr>
 <tr>
  <td bgcolor="#446688" align="center">
   feel free to add another one<br />
   <a href="lists.php?which=<? print($which); ?>">see what you've done</a><br />
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
  <th bgcolor="#224488">list items</th>
 </tr>
 <tr>
  <td bgcolor="#446688">
   <table>
	<tr>
	 <td>list:</td>
	 <td><input type="hidden" name="which" value="<?=$which?>"/><?=$which?></td>
	</tr>
	<tr>
	 <td>itemid:</td>
	 <td><input type="text" name="itemid" value="<? print($itemid); ?>"><br /></td>
	</tr>
	<tr>
	 <td>type:</td>
	 <td>
	  <select name="type">
		<option value="0"></option>
	   <?
		for($i=1;$i<count($types);$i+=2) {
		  print("<option value=\"".$types[$i]."\">".strtolower($types[$i])."</option>\n");
		}
	   ?>
	  </select>
	 </td>
	</tr>
	<? //<tr>
	 //<td colspan="2">if there is a HQ type missing <a href="topic.php?which=1024">just inform us</a>, use plain HQ in the meantime..</td>
	//</tr>
	?>
   </table>
  </td>
 </tr>
 <tr>
  <td bgcolor="#224488" align="right"><input type="image" src="gfx/submit.gif" style="border: 0px"></td>
 </tr>
</table>
</td></tr></table>
</form>

<? else : ?>

<? if(isset($_SESSION['SESSION'])): ?>
<p>the lobster says "SOD OFF PUNK!"</p>
<p></p>
<p>you need special priveligies to access this page.</p>
<? else: ?>
<br />
<table width="20%"><tr><td>
<form action="login.php" method="post">
<table cellspacing="1" cellpadding="2" class="box">
 <tr><th>login</th></tr>
 <tr bgcolor="#446688">
  <td nowrap align="center">
   You need to be logged in to submit a listitem :: <a href="account.php">register here</a><br />
   <input type="text" name="login" value="SceneID" size="15" maxlength="16" onfocus="this.value=''">
   <input type="password" name="password" value="password" size="15" onfocus="javascript:if(this.value=='password') this.value='';"><br />
  </td>
 </tr>
 <tr>
  <td bgcolor="#6688AA" align="right">
   <input type="image" src="gfx/submit.gif">
  </td>
 </tr>
</table>
</form>
</td></tr></table>
<? endif; ?>

<? endif; ?>
<br />

<? require("include/bottom.php"); ?>
