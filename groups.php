<?
require("include/top.php");

function lettermenu($pattern) {
  print("[ ");
  if($pattern=="#") {
    print("<b>#</b>");
  } else {
    print("<a href=\"groups.php?pattern=%23\">#</a>");
  }
  for($i=1;$i<=26;$i++) {
    print(" | ");
    if($pattern==chr(96+$i)) {
      print("<b>".chr(96+$i)."</b>");
    } else {
      print("<a href=\"groups.php?pattern=".chr(96+$i)."\">".chr(96+$i)."</a>");
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
if (strlen($pattern) > 1)
{
	$pattern = $pattern[0];
}
$which=intval($_REQUEST['which']);
if(!$pattern&&!$which) {
  $pattern=chr(mt_rand(96,122));
  if($pattern==chr(96)) {
    $pattern="#";
  }
}

/*
if ($which == 1317 && strstr($_SERVER["HTTP_USER_AGENT"],"Yahoo Pipes")!==false) {
  // ha ha asd
  $a = array(796,5718,1564,2085);
  $which = $a[ array_rand($a) ];
}
*/

if($which) {
$query = "SELECT group2 from groupsaka WHERE group1=".$which;
$result = mysql_query($query);
while($tmp=mysql_fetch_array($result)) {
  $groupaka[]=$tmp;
}


  $query="SELECT id,name,acronym,web,csdb,zxdemo,added,quand FROM groups WHERE id=".$which;
  for($i=0;$i<count($groupaka);$i++) { $query.=" OR id=".$groupaka[$i]["group2"]; }
} elseif($pattern) {
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
if($which) {
  	$query="SELECT prods.id,prods.name,prods.group1,prods.group2,prods.group3,prods.type,prods.partycompo,prods.date,prods.party,prods.party_year,prods.party_place,prods.views,prods.voteup,prods.votepig,prods.votedown,prods.voteavg,parties1.name as partyname,".
  	" g1.id as g1id,g1.name as g1name,g2.id as g2id,g2.name as g2name,g3.id as g3id,g3.name as g3name ".
  	" FROM prods LEFT JOIN parties as parties1 ON parties1.id=prods.party ".
  	" LEFT JOIN groups as g1 ON g1.id=prods.group1 ".
  	" LEFT JOIN groups as g2 ON g2.id=prods.group2 ".
  	" LEFT JOIN groups as g3 ON g3.id=prods.group3 ".
  	" WHERE prods.group1=".$which." OR prods.group2=".$which." OR prods.group3=".$which;
  	for($i=0;$i<count($groupaka);$i++) { $query.= " OR prods.group1=".$groupaka[$i]['group2']." OR prods.group2=".$groupaka[$i]['group2']." OR prods.group3=".$groupaka[$i]['group2']; }
	switch($order) {
	  case "party": $query.=" ORDER BY prods.party,prods.party_year,prods.name"; break;
	  case "type": $query.=" ORDER BY prods.type,prods.name"; break;
	  //case "platform": $query.=" ORDER BY prods.platform,prods.name"; break;
	  case "views": $query.=" ORDER BY prods.views DESC"; break;
	  case "release": $query.=" ORDER BY prods.date DESC, prods.quand DESC"; break;
  	  case "thumbup": $query.=" ORDER BY prods.voteup DESC, prods.quand DESC"; break;
	  case "thumbpig": $query.=" ORDER BY prods.votepig DESC, prods.quand DESC"; break;
	  case "thumbdown": $query.=" ORDER BY prods.votedown DESC, prods.quand DESC"; break;
	  case "avg": $query.=" ORDER BY prods.voteavg DESC, prods.voteup DESC, prods.quand DESC"; break;
	  default: //if (($order!="avg") && ($order!="avg_rev") && ($order!="thumbs")) { $order="name"; }
	           $query.=" ORDER BY prods.name"; break;
	}
} else {
  #
  # This must be the most ugly thing I have seen... (jeffry)
  #
  #$query="SELECT id,name,group1,group2,group3,type FROM prods WHERE 0";
  #for($i=0;$i<count($groups);$i++) {
  #  $query.=" OR group1=".$groups[$i]["id"]." OR group2=".$groups[$i]["id"]." OR group3=".$groups[$i]["id"];
  #}
  
  # Maybe IN clauses are more efficient than hundreds of equality checks (jeffry)
  foreach ($groups as $group) {
    $groupin.=','.$group["id"];
  }
  $groupin=substr($groupin, 1);
  $query="SELECT id,name,group1,group2,group3,type FROM prods WHERE 0 OR group1 IN ($groupin) OR group2 IN ($groupin) OR group3 IN ($groupin)";
}

$result = mysql_query($query);
while($tmp = mysql_fetch_array($result)) {
  $prods[]=$tmp;
}
if($which) {
	$csdbflag=0;
	$zxdemoflag=0;
	for($i=0;$i<count($prods);$i++) {
			
		//cdc count
		$result=mysql_query("SELECT count(0) from users_cdcs where cdc=".$prods[$i]["id"]);
		$prods[$i]["cdc"]=mysql_result($result,0);
		
		$result=mysql_query("SELECT count(0) from cdc where which=".$prods[$i]["id"]);
		$prods[$i]["cdc"]=$prods[$i]["cdc"]+mysql_result($result,0);
		
		//get latestcomment
		$result=mysql_query("SELECT users.nickname,users.avatar,comments.quand,comments.rating,comments.who from comments LEFT JOIN users ON users.id=comments.who where comments.which=".$prods[$i]["id"]." order by quand desc limit 1");
		$lcom=mysql_fetch_array($result);
		$prods[$i]["lcom_nick"]=$lcom["nickname"];
		$prods[$i]["lcom_avatar"]=$lcom["avatar"];
		$prods[$i]["lcom_quand"]=$lcom["quand"];
		$prods[$i]["lcom_who"]=$lcom["who"];
		$prods[$i]["lcom_rating"]=$lcom["rating"];

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
		
		if ($prods[$i]["platform"]=="Commodore 64") $csdbflag=1;
		if ($prods[$i]["platform"]=="ZX Spectrum") $zxdemoflag=1;

		//get array of sceneorgrecommendations for this group
		$result=mysql_query("SELECT * from sceneorgrecommended where prodid=".$prods[$i]["id"]." ORDER BY type");
		while($tmp=mysql_fetch_array($result)) {
  			$sceneorgrecommends[]=$tmp;
		}
		
		
 	}

	
	//get max_views for popularity
	$result=mysql_query("SELECT MAX(views) FROM prods");
	$max_views=mysql_result($result,0);
  
  	//get user who added this group
  	$query="SELECT id,nickname,avatar FROM users WHERE id=".$groups[0]["added"];
  	$result=mysql_query($query);
  	$myuser=mysql_fetch_array($result);
  	
  	if ($order=="latestcomment") usort($prods, "cmpcomments");

	if (!$groupiesort) $groupiesort=1;
	switch ($groupiesort)
	{
		//groupies
		case 1: $fftype="comments.rating=1";
		break;
		//dissers
		case 2: $fftype="comments.rating=-1";
		break;
		//followers
		default: $fftype="1";
		break;
	}
  	
	if ($groupiesort!=0)
	{
  	//get groupies
  	$sql = "select comments.who AS who,count(0) as c,users.nickname,users.avatar from prods,comments left join users on users.id=comments.who where ".$fftype." and comments.which=prods.id and (prods.group1=".$groups[0]['id']." or prods.group2=".$groups[0]['id']." or prods.group3=".$groups[0]['id'].") group by comments.who order by c DESC LIMIT 10";
  	debuglog($sql);
  	$result=mysql_query($sql);
  	debuglog(mysql_error());
		while($tmp=mysql_fetch_array($result)) {
	  		$groupies[]=$tmp;
		}
		//print(count($groupies)." ".$result);
	}
	
	//get bbsaffils
  	$result=mysql_query("SELECT bbses.id, bbses.name, affiliatedbbses.type from bbses,affiliatedbbses where affiliatedbbses.group=".$groups[0]['id']." and affiliatedbbses.bbs=bbses.id ORDER BY affiliatedbbses.type, bbses.name");
	while($tmp=mysql_fetch_array($result)) {
  		$bbsaffils[]=$tmp;
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
    <? $sortlink="groups.php?which=".$which."&order="; ?>
    <tr bgcolor="#224488">
     <th colspan="9">
     <center>
     <? 
     	$i=0;
	print("<b><a href=\"groups.php?which=".$groups[$i]["id"]."\">".$groups[$i]["name"]);
	if($groups[$i]["acronym"]) print(" [".$groups[$i]["acronym"]."]");
	print("</a></b>");
	if($SESSION_LEVEL=='administrator' || $SESSION_LEVEL=='moderator' || $SESSION_LEVEL=='gloperator') print(" <b>[<a href=\"editgroups.php?which=".$groups[$i]["id"]."\">editgroup</a>]</b>\n");
	if($groups[$i]["web"]) print(" <b>[<a href=\"".$groups[$i]["web"]."\">web</a>]</b>\n");
	if($groups[$i]["csdb"])
		{
		print(" <b>[<a href=\"http://noname.c64.org/csdb/group/?id=".$groups[$i]["csdb"]."\">csdb</a>]</b>\n");
		}
	else
		{
		if(($csdbflag>0)&&($_SESSION["SCENEID_ID"])) print(" <b>[<a href=\"submitgroupcsdb.php?which=".$groups[$i]["id"]."\">+csdb</a>]</b>\n");
		}
	if($groups[$i]["zxdemo"])
		{
		print(" <b>[<a href=\"http://zxdemo.org/author.php?id=".$groups[$i]["zxdemo"]."\">zxdemo</a>]</b>\n");
		}
	else
		{
		if(($zxdemoflag>0)&&($_SESSION["SCENEID_ID"])) print(" <b>[<a href=\"submitgroupzxdemo.php?which=".$groups[$i]["id"]."\">+zxdemo</a>]</b>\n");
		}	
	if (count($groupaka))
	{
	     	print("<b> aka <a href=\"groups.php?which=".$groups[$i+1]["id"]."\">".$groups[$i+1]["name"]);
		if($groups[$i+1]["acronym"]) print(" [".$groups[$i+1]["acronym"]."]");
		print("</a></b>");
		if($SESSION_LEVEL=='administrator' || $SESSION_LEVEL=='moderator' || $SESSION_LEVEL=='gloperator') print(" <b>[<a href=\"editgroups.php?which=".$groups[$i+1]["id"]."\">editgroup</a>]</b>\n");
	if($groups[$i+1]["web"]) print(" <b>[<a href=\"".$groups[$i+1]["web"]."\">web</a>]</b>\n");
	if($groups[$i+1]["csdb"])
		{
		print(" <b>[<a href=\"http://noname.c64.org/csdb/group/?id=".$groups[$i+1]["csdb"]."\">csdb</a>]</b>\n");
		}
	else
		{
		if(($csdbflag>0)&&($_SESSION["SCENEID_ID"])) print(" <b>[<a href=\"submitgroupcsdb.php?which=".$groups[$i+1]["id"]."\">+csdb</a>]</b>\n");
		}
	if($groups[$i+1]["zxdemo"])
		{
		print(" <b>[<a href=\"http://zxdemo.org/author.php?id=".$groups[$i+1]["zxdemo"]."\">zxdemo</a>]</b>\n");
		}
	else
		{
		if(($zxdemoflag>0)&&($_SESSION["SCENEID_ID"])) print(" <b>[<a href=\"submitgroupzxdemo.php?which=".$groups[$i+1]["id"]."\">+zxdemo</a>]</b>\n");
		}	
	}
	else { print("<br />\n"); }
	     		
     ?></center>
     </th>
     </tr>
     <tr bgcolor="#224488">
     <th>
      <table><tr>
       <td>
        <a href="<? print($sortlink); ?>type"><img src="gfx/<? print(goodfleche("type",$order)); ?>.gif" width="13" height="12" border="0"></a><br />
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
    <? /*   <td align="right">
        <a href="<? print($sortlink); ?>platform"><img src="gfx/<? print(goodfleche("platform",$order)); ?>.gif" width="13" height="12" border="0"></a><br />
       </td align="right">
       <td>
        <a href="<? print($sortlink); ?>platform"><b>platform</b></a>
       </td>
   */  ?>     </tr></table>
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
     <th>
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
     </th>
     <th>
      <table><tr>
       <td>
        <a href="<? print($sortlink); ?>views"><img src="gfx/<? print(goodfleche("views",$order)); ?>.gif" width="13" height="12" border="0"></a><br />
       </td>
       <td>
        <a href="<? print($sortlink); ?>views"><b>popularity</b></a>
       </td>
      </tr></table>
     </th>
     <th>
      <table><tr>
       <td>
        <a href="<? print($sortlink); ?>latestcomment"><img src="gfx/<? print(goodfleche("views",$order)); ?>.gif" width="13" height="12" border="0"></a><br />
       </td>
       <td>
        <a href="<? print($sortlink); ?>latestcomment"><b>last comment</b></a>
       </td>
      </tr></table>
     </th>
    </tr>
    <? if(count($groups)==0): ?>
    <tr bgcolor="#557799">
     <th colspan="9">
      <br />
       congratulations! you just found a dupe group that has been deleted from our database!! \o/<br />
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
         <b>groups</b>
        </td>
       </tr>
      </table>
     </th>
     <th><b>prods</b></th>
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
   <? endif; ?>
   <?
   if (!$which)
   {
        # Doing some homework saves us a lot of time lateron (jeffry)
        foreach ($prods as $prod) {
          $prodsbygroup[$prod['group1']][]=$prod;
          $prodsbygroup[$prod['group2']][]=$prod;
          $prodsbygroup[$prod['group3']][]=$prod;
        }

   	for($i=0;$i<count($groups);$i++)
   	{
     		if($i%2) {
       			print("<tr bgcolor=\"#446688\">\n");
     		} else {
       			print("<tr bgcolor=\"#557799\">\n");
     		}
     		print("<td valign=\"top\"><b><a href=\"groups.php?which=".$groups[$i]["id"]."\">".$groups[$i]["name"]);
     		if($groups[$i]["acronym"]) print(" [".$groups[$i]["acronym"]."]");
     		print("</a></b>");
     		if($SESSION_LEVEL=='administrator' || $SESSION_LEVEL=='moderator' || $SESSION_LEVEL=='gloperator') print(" <b>[<a href=\"editgroups.php?which=".$groups[$i]["id"]."\">editgroup</a>]</b>\n");
     		if($groups[$i]["web"]) print(" <b>[<a href=\"".$groups[$i]["web"]."\">web</a>]</b>\n");
     		if($groups[$i]["csdb"]) print(" <b>[<a href=\"http://noname.c64.org/csdb/group/?id=".$groups[$i]["csdb"]."\">csdb</a>]</b>\n");
     		if($groups[$i]["zxdemo"]) print(" <b>[<a href=\"http://zxdemo.org/author.php?id=".$groups[$i]["zxdemo"]."\">zxdemo</a>]</b>\n");
     		print("</td>\n");
     		print("<td>\n<table cellspacing=\"1\" cellpadding=\"0\">\n");
     		$k=0;

		# UGLY UGLY UGLY OLD CODE! (jeffry)
     		#for($j=0;$j<count($prods);$j++) {
      		#if(($prods[$j]["group1"]==$groups[$i]["id"])||($prods[$j]["group2"]==$groups[$i]["id"])||($prods[$j]["group3"]==$groups[$i]["id"]))
      		#{
             	#		
	       	#	$typess = explode(",", $prods[$j]["type"]);
      		#	print("<tr><td><a href=\"prod.php?which=".$prods[$j]["id"]."\">");
      		#	for($kk=0;$kk<count($typess);$kk++) {
      		#		print("<img src=\"gfx/types/".$types[$typess[$kk]]."\" width=\"16\" height=\"16\" border=\"0\" title=\"".$typess[$kk]."\">");
      		#	}
	       	#	print("<br /></a></td><td><img src=\"gfx/z.gif\" width=\"2\" height=\"1\" border=\"0\"><br /></td><td><a href=\"prod.php?which=".$prods[$j]["id"]."\">".strtolower(stripslashes($prods[$j]["name"]))."</a><br /></td></tr>\n");
	      	# 	$k++;
      		#}
     		#}

                # Try this instead, using the homework we did above (jeffry)
                foreach ($prodsbygroup[$groups[$i]["id"]] as $prod) {
                  $typess=explode(",", $prod["type"]);
                  print("<tr><td><a href=\"prod.php?which=".$prod["id"]."\">");
                  for($kk=0;$kk<count($typess);$kk++) {
                    print("<img src=\"gfx/types/".$types[$typess[$kk]]."\" width=\"16\" height=\"16\" border=\"0\" title=\"".$typess[$kk]."\">");
                  }
		  print("<br /></a></td><td><img src=\"gfx/z.gif\" width=\"2\" height=\"1\" border=\"0\"><br /></td><td><a href=\"prod.php?which=".$prod["id"]."\">".strtolower(stripslashes($prod["name"]))."</a><br /></td></tr>\n");
                  $k++;
                } 

     		if(!$k){
       		print("<tr><td><br /></td></tr>\n");
     		}
     		print("</table>\n</td>\n</tr>\n");
     	}
   } else {
			
     	$i=0;
     	if (count($prods)==0)
     	{ print("<tr bgcolor=\"#446688\">\n <td valign=\"top\" colspan=\"9\">no prods on pouet for this group yet!<br /></td>\n</tr>\n");
     	} else {
       	for($j=0;$j<count($prods);$j++) {
       		if($j%2) {
         		print("<tr bgcolor=\"#446688\">\n");
       		} else {
         		print("<tr bgcolor=\"#557799\">\n");
       		}

		$typess = explode(",", $prods[$j]["type"]);
		print("<td nowrap><table cellspacing=\"0\" cellpadding=\"0\"><tr><td nowrap><a href=\"prod.php?which=".$prods[$j]["id"]."\">");
		for($k=0;$k<count($typess);$k++) {
		  print("<img src=\"gfx/types/".$types[$typess[$k]]."\" width=\"16\" height=\"16\" border=\"0\" title=\"".$typess[$k]."\">");
		}
    $s = $prods[$j]["name"];
    $s = stripslashes($s);
    $s = htmlspecialchars($s);
    $s = str_replace("&amp;#","&#",$s);
		
		print("</a></td><td nowrap><a href=\"prod.php?which=".$prods[$j]["id"]."\">".strtolower($s)."</a><br /></td><td>&nbsp;</td>");
		$a = array();
		if ($prods[$j]["g1id"] && $prods[$j]["g1id"]!=$which)
		  $a[] = sprintf("<a href='groups.php?which=%d'>%s</a>",$prods[$j]["g1id"],htmlentities($prods[$j]["g1name"]));
		if ($prods[$j]["g2id"] && $prods[$j]["g2id"]!=$which)
		  $a[] = sprintf("<a href='groups.php?which=%d'>%s</a>",$prods[$j]["g2id"],htmlentities($prods[$j]["g2name"]));
		if ($prods[$j]["g3id"] && $prods[$j]["g3id"]!=$which)
		  $a[] = sprintf("<a href='groups.php?which=%d'>%s</a>",$prods[$j]["g3id"],htmlentities($prods[$j]["g3name"]));
		if (count($a)) 
		  echo "<td nowrap>(with ".implode(", ",$a).")</td>";
		  
		if(count($sceneorgrecommends)):
        	print("<td nowrap>");
		for($k=0;$k<count($sceneorgrecommends);$k++) {
			if ($sceneorgrecommends[$k]["prodid"]==$prods[$j]["id"]) print("<img src=\"gfx/sceneorg/".$sceneorgrecommends[$k]["type"].".gif\" width=\"16\" height=\"16\" border=\"0\" title=\"".$sceneorgrecommends[$k]["category"]."\" alt=\"".$sceneorgrecommends[$k]["category"]."\">");
		}
		print("<br /></td>");
		endif;

		if($prods[$j]["cdc"]):
		 print("<td nowrap>");
		 //for($ii=0;$ii<$prods[$j]["cdc"];$ii++): print("<img src=\"gfx/titles/coupdecoeur.gif\" width=\"16\" height=\"16\" border=\"0\" title=\"cdc\" alt=\"cdc\">");
		 //endfor;
		 cdcstack($prods[$j]["cdc"]);
		 print("<br /></td>");
		endif;
		
		print("<td width=\"100%\">&nbsp;</td>");
       	
       		$platforms = explode(",", $prods[$j]["platform"]);
       		for($kkk=0;$kkk<count($platforms);$kkk++) {
       		?><td align="right"><a href="prodlist.php?platform[]=<? print($platforms[$kkk]); ?>"><img src="gfx/os/<? print($os[$platforms[$kkk]]); ?>" width="16" height="16" border="0" title="<? print($platforms[$kkk]); ?>"></a><br /></td><?
       		}
       		
       		print("</tr></table></td>\n");
       		
	if(($prods[$j]["partyname"])&&!($prods[$j]["party"]==1024))
	{
		$placeadj="";
		if ($prods[$j]["partycompo"]=="") $compophrase="";
		 else $compophrase=" ".$prods[$j]["partycompo"];
		if (($prods[$j]["partycompo"]=="none")||($prods[$j]["partycompo"]=="invit")) $prods[$j]["party_place"]=98;
		
		if($prods[$j]["party_place"])
		{
           		switch($prods[$j]["party_place"]) {
             		case 1:
             		case 21:
             		case 31:
             		case 41:
             		case 51:
             		case 61:
             		case 71:
             		case 81:
             		case 91:  $placeadj="st"; 
             			print("<td>".$prods[$j]["party_place"].$placeadj." at <a href=\"party.php?which=".$prods[$j]["party"]."&when=".sprintf("%02d",$prods[$j]["party_year"])."\">".$prods[$j]["partyname"]." ".sprintf("%02d",$prods[$j]["party_year"])."</a>".$compophrase."<br /></td>\n");
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
             			print("<td>".$prods[$j]["party_place"].$placeadj." at <a href=\"party.php?which=".$prods[$j]["party"]."&when=".sprintf("%02d",$prods[$j]["party_year"])."\">".$prods[$j]["partyname"]." ".sprintf("%02d",$prods[$j]["party_year"])."</a>".$compophrase."<br /></td>\n");
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
             			print("<td>".$prods[$j]["party_place"].$placeadj." at <a href=\"party.php?which=".$prods[$j]["party"]."&when=".sprintf("%02d",$prods[$j]["party_year"])."\">".$prods[$j]["partyname"]." ".sprintf("%02d",$prods[$j]["party_year"])."</a>".$compophrase."<br /></td>\n");
             			break;
                	case 97: print("<td>disqualified at <a href=\"party.php?which=".$prods[$j]["party"]."&when=".sprintf("%02d",$prods[$j]["party_year"])."\">".$prods[$j]["partyname"]." ".sprintf("%02d",$prods[$j]["party_year"])."</a>".$compophrase."<br /></td>\n");
				break;
             		case 98: print("<td>for <a href=\"party.php?which=".$prods[$j]["party"]."&when=".sprintf("%02d",$prods[$j]["party_year"])."\">".$prods[$j]["partyname"]." ".sprintf("%02d",$prods[$j]["party_year"])."</a><br /></td>\n");
             			break;
             		case 99: print("<td>not shown at <a href=\"party.php?which=".$prods[$j]["party"]."&when=".sprintf("%02d",$prods[$j]["party_year"])."\">".$prods[$j]["partyname"]." ".sprintf("%02d",$prods[$j]["party_year"])."</a>".$compophrase."<br /></td>\n");
             			break;
             		default: $placeadj="th";
             			print("<td>".$prods[$j]["party_place"].$placeadj." at <a href=\"party.php?which=".$prods[$j]["party"]."&when=".sprintf("%02d",$prods[$j]["party_year"])."\">".$prods[$j]["partyname"]." ".sprintf("%02d",$prods[$j]["party_year"])."</a>".$compophrase."<br /></td>\n");
             			break;
           		}
         	} else 
         	{
         		 $placeadj = "??"; 
         		 print("<td>".$prods[$j]["party_place"].$placeadj." at <a href=\"party.php?which=".$prods[$j]["party"]."&when=".sprintf("%02d",$prods[$j]["party_year"])."\">".$prods[$j]["partyname"]." ".sprintf("%02d",$prods[$j]["party_year"])."</a>".$compophrase."<br /></td>\n");
		}
        } else {
       	  if ($prods[$j]["party"]==1024) print("<td>no party<br /></td>\n"); 
       	   else print("<td>??<br /></td>\n");
        } 

	if(($prods[$j]["date"]!="0000-00-00")&&(strlen($prods[$j]["date"])>0))
       	{
          $rdate=explode("-",$prods[$j]["date"]);
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
	
	if ($prods[$j]["voteup"])
	{ print("<td>\n".$prods[$j]["voteup"]."</td>\n");
	}
	else
	{print("<td>\n0</td>\n");
	}
	if ($prods[$j]["votepig"])
	{ print("<td>\n".$prods[$j]["votepig"]."</td>\n");
	}
	else
	{print("<td>\n0</td>\n");
	}
	if ($prods[$j]["votedown"])
	{ print("<td>\n".$prods[$j]["votedown"]."</td>\n");
	}
	else
	{print("<td>\n0</td>\n");
	}
	
	if($prods[$j]["voteavg"]>0)
		$thumbgfx="gfx/rulez.gif";
	elseif($prods[$j]["voteavg"]==0)
		$thumbgfx="gfx/isok.gif";
	else
		$thumbgfx="gfx/sucks.gif";
	printf("<td>\n<table cellspacing=\"0\" cellpadding=\"0\"><tr><td>&nbsp;</td><td>%.2f</td><td>&nbsp;</td><td><img src=\"".$thumbgfx."\" width=\"16\" height=\"16\" border=\"0\" alt=\"average rating\" align=\"left\"></td></tr></table></td>\n",$prods[$j]["voteavg"]);
	
	//popularity bar
	print("<td>\n");
	$pourcent = floor($prods[$j]["views"]*100/$max_views);
	DoBar($pourcent);
	print("</td>\n");
       	
       	
	if ($prods[$j]["lcom_quand"]){ ?>
       <td>
       <table cellspacing="0" cellpadding="0">
        <tr>
         <td nowrap>
 	  <a href="user.php?who=<?=$prods[$j]["lcom_who"]?>"><img src="avatars/<?=$prods[$j]["lcom_avatar"]?>" width="16" height="16" border="0" title="<?=$prods[$j]["lcom_nick"]?>"></a><br />
         </td>
         <td>
          <img src="gfx/z.gif" width="3" height="1"><br />
         </td>
         <?
          $rdate=explode(" ",$prods[$j]["lcom_quand"]);
          $rdate2=explode("-",$rdate[0]);
          switch($rdate2[1]) {
            case "01": $rmonth="Jan"; break;
            case "02": $rmonth="Feb"; break;
            case "03": $rmonth="Mar"; break;
            case "04": $rmonth="Apr"; break;
            case "05": $rmonth="May"; break;
            case "06": $rmonth="Jun"; break;
            case "07": $rmonth="Jul"; break;
            case "08": $rmonth="Aug"; break;
            case "09": $rmonth="Sep"; break;
            case "10": $rmonth="Oct"; break;
            case "11": $rmonth="Nov"; break;
            case "12": $rmonth="Dec"; break;
           }
           print("<td nowrap>".$rdate2[2]." ".$rmonth." ".$rdate2[0]."<br /></td>\n");
	?>
	<td width=\"100%\">&nbsp;</td>
	<td align="right">
	<? switch($prods[$j]["lcom_rating"])
	{
		case 1:   echo "<img src=\"gfx/rulez.gif\" alt=\"rulez\" border=\"0\">";
		break;
		case 0:   echo "<img src=\"gfx/isok.gif\" alt=\"piggie\" border=\"0\">";
		break;
		case -1:   echo "<img src=\"gfx/sucks.gif\" alt=\"sucks\" border=\"0\">";
		break;
	}?><br /></td>
        </tr>
       </table>
      </td>
      
       	<?
       	} else {
		print("<td>&nbsp;<br /></td>\n");  
	} }}} ?>
   
    <tr bgcolor="#224488">
     <? if($which): ?>
      <? if(count($groups)!=0): ?>
       <td colspan="9" align="right">
        <table cellspacing="0" cellpadding="0">
         <tr>
          <td>added on the <? print(substr($groups[0]["quand"],0,10)); ?> by <a href="user.php?who=<? print($myuser["id"]); ?>"><? print($myuser["nickname"]); ?></a></td>
          <td>&nbsp;<br /></td>
          <td><a href="user.php?who=<? print($myuser["id"]); ?>"><img src="avatars/<? print($myuser["avatar"]); ?>" width="16" height="16" border="0"></a></td>
         </tr>
        </table>
       </td>
       
       
       
       <? if(count($bbsaffils)>0): //board affils list ?>
       
        </tr>
   </table>
  </td>
 </tr>
</table>
<br />
<center>
<table bgcolor="#000000" cellspacing="1" cellpadding="0" border="0">
 <tr>
  <td align="center">
   <table bgcolor="#000000" cellspacing="1" cellpadding="2" border="0">
    <tr bgcolor="#224488">
       <th width="100%" colspan="2"><center><b>BBS affiliations</b><br /></center>
        </th>
        </tr>
        <? for($j=0;$j<count($bbsaffils);$j++):
        if($j%2) {
       			print("<tr bgcolor=\"#446688\">\n");
     		} else {
       			print("<tr bgcolor=\"#557799\">\n");
     		}
         ?>
        <td nowrap><a href="bbses.php?which=<? print($bbsaffils[$j]["id"]); ?>"><? print($bbsaffils[$j]["name"]); ?></a><br /></td>
        <td align="right"><? print($bbsaffils[$j]["type"]); ?><br /></td>
        </tr>
        
        <? endfor; ?>
       
       <? endif; ?>
       
       <? if(count($groupies)>0): ?>
       
        </tr>
   </table>
  </td>
 </tr>
</table>
<br />
<center>
<table bgcolor="#000000" cellspacing="1" cellpadding="0" border="0">
 <tr>
  <td align="center">
   <table bgcolor="#000000" cellspacing="1" cellpadding="2" border="0">
    <tr bgcolor="#224488">
       <th width="100%" align="center" colspan="2">
	   <form name="groupiesort" action="groups.php">
	    <table>
	        <tr>
		 <td nowrap><b>top </b>
			<input type="hidden" name="which" value="<? print($which); ?>">
			<select name="groupiesort" onChange="document.groupiesort.submit();">
			<option value="-1" <?=($groupiesort==-1)?'selected':''?>>none</option>
			<option value="1" <?=($groupiesort==1)?'selected':''?>>groupies</option>
			<option value="2" <?=($groupiesort==2)?'selected':''?>>haters</option>
			<option value="3" <?=($groupiesort==3)?'selected':''?>>followers</option>
			</select><br /></td>
		</tr>
	     </table>
	    </form>
        </th>
        </tr>
        <? if($groupiesort!=-1):
           for($j=0;$j<count($groupies);$j++):
            if($j%2) {
       			print("<tr bgcolor=\"#446688\">\n");
     		} else {
       			print("<tr bgcolor=\"#557799\">\n");
     		}
         ?>
        <td>
        <table cellspacing="0" cellpadding="0">
         <tr>
          <td nowrap><a href="user.php?who=<? print($groupies[$j]["who"]); ?>"><img src="avatars/<? print($groupies[$j]["avatar"]); ?>" width="16" height="16" border="0"></a><br /></td>
          <td>&nbsp;<br /></td>
          <td nowrap><a href="user.php?who=<? print($groupies[$j]["who"]); ?>"><? print($groupies[$j]["nickname"]); ?></a><br /></td>
          <td>&nbsp;<br /></td>
         </tr>
        </table>
        </td>
        <td align="right"><? print($groupies[$j]["c"]); ?><br /></td>
        </tr>
        
        <? endfor; 
           endif;?>
        
        <? endif; ?>
       
       
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
