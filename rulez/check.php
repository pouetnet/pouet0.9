<?
session_start();
if (!($SESSION_LEVEL=='administrator' || $SESSION_LEVEL=='moderator' || $SESSION_LEVEL=='gloperator'))
  die("OMG");
require("../include/top.php");
?>

<? if($action=="nfo_files"): ?>
- checking nfo files...<br>
<?
$d = dir("../nfo");
while ($entry=$d->read())
{
	if(strlen($entry)&&substr($entry,-4)==".nfo")
	{
		$entry=substr($entry,0,-4);
		$query="SELECT COUNT(*) FROM prods WHERE id=".$entry;
		$result=mysql_query($query);
		if(!mysql_result($result,0))
			print("nfo to delete: ".$entry."<br>\n");
	}
}
$d->close();
?>
done<br>
<? endif; ?>

<? if($action=="screenshots_files"): ?>
- checking nfo files...<br>
<?
$d = dir("../screenshots");
while ($entry=$d->read())
{
	if((substr($entry,-4)==".gif")||(substr($entry,-4)==".jpg")||(substr($entry,-4)==".png"))
	{
		$entry=substr($entry,0,-4);
		$query="SELECT COUNT(*) FROM prods WHERE id=".$entry;
		$result=mysql_query($query);
		if(!mysql_result($result,0))
			print("screenshot to delete: ".$entry."<br>\n");
	}
}
$d->close();
?>
done<br>
<? endif; ?>

<? if($action=="nfo_sql"): ?>
- checking nfo.sql...<br>
<?
$query="SELECT prod FROM nfos";
$result=mysql_query($query);
while($row=mysql_fetch_assoc($result))
	if(!file_exists("../nfo/".$row["prod"].".nfo"))
	{
		print("nfo to delete: ".$row["prod"]."<br>\n");
		// $q2 = "DELETE FROM nfos WHERE prod={$row['prod']} LIMIT 1";
		// mysql_query($q2);
	}
?>
done.<br>
<? endif; ?>

<? if($action=="screenshots_sql"): ?>
- checking screenshots.sql...<br>
<?
$query="SELECT prod FROM screenshots";
$result=mysql_query($query);
while($row=mysql_fetch_assoc($result))
	if(!(file_exists("../screenshots/".$row["prod"].".gif")||file_exists("../screenshots/".$row["prod"].".jpg")||file_exists("../screenshots/".$row["prod"].".png")))
	{
		print("screenshot to delete: ".$row["prod"]."<br>\n");
		// $q2 = "DELETE FROM screenshots WHERE prod={$row['prod']} LIMIT 1";
		// mysql_query($q2);
	}
?>
done.<br>
<? endif; ?>

<? if($action=="comments_sql"): ?>
- checking comments.sql...<br>
<?
$query="SELECT id,which FROM comments";
$result=mysql_query($query);
while($row=mysql_fetch_assoc($result))
{
	$query2="SELECT COUNT(*) FROM prods WHERE id=".$row["which"];
	$result2=mysql_query($query2);
	if(!mysql_result($result2,0))
		print("comment to delete: ".$row["which"]."<br>\n");
}
?>
done.<br>
<? endif; ?>

<? if(!$action): ?>
choose something to check:<br>
<form>
<input type="submit" name="action" value="nfo_sql">
</form>
<form>
<input type="submit" name="action" value="screenshots_sql">
</form>
<form>
<input type="submit" name="action" value="comments_sql">
</form>
<form action="check.php">
<input type="submit" name="action" value="nfo_files">
</form>
<form action="check.php">
<input type="submit" name="action" value="screenshots_files">
</form>
<? else: ?>
<br>
<a href="check.php">go back</a>
<? endif; ?>


<? require("../include/bottom.php"); ?>
