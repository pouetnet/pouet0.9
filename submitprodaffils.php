<?
require("include/top.php");

if (($SESSION_LEVEL=='administrator' || $SESSION_LEVEL=='moderator' || $SESSION_LEVEL=='gloperator')):

unset($submitok);
// check the submitted data
if($prod1)
{

  if(!$type) {
    $errormessage[] = "you must select a type";
  }

  if(!$errormessage)
    $submitok=true;
}


// insert the submitted prod
if($submitok){
	  $query = "INSERT INTO affiliatedprods SET ";
	  $query.= "derivative='".$prod1."', ";
	  $query.= "original='".$prod2."', ";
	  $query.= "type='".$type."'";
	  mysql_query($query);
}

// get data to build the page
$result = mysql_query("DESC affiliatedprods type");
$row = mysql_fetch_row($result);
$types = explode("'",$row[1]);

?>
<br />
<form action="submitprodaffils.php" method="post" enctype="multipart/form-data">
<table bgcolor="#000000" cellspacing="1" cellpadding="0"><tr><td>
<table bgcolor="#000000" cellspacing="1" cellpadding="2">
<? if($submitok): ?>
 <tr><th bgcolor="#224488">this affiliation has been added</th></tr>
 <tr>
  <td bgcolor="#446688" align="center">
   feel free to add another one<br />
   <a href="prod.php?which=<? print($prod1); ?>">see what you've done</a><br />
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
	 <td><input type="text" name="prod1" value="<?=$_GET["derivative"]?>"> (prod1)</td>
	 <td>is a </td>
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
	 <td>of</td>
	 <td><input type="text" name="prod2" value="<?=$_GET["original"]?>"> (prod2)</td>
	</tr>
	<tr>
	<td colspan="5" width="200">
	<b>WARNING ATTENTION CAUTION PENIS!</b>
	<br/>
	shit has changed.
	<br/>
	the affiliation is <i>BIDIRECTIONAL</i> now, which means that you don't have to add all prods twice.
	if you add a relation, it will appear on <b>BOTH</b> prodpages. (this is because
	every prod relation has it's exact opposite - i.e. if A is a remix of B, then B is the original of A,
	 and so on) IF SHIT BREAKS, TELL ME<br/>-garg
<!--
	the affiliation is unidirectional!<br />
	having prod1=n, prod2=m, type=remix, <br />
	on prod.php?which=n we'll have link to the remix m,<br />
	nothing will be shown on prod.php?which=m.<br />
	BE CAREFUL NOT TO ASSOCIATE INVERSE RELATIONS.<br />
	m is remix of n, not the other way around.<br />
	type=packed is an exception, it gets added oth ways<br />
-->
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

<? endif; ?>

<? endif; ?>
<br />

<? require("include/bottom.php"); ?>
