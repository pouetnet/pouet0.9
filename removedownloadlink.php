<?
require("include/top.php");

if ($SESSION_LEVEL=='administrator' || $SESSION_LEVEL=='moderator' || $SESSION_LEVEL=='gloperator'):

if ($id && $action=='delete')
{
	$query = "DELETE FROM downloadlinks WHERE id=$id";
	mysql_query($query);

	print("<br />downloadlink $id deleted<br />hope you didn't fuck up!<br />");
}

?>

<form action="<?=basename($_SERVER['SCRIPT_FILENAME'])?>" method="post">
download link id
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
