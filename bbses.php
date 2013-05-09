<?
require("include/top.php");

function lettermenu($pattern) {
  print("[ ");
  if($pattern=="#") {
    print("<b>#</b>");
  } else {
    print("<a href=\"bbses.php?pattern=%23\">#</a>");
  }
  for($i=1;$i<=26;$i++) {
    print(" | ");
    if($pattern==chr(96+$i)) {
      print("<b>".chr(96+$i)."</b>");
    } else {
      print("<a href=\"bbses.php?pattern=".chr(96+$i)."\">".chr(96+$i)."</a>");
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

$pattern=$_REQUEST['pattern'];
$which=$_REQUEST['which'];
if(!$pattern&&!$which) {
  $pattern=chr(mt_rand(96,122));
  if($pattern==chr(96)) {
    $pattern="#";
  }
}

if($which) {
	$query = "SELECT bbs2 from bbsesaka WHERE bbs1=".$which;
	$result = mysql_query($query);
	while($tmp=mysql_fetch_array($result)) {
	  $bbsesaka[]=$tmp;
	}
  	$query="SELECT id,name,sysop,started,closed,phonenumber,telnetip,added,adder FROM bbses WHERE id=".$which;
  	for($i=0;$i<count($bbsesaka);$i++) { $query.=" OR id=".$bbsesaka[$i]["bbs2"]; }
} elseif($pattern) {
  if($pattern=="#") {
    $sqlwhere="(name LIKE '0%')||(name LIKE '1%')||(name LIKE '2%')||(name LIKE '3%')||(name LIKE '4%')||(name LIKE '5%')||(name LIKE '6%')||(name LIKE '7%')||(name LIKE '8%')||(name LIKE '9%')";
  } else {
    $sqlwhere="name LIKE '".$pattern."%'";
  }
  $query="SELECT id,name,phonenumber,telnetip FROM bbses WHERE (".$sqlwhere.") ORDER BY name";
}
$result = mysql_query($query);
while($tmp = mysql_fetch_array($result)) {
  $bbses[]=$tmp;
}
if($which) {
  	//get user who added this bbs
  	$query="SELECT id,nickname,avatar FROM users WHERE id=".$bbses[0]["adder"];
  	$result=mysql_query($query);
  	$myuser=mysql_fetch_array($result);
  	
  	$query="SELECT prods.id, prods.name, prods.group1, groups.name as group1name from prods LEFT JOIN groups on prods.group1=groups.id WHERE prods.boardID=$which";
	$result = mysql_query($query);
	while($tmp = mysql_fetch_array($result)) {
  	 $bbstros[]=$tmp;
	}
	
  	$query="SELECT affiliatedbbses.group,affiliatedbbses.type,groups.name from affiliatedbbses LEFT JOIN groups on affiliatedbbses.group=groups.id WHERE affiliatedbbses.bbs=$which ORDER BY affiliatedbbses.type, groups.name";
	$result = mysql_query($query);
	while($tmp = mysql_fetch_array($result)) {
  	 $bbsaffils[]=$tmp;
	}

	$query="SELECT * from othernfos WHERE type='bbs' and refid='$which'";
	$result = mysql_query($query);
	while($tmp = mysql_fetch_array($result)) {
  	 $bbsnfos[]=$tmp;
	}
	
	$query="select platforms.name,platforms.icon from bbses_platforms, platforms where bbses_platforms.bbs='".$which."' and platforms.id=bbses_platforms.platform";
	$result = mysql_query($query);
	while($tmp = mysql_fetch_array($result)) {
  	 $platforms[]=$tmp;
	}
}

?>
<br />
<table><tr><td valign="top">
<table bgcolor="#000000" cellspacing="1" cellpadding="0" border="0">
 <tr>
  <td>
   <table bgcolor="#000000" cellspacing="1" cellpadding="2" border="0">
   <? if($which): ?>
    <? if(count($bbses)==0): ?>
     <tr bgcolor="#557799">
      <th colspan="2">
       <br />
        congratulations! you just found a dupe bbs that has been deleted from our database.<br />
       <br />
      </td>
     </tr>
     <? else: ?>
    <? $sortlink="bbses.php?which=".$which."&order="; ?>
    <tr bgcolor="#224488">
     <th colspan="9">
     <center>
     <?
     	$i=0;
	print("<b><a href=\"bbses.php?which=".$bbses[$i]["id"]."\">".$bbses[$i]["name"]);
	print("</a></b>");
	if($SESSION_LEVEL=='administrator' || $SESSION_LEVEL=='moderator' || $SESSION_LEVEL=='gloperator') print(" <b>[<a href=\"editbbs_light.php?which=".$which."\">editbbs</a>] [<a href=\"submitothernfo.php?refid=".$which."&type=bbs\">+.nfo</a>]</b>\n");
     ?></center>
     </th>
     </tr>
      <? endif; ?>
    <? else: ?>
    <tr bgcolor="#224488">
      <th colspan="2">
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
         <b>name</b>
        </td>
       </tr>
      </table>
      </th>
      <th>
      <table>
       <tr>
        <td>
         <img src="gfx/fleche1a.gif" width="13" height="12" border="0"><br />
        </td>
        <td>
         <b>countrycode</b>
        </td>
       </tr>
      </table>
     </th>
    </tr>
    <? if(count($bbses)==0): ?>
    <tr bgcolor="#557799">
     <th colspan="2">
      <br />
      no bbs name beginning with a <b><? print($pattern); ?></b> yet =(<br />
      <br />
     </td>
    </tr>
    <? endif; ?>
   <? endif; ?>

   <? if (!$which){
   	
   	for($i=0;$i<count($bbses);$i++)
   	{
     		if($i%2) {
       			print("<tr bgcolor=\"#446688\">\n");
     		} else {
       			print("<tr bgcolor=\"#557799\">\n");
     		}
     		print("<td valign=\"top\"><b><a href=\"bbses.php?which=".$bbses[$i]["id"]."\">".$bbses[$i]["name"]);
     		print("</a></b>");
     		if($SESSION_LEVEL=='administrator' || $SESSION_LEVEL=='moderator' || $SESSION_LEVEL=='gloperator') print(" <b>[<a href=\"editbbs_light.php?which=".$bbses[$i]["id"]."\">editbbs</a>]</b>\n");
     		print("</td>\n");
     		print("<td>".$bbses[$i]["phonenumber"]."</td>\n</tr>\n");
     	}
     	
     } else { ?>
     <? if(count($bbses)!=0): ?>
     	<tr bgcolor="#446688">
     	 <td valign="top">
     	  <table border="0">
     	   <? if($bbses[0]["sysop"]): ?>
     	   <tr>
     	   <td>sysop :</td>
     	   <td><? print($bbses[0]["sysop"]); ?></td>
     	   </tr>
     	   <? endif; ?>
     	   <? if($bbses[0]["phonenumber"]): ?>
     	   <tr>
     	   <td>number:</td>
     	   <td><? print($bbses[0]["phonenumber"]); ?></td>
     	   </tr>
     	   <? endif; ?>
     	   <? if($bbses[0]["telnetip"]): ?>
     	   <tr>
     	   <td>telnet :</td>
     	   <td><? print($bbses[0]["telnetip"]); ?></td>
     	   </tr>
     	   <? endif; ?>
		<tr>
		<td nowrap valign="top">platform :</td>
		<td nowrap>
		<table cellspacing="0" cellpadding="0" border="0">
		<?
		//$platforms = explode(",", $bbses[0]["platforms"]);
		for($i=0;$i<count($platforms);$i++) {
		?>
		<tr>
		<td>
		<a href="prodlist.php?platform=<? print($platforms[$i]["name"]); ?>"><img src="gfx/os/<? print($platforms[$i]["icon"]); ?>" width="16" height="16" border="0" title="<? print($platforms[$i]["name"]); ?>"></a><br />
		</td>
		<td>&nbsp;</td>
		<td nowrap>
		<a href="prodlist.php?platform=<? print($platforms[$i]["name"]); ?>"><? print($platforms[$i]["name"]); ?></a><br />
		</td>
		</tr>
		<? } ?>
		</table>
		</td>
		</tr>  	
		
		<? if($bbsnfos): ?>
		<tr>
		<td nowrap valign="top">infofiles :</td>
		<td nowrap>
		<table cellspacing="0" cellpadding="0" border="0">
		<? for($i=0;$i<count($bbsnfos);$i++) { ?>
		<tr>
		<td>
		<a href="othernfo.php?which=<? print($bbsnfos[$i]['id']); ?>"><? print($i+1); ?></a><br />
		</td>
		</tr>
		<? } ?>
		</table>
		</td>
		</tr>  	
		<? endif; ?>
     	  </table>
     	 </td>
     	 <td valign="top">
     	  <table border="0">
     	  <? if(count($bbstros)>0): ?>
     	  	<tr>
		<td nowrap valign="top">bbstros :</td>
		<td nowrap>
		<table cellspacing="0" cellpadding="0" border="0">
		<?
		for($i=0;$i<count($bbstros);$i++) {
		?>
		<tr>
		<td>
		<a href="prod.php?which=<? print($bbstros[$i]["id"]); ?>"><? print($bbstros[$i]["name"]); ?></a>
		<? if ($bbstros[$i]["group1"]): ?> by <a href="groups.php?which=<? print($bbstros[$i]["group1"]); ?>"><? print($bbstros[$i]["group1name"]); ?></a><br />
		<? endif; ?>
		</td>
		</tr>
		<? } ?>
		</table>
		</td>
		</tr>
	   <? endif; ?>
	   <? if(count($bbsaffils)>0): ?>
     	  	<tr>
		<td nowrap valign="top">affiliations :</td>
		<td nowrap>
		<table cellspacing="0" cellpadding="0" border="0">
		<?
		for($i=0;$i<count($bbsaffils);$i++) {
		?>
		<tr>
		<td>
		<a href="groups.php?which=<? print($bbsaffils[$i]["group"]); ?>"><? print($bbsaffils[$i]["name"]); ?></a> <? print($bbsaffils[$i]["type"]); ?><br />
		</td>
		</tr>
		<? } ?>
		</table>
		</td>
		</tr>
	   <? endif; ?>
	   </table>
     	 </td>
     	</tr>
     	<? endif; ?>
     <? } ?>

    <tr bgcolor="#224488">
     <? if($which): ?>
      <? if(count($bbses)!=0): ?>
       <td colspan="2" align="right">
        <table cellspacing="0" cellpadding="0">
         <tr>
          <td>added on the <? print(substr($bbses[0]["added"],0,10)); ?> by <a href="user.php?who=<? print($myuser["id"]); ?>"><? print($myuser["nickname"]); ?></a></td>
          <td>&nbsp;<br /></td>
          <td><a href="user.php?who=<? print($myuser["id"]); ?>"><img src="avatars/<? print($myuser["avatar"]); ?>" width="16" height="16" border="0"></a></td>
         </tr>
        </table>
       </td>
      <? endif; ?>
     <? else: ?>
      <th colspan="2">
       <center><? lettermenu($pattern); ?></center>
      </th>
     <? endif; ?>
    </tr>
   </table>
  </td>
 </tr>
</table>
</td>
</tr></table>
<br />

<? require("include/bottom.php"); ?>

