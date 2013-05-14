<?
require("include/top.php");

if ($SESSION_LEVEL=='administrator' || $SESSION_LEVEL=='moderator'):

if ($id && $action=='delete')
{
	$query = "DELETE FROM bbses WHERE id=$id";
	mysql_query($query);
	$query = "DELETE FROM affiliatedbbses WHERE bbs=$id";
	mysql_query($query);
	
	$query="SELECT * from othernfos WHERE type='bbs' and refid='$which'";
	$result = mysql_query($query);
	while($tmp = mysql_fetch_array($result)) {
  	 $bbsnfos[]=$tmp;
	}
	
	if ($bbsnfos):
	  for($i=0;$i<count($bbsnfos);$i++) {
	 	if(file_exists("../othernfo/".$bbsnfos[$i]['id'].".nfo"))
		 unlink("../othernfo/".$bbsnfos[$i]['id'].".nfo");
	  }
	
	  $query = "DELETE FROM othernfos WHERE type='bbs' and refid=$id";
	  mysql_query($query);
	endif;
	
	print("bbs $id deleted<br />\n");
}

?>

<p>DOUBLE CHECK THAT YOU'RE DELETING THE RIGHT BBS NUMBER BEFORE CLICKING!</p>
<p>THE LOBSTER WILL EAT YOUR CHILDREN IF YOU DONT!!</p>
<p>THIS IS THE ONE AND ONLY WARNING!!!</p>
<p>DOUBLE CHECK THE NUMBER!!!</p>

<form action="<?=basename($_SERVER['SCRIPT_FILENAME'])?>" method="post">
bbs ID
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


<? require("include/bottom.php"); ?>
