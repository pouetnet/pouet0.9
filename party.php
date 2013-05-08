<?
$when=$_REQUEST['when'];
$which=$_REQUEST['which'];
$order=$_REQUEST['order'];

require("include/top.php");

function goodfleche($wanted,$current) {
  if($wanted==$current) {
    $fleche="fleche1a";
  } else {
    $fleche="fleche1b";
  }
  return $fleche;
}

function cmpyears($a, $b)
{
     if ($a["py"] == $b["py"])
     {
         return 0;
     }
     return ($a["py"] < $b["py"]) ? -1 : 1;
}

function pushinvit($a, $b)
{
	if ($a["partycompo"]=="invit") return -1;
	 else if ($b["partycompo"]=="invit") return 1;
	  if ($a["partycompo"]=="none") return 1;
	   else if ($b["partycompo"]=="none") return -1;
	  else if ($a["partycompo"]==$b["partycompo"])
	  {
	  	return ($a["party_place"] < $b["party_place"]) ? -1 : 1;
	}
	   else return ($a["partycompo"] < $b["partycompo"]) ? -1 : 1;
}

function reorder_type($a, $b)
{
     if ($a["type"] == $b["type"])
     {
         return 0;
     }
     return ($a["type"] < $b["type"]) ? -1 : 1;
}

function reorder_partycompo($a, $b)
{
     if ($a["partycompo"] == $b["partycompo"])
     {
         return 0;
     }
     return ($a["partycompo"] < $b["partycompo"]) ? -1 : 1;
}

function reorder_name($a, $b)
{
     if (strtolower($a["name"]) == strtolower($b["name"]))
     {
         return 0;
     }
     return (strtolower($a["name"]) < strtolower($b["name"])) ? -1 : 1;
}

function reorder_platform($a, $b)
{
     if ($a["platform"] == $b["platform"])
     {
         return 0;
     }
     return ($a["platform"] < $b["platform"]) ? -1 : 1;
}

function reorder_views($a, $b)
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
}


if (!$when)
{
  $query="SELECT party_year,invitation,invitationyear FROM prods WHERE party=".$which." or invitation=".$which." ORDER BY RAND() LIMIT 1";
  $result=mysql_query($query);
  $o=mysql_fetch_object($result);
  $when = max($o->party_year,$o->invitation ? $o->invitationyear : 0);
}

/* extend to 4 years */
$when = intval($when);
if ($when < 50) {
  $when = $when + 2000;
} elseif ($when < 100) {
  $when = $when + 1900;
}
/* results filenames still use 2-digit names */
$when2d = substr($when,-2);

$query = sprintf("SELECT * from partiesaka WHERE party1=%d OR party2=%d",$which,$which);
//var_dump($query);
$result=mysql_query($query);
while($tmp=mysql_fetch_assoc($result)) {
  $partyaka[]=$tmp["party1"];
  $partyaka[]=$tmp["party2"];
}
if ($partyaka)
{
  $partyaka = array_unique($partyaka);
}

if (count($partyaka))
{
  $s = implode(",",$partyaka);
  $query = sprintf("SELECT * from partiesaka WHERE party1 IN (%s) OR party2 IN (%s)",$s,$s);
  //var_dump($query);
  $result=mysql_query($query);
  while($tmp=mysql_fetch_assoc($result)) {
    $partyaka[]=$tmp["party1"];
    $partyaka[]=$tmp["party2"];
  }
  $partyaka = array_unique($partyaka);
}


$query="SELECT parties.name,parties.web,partylinks.download,partylinks.csdb,partylinks.zxdemo,partylinks.slengpung,partylinks.artcity FROM parties LEFT JOIN partylinks ON (partylinks.party=parties.id AND partylinks.year=".$when.") WHERE parties.id=".$which;
$query.=" LIMIT 1";
$result=mysql_query($query);
while($tmp=mysql_fetch_array($result)) {
  $party[]=$tmp;
}

$query="SELECT prods.views,prods.id,prods.name,prods.type,prods.party_place,prods.partycompo,prods.voteavg,prods.voteup,prods.votepig,prods.votedown,prods.group1,prods.group2,prods.group3,prods.invitation,prods.invitationyear FROM prods WHERE (prods.party=".$which." AND prods.party_year=".$when.") OR (prods.invitation=".$which." AND (prods.invitationyear=".$when." OR prods.invitationyear=".$when2d."))";
switch($order) {
  //case "pos": $query.=" ORDER BY prods.party_place"; break;
  case "type": $query.=" ORDER BY prods.type"; break;
  case "compo": $query.=" ORDER BY prods.partycompo"; break;
  case "name": $query.=" ORDER BY prods.name"; break;
  //case "platform": $query.=" ORDER BY prods.platform"; break;
  case "views": $query.=" ORDER BY prods.views DESC,prods.party_place"; break;
  case "thumbup": $query.=" ORDER BY prods.voteup DESC,prods.voteavg DESC"; break;
  case "thumbpig": $query.=" ORDER BY prods.votepig DESC,prods.voteavg DESC"; break;
  case "thumbdown": $query.=" ORDER BY prods.votedown DESC,prods.voteavg DESC"; break;
  case "avg": $query.=" ORDER BY prods.voteavg DESC,prods.voteup DESC"; break;
  default: $query.=" ORDER BY prods.partycompo";
           $order="compo";
           break;
}
$query.=",prods.party_place";
$result=mysql_query($query);
while($tmp=mysql_fetch_array($result)) {
  $prods[]=$tmp;
}

$csdbflag=0;
$zxdemoflag=0;
//$pushinvitflag=0;
for($i=0;$i<count($prods);$i++) {

		$prods[$i]["total"] = $prods[$i]["voteup"]+$prods[$i]["votedown"]+$prods[$i]["votepig"];

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
//		print("passing ->".$prods[$i]["id"]."-".$prods[$i]["invitation"]."-".$prods[$i]["invitationyear"]."<- <br />");
		if ($prods[$i]["invitation"]==$which && $prods[$i]["invitationyear"]==$when) {
//			print("entered ->".$prods[$i]["id"]."<- <br />");
			$prods[$i]["partycompo"]="invit";
			$prods[$i]["party_place"]="98";
			//$pushinvitflag=1;
		}

		$result=mysql_query("SELECT * from sceneorgrecommended where prodid=".$prods[$i]["id"]." ORDER BY type");
		  while($tmp=mysql_fetch_array($result)) {
		  	$sceneorgrecommends[]=$tmp;
		  }
}

//get list of years this party occured on and sort them

$query="SELECT distinct party_year as py, party FROM prods where prods.party=".$which;
if ($partyaka)
{
  foreach($partyaka as $v)
  {
    $query.=" OR party=".$v;
  }
}
$result=mysql_query($query);
while($tmp=mysql_fetch_array($result)) {
  if ($tmp["py"]) {
    $partyyears[$tmp["py"]]=$tmp;
    //$partyyears[$tmp["py"]]["party"] = $which;
  }
}

$query="SELECT distinct invitationyear as py, invitation as party, prods.id FROM prods where invitationyear > 0 and (prods.invitation=".$which;
if ($partyaka)
{
  foreach($partyaka as $v)
  {
    $query.=" OR invitation=".$v;
  }
}
$query.=")";

$result=mysql_query($query);
while($tmp=mysql_fetch_array($result)) {
//  var_dump($tmp);
  if (!$partyyears[$tmp["py"]]) {
    $partyyears[$tmp["py"]]=$tmp;
  }
}

usort($partyyears, "cmpyears");

if ($order=="compo") usort($prods, "pushinvit");

//get prodotherparty
$extracount=0;
$query="SELECT prods.views,prods.id,prods.name,prods.type,prodotherparty.party_place,prodotherparty.partycompo,prods.voteavg,prods.voteup,prods.votepig,prods.votedown,prods.group1,prods.group2,prods.group3,prods.invitation FROM prods LEFT JOIN prodotherparty ON prodotherparty.prod=prods.id WHERE (prodotherparty.party=".$which." AND prodotherparty.party_year=".$when.")";
$result=mysql_query($query);
while($tmp=mysql_fetch_array($result)) {
  if ($tmp["group1"]):
	$gquery="select name,acronym from groups where id='".$tmp["group1"]."'";
	$gresult=mysql_query($gquery);
	while($gtmp = mysql_fetch_array($gresult)) {
	  $tmp["groupn1"]=$gtmp["name"];
	  $tmp["groupacron1"]=$gtmp["acronym"];
	 }
  endif;
  if ($tmp["group2"]):
	$gquery="select name,acronym from groups where id='".$tmp["group2"]."'";
	$gresult=mysql_query($gquery);
	while($gtmp = mysql_fetch_array($gresult)) {
	  $tmp["groupn2"]=$gtmp["name"];
	  $tmp["groupacron2"]=$gtmp["acronym"];
	 }
  endif;
  if ($tmp["group3"]):
	$gquery="select name,acronym from groups where id='".$tmp["group3"]."'";
	$gresult=mysql_query($gquery);
	while($gtmp = mysql_fetch_array($gresult)) {
	  $tmp["groupn3"]=$gtmp["name"];
	  $tmp["groupacron3"]=$gtmp["acronym"];
	 }
  endif;

  if (strlen($tmp["groupn1"].$tmp["groupn2"].$tmp["groupn3"])>27):
	if (strlen($tmp["groupn1"])>10 && $tmp["groupacron1"]) $tmp["groupn1"]=$tmp["groupacron1"];
	if (strlen($tmp["groupn2"])>10 && $tmp["groupacron2"]) $tmp["groupn2"]=$tmp["groupacron2"];
	if (strlen($tmp["groupn3"])>10 && $tmp["groupacron3"]) $tmp["groupn3"]=$tmp["groupacron3"];
  endif;

  $tquery="select platforms.name from prods_platforms, platforms where prods_platforms.prod='".$tmp["id"]."' and platforms.id=prods_platforms.platform";
  $tresult=mysql_query($tquery);
  $check=0;
  $tmp["platform"]="";
  while($ttmp = mysql_fetch_array($tresult)) {
    if ($check>0) $tmp["platform"].=",";
    $check++;
    $tmp["platform"].=$ttmp["name"];
  }
 // print($tquery."<-".$tmp["platform"]);

  $prods[]=$tmp;
  $extracount++;
}
if ($order=="platform") usort($prods, "reorder_platform");
//sort it on the prods[] list properly
if ($extracount)
{
	switch($order) {
	  case "type": usort($prods, "reorder_type"); break;
	  case "compo": usort($prods, "reorder_partycompo");
	  		usort($prods, "pushinvit"); break;
	  case "name": usort($prods, "reorder_name"); break;
	  //case "platform": usort($prods, "reorder_platform"); break;
	  case "views": usort($prods, "reorder_views"); break;
	  case "thumbup": usort($prods, "reorder_thumbup"); break;
	  case "thumbpig": usort($prods, "reorder_thumbpig"); break;
	  case "thumbdown": usort($prods, "reorder_thumbdown"); break;
	  case "avg": usort($prods, "reorder_avg"); break;
	  default: usort($prods, "reorder_partycompo");
	  	   usort($prods, "pushinvit");
	           $order="compo";
	           break;
	}
}

//get max views (for pop count)
$result=mysql_query("SELECT MAX(views) FROM prods");
$max_views=mysql_result($result,0);

//get desc of all prods.type
$result = mysql_query("DESC prods type");
$row = mysql_fetch_row($result);
$tmptypes = explode("'",$row[1]);
for($i=1;$i<count($tmptypes);$i+=2) {
  $typelist[]=$tmptypes[$i];
}

?>
<br />
<? if ((count($prods)>0)&&($which!=1024)): ?>

<table bgcolor="#000000" cellspacing="1" cellpadding="0">
 <tr>
  <td>
   <table bgcolor="#000000" cellspacing="1" cellpadding="2">
    <tr>
     <td bgcolor="#224488" nowrap>
      <center>
       <? print("<b>".$party[0]["name"]." ".$when."</b>"); ?>
      </center>
     </td>
    </tr>
    <tr>
     <td bgcolor="#224488" nowrap>
      <center><b>
      <? if($SESSION_LEVEL=='administrator' || $SESSION_LEVEL=='moderator' || $SESSION_LEVEL=='gloperator'): ?>
           [<a href="editparty.php?which=<? print($which); ?>">editparty</a>]
           [<a href="editpartylinks.php?which=<? print($which); ?>&amp;when=<? print($when); ?>">editpartylinks</a>]
      <? endif; ?>
      <? if($party[0]["web"]): ?>
      [<a href="<?=$party[0]["web"]?>">web</a>]
      <? else: ?>
      <? if($_SESSION["SCENEID_ID"]): ?>
      [<small><a href="submitpartyweb.php?which=<? echo $which; ?>">+web</a></small>]
      <? endif; ?>
      <? endif; ?>
      <? if(file_exists("results/".$which."_".$when2d.".txt")): ?>
           [<a href="results.php?which=<?=$which?>&when=<?=$when2d?>">results</a>]
          <? else: ?>
		   <? if($_SESSION["SCENEID_ID"]): ?>
           [<small><a href="submitpartyresults.php?which=<?=$which?>&when=<?=$when?>">+results</a></small>]

		   <? endif; ?>
          <? endif; ?>
      <? if($party[0]["download"]): ?>
      [<a href="<?=$party[0]["download"]?>">download</a>]
      <? else: ?>
      <? if($_SESSION["SCENEID_ID"]): ?>
      [<small><a href="submitpartylinks.php?which=<?=$which?>&when=<?=$when?>">+download</a></small>]
      <? endif; ?>
      <? endif; ?>

      <? if($party[0]["slengpung"]): ?>
	      <? if($party[0]["slengpung"]!=12): ?>
	      [<a href="http://www.slengpung.com/?eventid=<?=$party[0]["slengpung"]?>">slengpung</a>]
	      <? endif; ?>
      <? else: ?>
      <? if($_SESSION["SCENEID_ID"]): ?>
      [<small><a href="submitpartyslengpung.php?which=<?=$which?>&when=<?=$when?>">+slengpung</a></small>]
      <? endif; ?>
      <? endif; ?>

      <? if($party[0]["csdb"]): ?>
      [<a href="http://noname.c64.org/csdb/event/?id=<?=$party[0]["csdb"]?>">csdb</a>]
      <? else: ?>
      <? if(($csdbflag>0)&&($_SESSION["SCENEID_ID"])): ?>
      [<small><a href="submitpartycsdb.php?which=<?=$which;?>&when=<?=$when?>">+csdb</a></small>]
      <? endif; ?>
      <? endif; ?>

      <? if($party[0]["zxdemo"]): ?>
      [<a href="http://zxdemo.org/party.php?id=<?=$party[0]["zxdemo"]?>">zxdemo</a>]
      <? else: ?>
      <? if(($zxdemoflag>0)&&($_SESSION["SCENEID_ID"])): ?>
      [<small><a href="submitpartyzxdemo.php?which=<?=$which;?>&when=<?=$when?>">+zxdemo</a></small>]
      <? endif; ?>
      <? endif; ?>

      <? if($party[0]["artcity"]): ?>
      [<a href="http://artcity.bitfellas.org/index.php?a=search&type=tag&text=<?=rawurlencode($party[0]["artcity"])?>">artcity</a>]
      <? else: ?>
      <? if(($_SESSION["SCENEID_ID"])): ?>
      [<small><a href="submitpartyartcity.php?which=<?=$which;?>&when=<?=$when?>">+artcity</a></small>]
      <? endif; ?>
      <? endif; ?>

      </b>
      <br /></center>
     </td>
    </tr>
    <tr>
     <td bgcolor="#224488" nowrap>
      <center>
       <?
	   $first_year_to_display = true;
       foreach($partyyears as $value) {
		 if (! $first_year_to_display)
			 print(" | ");
		 else
			 $first_year_to_display = false;
         print("<a href=\"party.php?which=" . $value["party"] . "&amp;when=" . $value["py"] . "\">" . $value["py"] . "</a>");
	   }
	   ?>
      </center>
     </td>
    </tr>
   </table>
  </td>
 </tr>
</table>
<br />

<? $sortlink="party.php?which=".$which."&when=".$when."&order="; ?>
<table bgcolor="#000000" cellspacing="1" cellpadding="0">
 <tr>
  <td>
   <table bgcolor="#000000" cellspacing="1" cellpadding="2">
    <? for($i=0;$i<count($prods);$i++): ?>
    <? $pourcent=floor($prods[$i]["views"]*100/$max_views); ?>
     <? if((($order=="compo")&&($prods[$i]["partycompo"]!=$prods[$i-1]["partycompo"])) || (($order=="type")&&($prods[$i]["type"]!=$prods[$i-1]["type"])) || ($i==0) ): ?>
      <tr>

	<td bgcolor="#224488" nowrap>
        <? if($order=="compo"): ?>
            <b><?=$prods[$i]["partycompo"]?></b></td>
        <? else: ?>
	      <table><tr>
	       <td>
	        <a href="<? print($sortlink); ?>compo"><img src="gfx/<? print(goodfleche("compo",$order)); ?>.gif" width="13" height="12" border="0"></a><br />
	       </td>
	       <td>
	        <a href="<? print($sortlink); ?>compo"><b>compo</b></a>
	       </td>
	      </tr></table>
	<? endif; ?>
	</td>

       <td bgcolor="#224488" nowrap>
        <? if($order=="type"): ?>
        <table cellspacing="0" cellpadding="0">
         <tr>
          <td>
          <? $typess = explode(",", $prods[$i]["type"]);
           for($k=0;$k<count($typess);$k++)
           { ?><img src="gfx/types/<? print($types[$typess[$k]]); ?>" width="16" height="16" border="0" alt="<? print($typess[$k]); ?>"><? } ?><br /></td>
          <td>&nbsp;</td>
          <td>
           <b><?=$prods[$i]["type"]?></b></td>
         </tr>
        </table>
	<? else: ?>
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
	       <td>
	        <a href="<? print($sortlink); ?>name"><b>prodname</b></a><br />
	       </td>
	  </tr></table>
	<? endif; ?>
	</td>

	<td bgcolor="#224488" nowrap>
	      <table><tr>
	       <td>
	        <a href="<? print($sortlink); ?>platform"><img src="gfx/<? print(goodfleche("platform",$order)); ?>.gif" width="13" height="12" border="0"></a><br />
	       </td>
	       <td>
	        <a href="<? print($sortlink); ?>platform"><b>platform</b></a>
	       </td>
	      </tr></table>
	</td>
       <td bgcolor="#224488" nowrap>
        <a href="<? print($sortlink); ?>thumbup"><img src="gfx/rulez.gif" alt="rulez" border="0"></a>
       </td>
       <td bgcolor="#224488" nowrap>
        <a href="<? print($sortlink); ?>thumbpig"><img src="gfx/isok.gif" alt="piggie" border="0"></a>
       </td>
       <td bgcolor="#224488" nowrap>
        <a href="<? print($sortlink); ?>thumbdown"><img src="gfx/sucks.gif" alt="sucks" border="0"></a>
       </td>
       <td bgcolor="#224488" nowrap>
        <table><tr>
         <td>
          <a href="<? print($sortlink); ?>avg"><img src="gfx/<? print(goodfleche("avg",$order)); ?>.gif" width="13" height="12" border="0"></a><br />
         </td>
         <td>
          <a href="<? print($sortlink); ?>avg"><b>?</b></a>
         </td>
        </tr></table>
       </td>
       <td bgcolor="#224488" nowrap>
	      <table><tr>
	       <td>
	        <a href="<? print($sortlink); ?>views"><img src="gfx/<? print(goodfleche("views",$order)); ?>.gif" width="13" height="12" border="0"></a><br />
	       </td>
	       <td>
	        <a href="<? print($sortlink); ?>views"><b>popularity</b></a>
	       </td>
	      </tr></table>
       </td>
      </tr>
     <? endif; ?>
     <?
     if($i%2) {
       print("<tr bgcolor=\"#446688\">");
     } else {
       print("<tr bgcolor=\"#557799\">");
     }
     ?>
      <td nowrap>
       <?
       if($prods[$i]["party_place"]) {
       			if ($order!="compo") $partycompoinfo=$prods[$i]["partycompo"];
       				else $partycompoinfo="";
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
             			print($prods[$i]["party_place"].$placeadj." ".$partycompoinfo);
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
             			print($prods[$i]["party_place"].$placeadj." ".$partycompoinfo);
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
             			print($prods[$i]["party_place"].$placeadj." ".$partycompoinfo);
             			break;
                	case 97: print("disqualified ".$partycompoinfo);
                  		break;
             		case 98: print("n/a ");
             			 break;
             		case 99: print("not shown ".$partycompoinfo);
             			 break;
             		default: $placeadj="th";
             			 print($prods[$i]["party_place"].$placeadj." ".$partycompoinfo);
             			 break;
           		}
       }
       ?></td>
       <td>
       <table cellspacing="1" cellpadding="0"><tr><td>
       <? if($order=="type"): ?>
           <a href="prod.php?which=<? print($prods[$i]["id"]); ?>"><? print(stripslashes($prods[$i]["name"])."</a>");
          else:
           $typess = explode(",", $prods[$i]["type"]);
           print("<a href=\"prod.php?which=".$prods[$i]["id"]."\">");
           for($k=0;$k<count($typess);$k++) {
           	print("<img src=\"gfx/types/".$types[$typess[$k]]."\" width=\"16\" height=\"16\" border=\"0\" title=\"".$typess[$k]."\">");
           }
           print("<br /></a></td><td><img src=\"gfx/z.gif\" width=\"2\" height=\"1\" border=\"0\"><br /></td><td><a href=\"prod.php?which=".$prods[$i]["id"]."\">".strtolower(stripslashes($prods[$i]["name"]))."</a><br />");
          endif;
        ?></td>
        <td><img src="gfx/z.gif" width="2" height="1" border="0"><br /></td><td>
	<? if($prods[$i]["group1"]) { print("by"); } ?>
	<a href="groups.php?which=<?=$prods[$i]["group1"]?>"><?=$prods[$i]["groupn1"]?></a>
	<? if ($prods[$i]["groupn2"]) {print(" &amp; ");} ?><a href="groups.php?which=<?=$prods[$i]["group2"]?>"><?=$prods[$i]["groupn2"]?></a>
	<? if ($prods[$i]["groupn3"]) {print(" &amp; ");} ?><a href="groups.php?which=<?=$prods[$i]["group3"]?>"><?=$prods[$i]["groupn3"]?></a></td>
	<td><img src="gfx/z.gif" width="2" height="1" border="0"><br /></td><td>
	<?

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


	 //if($prods[$i]["cdc"]) print("<img src=\"gfx/titles/coupdecoeur.gif\" width=\"16\" height=\"16\" border=\"0\" alt=\"cdc\">".$prods[$i]["cdc"]);


	 ?></td>
	</tr></table>
      </td>
      <td>
      <?=$prods[$i]["platform"]?>
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
	printf("<td align=\"right\">%.2f</td>",$prods[$i]["voteavg"]);
?>
      <td>
       <? DoBar($pourcent); ?>
      </td>
     </tr>
    <? endfor; ?>
   </table>
  </td>
 </tr>
</table>

<br />
<? if($_SESSION["SCENEID_ID"]): ?>
<? else: ?>
<table width="20%"><tr><td>
<form action="login.php" method="post">
<table cellspacing="1" cellpadding="2" class="box">
 <tr><th>login</th></tr>
 <tr bgcolor="#446688">
  <td nowrap align="center">
   You need to be logged in to submit missing party info :: <a href="account.php">register here</a><br />
   <input type="text" name="login" value="SceneID" size="15" maxlength="16" onfocus="this.value=''">
   <input type="password" name="password" value="password" size="15" onfocus="this.value=''"><br />
  </td>
 </tr>
 <tr>
  <td bgcolor="#6688AA" align="right">
   <input type="image" src="gfx/submit.gif">
  </td>
 </tr>
</table>
</form>
</td></tr></table>
<? endif; ?>

<? else: ?>
	<? if ($party["name"]!=""): ?>
	<center><? print("no results for ".$party[0]["name"]." ".$when."<br /><br />you sure it took place that year?<br /><br />*clack* (\/) O ? (\/) *clack*"); ?></center>
	<? else: ?>
	<center><? print("party clones have been lobsterxiced! feel very afraid!<br /><br /> *clack clack clack* (\/) o O (\/) *clack clack clack*"); ?></center>
	<? endif; ?>
<? endif; ?>
<br />
<? require("include/bottom.php"); ?>
