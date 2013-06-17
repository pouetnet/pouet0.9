<?
require("include/misc.php");
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
 <meta http-equiv="Content-Type" content="text/html; charset=ISO-8859-1">
 <link rel="shortcut icon" href="/favicon.ico" type="image/x-icon">
 <title>pouet.net avatars</title>
 <link rel="stylesheet" href="include/style.css" type="text/css">
 <link rel="search" type="application/opensearchdescription+xml" href="opensearch_prod.xml" title="pouët.net - prod search" />
 <meta name="description" content="pouët.net - your online demoscene resource">
 <meta name="keywords" content="pouët.net,256b,1k,4k,40k,64k,cracktro,demo,dentro,diskmag,intro,invitation,lobster sex,musicdisk,Amiga AGA,Amiga ECS,Amiga PPC,Amstrad CPC,Atari ST,BeOS,Commodore 64,Falcon,MS-Dos,Linux,MacOS,Windows">

<script language="JavaScript" type="text/javascript">
<!--
function pickAvatar(s) {
  window.opener.document.forms["<?=htmlspecialchars($_GET['form'], ENT_QUOTES, 'UTF-8')?>"].<?=htmlspecialchars($_GET['field'], ENT_QUOTES, 'UTF-8')?>.value=s;
  window.opener.document.avatr.src='avatars/'+s;
  window.close();
}
//-->
</script>

</head>
<body background="gfx/trumpet.gif" bgcolor="#3A6EA5">
<br />

<br />
<table style="margin:0px auto"><tr><td valign="top">
<table bgcolor="#000000" cellspacing="1" cellpadding="0" border="0">
 <tr>
  <th>select your avatar</th>
 </tr>
 <tr>
  <td class="bg1" style="padding: 5px;">
<?
	$entry = glob("./avatars/*.gif");
	foreach($entry as $e) {
	  $en = str_replace("./avatars/","",$e);
    printf("<a href='javascript:pickAvatar(\"%s\")'><img src='%s' alt='avatar' border='0'/></a>\n",$en,$e);
	}
?>
  </td>
 </tr>
</table>
</td>
</tr></table>
<br />

</div>
</body>
</html>
