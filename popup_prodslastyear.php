<?
require("include/misc.php");
require("include/icons.php");
require("include/awardscategories.inc.php");
@session_start();
conn_db();

function lettermenu($pattern) {
  print("[ ");
  if($pattern=="#") {
    print("<b>#</b>");
  } else {
    printf("<a href=\"popup_prodslastyear.php?pattern=%%23&form=%s&field=%s\">#</a>",$_GET["form"],$_GET["field"]);
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
      printf("<a href=\"popup_prodslastyear.php?pattern=%s&form=%s&field=%s\">%s</a>",chr(96+$i),$_GET["form"],$_GET["field"],chr(96+$i));
    }
  }
  print(" ]<br />\n");
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
    $sqlwhere="name REGEXP '^".$pattern."'";
  }
  $query="SELECT prods.id,prods.name,prods.type FROM prods WHERE (".$sqlwhere.") and (";
  $query.="true";
  $query.=") and DATE>='".$sceneorgyear."-01-01' and date<'".($sceneorgyear+1)."-01-01' ORDER BY name";
  //debuglog($query);
}
$result = mysql_query($query);
while($tmp = mysql_fetch_array($result)) {
  $prods[]=$tmp;
}

?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
 <meta http-equiv="Content-Type" content="text/html; charset=ISO-8859-1">
 <link rel="shortcut icon" href="/favicon.ico" type="image/x-icon">
 <title>pouet.net BBS :: stop writing deliberately bad code</title>
 <link rel="stylesheet" href="include/style.css" type="text/css">
 <link rel="search" type="application/opensearchdescription+xml" href="opensearch_prod.xml" title="pouët.net - prod search" />
 <meta name="description" content="pouët.net - your online demoscene resource">
 <meta name="keywords" content="pouët.net,256b,1k,4k,40k,64k,cracktro,demo,dentro,diskmag,intro,invitation,lobster sex,musicdisk,Amiga AGA,Amiga ECS,Amiga PPC,Amstrad CPC,Atari ST,BeOS,Commodore 64,Falcon,MS-Dos,Linux,MacOS,Windows">

<script language="JavaScript" type="text/javascript">
<!--
function pickProd(s) {
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
         <b>prods</b>
        </td>
       </tr>
      </table>
     </th>
    </tr>
    <? if(count($prods)==0): ?>
    <tr bgcolor="#557799">
     <th colspan="3">
      <br />
      no prod beginning with a <b><? print($pattern); ?></b> yet =(<br />
      <br />
     </td>
    </tr>
    <? endif; ?>
   <?
   	for($i=0;$i<count($prods);$i++)
   	{
		//get platforms
		$query="select platforms.name from prods_platforms, platforms where prods_platforms.prod='".$prods[$i]["id"]."' and platforms.id=prods_platforms.platform";
		$result=mysql_query($query);
		$check=0;
		$prods[$i]["platform"]="";
		while($tmp = mysql_fetch_array($result)) {
		  if ($check>0) $prods[$i]["platform"].=",";
		  $check++;
		  $prods[$i]["platform"].=$tmp["name"];
		}
   		
   		
     		if($i%2) {
       			print("<tr bgcolor=\"#446688\">\n");
     		} else {
       			print("<tr bgcolor=\"#557799\">\n");
     		}

     		$typess = explode(",", $prods[$i]["type"]);
     		print("<td nowrap><table cellspacing=\"0\" cellpadding=\"0\"><tr><td nowrap><a href=\"javascript:pickProd(".$prods[$i]["id"].")\">");
     		for($k=0;$k<count($typess);$k++) {
		print("<img src=\"gfx/types/".$types[$typess[$k]]."\" width=\"16\" height=\"16\" border=\"0\" title=\"".$typess[$k]."\">");
		}
		print("<br /></a></td><td><img src=\"gfx/z.gif\" width=\"2\" height=\"1\" border=\"0\"><br /></td><td nowrap><a href=\"javascript:pickProd(".$prods[$i]["id"].")\">".strtolower(stripslashes($prods[$i]["name"]))."</a><br /></td><td>&nbsp;</td>");
     		//print($prods[$i]["name"]."</a></b></td>\n");
     		
		print("<td width=\"100%\">&nbsp;</td>");
       	
       		$platforms = explode(",", $prods[$i]["platform"]);
       		for($kkk=0;$kkk<count($platforms);$kkk++) {
       		?><td align="right"><a href="javascript:pickProd(<? print($prods[$i]["id"]); ?>)"><img src="gfx/os/<? print($os[$platforms[$kkk]]); ?>" width="16" height="16" border="0" title="<? print($platforms[$kkk]); ?>"></a><br /></td><?
       		}
       		
       		print("</tr></table></td>\n");
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
