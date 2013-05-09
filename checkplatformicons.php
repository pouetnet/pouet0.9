<?

require("include/top.php");

//$result = mysql_query("DESC prods platform");
//$row = mysql_fetch_row($result);
//$platforms = explode("'",$row[1]);

$query="select platforms.name, platforms.icon from platforms order by platforms.name asc";
$result=mysql_query($query);
while($tmp = mysql_fetch_array($result)) {
	 $platforms[]=$tmp;
}

?>

<br />
<table width="30%">
<tr><td>
<table cellspacing="1" cellpadding="2" class="box">
 <tr><th colspan="2"><center>platform icon check</center></th></tr>

<? $k=0;
	for($i=0;$i<count($platforms);$i++):
	$k++;
 	if($k%2) {
	    print("<tr bgcolor=\"#446688\">");
	  } else {
	    print("<tr bgcolor=\"#557799\">");
	  }?>
  <td><img src="gfx/os/<?=$os[$platforms[$i]["name"]]?>" width="16" height="16" border="0" title="<?=$platforms[$i]["name"]?>" alt="<?=$platforms[$i]["name"]?>"><?=$platforms[$i]["name"]?><br /></td>
  <td><img src="gfx/os/<?=$platforms[$i]["icon"]?>" width="16" height="16" border="0" title="<?=$platforms[$i]["name"]?>" alt="<?=$platforms[$i]["name"]?>"><?=$platforms[$i]["name"]?><br /></td>
 </tr>
<? endfor; ?>
</table>
<br />
</td>
</tr>
</table>
<? require("include/bottom.php"); ?>
