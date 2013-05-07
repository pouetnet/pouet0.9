<?
require("include/top.php");

if ($SESSION_LEVEL=='administrator' || $SESSION_LEVEL=='moderator' || $SESSION_LEVEL=='gloperator'):

if ($_GET["which"])
{
	$query = "DELETE FROM sceneorgrecommended WHERE id=".$_GET["which"];
	mysql_query($query);
	
	print("<br />sceneorgrecommended deleted<br />hope you didn't fuck up!<br />");

  logGloperatorAction("remove_sceneorg",$_GET["which"]);
}

?>

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
