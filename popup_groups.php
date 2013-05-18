<?
require("include/misc.php");
conn_db();

function lettermenu($pattern) {
  print("[ ");
  if($pattern=="#") {
    print("<b>#</b>");
  } else {
    printf("<a href=\"popup_groups.php?pattern=%%23&form=%s&field=%s\">#</a>",$_GET["form"],$_GET["field"]);
  }
  for($i=1;$i<=26;$i++) {
    if ($i==13) {
      print(" ]<br />\n");
      print("[ ");
    } else
      print(" | ");
    if($pattern==chr(96+$i)) {
      print("<b>".chr(96+$i)."</b>");
    } else {
      printf("<a href=\"popup_groups.php?pattern=%s&form=%s&field=%s\">%s</a>",chr(96+$i),$_GET["form"],$_GET["field"],chr(96+$i));
    }
  }
  print(" ]<br />\n");
}

function cmpcomments($a, $b)
{
     if ($a["lcom_quand"] == $b["lcom_quand"])
     {
         return 0;
     }
     return ($a["lcom_quand"] > $b["lcom_quand"]) ? -1 : 1;
}


function goodfleche($wanted,$current) {
  if($wanted==$current) {
    $fleche="fleche1a";
  } else {
    $fleche="fleche1b";
  }
  return $fleche;
}

if(!$pattern&&!$which) {
  $pattern=chr(mt_rand(96,122));
  if($pattern==chr(96)) {
    $pattern="#";
  }
}

if($pattern) {
  if($pattern=="#") {
    //$sqlwhere="(name LIKE '0%')||(name LIKE '1%')||(name LIKE '2%')||(name LIKE '3%')||(name LIKE '4%')||(name LIKE '5%')||(name LIKE '6%')||(name LIKE '7%')||(name LIKE '8%')||(name LIKE '9%')";
    $sqlwhere="(name REGEXP '^[^a-zA-Z]')";
  } else {
    $sqlwhere="name LIKE '".$pattern."%'";
  }
  $query="SELECT id,name,acronym,csdb,zxdemo,web FROM groups WHERE (".$sqlwhere.") ORDER BY name";
}
$result = mysql_query($query);
while($tmp = mysql_fetch_array($result)) {
  $groups[]=$tmp;
}

?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
 <meta http-equiv="Content-Type" content="text/html; charset=ISO-8859-1">
 <link rel="shortcut icon" href="/favicon.ico" type="image/x-icon">
 <title>pouet.net groups</title>
 <link rel="stylesheet" href="include/style.css" type="text/css">
 <link rel="search" type="application/opensearchdescription+xml" href="opensearch_prod.xml" title="pouët.net - prod search" />
 <meta name="description" content="pouët.net - your online demoscene resource">
 <meta name="keywords" content="pouët.net,256b,1k,4k,40k,64k,cracktro,demo,dentro,diskmag,intro,invitation,lobster sex,musicdisk,Amiga AGA,Amiga ECS,Amiga PPC,Amstrad CPC,Atari ST,BeOS,Commodore 64,Falcon,MS-Dos,Linux,MacOS,Windows">

<script language="JavaScript" type="text/javascript">
<!--
function pickGroup(s) {
  window.opener.document.forms["<?=$_GET["form"]?>"].<?=$_GET["field"]?>.value=s;
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
  <td>
   <table bgcolor="#000000" cellspacing="1" cellpadding="2" border="0">
    <tr bgcolor="#224488">
      <th colspan="3">
       <center><? lettermenu($pattern); ?></center>
      </th>
    </tr>
    <tr bgcolor="#224488">
     <th>
      <table>
       <tr>
        <td>
         <img src="gfx/fleche1a.gif" width="13" height="12" border="0"><br />
        </td>
        <td>
         <b>groups</b>
        </td>
       </tr>
      </table>
     </th>
    </tr>
    <? if(count($groups)==0): ?>
    <tr bgcolor="#557799">
     <th colspan="3">
      <br />
      no group name beginning with a <b><? print($pattern); ?></b> yet =(<br />
      <br />
     </td>
    </tr>
    <? endif; ?>
   <?
   	for($i=0;$i<count($groups);$i++)
   	{
     		if($i%2) {
       			print("<tr bgcolor=\"#446688\">\n");
     		} else {
       			print("<tr bgcolor=\"#557799\">\n");
     		}
     		print("<td valign=\"top\"><b><a href=\"javascript:pickGroup(".$groups[$i]["id"].")\">".$groups[$i]["name"]);
     		if($groups[$i]["acronym"]) print(" [".$groups[$i]["acronym"]."]");
     		print("</a></b>");
//     		if($SESSION_LEVEL=='administrator' || $SESSION_LEVEL=='moderator' || $SESSION_LEVEL=='gloperator') print(" <b>[<a href=\"editgroups.php?which=".$groups[$i]["id"]."\">editgroup</a>]</b>\n");
     		if($groups[$i]["web"]) print(" <b>[<a href=\"".$groups[$i]["web"]."\">web</a>]</b>\n");
     		if($groups[$i]["csdb"]) print(" <b>[<a href=\"http://noname.c64.org/csdb/group/?id=".$groups[$i]["csdb"]."\">csdb</a>]</b>\n");
     		if($groups[$i]["zxdemo"]) print(" <b>[<a href=\"http://zxdemo.org/author.php?id=".$groups[$i]["zxdemo"]."\">zxdemo</a>]</b>\n");
     		print("</td>\n");
//     		print("<td>\n<table cellspacing=\"1\" cellpadding=\"0\">\n");
     		$k=0;
/*
     		for($j=0;$j<count($prods);$j++) {

		if(($prods[$j]["group1"]==$groups[$i]["id"])||($prods[$j]["group2"]==$groups[$i]["id"])||($prods[$j]["group3"]==$groups[$i]["id"]))
		{

	       		$typess = explode(",", $prods[$j]["type"]);
			print("<tr><td><a href=\"prod.php?which=".$prods[$j]["id"]."\">");
			for($kk=0;$kk<count($typess);$kk++) {
				print("<img src=\"gfx/types/".$types[$typess[$kk]]."\" width=\"16\" height=\"16\" border=\"0\" title=\"".$typess[$kk]."\">");
			}
	       		print("<br /></a></td><td><img src=\"gfx/z.gif\" width=\"2\" height=\"1\" border=\"0\"><br /></td><td><a href=\"prod.php?which=".$prods[$j]["id"]."\">".strtolower(stripslashes($prods[$j]["name"]))."</a><br /></td></tr>\n");
	      	 	$k++;
		}
     		}
*/
      }
?>

    <tr bgcolor="#224488">
      <th colspan="3">
       <center><? lettermenu($pattern); ?></center>
      </th>
    </tr>
   </table>
  </td>
 </tr>
</table>
</td>
</tr></table>
<br />

</div>
</body>
</html>
