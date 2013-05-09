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

if(!$pattern&&!$which) {
  $pattern=chr(mt_rand(96,122));
  if($pattern==chr(96)) {
    $pattern="#";
  }
}

  	$query="SELECT distinct(prods.id),prods.name,prods.group1,prods.group2,prods.group3,prods.type,prods.partycompo,prods.date,prods.party,prods.party_year,prods.party_place,prods.views,prods.voteup,prods.votepig,prods.votedown,prods.voteavg,dl.link,parties1.name as partyname FROM prods join downloadlinks dl on dl.prod=prods.id LEFT JOIN parties as parties1 ON parties1.id=prods.party WHERE dl.type like 'source'";
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

$result = mysql_query($query);
while($tmp = mysql_fetch_array($result)) {
  $prods[]=$tmp;
}

	for($i=0;$i<count($prods);$i++) {
			
		//cdc count
		$result=mysql_query("SELECT count(0) from users_cdcs where cdc=".$prods[$i]["id"]);
		$prods[$i]["cdc"]=mysql_result($result,0);
		
		$result=mysql_query("SELECT count(0) from cdc where which=".$prods[$i]["id"]);
		$prods[$i]["cdc"]=$prods[$i]["cdc"]+mysql_result($result,0);
		
		//get latestcomment
		$result=mysql_query("SELECT users.nickname,users.avatar,comments.quand, comments.who from comments LEFT JOIN users ON users.id=comments.who where comments.which=".$prods[$i]["id"]." order by quand desc limit 1");
		$lcom=mysql_fetch_array($result);
		$prods[$i]["lcom_nick"]=$lcom["nickname"];
		$prods[$i]["lcom_avatar"]=$lcom["avatar"];
		$prods[$i]["lcom_quand"]=$lcom["quand"];
		$prods[$i]["lcom_who"]=$lcom["who"];

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

  	if ($order=="latestcomment") usort($prods, "cmpcomments");

?>
<br />
<table><tr><td valign="top">
<table bgcolor="#000000" cellspacing="1" cellpadding="0" border="0">
 <tr>
  <td>
   <table bgcolor="#000000" cellspacing="1" cellpadding="2" border="0">

    <? $sortlink="sourceprod.php?order="; ?>
    <tr bgcolor="#224488">
     <th colspan="9" align="center">source prods</td>
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
<?			
     	$i=0;
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
		print("<br /></a></td><td><img src=\"gfx/z.gif\" width=\"2\" height=\"1\" border=\"0\"><br /></td><td nowrap><a href=\"prod.php?which=".$prods[$j]["id"]."\">".strtolower(htmlentities(stripslashes($prods[$j]["name"])))."</a> (<a href=\"".$prods[$j]["link"]."\">source</a>)<br /></td><td>&nbsp;</td>");
		
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
         <td>
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
           print("<td>".$rdate2[2]." ".$rmonth." ".$rdate2[0]."<br /></td>\n");
	?>
        </tr>
       </table>
      </td>

       	<?
       	} else {
		print("<td>&nbsp;<br /></td>\n");
	} } ?>
   </table>
  </td>
 </tr>
</table>
<br />
<? require("include/bottom.php"); ?>
