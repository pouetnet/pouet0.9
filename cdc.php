<?
require("include/top.php");

function SQLToDate($sqldate) {
  global $months;

  $txtdate=substr($sqldate,-2);
  $txtdate.=" ";
  $txtdate.=$months[sprintf("%d",substr($sqldate,5,2))];
  $txtdate.=" ";
  $txtdate.=substr($sqldate,0,4);

  return $txtdate;
}

$query="SELECT cdc.which,cdc.quand,prods.name,prods.type,prods.group1,prods.group2,prods.group3 FROM cdc,prods WHERE cdc.which=prods.id AND cdc.quand<=NOW() ORDER BY cdc.quand DESC";
$result=mysql_query($query);
while($tmp=mysql_fetch_array($result)) {
  $cdc[]=$tmp;
}

for ($i=0; $i<count($cdc); $i++):
	if ($cdc[$i]["group1"]):
		$query="select name,acronym from groups where id='".$cdc[$i]["group1"]."'";
		$result=mysql_query($query);
		while($tmp = mysql_fetch_array($result)) {
		  $cdc[$i]["groupname1"]=$tmp["name"];
		  $cdc[$i]["groupacron1"]=$tmp["acronym"];
		 }
	endif;
	if ($cdc[$i]["group2"]):
		$query="select name,acronym from groups where id='".$cdc[$i]["group2"]."'";
		$result=mysql_query($query);
		while($tmp = mysql_fetch_array($result)) {
		  $cdc[$i]["groupname2"]=$tmp["name"];
		  $cdc[$i]["groupacron2"]=$tmp["acronym"];
		 }
	endif;
	if ($cdc[$i]["group3"]):
		$query="select name,acronym from groups where id='".$cdc[$i]["group3"]."'";
		$result=mysql_query($query);
		while($tmp = mysql_fetch_array($result)) {
		  $cdc[$i]["groupname3"]=$tmp["name"];
		  $cdc[$i]["groupacron3"]=$tmp["acronym"];
		 }
	endif;

	if (strlen($cdc[$i]["groupname1"].$cdc[$i]["groupname2"].$cdc[$i]["groupname3"])>27):
		if (strlen($cdc[$i]["groupname1"])>10 && $cdc[$i]["groupacron1"]) $cdc[$i]["groupname1"]=$cdc[$i]["groupacron1"];
		if (strlen($cdc[$i]["groupname2"])>10 && $cdc[$i]["groupacron2"]) $cdc[$i]["groupname2"]=$cdc[$i]["groupacron2"];
		if (strlen($cdc[$i]["groupname3"])>10 && $cdc[$i]["groupacron3"]) $cdc[$i]["groupname3"]=$cdc[$i]["groupacron3"];
	endif;

	$query="select platforms.name from prods_platforms, platforms where prods_platforms.prod='".$cdc[$i]["which"]."' and platforms.id=prods_platforms.platform";
	$result=mysql_query($query);
	$check=0;
	$cdc[$i]["platform"]="";
	while($tmp = mysql_fetch_array($result)) {
	  if ($check>0) $cdc[$i]["platform"].=",";
	  $check++;
	  $cdc[$i]["platform"].=$tmp["name"];
	 }
	 //print($query."<->".$cdc[$i]["platform"]."<br/>");

endfor;

$query=" SELECT distinct prods.id as which,count(prods.id) as count,prods.name,prods.type,prods.group1,prods.group2,prods.group3 FROM users_cdcs JOIN prods WHERE users_cdcs.cdc=prods.id group by prods.id order by count desc";
$result=mysql_query($query);
while($tmp=mysql_fetch_array($result)) {
  $pcdc[]=$tmp;
}
for ($i=0; $i<count($pcdc); $i++):
	if ($pcdc[$i]["group1"]):
		$query="select name,acronym from groups where id='".$pcdc[$i]["group1"]."'";
		$result=mysql_query($query);
		while($tmp = mysql_fetch_array($result)) {
		  $pcdc[$i]["groupname1"]=$tmp["name"];
		  $pcdc[$i]["groupacron1"]=$tmp["acronym"];
		 }
	endif;
	if ($pcdc[$i]["group2"]):
		$query="select name,acronym from groups where id='".$pcdc[$i]["group2"]."'";
		$result=mysql_query($query);
		while($tmp = mysql_fetch_array($result)) {
		  $pcdc[$i]["groupname2"]=$tmp["name"];
		  $pcdc[$i]["groupacron2"]=$tmp["acronym"];
		 }
	endif;
	if ($pcdc[$i]["group3"]):
		$query="select name,acronym from groups where id='".$pcdc[$i]["group3"]."'";
		$result=mysql_query($query);
		while($tmp = mysql_fetch_array($result)) {
		  $pcdc[$i]["groupname3"]=$tmp["name"];
		  $pcdc[$i]["groupacron3"]=$tmp["acronym"];
		 }
	endif;

	if (strlen($pcdc[$i]["groupname1"].$pcdc[$i]["groupname2"].$pcdc[$i]["groupname3"])>27):
		if (strlen($pcdc[$i]["groupname1"])>10 && $pcdc[$i]["groupacron1"]) $pcdc[$i]["groupname1"]=$pcdc[$i]["groupacron1"];
		if (strlen($pcdc[$i]["groupname2"])>10 && $pcdc[$i]["groupacron2"]) $pcdc[$i]["groupname2"]=$pcdc[$i]["groupacron2"];
		if (strlen($pcdc[$i]["groupname3"])>10 && $pcdc[$i]["groupacron3"]) $pcdc[$i]["groupname3"]=$pcdc[$i]["groupacron3"];
	endif;

	$query="select platforms.name from prods_platforms, platforms where prods_platforms.prod='".$pcdc[$i]["which"]."' and platforms.id=prods_platforms.platform";
	$result=mysql_query($query);
	$check=0;
	$pcdc[$i]["platform"]="";
	while($tmp = mysql_fetch_array($result)) {
	  if ($check>0) $pcdc[$i]["platform"].=",";
	  $check++;
	  $pcdc[$i]["platform"].=$tmp["name"];
	 }

endfor;


/*
//sceneorgrecommended check
$result=mysql_query("SELECT sceneorgrecommended.prodid, sceneorgrecommended.type, sceneorgrecommended.category, prods.name,prods.type,prods.group1,prods.group2,prods.group3 from sceneorgrecommended LEFT JOIN prods ON prods.id=sceneorgrecommended.prodid WHERE sceneorgrecommended.type='viewingtip' ORDER BY prods.date");
while($tmp=mysql_fetch_array($result)) {
  $rec[]=$tmp;
  }
for ($i=0; $i<count($rec); $i++):
	if ($rec[$i]["group1"]):
		$query="select name,acronym from groups where id='".$rec[$i]["group1"]."'";
		$result=mysql_query($query);
		while($tmp = mysql_fetch_array($result)) {
		  $rec[$i]["groupname1"]=$tmp["name"];
		  $rec[$i]["groupacron1"]=$tmp["acronym"];
		 }
	endif;
	if ($rec[$i]["group2"]):
		$query="select name,acronym from groups where id='".$rec[$i]["group2"]."'";
		$result=mysql_query($query);
		while($tmp = mysql_fetch_array($result)) {
		  $rec[$i]["groupname2"]=$tmp["name"];
		  $rec[$i]["groupacron2"]=$tmp["acronym"];
		 }
	endif;
	if ($rec[$i]["group3"]):
		$query="select name,acronym from groups where id='".$rec[$i]["group3"]."'";
		$result=mysql_query($query);
		while($tmp = mysql_fetch_array($result)) {
		  $rec[$i]["groupname3"]=$tmp["name"];
		  $rec[$i]["groupacron3"]=$tmp["acronym"];
		 }
	endif;

	if (strlen($rec[$i]["groupname1"].$rec[$i]["groupname2"].$rec[$i]["groupname3"])>27):
		if (strlen($rec[$i]["groupname1"])>10 && $rec[$i]["groupacron1"]) $rec[$i]["groupname1"]=$rec[$i]["groupacron1"];
		if (strlen($rec[$i]["groupname2"])>10 && $rec[$i]["groupacron2"]) $rec[$i]["groupname2"]=$rec[$i]["groupacron2"];
		if (strlen($rec[$i]["groupname3"])>10 && $rec[$i]["groupacron3"]) $rec[$i]["groupname3"]=$rec[$i]["groupacron3"];
	endif;

	$query="select platforms.name from prods_platforms, platforms where prods_platforms.prod='".$rec[$i]["prodid"]."' and platforms.id=prods_platforms.platform";
	$result=mysql_query($query);
	$check=0;
	$rec[$i]["platform"]="";
	while($tmp = mysql_fetch_array($result)) {
	  if ($check>0) $rec[$i]["platform"].=",";
	  $check++;
	  $rec[$i]["platform"].=$tmp["name"];
	 }

endfor;
*/
?>

<br />

<table><tr>
<td valign="top" align="center">
<table bgcolor="#000000" cellspacing="1" cellpadding="0">
 <tr>
  <td>
   <table bgcolor="#000000" cellspacing="1" cellpadding="2">
    <tr>
     <td bgcolor="#224488" colspan="4">
      <table cellspacing="0" cellpadding="0"><tr>
       <td><img src="gfx/titles/coupdecoeur.gif"></td>
       <td>&nbsp;<b>moderators coup de coeur history</b></td>
      </tr></table>
	 </td>
    </tr>
    <? for($i=0;$i<count($cdc);$i++):
      if($i%2) {
       print("<tr bgcolor=\"#446688\">");
     } else {
       print("<tr bgcolor=\"#557799\">");
     }

	     	$typess = explode(",", $cdc[$i]["type"]);
		print("<td nowrap><table cellspacing=\"0\" cellpadding=\"0\"><tr><td nowrap><a href=\"prod.php?which=".$cdc[$i]["which"]."\">");
		for($k=0;$k<count($typess);$k++) {
		print("<img src=\"gfx/types/".$types[$typess[$k]]."\" width=\"16\" height=\"16\" border=\"0\" title=\"".$typess[$k]."\">");
		}
		print("<br /></a></td><td><img src=\"gfx/z.gif\" width=\"2\" height=\"1\" border=\"0\"><br /></td><td nowrap><a href=\"prod.php?which=".$cdc[$i]["which"]."\">".strtolower(stripslashes($cdc[$i]["name"]))."</a><br /></td><td>&nbsp;</td>");

		print("<td width=\"100%\">&nbsp;</td>");

       		$platforms = explode(",", $cdc[$i]["platform"]);
       		for($kkk=0;$kkk<count($platforms);$kkk++) {
       		?><td align="right"><a href="prodlist.php?platform[]=<? print($platforms[$kkk]); ?>"><img src="gfx/os/<? print($os[$platforms[$kkk]]); ?>" width="16" height="16" border="0" title="<? print($platforms[$kkk]); ?>"></a><br /></td><?
       		}
       		print("</tr></table></td>\n");
     ?>

     <td nowrap>
     <? if(strlen($cdc[$i]["groupname1"])): ?>
       <a href="groups.php?which=<? print($cdc[$i]["group1"]); ?>">
        <? print(strtolower($cdc[$i]["groupname1"])); ?>
       </a>
       <? else: ?>
       &nbsp;
       <? endif; ?>
       <? if(strlen($cdc[$i]["groupname2"])): ?>
       ::
       <a href="groups.php?which=<? print($cdc[$i]["group2"]); ?>">
        <? print(strtolower($cdc[$i]["groupname2"])); ?>
       </a>
       <? endif; ?>
       <? if(strlen($cdc[$i]["groupname3"])): ?>
       ::
       <a href="groups.php?which=<? print($cdc[$i]["group3"]); ?>">
        <? print(strtolower($cdc[$i]["groupname3"])); ?>
       </a>
       <? endif; ?>
     </td>
     <td align="right" nowrap>
      <? print(sprintf("%d",substr($cdc[$i]["quand"],-2))); ?>
      <? print(substr($months[sprintf("%d",substr($cdc[$i]["quand"],5,2))],0,3)); ?>
      <? print(substr($cdc[$i]["quand"],0,4)); ?>
     </td>
    </tr>
    <? endfor; ?>
   </table>
  </td>
 </tr>
</table>

<br /></td></tr><tr><td valign="top" align="center">

<table bgcolor="#000000" cellspacing="1" cellpadding="0">
 <tr>
  <td>
   <table bgcolor="#000000" cellspacing="1" cellpadding="2">
    <tr>
     <td bgcolor="#224488" colspan="4">
      <table cellspacing="0" cellpadding="0"><tr>
       <td><img src="gfx/titles/coupdecoeur.gif"></td>
       <td>&nbsp;<b>glopers pcdc, top of the hearts</b></td>
      </tr></table>
	 </td>
    </tr>
    <? for($i=0;$i<count($pcdc);$i++):
    if($i%2) {
       print("<tr bgcolor=\"#446688\">");
     } else {
       print("<tr bgcolor=\"#557799\">");
     }
     		$typess = explode(",", $pcdc[$i]["type"]);
		print("<td nowrap><table cellspacing=\"0\" cellpadding=\"0\"><tr><td nowrap><a href=\"prod.php?which=".$pcdc[$i]["which"]."\">");
		for($k=0;$k<count($typess);$k++) {
		print("<img src=\"gfx/types/".$types[$typess[$k]]."\" width=\"16\" height=\"16\" border=\"0\" title=\"".$typess[$k]."\">");
		}
		print("<br /></a></td><td><img src=\"gfx/z.gif\" width=\"2\" height=\"1\" border=\"0\"><br /></td><td nowrap><a href=\"prod.php?which=".$pcdc[$i]["which"]."\">".strtolower(stripslashes($pcdc[$i]["name"]))."</a><br /></td><td>&nbsp;</td>");

		print("<td width=\"100%\">&nbsp;</td>");

       		$platforms = explode(",", $pcdc[$i]["platform"]);
       		for($kkk=0;$kkk<count($platforms);$kkk++) {
       		?><td align="right"><a href="prodlist.php?platform[]=<? print($platforms[$kkk]); ?>"><img src="gfx/os/<? print($os[$platforms[$kkk]]); ?>" width="16" height="16" border="0" title="<? print($platforms[$kkk]); ?>"></a><br /></td><?
       		}
       		print("</tr></table></td>\n");

     ?>
     <td nowrap>
     <? if(strlen($pcdc[$i]["groupname1"])): ?>
       <a href="groups.php?which=<? print($pcdc[$i]["group1"]); ?>">
        <? print(strtolower($pcdc[$i]["groupname1"])); ?>
       </a>
       <? else: ?>
       &nbsp;
       <? endif; ?>
       <? if(strlen($pcdc[$i]["groupname2"])): ?>
       ::
       <a href="groups.php?which=<? print($pcdc[$i]["group2"]); ?>">
        <? print(strtolower($pcdc[$i]["groupname2"])); ?>
       </a>
       <? endif; ?>
       <? if(strlen($pcdc[$i]["groupname3"])): ?>
       ::
       <a href="groups.php?which=<? print($pcdc[$i]["group3"]); ?>">
        <? print(strtolower($pcdc[$i]["groupname3"])); ?>
       </a>
       <? endif; ?>
     </td>
     <td>
     <? print($pcdc[$i]["count"]); ?>
     </td>
    </tr>
    <? endfor; ?>
   </table>
  </td>
 </tr>
</table>

<br /></td>
</tr>
</table>

<br />
<? require("include/bottom.php"); ?>
