<?
$when=$_REQUEST['when'];
$which=$_REQUEST['which'];
$order=$_REQUEST['order'];

require("include/top.php");

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

$pattern = chr(rand(ord('a'),ord('z')));
if (isset($_GET["pattern"]))
  $pattern = substr($_GET["pattern"],0,1);

$query = "SELECT parties.name as name,prods.party as id,prods.party_year ".
"as year,count(0) as count,COUNT(prods.party) as cprods,parties.web, ".
"partylinks.csdb, partylinks.slengpung, partylinks.zxdemo, ".
"partylinks.download FROM prods JOIN parties LEFT JOIN partylinks ON ".
"(prods.party_year=partylinks.year AND parties.id=partylinks.party) WHERE parties.id=prods.party ";

if($pattern=="#") {
  //$sqlwhere="(name LIKE '0%')||(name LIKE '1%')||(name LIKE '2%')||(name LIKE '3%')||(name LIKE '4%')||(name LIKE '5%')||(name LIKE '6%')||(name LIKE '7%')||(name LIKE '8%')||(name LIKE '9%')";
  $query.="and (parties.name REGEXP '^[^a-zA-Z]')";
} else {
  $query.="and parties.name LIKE '".$pattern."%'";
}
$query .=" GROUP BY prods.party, prods.party_year ";
if ($order=="year") {
	$query .= "ORDER BY prods.party_year DESC,parties.name ASC";
} else {
	$query .= "ORDER BY parties.name ASC,prods.party_year DESC";
}
$result = mysql_query($query);
while($row = mysql_fetch_assoc($result)) {
	$parties[] = $row;
}

function lettermenu($pattern) {
  print("[ ");
  if($pattern=="#") {
    print("<b>#</b>");
  } else {
    print("<a href=\"parties.php?pattern=%23\">#</a>");
  }
  for($i=1;$i<=26;$i++) {
    print(" | ");
    if($pattern==chr(96+$i)) {
      print("<b>".chr(96+$i)."</b>");
    } else {
      print("<a href=\"parties.php?pattern=".chr(96+$i)."\">".chr(96+$i)."</a>");
    }
  }
  print(" ]<br />\n");
}


?>


<br>
<table bgcolor="#000000" cellspacing="1" cellpadding="0" border="0">
 <tr>
  <td>
   <table bgcolor="#000000" cellspacing="1" cellpadding="2" border="0">
    <tr bgcolor="#224488">
      <th colspan="4">
       <center><? lettermenu($pattern); ?></center>
      </th>
    </tr>

    <tr bgcolor="#224488">
     <th>
      <table><tr>
       <td>
        <a href="parties.php?order=name&amp;pattern=<?php print($pattern); ?>"><img src="gfx/<? print(goodfleche("name",$order)); ?>.gif" width="13" height="12" border="0"></a><br>
       </td>
       <td>
        <a href="parties.php?order=name&amp;pattern=<?php print($pattern); ?>"><b>partyname</b></a><br>
       </td>
      </tr></table>
     </th>
     <th>
      <table><tr>
       <td>
        <a href="parties.php?order=year&amp;pattern=<?php print($pattern); ?>"><img src="gfx/<? print(goodfleche("year",$order)); ?>.gif" width="13" height="12" border="0"></a><br>
       </td>
       <td>
        <a href="parties.php?order=year&amp;pattern=<?php print($pattern); ?>"><b>year</b></a><br>
       </td>
      </tr></table>
     </th>
     <th>
      <b>releases</b><br>
     </th>
     <th>
      <b>download</b><br>
     </th>
    </tr>

   <?
    for($i=0,$j=0;$i<count($parties);$i++):

       if($parties[$i]['id']!=1024):

     	if($parties[$i]['id']!=$parties[$i-1]['id']):
     		
     		if (($order=="year") && ($parties[$i]['year']!=$parties[$i-1]['year']))
     		 print("<tr bgcolor=\"#224488\"><td></td><td><b>".$parties[$i]["year"]."</b><br /></td><td></td><td></td></tr>");
     	 	
     	 	$j++;
     	 	if($j%2) {
		       print("<tr bgcolor=\"#446688\"><td>");
		     } else {
		       print("<tr bgcolor=\"#557799\"><td>");
		     }
		print("<b><a href=\"party.php?which=".$parties[$i]['id']."\">".$parties[$i]['name']."</a></b>\n");

		if($parties[$i]["web"])
		{
		   print(" [<a href=\"".$parties[$i]['web']."\">web</a>]");
		 } ?>
		 <br />
	<? else:
     	 	if($j%2) {
		       print("<tr bgcolor=\"#446688\"><td><br /></td>");
		     } else {
		       print("<tr bgcolor=\"#557799\"><td><br /></td>");
		     }
		
	endif; ?>
	
	      <td>
	      <? print("<a href=\"party.php?which=".$parties[$i]['id']."&when=".$parties[$i]['year']."\">".$parties[$i]['year']."</a>\n"); ?>
	      <? if($parties[$i]["slengpung"]): ?>
	       [<a href="http://www.slengpung.com/?eventid=<?=$parties[$i]["slengpung"]?>">slengpung</a>]
	      <? endif; ?>
	      <? if($parties[$i]["csdb"]): ?>
	       [<a href="http://noname.c64.org/csdb/event/?id=<?=$parties[$i]["csdb"]?>">csdb</a>]
	      <? endif; ?>
	      <? if($parties[$i]["zxdemo"]): ?>
	       [<a href="http://zxdemo.org/party.php?id=<?=$parties[$i]["zxdemo"]?>">zxdemo</a>]
	      <? endif; ?>
      		<br />
	      </td>
	
	      <td>
	      <? print($parties[$i]['cprods']); ?><br />
	      </td>
	
	      <td>
              <? if($parties[$i]["download"]): ?>
      		[<a href="<?=$parties[$i]["download"]?>">prods</a>]
              <? endif; ?>
              <? if(file_exists("results/".$parties[$i]["id"]."_".substr($parties[$i]["year"],-2).".txt")): ?>
           	[<a href="results.php?which=<?=$parties[$i]["id"]?>&when=<?=substr($parties[$i]["year"],-2)?>">results</a>]
              <? endif; ?>
	      </td>
	
	      	
	     </tr>
	<? endif; ?>
    <? endfor; ?>
   </table>
  </td>
 </tr>
    <tr bgcolor="#224488">
      <th colspan="3">
       <center><? lettermenu($pattern); ?></center>
      </th>
    </tr>

</table>
<br />

<? require("include/bottom.php"); ?>
