<?
require("include/top.php");

$query = 'SELECT id,name FROM prods';
$result = mysql_query($query);
while($row = mysql_fetch_assoc($result))
{
	if(!(file_exists('screenshots/'.$row['id'].'.gif')
		||file_exists('screenshots/'.$row['id'].'.jpg')
		||file_exists('screenshots/'.$row['id'].'.png')))
	{
		$prods[] = $row;
	}
}
?>
<br />
<table>
	<tr>
		<th>Productions missing a screenshot</th>
	</tr>
	<? foreach($prods as $p) { ?>
	<tr>
		<td><a href="prod.php?which=<?=$p['id']?>"><?=$p['name']?></a></td>
	</tr>
	<? } ?>
</table>
<br />
<? require("include/bottom.php"); ?>
