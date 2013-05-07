<?
require("include/misc.php");
require("include/libbb.php");
session_start();
conn_db();

$user = array();
$customid = $_SESSION["SCENEID_ID"];
$result=mysql_query(sprintf('select displayimages from usersettings where id=%d',$customid));
$usersettings=mysql_fetch_assoc($result);
if (!$usersettings) {
  $result=mysql_query(sprintf('select displayimages from usersettings where id=11057',$customid));
  $usersettings=mysql_fetch_assoc($result);
}
$user=array_merge($user,$usersettings);

$which = intval($_POST["which"]);
if ($which) {
  $query="SELECT topic FROM bbs_topics WHERE id=".$which;
  $result=mysql_query($query) or die(mysql_error());
  //$topictitle=htmlcleanonerow(mysql_result($result,0));
  $o = mysql_fetch_object($result);
}
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
 <meta http-equiv="Content-Type" content="text/html; charset=ISO-8859-1">
 <link rel="shortcut icon" href="/favicon.ico" type="image/x-icon">
 <title>pouet.net BBS :: preview :: because broken bbcode makes ps cry</title>
 <link rel="stylesheet" href="include/style.css" type="text/css">
 <link rel="search" type="application/opensearchdescription+xml" href="opensearch_prod.xml" title="pouët.net - prod search" />
 <meta name="description" content="pouët.net - your online demoscene resource">
 <meta name="keywords" content="pouët.net,256b,1k,4k,40k,64k,cracktro,demo,dentro,diskmag,intro,invitation,lobster sex,musicdisk,Amiga AGA,Amiga ECS,Amiga PPC,Amstrad CPC,Atari ST,BeOS,Commodore 64,Falcon,MS-Dos,Linux,MacOS,Windows">

</head>
<body background="gfx/trumpet.gif" bgcolor="#3A6EA5">
<br />

<br />
<table bgcolor="#000000" cellspacing="1" cellpadding="0" width="75%" style="margin: 0px auto;">
 <tr>
  <td>
   <table bgcolor="#000000" cellspacing="1" cellpadding="2" width="100%">
    <tr>
     <th><center>
	<?=$o->topic ? htmlcleanonerow($o->topic) : $_POST["topic"]?>
	</center></th>
    </tr>

    <tr><td bgcolor="#446688">
    <? print(parse_message($_POST["message"])); ?>
    </td></tr>
    <tr class="cite-<?=$_SESSION["SCENEID_ID"]?>">
     <td bgcolor="#6688AA" align="right">
      <table cellspacing="0" cellpadding="0">
       <tr>
        <td>added on the <?=date("Y-m-d")?> by <a href="user.php?who=<?=$_SESSION["SCENEID_ID"]?>"><?=$_SESSION["SESSION_NICKNAME"]?></a></td>
        <td>&nbsp;<br /></td>
        <td><a href="user.php?who=<?=$_SESSION["SCENEID_ID"]?>"><img src="avatars/<?=$_SESSION["SESSION_AVATAR"]?>" width="16" height="16" border="0" alt="<?=$_SESSION["SESSION_NICKNAME"]?>" title="<?=$_SESSION["SESSION_NICKNAME"]?>"></a></td>
       </tr>
      </table>
     </td>
    </tr>
   </table>
  </td>
 </tr>
</table>

<br />

</div>
</body>
</html>