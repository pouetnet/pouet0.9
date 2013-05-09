<?
require("include/top.php");

$query = "SELECT logos.id,logos.file FROM logos";
$result = mysql_query($query);
while ($tmp = mysql_fetch_assoc($result))
	$logos[] = $tmp;
?>

<?
foreach ($logos as $logo) { // we display each logo
?>

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
     </td>
    </tr>
   </table>
  </td>
 </tr>
</table>

<? } ?>

<? require("include/bottom.php"); ?>
