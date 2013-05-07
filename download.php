<?
require("include/top.php");

$query ="SELECT * FROM prods ";
$query.="WHERE id=".$which;
$result = mysql_query($query);
$prod = mysql_fetch_array($result);

$currentip=getenv("REMOTE_ADDR");
$referer=getenv("HTTP_REFERER");
if(basename($referer)=="prod.php?which=".$which && $prod["downloads_ip"]!=$currentip)
{
	mysql_query("UPDATE prods SET downloads=downloads+1,downloads_ip='".$currentip."' WHERE id=".$prod["id"]);
}

$prod["download"] = str_replace("%2F","/",$prod["download"]);

?>
<br />
<table bgcolor="#000000" cellspacing="1" cellpadding="0">
 <tr>
  <td>
   <table bgcolor="#000000" cellspacing="1" cellpadding="2">
    <tr>
     <td bgcolor="#224488" nowrap align="center">
      <b><?=$prod["name"]?> download page</b><br />
     </td>
    </tr>
    <tr>
     <td bgcolor="#557799">
      get <?=$prod["name"]?> here: <a href="<?=$prod["download"]?>"><?=$prod["download"]?></a><br />
      <br />
      got a 404 ? try one of those mirror list:<br />
      <? 
        $somepos = strrpos(basename($prod["download"]), ".");
	if ($pos === false) { // not found means it is extensionless, cool for amiga stuff
	  $extensionless = basename($prod["download"]);
	} else { //lets strip the extension to help searches for prods of morons who insist in using .rar instead of .zip
	  $extensionless = substr(basename($prod["download"]), 0, $somepos);
      	} ?>
	- <a href="http://www.scene.org/search.php?search=<? print($extensionless); ?>"><?=$prod["name"]?> on scene.org</a> (works now! [in theory])<br />
	- <a href="http://www.google.com/search?q=<? print($extensionless); ?>"><?=$prod["name"]?> on google</a><br />
	- <a href="http://www.filesearching.com/cgi-bin/s?q=<? print($extensionless); ?>"><?=$prod["name"]?> on filesearching.com</a><br />
	- <a href="http://www.filemirrors.com/search.src?file=<? print($extensionless); ?>"><?=$prod["name"]?> on filemirrors</a><br />
	- <a href="http://hornet.scene.org/cgi-bin/scene-search.cgi?search=<? print($extensionless); ?>"><?=$prod["name"]?> on the hornet archive</a><br />
	
	<? 
	//- <a href="http://www.ojuice.net/ftpsearch.htm?sftp=
	//- <a href="http://www.chscene.ch/ftpsearch2/search.php?type=file&query=
	$query="select count(0) from prods_platforms, platforms WHERE platforms.name like 'Amiga%' and prods_platforms.platform=platforms.id and prods_platforms.prod=".$which;
	$result = mysql_query($query);
	if (mysql_result($result,0)>0): ?> 
	- <a href="http://aminet.net/search.php?query=<? print($extensionless); ?>"><?=$prod["name"]?> on aminet (new)</a><br />
	- <a href="http://uk.aminet.net/aminetbin/find?<? print($extensionless); ?>"><?=$prod["name"]?> on aminet (uk)</a><br />
	- <a href="http://de.aminet.net/aminetbin/find?<? print($extensionless); ?>"><?=$prod["name"]?> on aminet (de)</a><br />
	- <a href="http://no.aminet.net/aminetbin/find?<? print($extensionless); ?>"><?=$prod["name"]?> on aminet (no)</a><br />
	- <a href="http://amigascne.org/cgi-bin/search.cgi?searchstr=<? print($extensionless); ?>"><?=$prod["name"]?> on amigascne.org</a><br />
	<? endif; ?>
	<? if ($prod["type"]=="cracktro"): ?>
	- <a href="http://www.defacto2.net/cracktros-detail.cfm?type=file&value=<? print($extensionless); ?>"><?=$prod["name"]?> on defacto2</a><br />
	<? endif; ?>
     </td>
    </tr>
    <tr>
     <td bgcolor="#446688" nowrap align="center">
      <a href="prod.php?which=<?=$prod["id"]?>"><b>back to <?=$prod["name"]?></b></a><br />
     </td>
    </tr>
   </table>
  </td>
 </tr>
</table>
<br />
<? require("include/bottom.php"); ?>
