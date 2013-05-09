<?
require("include/top.php");
require("include/libbb.php");

function cmp_avg($a, $b)
{
     if ($a["avg_rating"] == $b["avg_rating"])
     {
         return 0;
     }
     return ($a["avg_rating"] > $b["avg_rating"]) ? -1 : 1;
}

function cmp_avg_rev($a, $b)
{
     if ($a["avg_rating"] == $b["avg_rating"])
     {
         return 0;
     }
     return ($a["avg_rating"] < $b["avg_rating"]) ? -1 : 1;
}

function cmp_tot($a, $b)
{
     if ($a["total"] == $b["total"])
     {
         return 0;
     }
     return ($a["total"] > $b["total"]) ? -1 : 1;
}

$posts_per_page=$user["searchprods"];

if(($type!="prod")&&($type!="group")&&($type!="party")&&($type!="board")&&($type!="user")&&($type!="bbs")) {
  $type="prod";
}

$what=trim($what);
?>
<br />
<form action="search.php">
<table bgcolor="#000000" cellspacing="1" cellpadding="0" border="0">
 <tr>
  <td>
   <table bgcolor="#000000" cellspacing="1" cellpadding="2" border="0" width="100%">
    <tr>
     <td bgcolor="#224488">
      <b>search on pouët.net</b><br />
     </td>
    </tr>
    <tr>
  	 <td bgcolor="#446688">
      <table cellspacing="0" cellpadding="0">
       <tr>
        <td nowrap>&nbsp;I'm looking for&nbsp;</td>
        <td><input type="text" name="what" size="20" value="<? print($what); ?>"></td>
        <td nowrap>&nbsp;and this is a&nbsp;[</td>
        <?
        ($type=="prod") ? $checked1=" checked" : $checked1="";
        ($type=="group")? $checked2=" checked" : $checked2="";
        ($type=="party")? $checked5=" checked" : $checked5="";
        ($type=="board")? $checked6=" checked" : $checked6="";
        ($type=="user") ? $checked3=" checked" : $checked3="";
        ($type=="bbs") ? $checked4=" checked" : $checked4="";
        ?>
        <td><input type="radio" name="type" value="prod"<? print($checked1); ?>></td>
        <td nowrap>prod&nbsp;|</td>
        <td><input type="radio" name="type" value="group"<? print($checked2); ?>></td>
        <td nowrap>group&nbsp;|</td>
        <td><input type="radio" name="type" value="party"<? print($checked5); ?>></td>
        <td nowrap>party&nbsp;|</td>
        <td><input type="radio" name="type" value="board"<? print($checked6); ?>></td>
        <td nowrap>board&nbsp;|</td>
        <td><input type="radio" name="type" value="user"<? print($checked3); ?>></td>
        <td nowrap>user&nbsp;|</td>
        <td><input type="radio" name="type" value="bbs"<? print($checked4); ?>></td>
        <td nowrap>bbs&nbsp;]&nbsp;</td>
       </tr>
      </table>
     </td>
    </tr>
    <tr>
     <td bgcolor="#6688AA" align="right">
      <input type="image" src="gfx/submit.gif" border="0"><br />
     </td>
    </tr>
   </table>
  </td>
 </tr>
</table>
</form>
<br />
<? if($what): ?>

<?
$mywhat=strtr($what," ","%");
$subquery="%".$mywhat."%";
if($type=="prod") {
  $query="SELECT count(0) FROM prods WHERE prods.name LIKE '".$subquery."'";
  $result=mysql_query($query);
  $nb_posts=mysql_result($result,0);

	if(($page<=0)||(!$page)) {
   $page=1; //ceil($nb_posts/$posts_per_page);
	}
	$query="SELECT prods.id,prods.name,prods.views,prods.type,prods.date,prods.party,prods.party_year,prods.party_place,prods.partycompo,prods.group1,prods.group2,prods.group3,parties1.name as partyname FROM prods LEFT JOIN parties as parties1 ON parties1.id=prods.party WHERE prods.name LIKE '".$subquery."' ORDER BY name";
	$query.=" LIMIT ".(($page-1)*$posts_per_page).",".$posts_per_page;
} elseif($type=="group") {
  $query="SELECT count(0) FROM groups WHERE name LIKE '".$subquery."' OR acronym LIKE '".$subquery."'";
  $result=mysql_query($query);
  $nb_posts=mysql_result($result,0);
	if(($page<=0)||(!$page)) {
   $page=1; //ceil($nb_posts/$posts_per_page);
	}
  $query="SELECT id,name,acronym,web FROM groups WHERE name LIKE '".$subquery."' OR acronym LIKE '".$subquery."' ORDER BY name";
  $query.=" LIMIT ".(($page-1)*$posts_per_page).",".$posts_per_page;
} elseif($type=='party') {
  $query="SELECT count(0) FROM parties WHERE name LIKE '".$subquery."'";
  $result=mysql_query($query);
  $nb_posts=mysql_result($result,0);
	if(($page<=0)||(!$page)) {
   $page=1; //ceil($nb_posts/$posts_per_page);
	}
  $query="SELECT id,name,web FROM parties WHERE name LIKE '".$subquery."' ORDER BY name";
  $query.=" LIMIT ".(($page-1)*$posts_per_page).",".$posts_per_page;
} elseif($type=='board') {
  $query="SELECT count(0) FROM bbses WHERE name LIKE '".$subquery."' OR sysop LIKE '".$subquery."' OR phonenumber LIKE '".$subquery."'";
  $result=mysql_query($query);
  $nb_posts=mysql_result($result,0);
	if(($page<=0)||(!$page)) {
   $page=1; //ceil($nb_posts/$posts_per_page);
	}
  $query="SELECT id,name,sysop,phonenumber FROM bbses WHERE name LIKE '".$subquery."' OR sysop LIKE '".$subquery."' OR phonenumber LIKE '".$subquery."' ORDER BY name";
  $query.=" LIMIT ".(($page-1)*$posts_per_page).",".$posts_per_page;
} elseif($type=="user") {
  $query="SELECT count(0) FROM users WHERE nickname LIKE '".$subquery."'";
  $result=mysql_query($query);
  $nb_posts=mysql_result($result,0);
	if(($page<=0)||(!$page)) {
   $page=1; //ceil($nb_posts/$posts_per_page);
	}
  $query="SELECT id,nickname,avatar,glops,quand FROM users WHERE nickname LIKE '".$subquery."' ORDER BY nickname";
  $query.=" LIMIT ".(($page-1)*$posts_per_page).",".$posts_per_page;
} elseif($type=='bbs') {
  $query="SELECT COUNT(distinct bbs_topics.id) FROM bbs_topics LEFT JOIN bbs_posts ON bbs_topics.id=bbs_posts.topic WHERE bbs_topics.topic LIKE '".$subquery."' OR bbs_posts.post LIKE '".$subquery."'";
  $result=mysql_query($query);
  $nb_posts=mysql_result($result,0);
	if(($page<=0)||(!$page)) {
   $page=1; //ceil($nb_posts/$posts_per_page);
	}
  $query="SELECT bbs_topics.id,bbs_topics.topic,bbs_topics.lastpost FROM bbs_topics LEFT JOIN bbs_posts ON bbs_topics.id=bbs_posts.topic WHERE bbs_topics.topic LIKE '".$subquery."' OR bbs_posts.post LIKE '".$subquery."' GROUP BY bbs_topics.id ORDER BY bbs_topics.lastpost DESC";
  $query.=" LIMIT ".(($page-1)*$posts_per_page).",".$posts_per_page;
}

$result=mysql_query($query);
$nbresults=mysql_num_rows($result);
while($tmp=mysql_fetch_array($result)) {
  if($type=="user") {
    if(!$tmp["nickname"]) {
      $tmp["nickname"]=$tmp["login"];
    }
  }
  $results[]=$tmp;
}
if($nbresults>0):
if($type=="prod") {

	for($i=0;$i<$nbresults;$i++) {

		unset($commentss);
		unset($checktable);
		unset($rulez);
		unset($sucks);
		unset($total);

			//thumbs and avg math
			$query  = "SELECT comments.rating,comments.who FROM comments WHERE comments.which='".$results[$i]["id"]."'";
			$result=mysql_query($query);
			while($tmp=mysql_fetch_array($result)) {
			  $commentss[]=$tmp;
			}
			for($ii=0;$ii<count($commentss);$ii++)
			{
				if(!array_key_exists($commentss[$ii]["who"], $checktable)||$commentss[$ii]["rating"]!=0)
					$checktable[$commentss[$ii]["who"]] = $commentss[$ii]["rating"];
			}
			while(list($k,$v)=each($checktable))
			{
				if($v==1) $rulez++;
				else if($v==-1) $sucks++;
				$total++;
			}

			$results[$i]["avg_rating"] = (float)(($rulez*1+$sucks*-1)/$total);
			$results[$i]["total"] = $total;
			
		//cdc count
		$result=mysql_query("SELECT count(0) from users_cdcs where cdc=".$results[$i]["id"]);
		$results[$i]["cdc"]=mysql_result($result,0);
		
		$result=mysql_query("SELECT count(0) from cdc where which=".$results[$i]["id"]);
		$results[$i]["cdc"]=$results[$i]["cdc"]+mysql_result($result,0);


		if ($results[$i]["group1"]):
			$query="select name,acronym from groups where id='".$results[$i]["group1"]."'";
			$result=mysql_query($query);
			while($tmp = mysql_fetch_array($result)) {
			  $results[$i]["groupn1"]=$tmp["name"];
			  $results[$i]["groupacron1"]=$tmp["acronym"];
			 }
		endif;
		if ($results[$i]["group2"]):
			$query="select name,acronym from groups where id='".$results[$i]["group2"]."'";
			$result=mysql_query($query);
			while($tmp = mysql_fetch_array($result)) {
			  $results[$i]["groupn2"]=$tmp["name"];
			  $results[$i]["groupacron2"]=$tmp["acronym"];
			 }
		endif;
		if ($results[$i]["group3"]):
			$query="select name,acronym from groups where id='".$results[$i]["group3"]."'";
			$result=mysql_query($query);
			while($tmp = mysql_fetch_array($result)) {
			  $results[$i]["groupn3"]=$tmp["name"];
			  $results[$i]["groupacron3"]=$tmp["acronym"];
			 }
		endif;
		
		if (strlen($results[$i]["groupn1"].$results[$i]["groupn2"].$results[$i]["groupn3"])>27):
		if (strlen($results[$i]["groupn1"])>10 && $results[$i]["groupacron1"]) $results[$i]["groupn1"]=$results[$i]["groupacron1"];
		if (strlen($results[$i]["groupn2"])>10 && $results[$i]["groupacron2"]) $results[$i]["groupn2"]=$results[$i]["groupacron2"];
		if (strlen($results[$i]["groupn3"])>10 && $results[$i]["groupacron3"]) $results[$i]["groupn3"]=$results[$i]["groupacron3"];
	endif;

		//get platforms
		$query="select platforms.name from prods_platforms, platforms where prods_platforms.prod='".$results[$i]["id"]."' and platforms.id=prods_platforms.platform";
		$result=mysql_query($query);
		$check=0;
		$results[$i]["platform"]="";
		while($tmp = mysql_fetch_array($result)) {
		  if ($check>0) $results[$i]["platform"].=",";
		  $check++;
		  $results[$i]["platform"].=$tmp["name"];
		 }
			
		//get array of sceneorgrecommendations for these results
		$result=mysql_query("SELECT * from sceneorgrecommended where prodid=".$results[$i]["id"]." ORDER BY type");
		while($tmp=mysql_fetch_array($result)) {
  		$sceneorgrecommends[]=$tmp;
		}
	}

	//get max_views for popularity
	$result=mysql_query("SELECT MAX(views) FROM prods");
	$max_views=mysql_result($result,0);

}
?>
<? if($type=="prod"): ?>
<table bgcolor="#000000" cellspacing="1" cellpadding="0" border="0">
 <tr>
  <td>
   <table bgcolor="#000000" cellspacing="1" cellpadding="2" border="0">
    <? $sortlink="prodlist.php?page=".$page."&order="; ?>
    <tr bgcolor="#224488">
     <th>
      <table><tr>
       <td>
        <img src="gfx/fleche1a.gif" width="13" height="12" border="0"><br />
       </td>
       <td>
        <b>name</b><br />
       </td>
      </tr></table>
     </th>
     <th>
      <b>group</b><br />
     </th>
     <th>
      <b>release party</b><br />
     </th>
     <th>
      <b>release date</b><br />
     </th>
     <th>
        <img src="gfx/sucks.gif" alt="rating">
        <img src="gfx/isok.gif" alt="rating">
        <img src="gfx/rulez.gif" alt="rating">
     </th>
     <th>
      <b>#</b>
     </th>
     <th>
        <b>popularity</b>
     </th>
    </tr>

   <?
   for($i=0;$i<$nbresults;$i++):
     if($i%2) {
       print("<tr bgcolor=\"#446688\">");
     } else {
       print("<tr bgcolor=\"#557799\">");
     }

     		$typess = explode(",", $results[$i]["type"]);
		print("<td nowrap><table cellspacing=\"0\" cellpadding=\"0\"><tr><td nowrap><a href=\"prod.php?which=".$results[$i]["id"]."\">");
		for($k=0;$k<count($typess);$k++) {
		print("<img src=\"gfx/types/".$types[$typess[$k]]."\" width=\"16\" height=\"16\" border=\"0\" title=\"".$typess[$k]."\">");
		}
		print("<br /></a></td><td><img src=\"gfx/z.gif\" width=\"2\" height=\"1\" border=\"0\"><br /></td><td nowrap><a href=\"prod.php?which=".$results[$i]["id"]."\">".strtolower(stripslashes($results[$i]["name"]))."</a><br /></td><td>&nbsp;</td>");
		
		if(count($sceneorgrecommends)):
        	print("<td nowrap>");
		for($k=0;$k<count($sceneorgrecommends);$k++) {
			if ($sceneorgrecommends[$k]["prodid"]==$results[$i]["id"]) print("<img src=\"gfx/sceneorg/".$sceneorgrecommends[$k]["type"].".gif\" width=\"16\" height=\"16\" border=\"0\" title=\"".$sceneorgrecommends[$k]["category"]."\" alt=\"".$sceneorgrecommends[$k]["category"]."\">");
		}
		print("<br /></td>");
		endif;

		if($results[$i]["cdc"]):
		 print("<td nowrap>");
		 for($ii=0;$ii<$results[$i]["cdc"];$ii++): print("<img src=\"gfx/titles/coupdecoeur.gif\" width=\"16\" height=\"16\" border=\"0\" title=\"cdc\" alt=\"cdc\">");
		 endfor;
		 print("<br /></td>");
		endif;
		
		print("<td width=\"100%\">&nbsp;</td>");
       	
       		$platforms = explode(",", $results[$i]["platform"]);
       		for($kkk=0;$kkk<count($platforms);$kkk++) {
       		?><td align="right"><a href="prodlist.php?platform=<? print($platforms[$kkk]); ?>"><img src="gfx/os/<? print($os[$platforms[$kkk]]); ?>" width="16" height="16" border="0" title="<? print($platforms[$kkk]); ?>"></a><br /></td><?
       		}
       		
       		print("</tr></table></td>\n");


     ?>

      <td><a href="groups.php?which=<? print($results[$i]["group1"]); ?>"><? print(strtolower($results[$i]["groupn1"])); ?></a>
      <? if ($results[$i]["groupn2"]) {print(" :: ");} ?><a href="groups.php?which=<? print($results[$i]["group2"]); ?>"><? print(strtolower($results[$i]["groupn2"])); ?></a>
      <? if ($results[$i]["groupn3"]) {print(" :: ");} ?><a href="groups.php?which=<? print($results[$i]["group3"]); ?>"><? print(strtolower($results[$i]["groupn3"])); ?></a>
      </td>


<?
      if(($results[$i]["partyname"])&&!($results[$i]["party"]==1024))
	{
		if($results[$i]["party_place"])
		{
			$placeadj="";
			if ($results[$i]["partycompo"]=="") $compophrase="";
		 	 else $compophrase=" ".$results[$i]["partycompo"];
			if (($results[$i]["partycompo"]=="none")||($results[$i]["partycompo"]=="invit")) $results[$i]["party_place"]=98;
			
           		switch($results[$i]["party_place"]) {
           		case 1:
             		case 21:
             		case 31:
             		case 41:
             		case 51:
             		case 61:
             		case 71:
             		case 81:
             		case 91:  $placeadj="st";
             			print("<td>".$results[$i]["party_place"].$placeadj." at <a href=\"party.php?which=".$results[$i]["party"]."&when=".sprintf("%02d",$results[$i]["party_year"])."\">".$results[$i]["partyname"]." ".sprintf("%02d",$results[$i]["party_year"])."</a>".$compophrase."<br /></td>\n");
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
             			print("<td>".$results[$i]["party_place"].$placeadj." at <a href=\"party.php?which=".$results[$i]["party"]."&when=".sprintf("%02d",$results[$i]["party_year"])."\">".$results[$i]["partyname"]." ".sprintf("%02d",$results[$i]["party_year"])."</a>".$compophrase."<br /></td>\n");
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
             			print("<td>".$results[$i]["party_place"].$placeadj." at <a href=\"party.php?which=".$results[$i]["party"]."&when=".sprintf("%02d",$results[$i]["party_year"])."\">".$results[$i]["partyname"]." ".sprintf("%02d",$results[$i]["party_year"])."</a>".$compophrase."<br /></td>\n");
             			break;
                	case 97: print("<td>disqualified at <a href=\"party.php?which=".$results[$i]["party"]."&when=".sprintf("%02d",$results[$i]["party_year"])."\">".$results[$i]["partyname"]." ".sprintf("%02d",$results[$i]["party_year"])."</a>".$compophrase."<br /></td>\n");
				break;
             		case 98: print("<td>for <a href=\"party.php?which=".$results[$i]["party"]."&when=".sprintf("%02d",$results[$i]["party_year"])."\">".$results[$i]["partyname"]." ".sprintf("%02d",$results[$i]["party_year"])."</a><br /></td>\n");
             			break;
             		case 99: print("<td>not shown at <a href=\"party.php?which=".$results[$i]["party"]."&when=".sprintf("%02d",$results[$i]["party_year"])."\">".$results[$i]["partyname"]." ".sprintf("%02d",$results[$i]["party_year"])."</a>".$compophrase."<br /></td>\n");
             			break;
             		default: $placeadj="th";
             			print("<td>".$results[$i]["party_place"].$placeadj." at <a href=\"party.php?which=".$results[$i]["party"]."&when=".sprintf("%02d",$results[$i]["party_year"])."\">".$results[$i]["partyname"]." ".sprintf("%02d",$results[$i]["party_year"])."</a>".$compophrase."<br /></td>\n");
             			break;
           		}
         	} else
         	{
         		 $placeadj = "??";
         		 print("<td>".$results[$i]["party_place"].$placeadj." at <a href=\"party.php?which=".$results[$i]["party"]."&when=".sprintf("%02d",$results[$i]["party_year"])."\">".$results[$i]["partyname"]."</a><br /></td>\n");
			}
        } else {
          print("<td>&nbsp<br /></td>\n");
        }

	if( ($results[$i]["date"]!="0000-00-00") && (strlen($results[$i]["date"])>0) )
       	{
          $rdate=explode("-",$results[$i]["date"]);
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
           }
           print("<td>".$rmonth." ".$rdate[0]."<br /></td>\n");
	} else {
		print("<td>&nbsp<br /></td>\n");
	}

	if($results[$i]["avg_rating"]>0)
		$thumbgfx="gfx/rulez.gif";
	elseif($results[$i]["avg_rating"]==0)
		$thumbgfx="gfx/isok.gif";
	else
		$thumbgfx="gfx/sucks.gif";
	printf("<td>\n<table cellspacing=\"0\" cellpadding=\"0\"><tr><td>&nbsp;</td><td>%.2f</td><td>&nbsp;</td><td><img src=\"".$thumbgfx."\" width=\"16\" height=\"16\" border=\"0\" alt=\"average rating\" align=\"left\"></td></tr></table></td>\n",$results[$i]["avg_rating"]);

	if ($results[$i]["total"])
	{ print("<td>\n".$results[$i]["total"]."</td>\n");
	}
	else
	{print("<td>\n0</td>\n");
	}
	
	//popularity bar
	print("<td>\n");
	$pourcent = floor($results[$i]["views"]*100/$max_views);
	//print($pourcent."%<\td>\n");
	DoBar($pourcent);
	print("</td>\n");

?>


     </tr>
    <? endfor; ?>

       <? if((($nb_posts-1)/$posts_per_page)>0): ?>
		<tr bgcolor="#224488">
         <td colspan="9">
      <table width="100%" cellspacing="0" cellpadding="0" border="0">
       <tr>
       <? if($page>=2): ?>
        <td>
         <a href="search.php?what=<?=$what?>&type=<?=$type?>&page=<?=($page-1)?>">
          <img src="gfx/flecheg.gif" border="0"><br />
         </a>
        </td>
        <td>&nbsp;</td>
        <td nowrap>
         <a href="search.php?what=<?=$what?>&type=<?=$type?>&page=<?=($page-1)?>">
          <b>previous page</b><br />
         </a>
        </td>
       <? endif; ?>
        <form action="search.php">
                <input type="hidden" name="what" value="<?=$what?>">
                <input type="hidden" name="type" value="<?=$type?>">
        <td width="50%" align="right">
	<select name="page">
        <? for($i=1;($i-1)<=(($nb_posts-1)/$posts_per_page);$i++): ?>
        <? if($i==$page): ?>
				<option value="<?=$i."\" selected>".$i?></option>
        <? else: ?>
        <option value="<?=$i."\">".$i?></option>
        <? endif; ?>
        <? endfor; ?>
        </select>
       <font color="#9FCFFF"><b><? printf("of ".($i-1)); ?></b></font>
        &nbsp</td>
	<td width="50%">
	<input type="image" src="gfx/submit.gif" border="0"><br />
        </td>
        </form>
       <? if(($page*$posts_per_page)<=($nb_posts-1)): ?>
        <td nowrap>
         <a href="search.php?what=<?=$what?>&type=<?=$type?>&page=<?=($page+1)?>">
          <b>next page</b><br />
         </a>
        </td>
        <td>&nbsp;</td>
        <td>
         <a href="search.php?what=<?=$what?>&type=<?=$type?>&page=<?=($page+1)?>">
          <img src="gfx/fleched.gif" border="0"><br />
         </a>
        </td>
       <? endif; ?>
       </tr>
      </table>
         </td>
        </tr>
      <? endif; ?>
   </table>
  </td>
 </tr>
</table>
<? elseif($type=="group"): ?>
<table bgcolor="#000000" cellspacing="1" cellpadding="0" border="0">
 <tr>
  <td>
   <table bgcolor="#000000" cellspacing="1" cellpadding="2" border="0">
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
     <th><b>websites</b></th>
     <th><b>prods</b></th>
    </tr>
   <?
   for($i=0;$i<count($results);$i++) {
     $query="SELECT count(0) FROM prods WHERE group1=".$results[$i]["id"]." OR group2=".$results[$i]["id"]." OR group3=".$results[$i]["id"]." LIMIT 1";
	 $result=mysql_query($query);
	 $nb_prods=mysql_result($result,0);

     if($i%2) {
       print("<tr bgcolor=\"#446688\">\n");
     } else {
       print("<tr bgcolor=\"#557799\">\n");
     }
     print("<td valign=\"top\"><b><a href=\"groups.php?which=".$results[$i]["id"]."\">".$results[$i]["name"]);
     if($results[$i]["acronym"]) print(" [".$results[$i]["acronym"]."]");
     print("</a></b><br /></td>\n");
     if($results[$i]["web"]) {
       print("<td valign=\"top\"><a href=\"".$results[$i]["web"]."\">".strtolower($results[$i]["web"])."</a></td>\n");
     } else {
       print("<td><br /></td>\n");
     }

     if($nb_prods) {
       print("<td valign=\"top\">".$nb_prods."</td>\n");
     } else {
       print("<td><br /></td>\n");
     }

     print("</tr>\n");
   }
   ?>
       <? if((($nb_posts-1)/$posts_per_page)>1): ?>
    <tr bgcolor="#224488">
     <td colspan="3">
      <table width="100%" cellspacing="0" cellpadding="0" border="0">
       <tr>
       <? if($page>=2): ?>
        <td>
         <a href="search.php?what=<?=$what?>&type=<?=$type?>&page=<?=($page-1)?>">
          <img src="gfx/flecheg.gif" border="0"><br />
         </a>
        </td>
        <td>&nbsp;</td>
        <td nowrap>
         <a href="search.php?what=<?=$what?>&type=<?=$type?>&page=<?=($page-1)?>">
          <b>previous page</b><br />
         </a>
        </td>
       <? endif; ?>
        <form action="search.php">
                <input type="hidden" name="what" value="<?=$what?>">
                <input type="hidden" name="type" value="<?=$type?>">
        <td width="50%" align="right">
	<select name="page">
        <? for($i=1;($i-1)<=(($nb_posts-1)/$posts_per_page);$i++): ?>
        <? if($i==$page): ?>
				<option value="<?=$i."\" selected>".$i?></option>
        <? else: ?>
        <option value="<?=$i."\">".$i?></option>
        <? endif; ?>
        <? endfor; ?>
        </select>
       <font color="#9FCFFF"><b><? printf("of ".($i-1)); ?></b></font>
        &nbsp</td>
	<td width="50%">
	<input type="image" src="gfx/submit.gif" border="0"><br />
        </td>
        </form>
       <? if(($page*$posts_per_page)<=($nb_posts-1)): ?>
        <td nowrap>
         <a href="search.php?what=<?=$what?>&type=<?=$type?>&page=<?=($page+1)?>">
          <b>next page</b><br />
         </a>
        </td>
        <td>&nbsp;</td>
        <td>
         <a href="search.php?what=<?=$what?>&type=<?=$type?>&page=<?=($page+1)?>">
          <img src="gfx/fleched.gif" border="0"><br />
         </a>
        </td>
       <? endif; ?>
       </tr>
      </table>
         </td>
        </tr>
       <? endif; ?>
   </table>
  </td>
 </tr>
</table>
<? elseif($type=="party"): ?>
<table bgcolor="#000000" cellspacing="1" cellpadding="0" border="0">
 <tr>
  <td>
   <table bgcolor="#000000" cellspacing="1" cellpadding="2" border="0">
    <tr bgcolor="#224488">
     <th>
      <table>
       <tr>
        <td>
         <img src="gfx/fleche1a.gif" width="13" height="12" border="0"><br />
        </td>
        <td>
         <b>party name</b>
        </td>
       </tr>
      </table>
     </th>
     <th><b>websites</b></th>
     <th><b>prods</b></th>
    </tr>
   <?
   for($i=0;$i<count($results);$i++) {
     $query="SELECT count(0) FROM prods WHERE party=".$results[$i]["id"]." LIMIT 1";
	 $result=mysql_query($query);
	 $nb_prods=mysql_result($result,0);

     if($i%2) {
       print("<tr bgcolor=\"#446688\">\n");
     } else {
       print("<tr bgcolor=\"#557799\">\n");
     }
     print("<td valign=\"top\"><a href=\"party.php?which=".$results[$i]["id"]."\"><b>".$results[$i]["name"]."</b></a><br /></td>\n");
     if($results[$i]["web"]) {
       print("<td valign=\"top\"><a href=\"".$results[$i]["web"]."\">".strtolower($results[$i]["web"])."</a></td>\n");
     } else {
       print("<td><br /></td>\n");
     }

     if($nb_prods) {
       print("<td valign=\"top\">".$nb_prods."</td>\n");
     } else {
       print("<td><br /></td>\n");
     }

     print("</tr>\n");
   }
   ?>
       <? if((($nb_posts-1)/$posts_per_page)>1): ?>
    <tr bgcolor="#224488">
     <td colspan="3">
      <table width="100%" cellspacing="0" cellpadding="0" border="0">
       <tr>
       <? if($page>=2): ?>
        <td>
         <a href="search.php?what=<?=$what?>&type=<?=$type?>&page=<?=($page-1)?>">
          <img src="gfx/flecheg.gif" border="0"><br />
         </a>
        </td>
        <td>&nbsp;</td>
        <td nowrap>
         <a href="search.php?what=<?=$what?>&type=<?=$type?>&page=<?=($page-1)?>">
          <b>previous page</b><br />
         </a>
        </td>
       <? endif; ?>
        <form action="search.php">
                <input type="hidden" name="what" value="<?=$what?>">
                <input type="hidden" name="type" value="<?=$type?>">
        <td width="50%" align="right">
	<select name="page">
        <? for($i=1;($i-1)<=(($nb_posts-1)/$posts_per_page);$i++): ?>
        <? if($i==$page): ?>
				<option value="<?=$i."\" selected>".$i?></option>
        <? else: ?>
        <option value="<?=$i."\">".$i?></option>
        <? endif; ?>
        <? endfor; ?>
        </select>
       <font color="#9FCFFF"><b><? printf("of ".($i-1)); ?></b></font>
        &nbsp</td>
	<td width="50%">
	<input type="image" src="gfx/submit.gif" border="0"><br />
        </td>
        </form>
       <? if(($page*$posts_per_page)<=($nb_posts-1)): ?>
        <td nowrap>
         <a href="search.php?what=<?=$what?>&type=<?=$type?>&page=<?=($page+1)?>">
          <b>next page</b><br />
         </a>
        </td>
        <td>&nbsp;</td>
        <td>
         <a href="search.php?what=<?=$what?>&type=<?=$type?>&page=<?=($page+1)?>">
          <img src="gfx/fleched.gif" border="0"><br />
         </a>
        </td>
       <? endif; ?>
       </tr>
      </table>
         </td>
        </tr>
       <? endif; ?>
   </table>
  </td>
 </tr>
</table>
<? elseif($type=="party"): ?>
<table bgcolor="#000000" cellspacing="1" cellpadding="0" border="0">
 <tr>
  <td>
   <table bgcolor="#000000" cellspacing="1" cellpadding="2" border="0">
    <tr bgcolor="#224488">
     <th>
      <table>
       <tr>
        <td>
         <img src="gfx/fleche1a.gif" width="13" height="12" border="0"><br />
        </td>
        <td>
         <b>party name</b>
        </td>
       </tr>
      </table>
     </th>
     <th><b>websites</b></th>
     <th><b>prods</b></th>
    </tr>
   <?
   for($i=0;$i<count($results);$i++) {
     $query="SELECT count(0) FROM prods WHERE party=".$results[$i]["id"]." LIMIT 1";
	 $result=mysql_query($query);
	 $nb_prods=mysql_result($result,0);

     if($i%2) {
       print("<tr bgcolor=\"#446688\">\n");
     } else {
       print("<tr bgcolor=\"#557799\">\n");
     }
     print("<td valign=\"top\"><a href=\"party.php?which=".$results[$i]["id"]."\"><b>".$results[$i]["name"]."</b></a><br /></td>\n");
     if($results[$i]["web"]) {
       print("<td valign=\"top\"><a href=\"".$results[$i]["web"]."\">".strtolower($results[$i]["web"])."</a></td>\n");
     } else {
       print("<td><br /></td>\n");
     }

     if($nb_prods) {
       print("<td valign=\"top\">".$nb_prods."</td>\n");
     } else {
       print("<td><br /></td>\n");
     }

     print("</tr>\n");
   }
   ?>
       <? if((($nb_posts-1)/$posts_per_page)>1): ?>
    <tr bgcolor="#224488">
     <td colspan="3">
      <table width="100%" cellspacing="0" cellpadding="0" border="0">
       <tr>
       <? if($page>=2): ?>
        <td>
         <a href="search.php?what=<?=$what?>&type=<?=$type?>&page=<?=($page-1)?>">
          <img src="gfx/flecheg.gif" border="0"><br />
         </a>
        </td>
        <td>&nbsp;</td>
        <td nowrap>
         <a href="search.php?what=<?=$what?>&type=<?=$type?>&page=<?=($page-1)?>">
          <b>previous page</b><br />
         </a>
        </td>
       <? endif; ?>
        <form action="search.php">
                <input type="hidden" name="what" value="<?=$what?>">
                <input type="hidden" name="type" value="<?=$type?>">
        <td width="50%" align="right">
	<select name="page">
        <? for($i=1;($i-1)<=(($nb_posts-1)/$posts_per_page);$i++): ?>
        <? if($i==$page): ?>
				<option value="<?=$i."\" selected>".$i?></option>
        <? else: ?>
        <option value="<?=$i."\">".$i?></option>
        <? endif; ?>
        <? endfor; ?>
        </select>
       <font color="#9FCFFF"><b><? printf("of ".($i-1)); ?></b></font>
        &nbsp</td>
	<td width="50%">
	<input type="image" src="gfx/submit.gif" border="0"><br />
        </td>
        </form>
       <? if(($page*$posts_per_page)<=($nb_posts-1)): ?>
        <td nowrap>
         <a href="search.php?what=<?=$what?>&type=<?=$type?>&page=<?=($page+1)?>">
          <b>next page</b><br />
         </a>
        </td>
        <td>&nbsp;</td>
        <td>
         <a href="search.php?what=<?=$what?>&type=<?=$type?>&page=<?=($page+1)?>">
          <img src="gfx/fleched.gif" border="0"><br />
         </a>
        </td>
       <? endif; ?>
       </tr>
      </table>
         </td>
        </tr>
       <? endif; ?>
   </table>
  </td>
 </tr>
</table>
<? elseif($type=="board"): ?>
<table bgcolor="#000000" cellspacing="1" cellpadding="0" border="0">
 <tr>
  <td>
   <table bgcolor="#000000" cellspacing="1" cellpadding="2" border="0">
    <tr bgcolor="#224488">
     <th>
      <table>
       <tr>
        <td>
         <img src="gfx/fleche1a.gif" width="13" height="12" border="0"><br />
        </td>
        <td>
         <b>board name</b>
        </td>
       </tr>
      </table>
     </th>
     <th><b>sysop</b></th>
     <th><b>number</b></th>
    </tr>
   <?
   for($i=0;$i<count($results);$i++) {

     if($i%2) {
       print("<tr bgcolor=\"#446688\">\n");
     } else {
       print("<tr bgcolor=\"#557799\">\n");
     }
     print("<td valign=\"top\"><a href=\"bbses.php?which=".$results[$i]["id"]."\"><b>".$results[$i]["name"]."</b></a><br /></td>\n");
     if($results[$i]["sysop"]) {
       print("<td valign=\"top\"><a href=\"".$results[$i]["sysop"]."\">".strtolower($results[$i]["sysop"])."</a></td>\n");
     } else {
       print("<td><br /></td>\n");
     }

     if($results[$i]["phonenumber"]) {
       print("<td valign=\"top\"><a href=\"".$results[$i]["phonenumber"]."\">".strtolower($results[$i]["phonenumber"])."</a></td>\n");
     } else {
       print("<td><br /></td>\n");
     }

     print("</tr>\n");
   }
   ?>
       <? if((($nb_posts-1)/$posts_per_page)>1): ?>
    <tr bgcolor="#224488">
     <td colspan="3">
      <table width="100%" cellspacing="0" cellpadding="0" border="0">
       <tr>
       <? if($page>=2): ?>
        <td>
         <a href="search.php?what=<?=$what?>&type=<?=$type?>&page=<?=($page-1)?>">
          <img src="gfx/flecheg.gif" border="0"><br />
         </a>
        </td>
        <td>&nbsp;</td>
        <td nowrap>
         <a href="search.php?what=<?=$what?>&type=<?=$type?>&page=<?=($page-1)?>">
          <b>previous page</b><br />
         </a>
        </td>
       <? endif; ?>
        <form action="search.php">
                <input type="hidden" name="what" value="<?=$what?>">
                <input type="hidden" name="type" value="<?=$type?>">
        <td width="50%" align="right">
	<select name="page">
        <? for($i=1;($i-1)<=(($nb_posts-1)/$posts_per_page);$i++): ?>
        <? if($i==$page): ?>
				<option value="<?=$i."\" selected>".$i?></option>
        <? else: ?>
        <option value="<?=$i."\">".$i?></option>
        <? endif; ?>
        <? endfor; ?>
        </select>
       <font color="#9FCFFF"><b><? printf("of ".($i-1)); ?></b></font>
        &nbsp</td>
	<td width="50%">
	<input type="image" src="gfx/submit.gif" border="0"><br />
        </td>
        </form>
       <? if(($page*$posts_per_page)<=($nb_posts-1)): ?>
        <td nowrap>
         <a href="search.php?what=<?=$what?>&type=<?=$type?>&page=<?=($page+1)?>">
          <b>next page</b><br />
         </a>
        </td>
        <td>&nbsp;</td>
        <td>
         <a href="search.php?what=<?=$what?>&type=<?=$type?>&page=<?=($page+1)?>">
          <img src="gfx/fleched.gif" border="0"><br />
         </a>
        </td>
       <? endif; ?>
       </tr>
      </table>
         </td>
        </tr>
       <? endif; ?>
   </table>
  </td>
 </tr>
</table>
<? elseif($type=="user"): ?>
<table bgcolor="#000000" cellspacing="1" cellpadding="0" border="0">
 <tr>
  <td>
   <table bgcolor="#000000" cellspacing="1" cellpadding="2" border="0">
    <tr bgcolor="#224488">
     <th>
      <table>
       <tr>
        <td>
         <img src="gfx/fleche1a.gif" width="13" height="12" border="0"><br />
        </td>
        <td>
         <b>nickname</b>
        </td>
       </tr>
      </table>
     </th>
     <th>
         <b>glops</b>
     </th>
     <th>
         <b>registered</b>
     </th>
    </tr>
   <? for($i=0;$i<count($results);$i++): ?>
   <? if($i%2): ?>
   <tr bgcolor="#446688">
   <? else: ?>
   <tr bgcolor="#557799">
   <? endif; ?>
   <td valign="top">
    <table cellspacing="0" cellpadding="0" border="0"><tr>
    <td><a href="user.php?who=<? print($results[$i]["id"]); ?>"><img src="avatars/<? print($results[$i]["avatar"]); ?>" width="16" height="16" border="0" alt="<? print($results[$i]["nickname"]); ?>"></a><br /></td>
    <td>&nbsp;</td>
    <td><a href="user.php?who=<? print($results[$i]["id"]); ?>"><b><? print($results[$i]["nickname"]); ?></b></a><br /></td>
    </tr></table>
   </td>
       <td><? print($results[$i]["glops"]); ?><br /></td>
       <td><? print($results[$i]["quand"]); ?><br /></td>
   </tr>
   <? endfor; ?>
       <? if((($nb_posts-1)/$posts_per_page)>1): ?>
    <tr bgcolor="#224488">
     <td colspan="3">
      <table width="100%" cellspacing="0" cellpadding="0" border="0">
       <tr>
       <? if($page>=2): ?>
        <td>
         <a href="search.php?what=<?=$what?>&type=<?=$type?>&page=<?=($page-1)?>">
          <img src="gfx/flecheg.gif" border="0"><br />
         </a>
        </td>
        <td>&nbsp;</td>
        <td nowrap>
         <a href="search.php?what=<?=$what?>&type=<?=$type?>&page=<?=($page-1)?>">
          <b>previous page</b><br />
         </a>
        </td>
       <? endif; ?>
        <form action="search.php">
                <input type="hidden" name="what" value="<?=$what?>">
                <input type="hidden" name="type" value="<?=$type?>">
        <td width="50%" align="right">
	<select name="page">
        <? for($i=1;($i-1)<=(($nb_posts-1)/$posts_per_page);$i++): ?>
        <? if($i==$page): ?>
				<option value="<?=$i."\" selected>".$i?></option>
        <? else: ?>
        <option value="<?=$i."\">".$i?></option>        <? endif; ?>
        <? endfor; ?>
        </select>
       <font color="#9FCFFF"><b><? printf("of ".($i-1)); ?></b></font>
        &nbsp</td>
	<td width="50%">
	<input type="image" src="gfx/submit.gif" border="0"><br />
        </td>
        </form>
       <? if(($page*$posts_per_page)<=($nb_posts-1)): ?>
        <td nowrap>
         <a href="search.php?what=<?=$what?>&type=<?=$type?>&page=<?=($page+1)?>">
          <b>next page</b><br />
         </a>
        </td>
        <td>&nbsp;</td>
        <td>
         <a href="search.php?what=<?=$what?>&type=<?=$type?>&page=<?=($page+1)?>">
          <img src="gfx/fleched.gif" border="0"><br />
         </a>
        </td>
       <? endif; ?>
       </tr>
      </table>
         </td>
        </tr>
       <? endif; ?>

   </table>
  </td>
 </tr>
</table>
<? elseif($type=="bbs"): ?>
<table bgcolor="#000000" cellspacing="1" cellpadding="0" border="0">
 <tr>
  <td>
   <table bgcolor="#000000" cellspacing="1" cellpadding="2" border="0">
    <tr bgcolor="#224488">
     <th>
      <table>
       <tr>
        <td>
         <img src="gfx/fleche1a.gif" width="13" height="12" border="0"><br />
        </td>
        <td>
         <b>topic</b>
        </td>
       </tr>
      </table>
     </th>
     <th>last post</th>
    </tr>
   <? for($i=0;$i<count($results);$i++): ?>
   <? if($i%2): ?>
   <tr bgcolor="#446688">
   <? else: ?>
   <tr bgcolor="#557799">
   <? endif; ?>
   <td valign="top">
    <a href="topic.php?which=<?=$results[$i]["id"]?>"><?=htmlcleanonerow($results[$i]["topic"])?></a>
   </td>
   <td valign="top">
    <?=$results[$i]["lastpost"]?>
   </td>
   </tr>
   <? endfor; ?>
       <? if((($nb_posts-1)/$posts_per_page)>1): ?>
    <tr bgcolor="#224488">
     <td colspan="2">
      <table width="100%" cellspacing="0" cellpadding="0" border="0">
       <tr>
       <? if($page>=2): ?>
        <td>
         <a href="search.php?what=<?=$what?>&type=<?=$type?>&page=<?=($page-1)?>">
          <img src="gfx/flecheg.gif" border="0"><br />
         </a>
        </td>
        <td>&nbsp;</td>
        <td nowrap>
         <a href="search.php?what=<?=$what?>&type=<?=$type?>&page=<?=($page-1)?>">
          <b>previous page</b><br />
         </a>
        </td>
       <? endif; ?>
        <form action="search.php">
                <input type="hidden" name="what" value="<?=$what?>">
                <input type="hidden" name="type" value="<?=$type?>">
        <td width="50%" align="right">
	<select name="page">
        <? for($i=1;($i-1)<=(($nb_posts-1)/$posts_per_page);$i++): ?>
        <? if($i==$page): ?>
        <option value="<?=$i."\" selected>".$i?></option>
        <? else: ?>
        <option value="<?=$i."\">".$i?></option>
        <? endif; ?>
        <? endfor; ?>
        </select>
       <font color="#9FCFFF"><b><? printf("of ".($i-1)); ?></b></font>
        &nbsp</td>
	<td width="50%">
	<input type="image" src="gfx/submit.gif" border="0"><br />
        </td>
        </form>
       <? if(($page*$posts_per_page)<=($nb_posts-1)): ?>
        <td nowrap>
         <a href="search.php?what=<?=$what?>&type=<?=$type?>&page=<?=($page+1)?>">
          <b>next page</b><br />
         </a>
        </td>
        <td>&nbsp;</td>
        <td>
         <a href="search.php?what=<?=$what?>&type=<?=$type?>&page=<?=($page+1)?>">
          <img src="gfx/fleched.gif" border="0"><br />
         </a>
        </td>
       <? endif; ?>
       </tr>
      </table>
         </td>
        </tr>
       <? endif; ?>
   </table>
  </td>
 </tr>
</table>
<? endif; ?>
<br />
<? else: print("no results again! life sux! :¨(<br /><br />"); endif; ?>
<? endif; ?>
<? require("include/bottom.php"); ?>
