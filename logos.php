<?
require("include/top.php");

if (isset($_SESSION['SESSION']) && $id && $vote) {
	$query = 'SELECT count(0) FROM logos_votes WHERE logo='.(int)$id.' AND user='.$_SESSION["SCENEID_ID"];
	$result = mysql_query($query);
	if (!mysql_result($result,0)) {
		if ($vote == rulez)
			$vote = 1;
		else
			$vote = -1;
		$query = 'INSERT INTO logos_votes SET logo='.(int)$id.', user='.$_SESSION['SCENEID_ID'].', vote='.$vote;
		mysql_query($query);
	}
}

// Lamas pictures displayed when there are no logo to vote for anymore
$lama_pictures = array(
	'logos-lamer.jpg',
	'logos-lama.jpg',
	'logos-dalai-lama.jpg');

// let's get all the logos + LIMIT 5
unset($tmp);
unset($logos);
$query = "SELECT logos.id,logos.file FROM logos LEFT JOIN logos_votes ON logos.id=logos_votes.logo AND logos_votes.user=" . $_SESSION["SCENEID_ID"] . " WHERE logos_votes.vote is NULL ORDER BY RAND() LIMIT 5";
$result = mysql_query($query);
while ($tmp = mysql_fetch_assoc($result))
	$logos[] = $tmp;
?>

<br>

<? if($_SESSION["SCENEID_ID"]): ?>



<?
$i = 0;
if (!empty($logos))
{
	foreach ($logos as $logo) { // we display each logo
?>

<form action="<?=basename($SCRIPT_FILENAME)?>" method="post">
<input type="hidden" name="id" value="<?=$logo['id']?>">
<table bgcolor="#000000" cellspacing="1" cellpadding="0">
 <tr>
  <td>
   <table bgcolor="#000000" cellspacing="1" cellpadding="2">
    <tr>
	 <td background="gfx/trumpet.gif" align="center">
	 <img src="gfx/logos/<?=$logo['file']?>" hspace="50" vspace="50"><br>
	 </td>
	</tr>
	<tr>
     <td bgcolor="#446688" align="right">
	 <input type="submit" name="vote" value="rulez">
	 <input type="submit" name="vote" value="sucks">
     </td>
    </tr>
   </table>
  </td>
 </tr>
</table>
</form>

<br />

<?
	}
}
else
{
?>
<table width="50%"><tr><td>
<table cellspacing="1" cellpadding="2" class="box">
 <tr><th>no logo left, you are now a l4m4h</th></tr>
 <tr bgcolor="#557799">
  <td nowrap align="center">
   <img src="gfx/<?=$lama_pictures[array_rand($lama_pictures)]?>" alt="Lamer picture">
  </td>
 </tr>
    <tr>
     <td bgcolor="#446688" align="center">
      <a href="/"><b>get back</b></a><br>
     </td>
    </tr>
</table>
</form>
</td></tr></table>
</td></tr></table>
<?
}
?>

<? else: ?>

<table width="50%"><tr><td>
<form action="login.php" method="post">
<table cellspacing="1" cellpadding="2" class="box">
 <tr><th>vote for the logos</th></tr>
 <tr bgcolor="#446688">
  <td nowrap align="center">
   You need to be logged in to vote for the logos :: <a href="account.php">register here</a><br>
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

<br>

<? require("include/bottom.php"); ?>
