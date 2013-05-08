<?
require("include/top.php");

if (($SESSION_LEVEL=='administrator' || $SESSION_LEVEL=='moderator' || $SESSION_LEVEL=='gloperator')):

unset($submitok);
// check the submitted data
if($prodid)
{

  if(!$type) {
    $errormessage[] = "you must select a type";
  }
  if(($type!="viewingtip") && (!$category)) {
    $errormessage[] = "you must select a category";
  }

  if(!$errormessage)
    $submitok=true;
}


// insert the submitted prod
if($submitok){
	  $query = "INSERT INTO sceneorgrecommended SET ";
	  $query.= "prodid='".$prodid."', ";
    	  $query.= "type='".$type."'";
	  if($category) {
	    $query.= ", category='".$category."'";
	  }
	  mysql_query($query);
	  $lastid=mysql_insert_id();

  logGloperatorAction("submit_sceneorg",$prodid);
}

// get data to build the page
$result = mysql_query("SELECT id,name FROM groups ORDER BY name ASC");
while($tmp = mysql_fetch_row($result)) {
  $groups[] = $tmp;
}
$result = mysql_query("DESC sceneorgrecommended type");
$row = mysql_fetch_row($result);
$types = explode("'",$row[1]);
$result = mysql_query("DESC sceneorgrecommended category");
$row = mysql_fetch_row($result);
$categorys = explode("'",$row[1]);

?>
<br />
<form action="submitsceneorgrecommended.php" method="post" enctype="multipart/form-data">
<table bgcolor="#000000" cellspacing="1" cellpadding="0"><tr><td>
<table bgcolor="#000000" cellspacing="1" cellpadding="2">
<? if($submitok): ?>
 <tr><th bgcolor="#224488">this prod has been added</th></tr>
 <tr>
  <td bgcolor="#446688" align="center">
   feel free to add another one<br />
   <a href="prod.php?which=<? print($prodid); ?>">see what you've done</a><br />
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
	 <td>prodid:</td>
	 <td><input type="text" name="prodid" value="<? print($prodid?$prodid:$_GET["which"]); ?>"><br /></td>
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
	<tr>
	 <td>category:</td>
	 <td>
	  <select name="category">
		<option value="0"></option>
	   <?
		for($i=1;$i<count($categorys);$i+=2) {
		  print("<option value=\"".$categorys[$i]."\">".strtolower($categorys[$i])."</option>\n");
		}
	   ?>
	  </select>
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

<? else : ?>

<? if(isset($_SESSION['SESSION'])): ?>
<p>the lobster says "NO ENTRANCE!"</p>
<p></p>
<p>you need god priveligies to access this place.</p>
<? else: ?>
<br />
<table width="20%"><tr><td>
<form action="login.php" method="post">
<table cellspacing="1" cellpadding="2" class="box">
 <tr><th>login</th></tr>
 <tr bgcolor="#446688">
  <td nowrap align="center">
   You need to be logged in to submit a prod :: <a href="account.php">register here</a><br />
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
<br />
<? endif; ?>

<? endif; ?>


<? require("include/bottom.php"); ?>
