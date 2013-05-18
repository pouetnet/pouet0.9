<?
require("include/top.php");

if ($SESSION_LEVEL=='administrator' || $SESSION_LEVEL=='moderator'):

if ($id && $action=='delete')
{
	$query = "DELETE FROM prods WHERE id=$id LIMIT 1";
	mysql_query($query);
	$query = "DELETE FROM downloadlinks WHERE prod=$id";
	mysql_query($query);
	$query = "DELETE FROM comments WHERE which=$id";
	mysql_query($query);
	$query = "DELETE FROM nfos WHERE prod=$id";
	mysql_query($query);
	$query = "DELETE FROM screenshots WHERE prod=$id";
	mysql_query($query);
	$query = "DELETE FROM prods_platforms WHERE prod=$id";
	mysql_query($query);
	$query = "DELETE FROM sceneorgrecommended WHERE prodid=$id";
	mysql_query($query);
	$query = "DELETE FROM users_cdcs WHERE cdc=$id";
	mysql_query($query);
	$query = "DELETE FROM affiliatedprods WHERE original=$id or derivative=$id";
	mysql_query($query);
	$query = "DELETE FROM prods_refs WHERE prod=$id";
	mysql_query($query);
	$query = "DELETE FROM prodotherparty WHERE prod=$id";
	mysql_query($query);
	$query = "DELETE FROM cdc WHERE which=$id";
	mysql_query($query);

	if(file_exists("../nfo/$id.nfo"))
		unlink("../nfo/$id.nfo");
	if(file_exists("../screenshots/$id.gif"))
		unlink("../screenshots/$id.gif");
	if(file_exists("../screenshots/$id.jpg"))
		unlink("../screenshots/$id.jpg");
	if(file_exists("../screenshots/$id.png"))
		unlink("../screenshots/$id.png");

	print("prod $id deleted<br />\n");
  logGloperatorAction("prod_del",$id);
}

?>

<p>DOUBLE CHECK THAT YOU'RE DELETING THE RIGHT PROD NUMBER BEFORE CLICKING!</p>
<p>THE LOBSTER WILL EAT YOUR CHILDREN IF YOU DONT!!</p>
<p>THIS IS THE ONE AND ONLY WARNING!!!</p>
<p>DOUBLE CHECK THE NUMBER!!!</p>

<form action="<?=basename($_SERVER['SCRIPT_FILENAME'])?>" method="post">
prod ID
<input type="text" name="id">
<input type="submit" name="action" value="delete">
</form>

<? else: ?>

<p>dude, that's a dangerous page...</p>

<form action="login.php" method="post">
<table cellspacing="1" cellpadding="2" class="box">
 <tr bgcolor="#446688">
  <td nowrap align="center">
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

<? endif; ?>
<br />

<? require("include/bottom.php"); ?>
