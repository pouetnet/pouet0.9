<?
require("include/top.php");

if (($SESSION_LEVEL=='administrator' || $SESSION_LEVEL=='moderator' || $SESSION_LEVEL=='gloperator')):

unset($submitok);
// check the submitted data
if ($name)
{
  // check user account
  if(!$_SESSION["SESSION"]||!$_SESSION["SCENEID"])
	$errormessage[]="you need to be logged in first.";

  if(!($upkeeper>0))
	$errormessage[]="upkeeper must be a valid number";
  
  // if everything is ok
  if(!$errormessage)
    $submitok=true;
}

// insert the submitted prod
if($submitok){
	//double insert check
	$query="SELECT name FROM lists ORDER BY added DESC LIMIT 1";
	$result=mysql_query($query);
	$lastone=mysql_fetch_assoc($result);
	if($lastone["name"]!=$name)
	{
	  $query = "INSERT INTO lists SET ";
	  $query.= "lists.name='".$name."', ";
	  $query.= "lists.desc='".$desc."', ";
	  $query.= "upkeeper='".$upkeeper."', ";
	  $query.= "adder='".$_SESSION["SCENEID_ID"]."', ";
	  $query.= "added=NOW()";
	  mysql_query($query);
//	  print("pong->".$query);
	  $lastid=mysql_insert_id();
	}
	//print("ping");
}
?>

<br />
<form action="submitlist.php" method="post" enctype="multipart/form-data">
<table bgcolor="#000000" cellspacing="1" cellpadding="0"><tr><td>
<table bgcolor="#000000" cellspacing="1" cellpadding="2">
<? if($submitok): ?>
 <tr><th bgcolor="#224488">this list has been added</th></tr>
 <tr>
  <td bgcolor="#446688" align="center">
   feel free to add another one<br />
   <a href="lists.php?which=<? print($lastid); ?>">see what you've done</a><br />
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
  <th bgcolor="#224488">list information</th>
 </tr>
 <tr>
  <td bgcolor="#446688">
   <table>
	<tr>
	 <td>name:</td>
	 <td><input type="text" name="name" value="<? print($name); ?>"><br ></td>
	</tr>
	<tr>
	 <td>description:</td>
	 <td><input type="text" name="desc" value="<? print($desc); ?>"><br ></td>
	</tr>
	<tr>
	 <td>upkeeper id:</td>
	 <td><input type="text" name="upkeeper" value="<? print($upkeeper); ?>"><br ></td>
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

<? if(session_is_registered("SESSION")): ?>
<p>the lobster says "FUCK OFF!"</p>
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
   You need to be logged in to submit a list :: <a href="account.php">register here</a><br />
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


<br />

<? require("include/bottom.php"); ?>
