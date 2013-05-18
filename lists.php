<?
require("include/top.php");

function lettermenu($pattern) {
  print("[ ");
  if($pattern=="#") {
    print("<b>#</b>");
  } else {
    print("<a href=\"lists.php?pattern=%23\">#</a>");
  }
  for($i=1;$i<=26;$i++) {
    print(" | ");
    if($pattern==chr(96+$i)) {
      print("<b>".chr(96+$i)."</b>");
    } else {
      print("<a href=\"lists.php?pattern=".chr(96+$i)."\">".chr(96+$i)."</a>");
    }
  }
  print(" ]<br />\n");
}

function goodfleche($wanted,$current) {
  if($wanted==$current) {
    $fleche="fleche1a";
  } else {
    $fleche="fleche1b";
  }
  return $fleche;
}

function reorder_prodtype($a, $b)
{
	if (($a["type"] == "prod") && ($b["type"] == "prod")) {
     if ($a["prodtype"] == $b["prodtype"])
     {
         return 0;
     }
     return ($a["prodtype"] < $b["prodtype"]) ? -1 : 1;
     }
	else {
	     if ($a["type"] == $b["type"])
	     {
	         if (strtolower($a["groupname"]) == strtolower($b["groupname"]))
		     {
		         if (strtolower($a["nickname"]) == strtolower($b["nickname"]))
			     {
			         if (strtolower($a["partyname"]) == strtolower($b["partyname"]))
				     {
				         return 0;
				     }
				     return (strtolower($a["partyname"]) < strtolower($b["partyname"])) ? -1 : 1;
			     }
			     return (strtolower($a["nickname"]) < strtolower($b["nickname"])) ? -1 : 1;
		     }
		     return (strtolower($a["groupname"]) < strtolower($b["groupname"])) ? -1 : 1;
	     }
	     return ($a["type"] < $b["type"]) ? -1 : 1;
	}
}

/*function reorder_date($a, $b)
{
     if ($a["date"] == $b["date"])
     {
         return 0;
     }
     return ($a["date"] > $b["date"]) ? -1 : 1;
}

function reorder_id($a, $b)
{
     if ($a["id"] == $b["id"])
     {
         return 0;
     }
     return ($a["id"] > $b["id"]) ? -1 : 1;
}*/

function reorder_id_and_date($a, $b)
{
	if (($a["type"] == "prod") && ($b["type"] == "prod")) {
	     if ($a["date"] == $b["date"])
	     {
	     	return ($a["id"] > $b["id"]) ? -1 : 1;
	     }
	     return ($a["date"] > $b["date"]) ? -1 : 1;
	}
	else {
	     if ($a["type"] == $b["type"])
	     {
	         if (strtolower($a["groupname"]) == strtolower($b["groupname"]))
		     {
		         if (strtolower($a["nickname"]) == strtolower($b["nickname"]))
			     {
			         if (strtolower($a["partyname"]) == strtolower($b["partyname"]))
				     {
				         return 0;
				     }
				     return (strtolower($a["partyname"]) < strtolower($b["partyname"])) ? -1 : 1;
			     }
			     return (strtolower($a["nickname"]) < strtolower($b["nickname"])) ? -1 : 1;
		     }
		     return (strtolower($a["groupname"]) < strtolower($b["groupname"])) ? -1 : 1;
	     }
	     return ($a["type"] < $b["type"]) ? -1 : 1;
	}
}


function reorder_partycompo($a, $b)
{
	if (($a["type"] == "prod") && ($b["type"] == "prod")) {
     if ($a["partycompo"] == $b["partycompo"])
     {
         return 0;
     }
     return ($a["partycompo"] < $b["partycompo"]) ? -1 : 1;
     }
	else {
	     if ($a["type"] == $b["type"])
	     {
	         if (strtolower($a["groupname"]) == strtolower($b["groupname"]))
		     {
		         if (strtolower($a["nickname"]) == strtolower($b["nickname"]))
			     {
			         if (strtolower($a["partyname"]) == strtolower($b["partyname"]))
				     {
				         return 0;
				     }
				     return (strtolower($a["partyname"]) < strtolower($b["partyname"])) ? -1 : 1;
			     }
			     return (strtolower($a["nickname"]) < strtolower($b["nickname"])) ? -1 : 1;
		     }
		     return (strtolower($a["groupname"]) < strtolower($b["groupname"])) ? -1 : 1;
	     }
	     return ($a["type"] < $b["type"]) ? -1 : 1;
	}
}

function reorder_name($a, $b)
{
	if (($a["type"] == "prod") && ($b["type"] == "prod")) {
     if (strtolower($a["name"]) == strtolower($b["name"]))
     {
         return 0;
     }
     return (strtolower($a["name"]) < strtolower($b["name"])) ? -1 : 1;
     }
	else {
	     if ($a["type"] == $b["type"])
	     {
	         if (strtolower($a["groupname"]) == strtolower($b["groupname"]))
		     {
		         if (strtolower($a["nickname"]) == strtolower($b["nickname"]))
			     {
			         if (strtolower($a["partyname"]) == strtolower($b["partyname"]))
				     {
				         return 0;
				     }
				     return (strtolower($a["partyname"]) < strtolower($b["partyname"])) ? -1 : 1;
			     }
			     return (strtolower($a["nickname"]) < strtolower($b["nickname"])) ? -1 : 1;
		     }
		     return (strtolower($a["groupname"]) < strtolower($b["groupname"])) ? -1 : 1;
	     }
	     return ($a["type"] < $b["type"]) ? -1 : 1;
	}
}

function reorder_platform($a, $b)
{
	if (($a["type"] == "prod") && ($b["type"] == "prod")) {
     if ($a["platform"] == $b["platform"])
     {
         return 0;
     }
     return ($a["platform"] < $b["platform"]) ? -1 : 1;
     }
	else {
	     if ($a["type"] == $b["type"])
	     {
	         if (strtolower($a["groupname"]) == strtolower($b["groupname"]))
		     {
		         if (strtolower($a["nickname"]) == strtolower($b["nickname"]))
			     {
			         if (strtolower($a["partyname"]) == strtolower($b["partyname"]))
				     {
				         return 0;
				     }
				     return (strtolower($a["partyname"]) < strtolower($b["partyname"])) ? -1 : 1;
			     }
			     return (strtolower($a["nickname"]) < strtolower($b["nickname"])) ? -1 : 1;
		     }
		     return (strtolower($a["groupname"]) < strtolower($b["groupname"])) ? -1 : 1;
	     }
	     return ($a["type"] < $b["type"]) ? -1 : 1;
	}
}

/*function reorder_views($a, $b)
{
     if ($a["views"] == $b["views"])
     {
         return 0;
     }
     return ($a["views"] > $b["views"]) ? -1 : 1;
}

function reorder_thumbup($a, $b)
{
     if ($a["voteup"] == $b["voteup"])
     {
         return 0;
     }
     return ($a["voteup"] > $b["voteup"]) ? -1 : 1;
}

function reorder_thumbpig($a, $b)
{
     if ($a["votepig"] == $b["votepig"])
     {
         return 0;
     }
     return ($a["votepig"] > $b["votepig"]) ? -1 : 1;
}


function reorder_thumbdown($a, $b)
{
     if ($a["votedown"] == $b["votedown"])
     {
         return 0;
     }
     return ($a["votedown"] > $b["votedown"]) ? -1 : 1;
}

function reorder_avg($a, $b)
{
     if ($a["voteavg"] == $b["voteavg"])
     {
         return 0;
     }
     return ($a["voteavg"] > $b["voteavg"]) ? -1 : 1;
}*/


if(!$pattern&&!$which) {
  $pattern=chr(mt_rand(96,122));
  if($pattern==chr(96)) {
    $pattern="#";
  }
}

if($which) {
  	$query="SELECT lists.id,lists.name,lists.desc,lists.upkeeper,lists.added,users.nickname,users.avatar FROM lists LEFT JOIN users on users.id=lists.upkeeper WHERE lists.id=".$which;
} elseif($pattern) {
  if($pattern=="#") {
    $sqlwhere="(name LIKE '0%')||(name LIKE '1%')||(name LIKE '2%')||(name LIKE '3%')||(name LIKE '4%')||(name LIKE '5%')||(name LIKE '6%')||(name LIKE '7%')||(name LIKE '8%')||(name LIKE '9%')";
  } else {
    $sqlwhere="name LIKE '".$pattern."%'";
  }
  $query="SELECT lists.id,lists.name,lists.desc,lists.upkeeper,lists.added,users.nickname,users.avatar FROM lists LEFT JOIN users on users.id=lists.upkeeper WHERE (".$sqlwhere.") ORDER BY name";
}
$result = mysql_query($query);
while($tmp = mysql_fetch_array($result)) {
  $lists[]=$tmp;
}
if($which) {

  	$query="SELECT * from listitems WHERE list=$which ORDER BY listitems.type";
	$result = mysql_query($query);
	//print("->".$query);
	while($tmp = mysql_fetch_array($result)) {
		//print("\n->".$tmp["type"]);
		if($tmp["type"]=="prod") {
			//print("kaja");
			  	$prodquery="SELECT prods.id,prods.name,prods.views,prods.type,prods.date,prods.party,prods.party_year,prods.party_place,prods.partycompo,prods.group1,prods.group2,prods.group3,parties1.name as partyname FROM prods LEFT JOIN parties as parties1 ON parties1.id=prods.party WHERE prods.id='".$tmp["itemid"]."' LIMIT 1";
				$prodresult = mysql_query($prodquery);
				$tmp2 = mysql_fetch_array($prodresult);
				$tmp["id"]=$tmp2["id"];
				$tmp["name"]=$tmp2["name"];
				$tmp["views"]=$tmp2["views"];
				$tmp["prodtype"]=$tmp2["type"];
				$tmp["date"]=$tmp2["date"];
				$tmp["party"]=$tmp2["party"];
				$tmp["party_year"]=$tmp2["party_year"];
				$tmp["party_place"]=$tmp2["party_place"];
				$tmp["partycompo"]=$tmp2["partycompo"];
				$tmp["group1"]=$tmp2["group1"];
				$tmp["group2"]=$tmp2["group2"];
				$tmp["group3"]=$tmp2["group3"];
				$tmp["partyname"]=$tmp2["partyname"];
				if ($tmp["group1"]):
					$gquery="select name,acronym from groups where id='".$tmp["group1"]."'";
					$gresult=mysql_query($gquery);
					while($gtmp = mysql_fetch_array($gresult)) {
					  $tmp["groupname1"]=$gtmp["name"];
					  $tmp["groupacron1"]=$gtmp["acronym"];
					 }
				endif;
				if ($tmp["group2"]):
					$gquery="select name,acronym from groups where id='".$tmp["group2"]."'";
					$gresult=mysql_query($gquery);
					while($gtmp = mysql_fetch_array($gresult)) {
					  $tmp["groupname2"]=$gtmp["name"];
					  $tmp["groupacron2"]=$gtmp["acronym"];
					 }
				endif;
				if ($tmp["group3"]):
					$gquery="select name,acronym from groups where id='".$tmp["group3"]."'";
					$gresult=mysql_query($gquery);
					while($gtmp = mysql_fetch_array($gresult)) {
					  $tmp["groupname3"]=$gtmp["name"];
					  $tmp["groupacron3"]=$gtmp["acronym"];
					 }
				endif;

				if (strlen($tmp["groupname1"].$tmp["groupname2"].$tmp["groupname3"])>27):
					if (strlen($tmp["groupname1"])>10 && $tmp["groupacron1"]) $tmp["groupname1"]=$tmp["groupacron1"];
					if (strlen($tmp["groupname2"])>10 && $tmp["groupacron2"]) $tmp["groupname2"]=$tmp["groupacron2"];
					if (strlen($tmp["groupname3"])>10 && $tmp["groupacron3"]) $tmp["groupname3"]=$tmp["groupacron3"];
				endif;

				//get platforms
				$pltquery="select platforms.name from prods_platforms, platforms where prods_platforms.prod='".$tmp["id"]."' and platforms.id=prods_platforms.platform";
				$pltresult=mysql_query($pltquery);
				$check=0;
				$tmp["platform"]="";
				while($pltmp = mysql_fetch_array($pltresult)) {
				  if ($check>0) $tmp["platform"].=",";
				  $check++;
				  $tmp["platform"].=$pltmp["name"];
				}

				//get array of sceneorgrecommendations for this group
				$result2=mysql_query("SELECT * from sceneorgrecommended where prodid=".$tmp["itemid"]." ORDER BY type");
				while($tmp2=mysql_fetch_array($result2)) {
		  			$sceneorgrecommends[]=$tmp2;
				}
		} elseif($tmp["type"]=="party") {
			  	$prodquery="SELECT name,web from parties where id='".$tmp["itemid"]."' LIMIT 1";
				$prodresult = mysql_query($prodquery);
				$tmp2 = mysql_fetch_array($prodresult);
				$tmp["partyname"]=$tmp2["name"];
				$tmp["partyweb"]=$tmp2["web"];
/*		} elseif($tmp["type"]=="partylinks") {
			  	$prodquery="SELECT name from parties where id='".$tmp["itemid"]."' LIMIT 1";
				$prodresult = mysql_query($prodquery);
				$tmp2 = mysql_fetch_array($prodresult);
				$tmp["partylinkname"]=$tmp2["name"];
				$prodquery="SELECT * from partylinks where id='".$tmp["itemid"]."' and year='".$tmp["itempartyyear"]."' LIMIT 1";
				$prodresult = mysql_query($prodquery);
				$tmp2 = mysql_fetch_array($prodresult);
				$tmp["partyname"]=$tmp2["name"];
*/		} elseif($tmp["type"]=="user") {
			  	$prodquery="SELECT nickname,avatar from users where id='".$tmp["itemid"]."' LIMIT 1";
				$prodresult = mysql_query($prodquery);
				$tmp2 = mysql_fetch_array($prodresult);
				$tmp["nickname"]=$tmp2["nickname"];
				$tmp["avatar"]=$tmp2["avatar"];
		} elseif($tmp["type"]=="group") {
			  	$prodquery="SELECT name,acronym,web,csdb,zxdemo from groups where id='".$tmp["itemid"]."' LIMIT 1";
				$prodresult = mysql_query($prodquery);
				$tmp2 = mysql_fetch_array($prodresult);
				$tmp["groupname"]=$tmp2["name"];
				$tmp["groupacronym"]=$tmp2["acronym"];
				$tmp["groupweb"]=$tmp2["web"];
				$tmp["groupcsdb"]=$tmp2["csdb"];
				$tmp["groupzxdemo"]=$tmp2["zxdemo"];
	/*	} elseif($tmp["type"]=="lists") {
			  	$prodquery="SELECT lists.name,lists.desc,lists.upkeeper,users.nickname,users.avatar from lists LEFT JOIN users ON users.id=lists.upkeeper where id='".$tmp["itemid"]."' LIMIT 1";
				$prodresult = mysql_query($prodquery);
				$tmp2 = mysql_fetch_array($prodresult);
				$tmp["listname"]=$tmp2["name"];
				$tmp["desc"]=$tmp2["desc"];
				$tmp["upkeeper"]=$tmp2["upkeeper"];
				$tmp["upkeepernickname"]=$tmp2["nickname"];
				$tmp["upkeeperavatar"]=$tmp2["avatar"];
	*/	}
  	 $listitems[]=$tmp;
	}

	switch($prodorder) {
	  case "type": usort($listitems, "reorder_id_and_date");
	  	       usort($listitems, "reorder_prodtype"); break;
	  case "name": usort($listitems, "reorder_name"); break;
	  case "release": usort($listitems, "reorder_id_and_date"); break;
	  case "platform": usort($listitems, "reorder_id_and_date");
	  		   usort($listitems, "reorder_platform"); break;
/*	  case "views": usort($listitems, "reorder_views"); break;
	  case "thumbup": usort($listitems, "reorder_thumbup"); break;
	  case "thumbpig": usort($listitems, "reorder_thumbpig"); break;
	  case "thumbdown": usort($listitems, "reorder_thumbdown"); break;
	  case "avg": usort($listitems, "reorder_avg"); break;*/
	  default: case "release": usort($listitems, "reorder_id_and_date"); break;
	           $prodorder="release";
	           break;
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
    <? $sortlink="lists.php?which=".$which."&prodorder="; ?>
    <tr bgcolor="#224488">
     <th colspan="9">
     <center>
     <?
     	$i=0;
	//print("<b><a href=\"lists.php?which=".$lists[$i]["id"]."\">".$lists[$i]["name"]."</a></b> - ".$lists[$i]["desc"]);
	print("<b><a href=\"lists.php?which=".$lists[$i]["id"]."\">".$lists[$i]["name"]."</a></b>");
	if($SESSION_LEVEL=='administrator' || $SESSION_LEVEL=='moderator' || $SESSION_LEVEL=='gloperator')
	  print(" <b>[<a href=\"editlist.php?which=".$which."\">editlist</a>]</b>\n");

	$query = "SELECT upkeeper FROM lists where id='".$lists[$i]["id"]."'";
  	$result=mysql_query($query);
  	$listupkeeper=mysql_result($result,0);
	if($_SESSION["SCENEID_ID"]==$listupkeeper || $SESSION_LEVEL=='administrator' || $SESSION_LEVEL=='moderator' || $SESSION_LEVEL=='gloperator') {
		print(" <b>[<a href=\"submitlistitem.php?which=".$which."\">add item</a>]</b>\n");
		print(" <b>[<a href=\"removelistitem.php?which=".$which."\">del item</a>]</b>\n");
	}

     ?></center>
     </th>
     </tr>
    <? if(count($lists)==0): ?>
    <tr bgcolor="#557799">
     <th colspan="3">
      <br />
       congratulations!! you just found a non existant list!!!!11<br />
      <br />
     </td>
    </tr>
   <? endif; ?>
   <? else: ?>
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
         <b>desc</b>
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
         <b>upkeeper</b>
        </td>
       </tr>
      </table>
     </th>
    </tr>
    <? if(count($lists)==0): ?>
    <tr bgcolor="#557799">
     <th colspan="3">
      <br />
      no list name beginning with a <b><? print($pattern); ?></b> yet =(<br />
      <br />
     </td>
    </tr>
    <? endif; ?>
   <? endif; ?>

   <? if (!$which){

   	for($i=0;$i<count($lists);$i++)
   	{
     		if($i%2) {
       			print("<tr bgcolor=\"#446688\">\n");
     		} else {
       			print("<tr bgcolor=\"#557799\">\n");
     		}
     		print("<td valign=\"top\"><b><a href=\"lists.php?which=".$lists[$i]["id"]."\">".$lists[$i]["name"]);
     		print("</a></b>");
     		if($SESSION_LEVEL=='administrator' || $SESSION_LEVEL=='moderator' || $SESSION_LEVEL=='gloperator') print(" <b>[<a href=\"editlist.php?which=".$lists[$i]["id"]."\">editlist</a>]</b>\n");
     		print("</td>\n");
     		print("<td>".$lists[$i]["desc"]."</td>\n");
     		print("<td><a href=\"user.php?who=".$lists[$i]["upkeeper"]."\">".$lists[$i]["nickname"]."</a></td>\n</tr>\n");
     	}

     } else {
     	//print("->kok9".count($listitems));
		for($i=0;$i<count($listitems);$i++):
		 //print("><pokachu".$i."->".$listitems[$i]["type"]);
	  	 if ($listitems[$i]["type"]!=$listitems[$i-1]["type"]):
	  	    if ($listitems[$i]["type"]=="prod")
	  	    { //print("kakak000");

	  	    	?>
	  	      <tr bgcolor="#224488">
     		      <th><table><tr>
		       <td>
		        <a href="<? print($sortlink); ?>type"><img src="gfx/<? print(goodfleche("prodtype",$order)); ?>.gif" width="13" height="12" border="0"></a><br />
		       </td>
		       <td>
		        <a href="<? print($sortlink); ?>type"><b>type</b></a><br />
		       </td>
		       <td>
		        <a href="<? print($sortlink); ?>name"><img src="gfx/<? print(goodfleche("name",$order)); ?>.gif" width="13" height="12" border="0"></a><br />
		       </td>
		       <td width="100%">
		        <a href="<? print($sortlink); ?>name"><b>prodname</b></a><br />
		       </td>
		       <td align="right">
		        <a href="<? print($sortlink); ?>platform"><img src="gfx/<? print(goodfleche("platform",$order)); ?>.gif" width="13" height="12" border="0"></a><br />
		       </td align="right">
		       <td>
		        <a href="<? print($sortlink); ?>platform"><b>platform</b></a>
		       </td>
		       </tr></table>
		     </th>
		     <th>
		      <table><tr>
		       <td>
		        <a href="<? print($sortlink); ?>party"><img src="gfx/<? print(goodfleche("party",$order)); ?>.gif" width="13" height="12" border="0"></a><br />
		       </td>
		       <td>
		        <a href="<? print($sortlink); ?>party"><b>release party</b></a>
		       </td>
		      </tr></table>
		     </th>
		     <th>
		      <table><tr>
		       <td>
		        <a href="<? print($sortlink); ?>release"><img src="gfx/<? print(goodfleche("release",$order)); ?>.gif" width="13" height="12" border="0"></a><br />
		       </td>
		       <td>
		        <a href="<? print($sortlink); ?>release"><b>release date</b></a>
		       </td>
		      </tr></table>
		     </th>
<? /*		     <th>
		        <a href="<? print($sortlink); ?>thumbup"><img src="gfx/rulez.gif" alt="rulez" border="0"></a>
		     </th>
		     <th>
		        <a href="<? print($sortlink); ?>thumbpig"><img src="gfx/isok.gif" alt="piggie" border="0"></a>
		     </th>
		     <th>
		        <a href="<? print($sortlink); ?>thumbdown"><img src="gfx/sucks.gif" alt="sucks" border="0"></a>
		     </th>
		     <th>
		      <table><tr>
		       <td>
		        <a href="<? print($sortlink); ?>avg"><img src="gfx/<? print(goodfleche("avg",$order)); ?>.gif" width="13" height="12" border="0"></a><br />
		       </td>
		       <td>
		        <a href="<? print($sortlink); ?>avg"><b>avg</b></a>
		       </td>
		      </tr></table>
		     </th> */ ?>
		     </tr>
	  	    	<?
	  		} else {
		   	 print("<tr bgcolor=\"#224488\"><th colspan=\"9\">".$listitems[$i]["type"]."<br /></th></tr>");
		   	}
		 endif;

		 if($i%2) {
       			print("<tr bgcolor=\"#446688\">\n");
     		 } else {
       			print("<tr bgcolor=\"#557799\">\n");
     		 }
			//print("pokaman");
		 if ($listitems[$i]["type"]=="prod"):

			$typess = explode(",", $listitems[$i]["prodtype"]);
			print("<td nowrap><table cellspacing=\"0\" cellpadding=\"0\"><tr><td nowrap><a href=\"prod.php?which=".$listitems[$i]["id"]."\">");
			for($k=0;$k<count($typess);$k++) {
			print("<img src=\"gfx/types/".$types[$typess[$k]]."\" width=\"16\" height=\"16\" border=\"0\" title=\"".$typess[$k]."\">");
			}
			print("<br /></a></td><td><img src=\"gfx/z.gif\" width=\"2\" height=\"1\" border=\"0\"><br /></td><td nowrap><a href=\"prod.php?which=".$listitems[$i]["id"]."\">".strtolower(stripslashes($listitems[$i]["name"]))."</a>");
			if ($listitems[$i]["group1"]) { print(" by <a href=\"groups.php?which=".$listitems[$i]["group1"]."\">".$listitems[$i]["groupname1"]."</a>"); }
			if ($listitems[$i]["group2"]) { print(" &amp; <a href=\"groups.php?which=".$listitems[$i]["group2"]."\">".$listitems[$i]["groupname2"]."</a>"); }
			if ($listitems[$i]["group3"]) { print(" &amp; <a href=\"groups.php?which=".$listitems[$i]["group3"]."\">".$listitems[$i]["groupname3"]."</a>"); }
			print("<br /></td><td>&nbsp;</td>");

			if(count($sceneorgrecommends)):
	        	print("<td nowrap>");
			for($k=0;$k<count($sceneorgrecommends);$k++) {
				if ($sceneorgrecommends[$k]["prodid"]==$listitems[$i]["id"]) print("<img src=\"gfx/sceneorg/".$sceneorgrecommends[$k]["type"].".gif\" width=\"16\" height=\"16\" border=\"0\" title=\"".$sceneorgrecommends[$k]["category"]."\" alt=\"".$sceneorgrecommends[$k]["category"]."\">");
			}
			print("<br /></td>");
			endif;

			if($listitems[$i]["cdc"]):
			 print("<td nowrap>");
			 for($ii=0;$ii<$prods[$j]["cdc"];$ii++): print("<img src=\"gfx/titles/coupdecoeur.gif\" width=\"16\" height=\"16\" border=\"0\" title=\"cdc\" alt=\"cdc\">");
			 endfor;
			 print("<br /></td>");
			endif;

			print("<td width=\"100%\">&nbsp;</td>");

	       		$platforms = explode(",", $listitems[$i]["platform"]);
	       		for($kkk=0; $kkk<count($platforms); $kkk++){
	       		 print("<td align=\"right\"><a href=\"prodlist.php?platform=".$platforms[$kkk]."\"><img src=\"gfx/os/".$os[$platforms[$kkk]]."\" width=\"16\" height=\"16\" border=\"0\" title=\"".$platforms[$kkk]."\"></a><br /></td>");
	       		}

	       		print("</tr></table></td>\n");

			if(($listitems[$i]["partyname"])&&!($listitems[$i]["party"]==1024))
			{
				$placeadj="";
				if ($listitems[$i]["partycompo"]=="") $compophrase="";
				 else $compophrase=" ".$listitems[$i]["partycompo"];
				if (($listitems[$i]["partycompo"]=="none")||($listitems[$i]["partycompo"]=="invit")) $listitems[$i]["party_place"]=98;

				if($listitems[$i]["party_place"])
				{
		           		switch($listitems[$i]["party_place"]) {
		             		case 1:
		             		case 21:
		             		case 31:
		             		case 41:
		             		case 51:
		             		case 61:
		             		case 71:
		             		case 81:
		             		case 91:  $placeadj="st";
		             			print("<td>".$listitems[$i]["party_place"].$placeadj." at <a href=\"party.php?which=".$listitems[$i]["party"]."&when=".sprintf("%02d",$listitems[$i]["party_year"])."\">".$listitems[$i]["partyname"]." ".sprintf("%02d",$listitems[$i]["party_year"])."</a>".$compophrase."<br /></td>\n");
		             			break;
		             		case 2:
		             		case 22:
		             		case 32:
		             		case 42:
		             		case 52:
		             		case 62:
		             		case 72:
		             		case 82:
		             		case 92:  $placeadj="nd";
		             			print("<td>".$listitems[$i]["party_place"].$placeadj." at <a href=\"party.php?which=".$listitems[$i]["party"]."&when=".sprintf("%02d",$listitems[$i]["party_year"])."\">".$listitems[$i]["partyname"]." ".sprintf("%02d",$listitems[$i]["party_year"])."</a>".$compophrase."<br /></td>\n");
		             			break;
		             		case 3:
		             		case 23:
		             		case 33:
		             		case 43:
		             		case 53:
		             		case 63:
		             		case 73:
		             		case 83:
		             		case 93:  $placeadj="rd";
		             			print("<td>".$listitems[$i]["party_place"].$placeadj." at <a href=\"party.php?which=".$listitems[$i]["party"]."&when=".sprintf("%02d",$listitems[$i]["party_year"])."\">".$listitems[$i]["partyname"]." ".sprintf("%02d",$listitems[$i]["party_year"])."</a>".$compophrase."<br /></td>\n");
		             			break;
		                	case 97: print("<td>disqualified at <a href=\"party.php?which=".$listitems[$i]["party"]."&when=".sprintf("%02d",$listitems[$i]["party_year"])."\">".$listitems[$i]["partyname"]." ".sprintf("%02d",$listitems[$i]["party_year"])."</a>".$compophrase."<br /></td>\n");
						break;
		             		case 98: print("<td>for <a href=\"party.php?which=".$listitems[$i]["party"]."&when=".sprintf("%02d",$listitems[$i]["party_year"])."\">".$listitems[$i]["partyname"]." ".sprintf("%02d",$listitems[$i]["party_year"])."</a><br /></td>\n");
		             			break;
		             		case 99: print("<td>not shown at <a href=\"party.php?which=".$prods[$j]["party"]."&when=".sprintf("%02d",$listitems[$i]["party_year"])."\">".$listitems[$i]["partyname"]." ".sprintf("%02d",$listitems[$i]["party_year"])."</a>".$compophrase."<br /></td>\n");
		             			break;
		             		default: $placeadj="th";
		             			print("<td>".$listitems[$i]["party_place"].$placeadj." at <a href=\"party.php?which=".$listitems[$i]["party"]."&when=".sprintf("%02d",$listitems[$i]["party_year"])."\">".$listitems[$i]["partyname"]." ".sprintf("%02d",$listitems[$i]["party_year"])."</a>".$compophrase."<br /></td>\n");
		             			break;
		           		}
		         	} else
		         	{
		         		 $placeadj = "??";
		         		 print("<td>".$listitems[$i]["party_place"].$placeadj." at <a href=\"party.php?which=".$listitems[$i]["party"]."&when=".sprintf("%02d",$listitems[$i]["party_year"])."\">".$listitems[$i]["partyname"]." ".sprintf("%02d",$listitems[$i]["party_year"])."</a>".$compophrase."<br /></td>\n");
				}
		        } else {
		       	  if ($listitems[$i]["party"]==1024) print("<td>no party<br /></td>\n");
		       	   else print("<td>??<br /></td>\n");
		        }

			if(($listitems[$i]["date"]!="0000-00-00")&&(strlen($listitems[$i]["date"])>0))
		       	{
		          $rdate=explode("-",$listitems[$i]["date"]);
		          switch($rdate[1]) {
		            case "01": $rmonth="January"; break;
		            case "02": $rmonth="February"; break;
		            case "03": $rmonth="March"; break;
		            case "04": $rmonth="April"; break;
		            case "05": $rmonth="May"; break;
		            case "06": $rmonth="June"; break;
		            case "07": $rmonth="July"; break;
		            case "08": $rmonth="August"; break;
		            case "09": $rmonth="September"; break;
		            case "10": $rmonth="October"; break;
		            case "11": $rmonth="November"; break;
		            case "12": $rmonth="December"; break;
		            default: $rmonth=""; break;
		           }
		           print("<td>".$rmonth." ".$rdate[0]."<br /></td>\n");
			} else {
				print("<td>&nbsp;<br /></td>\n");
			}

	/*		if ($listitems[$i]["voteup"])
			{ print("<td>\n".$listitems[$i]["voteup"]."</td>\n");
			}
			else
			{print("<td>\n0</td>\n");
			}
			if ($listitems[$i]["votepig"])
			{ print("<td>\n".$listitems[$i]["votepig"]."</td>\n");
			}
			else
			{print("<td>\n0</td>\n");
			}
			if ($listitems[$i]["votedown"])
			{ print("<td>\n".$listitems[$i]["votedown"]."</td>\n");
			}
			else
			{print("<td>\n0</td>\n");
			}

			if($listitems[$i]["voteavg"]>0)
				$thumbgfx="gfx/rulez.gif";
			elseif($listitems[$i]["voteavg"]==0)
				$thumbgfx="gfx/isok.gif";
			else
				$thumbgfx="gfx/sucks.gif";
			printf("<td>\n<table cellspacing=\"0\" cellpadding=\"0\"><tr><td>&nbsp;</td><td>%.2f</td><td>&nbsp;</td><td><img src=\"".$thumbgfx."\" width=\"16\" height=\"16\" border=\"0\" alt=\"average rating\" align=\"left\"></td></tr></table></td>\n",$listitems[$i]["voteavg"]);

			//popularity bar
			print("<td>\n");
			$pourcent = floor($listitems[$i]["views"]*100/$max_views);
			DoBar($pourcent);
			print("</td>\n");	*/


		 /* ?>
			<td><a href="prod.php?which=<? print($listitems[$i]["id"]); ?>"><? print($listitems[$i]["name"]); ?></a>
			<? if($listitems[$i]["group1"]) { print(" by"); } ?>
			<a href="groups.php?which=<?=$listitems[$i]["group1"]?>"><?=$listitems[$i]["groupname1"]?></a>
			<? if ($listitems[$i]["groupname2"]) {print(" &amp; ");} ?><a href="groups.php?which=<?=$listitems[$i]["group2"]?>"><?=$listitems[$i]["groupname2"]?></a>
			<? if ($listitems[$i]["groupname3"]) {print(" &amp; ");} ?><a href="groups.php?which=<?=$listitems[$i]["group3"]?>"><?=$listitems[$i]["groupname3"]?></a>
			<br /></td> */
		 elseif ($listitems[$i]["type"]=="user"):  ?>
		  <td colspan="9">
		   <table cellspacing="0" cellpadding="0">
		    <tr>
		     <td><a href="user.php?who=<?=$listitems[$i]["itemid"]?>"><img src="avatars/<?=$listitems[$i]["avatar"]?>" width="16" height="16" border="0" title="<?=$listitems[$i]["nickname"]?>"></a><br /></td>
	             <td><img src="gfx/z.gif" width="3" height="1"><br /></td>
	             <td><a href="user.php?who=<? print($listitems[$i]["itemid"]); ?>"><? print($listitems[$i]["nickname"]); ?></a><br /></td>
	            </tr>
	           </table>
	          </td>
		<? elseif ($listitems[$i]["type"]=="party"):  ?>
		<td colspan="9"><a href="party.php?which=<? print($listitems[$i]["itemid"]); ?>"><? print($listitems[$i]["partyname"]); ?></a>
		 <? if($listitems[$i]["partyweb"]) print(" [<a href=\"".$listitems[$i]["partyweb"]."\">web</a>]\n"); ?><br /></td>
		<? elseif ($listitems[$i]["type"]=="group"):  ?>
		<td colspan="9"><a href="groups.php?which=<? print($listitems[$i]["itemid"]); ?>"><? print($listitems[$i]["groupname"]);
		 if($listitems[$i]["groupacronym"]) print(" [".$listitems[$i]["groupacronym"]."]"); ?></a>
		 <? if($listitems[$i]["groupweb"]) print(" [<a href=\"".$listitems[$i]["groupweb"]."\">web</a>]\n");
		 if($listitems[$i]["groupcsdb"]) print(" [<a href=\"http://noname.c64.org/csdb/group/?id=".$listitems[$i]["groupcsdb"]."\">csdb</a>]\n");
		 if($listitems[$i]["groupzxdemo"]) print(" [<a href=\"http://zxdemo.org/author.php?id=".$listitems[$i]["groupzxdemo"]."\">zxdemo</a>]\n"); ?>
		 <br /></td>
		<? else: print("<td>".$listitems[$i]["itemid"]."<br /></td>"); ?>
		<? endif; ?>


     	 </td>
     	</tr>
     	<?
     	endfor;
     }
     print("<tr bgcolor=\"#224488\">");
     if($which):
       if(count($lists)!=0): ?>
       <td colspan="3" align="right">
        <table cellspacing="0" cellpadding="0">
         <tr>
          <td>added on the <? print(substr($lists[0]["added"],0,10)); ?> for <a href="user.php?who=<? print($lists[0]["upkeeper"]); ?>"><? print($lists[0]["nickname"]); ?></a></td>
          <td>&nbsp;<br /></td>
          <td><a href="user.php?who=<? print($lists[0]["upkeeper"]); ?>"><img src="avatars/<? print($lists[0]["avatar"]); ?>" width="16" height="16" border="0"></a></td>
         </tr>
        </table>
       </td>
      <? endif; ?>
     <? else: ?>
      <th colspan="3">
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

