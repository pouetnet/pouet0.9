<?
require("include/misc.php");
conn_db();

function cmp_year($a, $b)
{
     if ($a["year"] == $b["year"])
     {
         return ($a["name"] < $b["name"]) ? -1 : 1;
     }
     return ($a["year"] > $b["year"]) ? -1 : 1;
}

function cmp_name($a, $b)
{
     if (strtolower($a["name"]) == strtolower($b["name"]))
     {
         return (strtolower($a["year"]) > strtolower($b["year"])) ? -1 : 1;
     }
     return (strtolower($a["name"]) < strtolower($b["name"])) ? -1 : 1;
}

function goodfleche($wanted,$current) {
  if($wanted==$current) {
    $fleche="fleche1a";
  } else {
    $fleche="fleche1b";
  }
  return $fleche;
}

function lettermenu($pattern) {
  print("[ ");
  if($pattern=="#") {
    print("<b>#</b>");
  } else {
    printf("<a href=\"popup_parties.php?pattern=%%23&form=%s&field=%s\">#</a>",htmlspecialchars($_GET['form'], ENT_QUOTES, 'UTF-8'),htmlspecialchars($_GET['field'], ENT_QUOTES, 'UTF-8'));
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
      printf("<a href=\"popup_parties.php?pattern=%s&form=%s&field=%s\">%s</a>",chr(96+$i),htmlspecialchars($_GET['form'], ENT_QUOTES, 'UTF-8'),htmlspecialchars($_GET['field'], ENT_QUOTES, 'UTF-8'),chr(96+$i));
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

if($pattern=="#") {
//  $sqlwhere="(name LIKE '0%')||(name LIKE '1%')||(name LIKE '2%')||(name LIKE '3%')||(name LIKE '4%')||(name LIKE '5%')||(name LIKE '6%')||(name LIKE '7%')||(name LIKE '8%')||(name LIKE '9%')";
  $sqlwhere="(name REGEXP '^[^a-zA-Z]')";
} else {
  $sqlwhere="name LIKE '".$pattern."%'";
}

$query = "SELECT * FROM parties ";
$query .= " WHERE ".$sqlwhere;
$query .= " ORDER BY parties.name ASC";
$result = mysql_query($query);
while($row = mysql_fetch_assoc($result)) {
	$parties[] = $row;
}

?>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
 <meta http-equiv="Content-Type" content="text/html; charset=ISO-8859-1">
 <link rel="shortcut icon" href="/favicon.ico" type="image/x-icon">
 <title>pouet.net parties</title>
 <link rel="stylesheet" href="include/style.css" type="text/css">
 <link rel="search" type="application/opensearchdescription+xml" href="opensearch_prod.xml" title="pouët.net - prod search" />
 <meta name="description" content="pouët.net - your online demoscene resource">
 <meta name="keywords" content="pouët.net,256b,1k,4k,40k,64k,cracktro,demo,dentro,diskmag,intro,invitation,lobster sex,musicdisk,Amiga AGA,Amiga ECS,Amiga PPC,Amstrad CPC,Atari ST,BeOS,Commodore 64,Falcon,MS-Dos,Linux,MacOS,Windows">

<script language="JavaScript" type="text/javascript">
<!--
function pickParty(s) {
  window.opener.document.forms["<?=htmlspecialchars($_GET['form'], ENT_QUOTES, 'UTF-8')?>"].<?=htmlspecialchars($_GET['field'], ENT_QUOTES, 'UTF-8')?>.value=s;
  window.close();
}
//-->
</script>

</head>
<body background="gfx/trumpet.gif" bgcolor="#3A6EA5">
<br />

<br>
<table bgcolor="#000000" cellspacing="1" cellpadding="0" border="0" style="margin: 0px auto">
 <tr>
  <td>
   <table bgcolor="#000000" cellspacing="1" cellpadding="2" border="0">
    <tr bgcolor="#224488">
    <th>
    <? lettermenu($pattern); ?>
    </th>
    </tr>
    <tr bgcolor="#224488">
     <th>
      <table><tr>
       <td>
        <a href="parties.php?order=name"><img src="gfx/<? print(goodfleche("name",$order)); ?>.gif" width="13" height="12" border="0"></a><br>
       </td>
       <td>
        <a href="parties.php?order=name"><b>partyname</b></a><br>
       </td>
      </tr></table>
     </th>
    </tr>

   <?
    for($i=0;$i<count($parties);$i++) {
?>
   <tr bgcolor="#446688">
     <td><b><a href="javascript:pickParty(<?=$parties[$i]["id"]?>)"><?=$parties[$i]["name"]?></a></b></td>
   </tr>
<?
    } ?>

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
