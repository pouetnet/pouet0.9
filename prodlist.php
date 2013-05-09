<?
require("include/top.php");

$prods_per_page=$user["prodlistprods"];

//print("->".$typ."->".$platf."->".$order."<-<br />");
//session_start();
//if ($_SESSION['ourtype'] && !$typ) $typ=$_SESSION['ourtype'];
//if ($_SESSION['ourplatform'] && !$platf) $platf=$_SESSION['ourplatform'];
//if ($_SESSION['ourorder'] && !$order) $order=$_SESSION['ourorder'];

//print("->".$typ[0]."->".count($platf)."->".$order."<-<br />");
/*
if ($type) $typ[0]=$type;
if ($type2) $typ[1]=$type2;
if ($type3) $typ[2]=$type3;

if ($platform) $platf[0]=$platform;
if ($platform2) $platf[1]=$platform2;
if ($platform3) $platf[2]=$platform3;
*/
//print("->".count($typ)."->".count($platf)."->".$order."<-<br />");

$result = mysql_query("DESC prods type");
$row = mysql_fetch_row($result);
$typeslist = explode("'",$row[1]);
// get list of all platforms
$query="select * from platforms order by name asc";
$result = mysql_query($query);
while($tmp = mysql_fetch_array($result)) {
  	 $platforms[]=$tmp;
}
?>
<br />
<form action="prodlist.php" method="get">
<table bgcolor="#000000" cellspacing="1" cellpadding="0" border="0">
<tr><td>
<table bgcolor="#000000" cellspacing="1" cellpadding="2" border="0">
 <tr>
 <th bgcolor="#224488" colspan="2"><center><b>selection</b></center></th>
 </tr>
 <tr>
	 <td bgcolor="#446688">type:<br />
	 <select name="type[]" multiple size="10">
	  <?
		for($i=1;$i<count($typeslist);$i+=2) {
		  $ok=0;
		  for($j=0;$j<count($_GET["type"]);$j++) {
			//print($typeslist[$i]."\n".$type[$j]."\n");
  			if($typeslist[$i]==$_GET["type"][$j]) {
  			  $ok++;
  			}
		  }
		  if($ok) {
			$is_selected = " selected";
		  } else {
			$is_selected = "";
		  }
		  print("<option".$is_selected.">".$typeslist[$i]."</option>\n");
		}
	  ?>
	  </select>
	 </td>
	 <td bgcolor="#446688">platform:<br />
	  <select name="platform[]" multiple size="10">
	  <?
		for($i=0;$i<count($platforms);$i++) {
		  $ok=0;
		  for($j=0;$j<count($_GET["platform"]);$j++) {
  			//print($platforms[$i]["name"]."\n".$platf[$j]."\n");
  			if($platforms[$i]["name"]==$_GET["platform"][$j]) {
  			  $ok++;
  			}
		  }
		  if($ok) {
			$is_selected = " selected";
		  } else {
			$is_selected = "";
		  }
		  print("<option".$is_selected.">".$platforms[$i]["name"]."</option>\n");
		}
	  ?>
	  </select>
	  <input name="order" value="<?=$_GET["order"]?>" type="hidden"/>
	 </td>
 </tr>
 <tr><td bgcolor="#224488" colspan="2" align="center"><input type="image" src="gfx/submit.gif" border="0"></td></tr>
 </table>
</tr></td>
</table>

<br />
<?
function goodfleche($wanted,$current) { // [17:12:26] <Keops_Eqx> fleche = arrow
  $fleche = "fleche1";
  if ($_GET["reverse"] && $wanted==$current)
    $fleche = "fleche2";
  if($wanted==$current) {
    $fleche.="a";
  } else {
    $fleche.="b";
  }
  return $fleche;
}

$query="SELECT distinct prods.id,prods.name,prods.group1,prods.group2,prods.group3,prods.type,prods.views,prods.date,prods.party,prods.party_year,prods.party_place,prods.quand,prods.voteup,prods.votepig,prods.votedown,prods.voteavg,platforms.name as platform,parties1.name as partyname,GROUP_CONCAT(platforms.name) as allplatforms FROM prods ";
$query.="LEFT JOIN parties as parties1 ON parties1.id=prods.party ";

unset($platfcheck);
for($i=0;$i<count($_GET["platform"]);$i++) {
  	for($j=0;$j<count($platforms);$j++) {
  		if ($_GET["platform"][$i]==$platforms[$j]["name"]):
 			$platfquery.="platforms.name='".$_GET["platform"][$i]."' OR ";
 			$platfcheck++;
  		endif;
  	}
  }
unset($typecheck);
for($i=0;$i<count($_GET["type"]);$i++) {
	$typequery.="FIND_IN_SET('".$_GET["type"][$i]."',prods.type) OR ";
	$typecheck++;
  }

$query.=", prods_platforms, platforms WHERE prods_platforms.platform=platforms.id AND prods_platforms.prod=prods.id AND ";
if($typecheck) $query.="(".$typequery."0) AND ";
if($platfcheck) $query.="(".$platfquery."0) AND ";
if($_GET["year"]) $query.="(date between '".(int)$_GET["year"]."-01-01' and '".(int)$_GET["year"]."-12-31') AND ";
$query.="1";

$query.=" GROUP BY prods.id ";

$orderClause = "";
switch($order) {
  case "name": $orderClause.=" ORDER BY prods.name"; break;
  case "group": $orderClause.=" ORDER BY prods.group1,prods.group2,prods.group3"; break;
  case "party": $orderClause.=" ORDER BY partyname,prods.party_year,prods.name"; break;
  case "type": $orderClause.=" ORDER BY prods.type"; break;
  case "platform": $orderClause.=" ORDER BY platform"; break;
  case "views": $orderClause.=" ORDER BY prods.views DESC"; break;
  //case "release": $orderClause.=" ORDER BY prods.date DESC, prods.quand DESC"; break;
  case "added": $orderClause.=" ORDER BY prods.quand DESC"; break;
  case "thumbup": $orderClause.=" ORDER BY prods.voteup DESC, prods.quand DESC,prods.voteavg DESC"; break;
  case "thumbpig": $orderClause.=" ORDER BY prods.votepig DESC, prods.quand DESC,prods.voteavg DESC"; break;
  case "thumbdown": $orderClause.=" ORDER BY prods.votedown DESC, prods.quand DESC,prods.voteavg DESC"; break;
  case "avg": $orderClause.=" ORDER BY prods.voteavg DESC, prods.voteup DESC, prods.quand DESC"; break;
  case "avgrev": $orderClause.=" ORDER BY prods.voteavg, prods.votedown DESC, prods.quand DESC"; break;
  case "weighted": $orderClause.=" ORDER BY prods.voteavg * (prods.votedown+prods.votepig+prods.voteup) DESC"; break;
  default: $orderClause.=" ORDER BY prods.date DESC, prods.quand DESC"; break;
}
if ($_GET["reverse"])
{
  if (substr($orderClause,-5)==" DESC")
    $orderClause = str_replace(" DESC"," ASC",$orderClause);
  else
    $orderClause = str_replace(","," DESC,",$orderClause) . " DESC";
}

$query .= $orderClause;

if(($page<=0)||(!$page)) {
  $page=1;
}
$query.=" LIMIT ".(($page-1)*$prods_per_page).",$prods_per_page";

$result = mysql_query($query);
//print("->".$query."<-");

while($tmp = mysql_fetch_array($result)) {
  $prods[]=$tmp;
}

for($i=0;$i<count($prods);$i++) {
  //cdc count
		$result=mysql_query("SELECT count(0) from users_cdcs where cdc=".$prods[$i]["id"]);
		$prods[$i]["cdc"]=mysql_result($result,0);

		$result=mysql_query("SELECT count(0) from cdc where which=".$prods[$i]["id"]);
		$prods[$i]["cdc"]=$prods[$i]["cdc"]+mysql_result($result,0);


		if ($prods[$i]["group1"]):
			$query="select name,acronym from groups where id='".$prods[$i]["group1"]."'";
			$result=mysql_query($query);
			while($tmp = mysql_fetch_array($result)) {
			  $prods[$i]["groupn1"]=$tmp["name"];
			  $prods[$i]["groupacron1"]=$tmp["acronym"];
			 }
		endif;
		if ($prods[$i]["group2"]):
			$query="select name,acronym from groups where id='".$prods[$i]["group2"]."'";
			$result=mysql_query($query);
			while($tmp = mysql_fetch_array($result)) {
			  $prods[$i]["groupn2"]=$tmp["name"];
			  $prods[$i]["groupacron2"]=$tmp["acronym"];
			 }
		endif;
		if ($prods[$i]["group3"]):
			$query="select name,acronym from groups where id='".$prods[$i]["group3"]."'";
			$result=mysql_query($query);
			while($tmp = mysql_fetch_array($result)) {
			  $prods[$i]["groupn3"]=$tmp["name"];
			  $prods[$i]["groupacron3"]=$tmp["acronym"];
			 }
		endif;
		
		if (strlen($prods[$i]["groupn1"].$prods[$i]["groupn2"].$prods[$i]["groupn3"])>27):
		if (strlen($prods[$i]["groupn1"])>10 && $prods[$i]["groupacron1"]) $prods[$i]["groupn1"]=$prods[$i]["groupacron1"];
		if (strlen($prods[$i]["groupn2"])>10 && $prods[$i]["groupacron2"]) $prods[$i]["groupn2"]=$prods[$i]["groupacron2"];
		if (strlen($prods[$i]["groupn3"])>10 && $prods[$i]["groupacron3"]) $prods[$i]["groupn3"]=$prods[$i]["groupacron3"];
	endif;
	

  $result=mysql_query("SELECT * from sceneorgrecommended where prodid=".$prods[$i]["id"]." ORDER BY type");
  while($tmp=mysql_fetch_array($result)) {
  	$sceneorgrecommends[]=$tmp;
  }
}

$result=mysql_query("SELECT MAX(views) FROM prods");
$max_views=mysql_result($result,0);
$query="SELECT count(distinct prods.id) FROM prods";
$query.=", prods_platforms, platforms WHERE prods_platforms.platform=platforms.id AND prods_platforms.prod=prods.id AND ";
if($typecheck) $query.="(".$typequery."0) AND ";
if($platfcheck) $query.="(".$platfquery."0) AND ";
$query.="1";
$result=mysql_query($query);
$nbprods=mysql_result($result,0);
//$nbprods = count($prods);

if($typecheck) {
	//unset($_SESSION['ourtype']);
	//unset($GLOBALS[_SESSION]['ourtype']);
	$_SESSION['ourtype']=$_GET["type"];
}
if($platfcheck) {
	//unset($_SESSION['ourplatform']);
	//unset($GLOBALS[_SESSION]['ourtype']);
	$_SESSION['ourplatform']=$_GET["platform"];
}

?>
<table bgcolor="#000000" cellspacing="1" cellpadding="0" border="0">
 <tr>
  <td>
   <table bgcolor="#000000" cellspacing="1" cellpadding="2" border="0">
    <tr bgcolor="#224488">
     <th>
     <?
     	$pagelink="prodlist.php?";
     	if($_GET["platform"]) foreach($_GET["platform"] as $p)
     	  $pagelink.="platform[]=".rawurlencode($p)."&amp;";
     	if($_GET["type"]) foreach($_GET["type"] as $p)
     	  $pagelink.="type[]=".rawurlencode($p)."&amp;";
     	if($_GET["year"])
     	  $pagelink.="year=".(int)$_GET["year"]."&amp;";
     	
     	$sortlink=$pagelink;
     	//unset($GLOBALS[_SESSION]['ourorder']);
        //unset($_SESSION['ourorder']);
        $_SESSION['ourorder']=$order;
        //print("->".$sortlink."<-");

      function printSortlink($ord)
      {
        global $sortlink;
        $s = $sortlink;
        if ($_GET["order"] == $ord && !$_GET["reverse"])
          $s .= "reverse=1&amp;";
        $s .="order=".rawurlencode($ord);
        echo $s;
      }
      if ($_GET["reverse"])
        $pagelink.="reverse=1&amp;";
      if ($_GET["order"])
        $pagelink.="order=".rawurlencode($_GET["order"])."&amp;";
     ?>

      <table><tr>
       <td>
         <a href="<? printSortlink("type"); ?>"><img src="gfx/<? print(goodfleche("type",$order)); ?>.gif" width="13" height="12" border="0"></a><br />
       </td>
       <td>
        <a href="<? printSortlink("type"); ?>"><b>type</b></a><br />
       </td>
       <td>
        <a href="<? printSortlink("name"); ?>"><img src="gfx/<? print(goodfleche("name",$order)); ?>.gif" width="13" height="12" border="0"></a><br />
       </td>
       <td align="left" width="100%">
        <a href="<? printSortlink("name"); ?>"><b>name</b></a><br />
       </td>
       <td align="right">
        <a href="<? printSortlink("platform"); ?>"><img src="gfx/<? print(goodfleche("platform",$order)); ?>.gif" width="13" height="12" border="0"></a><br />
       </td>
       <td align="right">
        <a href="<? printSortlink("platform"); ?>"><b>platform</b></a>
       </td>
      </tr></table>
     </th>
     <th>
      <table><tr>
       <td>
        <a href="<? printSortlink("group"); ?>"><img src="gfx/<? print(goodfleche("group",$order)); ?>.gif" width="13" height="12" border="0"></a><br />
       </td>
       <td>
        <a href="<? printSortlink("group"); ?>"><b>group</b></a><br />
       </td>
      </tr></table>
     </th>
     <th>
      <table><tr>
       <td>
        <a href="<? printSortlink("party"); ?>"><img src="gfx/<? print(goodfleche("party",$order)); ?>.gif" width="13" height="12" border="0"></a><br />
       </td>
       <td>
        <a href="<? printSortlink("party"); ?>"><b>release party</b></a>
       </td>
      </tr></table>
     </th>
     <th>
      <table><tr>
       <td>
        <a href="<? printSortlink("release"); ?>"><img src="gfx/<? print(goodfleche("release",$order)); ?>.gif" width="13" height="12" border="0"></a><br />
       </td>
       <td>
        <a href="<? printSortlink("release"); ?>"><b>released</b></a>
       </td>
      </tr></table>
     </th>
     <th>
      <table><tr>
       <td>
        <a href="<? printSortlink("added"); ?>"><img src="gfx/<? print(goodfleche("added",$order)); ?>.gif" width="13" height="12" border="0"></a><br />
       </td>
       <td>
        <a href="<? printSortlink("added"); ?>"><b>added</b></a>
       </td>
      </tr></table>
     </th>
     <th>
        <a href="<? printSortlink("thumbup"); ?>"><img src="gfx/rulez.gif" alt="rulez" border="0"></a>
     </th>
     <th>
        <a href="<? printSortlink("thumbpig"); ?>"><img src="gfx/isok.gif" alt="piggie" border="0"></a>
     </th>
     <th>
        <a href="<? printSortlink("thumbdown"); ?>"><img src="gfx/sucks.gif" alt="sucks" border="0"></a>
     </th>
     <th>
      <table><tr>
       <td>
        <a href="<? printSortlink("avg"); ?>"><img src="gfx/<? print(goodfleche("avg",$order)); ?>.gif" width="13" height="12" border="0"></a><br />
       </td>
       <td>
        <a href="<? printSortlink("avg"); ?>"><b>µ</b></a>
       </td>
      </tr></table>
     </th>
     <th>
      <table><tr>
       <td>
        <a href="<? printSortlink("views"); ?>"><img src="gfx/<? print(goodfleche("views",$order)); ?>.gif" width="13" height="12" border="0"></a><br />
       </td>
       <td>
        <a href="<? printSortlink("views"); ?>"><b>popularity</b></a>
       </td>
      </tr></table>
     </th>
    </tr>
   <?
   for($i=0;$i<count($prods);$i++) {
     if($i%2) {
       print("<tr bgcolor=\"#446688\">");
     } else {
       print("<tr bgcolor=\"#557799\">");
     }
     ?>

      <? $typess = explode(",", $prods[$i]["type"]);
		print("<td nowrap><table cellspacing=\"0\" cellpadding=\"0\"><tr><td nowrap><a href=\"prod.php?which=".$prods[$i]["id"]."\">");
		for($k=0;$k<count($typess);$k++) {
		print("<img src=\"gfx/types/".$types[$typess[$k]]."\" width=\"16\" height=\"16\" border=\"0\" title=\"".$typess[$k]."\">");
		}
		print("<br /></a></td><td><img src=\"gfx/z.gif\" width=\"2\" height=\"1\" border=\"0\"><br /></td><td nowrap>");
      ?>
      <a href="prod.php?which=<? print($prods[$i]["id"]); ?>"><b><? print(strtolower(stripslashes($prods[$i]["name"]))); ?></b></a>
      <?

      		print("<td>&nbsp;</td>");
      		
      		if(count($sceneorgrecommends)):
        	print("<td nowrap>");
		for($k=0;$k<count($sceneorgrecommends);$k++) {
			if ($sceneorgrecommends[$k]["prodid"]==$prods[$i]["id"]) print("<img src=\"gfx/sceneorg/".$sceneorgrecommends[$k]["type"].".gif\" width=\"16\" height=\"16\" border=\"0\" title=\"".$sceneorgrecommends[$k]["category"]."\" alt=\"".$sceneorgrecommends[$k]["category"]."\">");
		}
		print("<br /></td>");
		endif;

		if($prods[$i]["cdc"]):
		 print("<td nowrap>");
 		 cdcstack($prods[$i]["cdc"]);
		 //for($ii=0;$ii<$prods[$i]["cdc"];$ii++): print("<img src=\"gfx/titles/coupdecoeur.gif\" width=\"16\" height=\"16\" border=\"0\" title=\"cdc\" alt=\"cdc\">");
		 //endfor;
		 print("<br /></td>");
		endif;

		print("<td width=\"100%\">&nbsp;</td>");
		
      		$platforms = explode(",", $prods[$i]["allplatforms"]);
       		for($kkk=0;$kkk<count($platforms);$kkk++) {
       		?><td align="right"><a href="prodlist.php?platform[]=<? print($platforms[$kkk]); ?>"><img src="gfx/os/<? print($os[$platforms[$kkk]]); ?>" width="16" height="16" border="0" title="<? print($platforms[$kkk]); ?>"></a><br /></td><?
       		}
       		
       		print("</tr></table></td>\n");
      ?>
      <td nowrap>
       <? if(strlen($prods[$i]["groupn1"])): ?>
       <a href="groups.php?which=<? print($prods[$i]["group1"]); ?>">
        <? print(strtolower($prods[$i]["groupn1"])); ?>
       </a>
       <? else: ?>
       &nbsp;
       <? endif; ?>
       <? if(strlen($prods[$i]["groupn2"])): ?>
       ::
       <a href="groups.php?which=<? print($prods[$i]["group2"]); ?>">
        <? print(strtolower($prods[$i]["groupn2"])); ?>
       </a>
       <? endif; ?>
       <? if(strlen($prods[$i]["groupn3"])): ?>
       ::
       <a href="groups.php?which=<? print($prods[$i]["group3"]); ?>">
        <? print(strtolower($prods[$i]["groupn3"])); ?>
       </a>
       <? endif; ?>
      </td>
      <? if(($prods[$i]["partyname"])&&!($prods[$i]["party"]==1024))
	{
		$placeadj="";
		if($prods[$i]["party_place"])
		{
           		switch($prods[$i]["party_place"]) {
             		case 1:
             		case 21:
             		case 31:
             		case 41:
             		case 51:
             		case 61:
             		case 71:
             		case 81:
             		case 91: $placeadj="st";
             			print("<td nowrap>".$prods[$i]["party_place"].$placeadj." at <a href=\"party.php?which=".$prods[$i]["party"]."&when=".sprintf("%02d",$prods[$i]["party_year"])."\">".$prods[$i]["partyname"]."</a><br /></td>\n");
             			break;
             		case 2:
             		case 22:
             		case 32:
             		case 42:
             		case 52:
             		case 62:
             		case 72:
             		case 82:
             		case 92: $placeadj="nd";
             			print("<td nowrap>".$prods[$i]["party_place"].$placeadj." at <a href=\"party.php?which=".$prods[$i]["party"]."&when=".sprintf("%02d",$prods[$i]["party_year"])."\">".$prods[$i]["partyname"]."</a><br /></td>\n");
             			break;
             		case 3:
             		case 23:
             		case 33:
             		case 43:
             		case 53:
             		case 63:
             		case 73:
             		case 83:
             		case 93: $placeadj="rd";
             			print("<td nowrap>".$prods[$i]["party_place"].$placeadj." at <a href=\"party.php?which=".$prods[$i]["party"]."&when=".sprintf("%02d",$prods[$i]["party_year"])."\">".$prods[$i]["partyname"]."</a><br /></td>\n");
             			break;
                case 97: print("<td nowrap>disqualified at <a href=\"party.php?which=".$prods[$i]["party"]."&when=".sprintf("%02d",$prods[$i]["party_year"])."\">".$prods[$i]["partyname"]."</a><br /></td>\n");
								  break;
             		case 98: print("<td nowrap>for <a href=\"party.php?which=".$prods[$i]["party"]."&when=".sprintf("%02d",$prods[$i]["party_year"])."\">".$prods[$i]["partyname"]."</a><br /></td>\n");
             			break;
             		case 99: print("<td nowrap>not shown at <a href=\"party.php?which=".$prods[$i]["party"]."&when=".sprintf("%02d",$prods[$i]["party_year"])."\">".$prods[$i]["partyname"]."</a><br /></td>\n");
             			break;
             		default: $placeadj="th";
             			print("<td nowrap>".$prods[$i]["party_place"].$placeadj." at <a href=\"party.php?which=".$prods[$i]["party"]."&when=".sprintf("%02d",$prods[$i]["party_year"])."\">".$prods[$i]["partyname"]."</a><br /></td>\n");
             			break;
           		}
         	} else
         	{
         		 $placeadj = "??";
         		 print("<td nowrap>".$prods[$i]["party_place"].$placeadj." at <a href=\"party.php?which=".$prods[$i]["party"]."&when=".sprintf("%02d",$prods[$i]["party_year"])."\">".$prods[$i]["partyname"]."</a><br /></td>\n");
		}
        } else {
          print("<td nowrap>&nbsp<br /></td>\n");
        } ?>
       <td nowrap align="right">
		<?
		$nbmonth=substr($prods[$i]["date"],5,2);
		$month=$months[sprintf("%d",$nbmonth)];
		$year=substr($prods[$i]["date"],0,4);
		?>
       <? print($month." ".$year); ?><br />
      </td>
      <td nowrap align="right">
      <?
		$nbmonth=substr($prods[$i]["quand"],5,2);
		$month=$months[sprintf("%d",$nbmonth)];
		$year=substr($prods[$i]["quand"],0,4);
		?>
       <? print($month." ".$year); ?><br />
      </td>

<?
	if ($prods[$i]["voteup"])
	{ print("<td>\n".$prods[$i]["voteup"]."</td>\n");
	}
	else
	{print("<td>\n0</td>\n");
	}
	if ($prods[$i]["votepig"])
	{ print("<td>\n".$prods[$i]["votepig"]."</td>\n");
	}
	else
	{print("<td>\n0</td>\n");
	}
	if ($prods[$i]["votedown"])
	{ print("<td>\n".$prods[$i]["votedown"]."</td>\n");
	}
	else
	{print("<td>\n0</td>\n");
	}
	
      /*if($prods[$i]["voteavg"]>0)
		$thumbgfx="gfx/rulez.gif";
	elseif($prods[$i]["voteavg"]==0)
		$thumbgfx="gfx/isok.gif";
	else
		$thumbgfx="gfx/sucks.gif";*/
	printf("<td align=\"right\">%.2f</td>",$prods[$i]["voteavg"]);
	
?>
      <td>
       <? DoBar(floor($prods[$i]["views"]*100/$max_views)); ?>
      </td>
     </tr>
     <?
   }
   ?>
    <tr bgcolor="#224488">
     <td colspan="12">
      <table width="100%" cellspacing="0" cellpadding="0" border="0">
       <tr>
       <? if($page>=2): ?>
        <td>
         <a href="<?=$pagelink?>page=<?=($page-1)?>">
          <img src="gfx/flecheg.gif" border="0"><br />
         </a>
        </td>
        <td>&nbsp;</td>
        <td nowrap>
         <a href="<?=$pagelink?>page=<?=($page-1)?>">
          <b>previous page</b><br />
         </a>
        </td>
       <? endif; ?>

        <td width="50%" align="right">
        <select name="page">
        <? for($i=1;($i-1)<=($nbprods/$prods_per_page);$i++): ?>
        <? if($i==$page): ?>
        <option value="<? print($i); ?>" selected><? print($i); ?></option>
        <? else: ?>
        <option value="<? print($i); ?>"><? print($i); ?></option>
        <? endif; ?>
        <? endfor; ?>
        </select><br />
        </td>
        <td>&nbsp;</td>
        <td width="50%">
    	  <input name="order" value="<?=$_GET["order"]?>" type="hidden"/>
        <input type="image" src="gfx/submit.gif" border="0"><br />
        </td>
       <? if(($page*$prods_per_page)<=$nbprods): ?>
        <td nowrap>
         <a href="<?=$pagelink?>page=<?=($page+1)?>">
          <b>next page</b><br />
         </a>
        </td>
        <td>&nbsp;</td>
        <td>
         <a href="<?=$pagelink?>page=<?=($page+1)?>">
          <img src="gfx/fleched.gif" border="0"><br />
         </a>
        </td>
       <? endif; ?>
       </form>
       </tr>
      </table>
     </td>
    </tr>
   </table>
  </td>
 </tr>
</table>
<br />
<? require("include/bottom.php"); ?>
