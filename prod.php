<?
$which=$_REQUEST['which'];

require("include/top.php");
require("include/libbb.php");
require("include/awardscategories.inc.php");

if ($_COOKIES) foreach( $_COOKIES as $key => $val )
{
     $trimmedKey = trim( key );
     if( $trimmedKey != $key )
          $_COOKIES[ $trimmedKey ] = $val;

}

$query="SELECT MAX(id) FROM prods";
$result=mysql_query($query);
$maxid=mysql_result($result,0);

$query ="SELECT * FROM prods ";
if((!$which)||($which>$maxid)) {
  $query.="ORDER BY RAND() ";
} else {
  $query.="WHERE id=".(int)$which." ";
}
$query.="LIMIT 1";
$result = mysql_query($query);
$prod = mysql_fetch_array($result);

if((!$which)||($which>$maxid)) {
	$which=$prod["id"];
}
$which = (int)$which;

$query="SELECT nickname,avatar FROM users WHERE id=".$prod["added"];
$result=mysql_query($query);
if ($result)
{
  $tmp=mysql_fetch_array($result);
  $prod["nickname"]=$tmp["nickname"];
  $prod["avatar"]=$tmp["avatar"];
}

if($prod["party"])
{
  $query="SELECT name FROM parties WHERE id=".$prod["party"];
  $result=mysql_query($query);
  $prod["partyname"]=mysql_result($result,0);
}
if($prod["invitation"])
{
  $query="SELECT name FROM parties WHERE id=".$prod["invitation"];
  $result=mysql_query($query);
  $prod["invitationpartyname"]=mysql_result($result,0);
}
if ($prod["boardID"])
{
  $query="SELECT name FROM bbses WHERE id=".$prod["boardID"];
  $result=mysql_query($query);
  $prod["boardname"]=mysql_result($result,0);
}
if(isset($prod["party_year"]))
{
	$pyear = $prod["party_year"];
}
if(isset($prod["invitationyear"]))
{
	$piyear = $prod["invitationyear"];
}
if($prod["group1"])
{
  $query="SELECT name,web,acronym FROM groups WHERE id=".$prod["group1"];
  $result=mysql_query($query);
  $tmp=mysql_fetch_array($result);
  $prod["groupname1"]=$tmp["name"];
  $prod["groupweb1"]=$tmp["web"];
  $prod["groupa1"]=$tmp["acronym"];
}
if($prod["group2"])
{
  $query="SELECT name,web,acronym FROM groups WHERE id=".$prod["group2"];
  $result=mysql_query($query);
  $tmp=mysql_fetch_array($result);
  $prod["groupname2"]=$tmp["name"];
  $prod["groupweb2"]=$tmp["web"];
  $prod["groupa2"]=$tmp["acronym"];
}
if($prod["group3"])
{
  $query="SELECT name,web,acronym FROM groups WHERE id=".$prod["group3"];
  $result=mysql_query($query);
  $tmp=mysql_fetch_array($result);
  $prod["groupname3"]=$tmp["name"];
  $prod["groupweb3"]=$tmp["web"];
  $prod["groupa3"]=$tmp["acronym"];
}


 $query="SELECT prodotherparty.party, prodotherparty.party_place, prodotherparty.party_year, prodotherparty.partycompo, parties.name FROM prodotherparty LEFT JOIN parties ON parties.id=prodotherparty.party WHERE prod=".$prod["id"];
 $result = mysql_query($query);
 if ($result)
 {
   while($tmp = mysql_fetch_array($result)) {
    $prodotherparties[]=$tmp;
   }
}

$query="select platforms.name, platforms.icon from prods_platforms, platforms where prods_platforms.prod='".$which."' and platforms.id=prods_platforms.platform";
	  $result = mysql_query($query);
	  while($tmp = mysql_fetch_array($result)) {
  	   $platforms[]=$tmp;
	  }

for ($i=0; $i<count($platforms); $i++)
{
	if ($platforms[$i]=="Commodore 64") $check64=1;
	if ($platforms[$i]=="C64 DTV") $check64=1;
	if ($platforms[$i]=="ZX Spectrum") $checkzx=1;
}

// popularity
function CheckReferrer($ref) {
  global $which;
  //if ($which == 55991 || $which == 55471) return false;

  $myurl=parse_url($ref);
  if(strstr($myurl["host"],"farb-rausch.de")) return false;
  if(strstr($myurl["host"],"flipcode.com")) return false;
  if(strstr($myurl["host"],"0ccult.de")) return false;
  if(strstr($myurl["host"],"ypocamp.fr")) return false;
  if(strstr($myurl["host"],"chanka.emulatronia.com")) return false;
  if(strstr($myurl["host"],"images.google")) return false;
  if(strstr($myurl["host"],"urlreload")) return false;
  if(strlen($ref)<2) return false;
  return true;
}

$currentip=getenv("REMOTE_ADDR");
if($prod["latestip"]!=$currentip && CheckReferrer($HTTP_REFERER) ) {
  //print("im about to add another view because my ref is ".$HTTP_REFERER." have a nice day!!");
  mysql_query("UPDATE prods SET views=views+1,latestip='".$currentip."' WHERE id=".$prod["id"]);
}
$result=mysql_query("SELECT MAX(views) FROM prods");
$max_views=mysql_result($result,0);
// popularity

// comments begin
if(!isset($howmanycomments)) $howmanycomments = 25;
$comments_per_page = $howmanycomments;
if ($comments_per_page != 0){
	// get nb of comments
	$result = mysql_query("SELECT count(0) FROM comments WHERE comments.which='".$prod["id"]."'");
	$nb_comments = mysql_result($result, 0);
	// if ($comments_per_page < 0) $comments_per_page = $nb_comments;
	if(!isset($page)) $page = floor($nb_comments / $comments_per_page);
	if ($page == ($nb_comments / $comments_per_page)) $page--;
	$comments_offset = $page * $comments_per_page;

	// get the comments and the associated data
	$query  = "SELECT comments.id,comments.comment,comments.rating,comments.who,comments.quand,users.nickname,users.avatar,users.level FROM comments,users WHERE comments.which='".$prod["id"]."' AND users.id=comments.who ORDER BY comments.quand ASC";
	if ($comments_per_page >= 0)
		$query .= " LIMIT $comments_offset, $comments_per_page";
	// print ($query);
	$result=mysql_query($query);
  if ($result)
  {
    while($tmp=mysql_fetch_array($result)) {
      $comments[]=$tmp;
    }
  }
}

$query  = "SELECT * from users_cdcs where users_cdcs.cdc='".$prod["id"]."'";
$result=mysql_query($query);
while($tmp=mysql_fetch_array($result)) {
  $cdcs[]=$tmp;
}

for($j=0; $j<count($cdcs); $j++)
{
	for($i=0; $i<count($comments); $i++)
	{
		if ($cdcs[$j]["user"]==$comments[$i]["who"]){
			$comments[$i]["cdc"]=$cdcs[$j]["cdc"];
		}
	}
}

$query  = "SELECT * from users_cdcs left join comments on users_cdcs.user=comments.who AND users_cdcs.cdc = comments.which where users_cdcs.cdc='".$prod["id"]."' and comments.id IS NULL";
$result=mysql_query($query);
while($tmp=mysql_fetch_object($result)) {
  $othercdc[]=$tmp->user;
}

// comments end

if($prod["id"])
{
	//cdc count
	$result=mysql_query("SELECT count(0) from users_cdcs where cdc=".$prod["id"]);
	$prod["cdc"]=mysql_result($result,0);

	$result=mysql_query("SELECT count(0) from cdc where which=".$prod["id"]);
	$prod["cdc"]=$prod["cdc"]+mysql_result($result,0);

	//sceneorgrecommended check
	$result=mysql_query("SELECT * from sceneorgrecommended where prodid=".$prod["id"]." ORDER BY type");
	while($tmp=mysql_fetch_array($result)) {
	  $sceneorgrecommends[]=$tmp;
	  }

	//affiliated prods
	$result=mysql_query(
	  " SELECT affiliatedprods.type as type,".
	  " affiliatedprods.derivative as derivative,".
	  " affiliatedprods.original as original,".
	  " prods.name as name".
	  " from affiliatedprods".
	  " join prods on prods.id=affiliatedprods.original".
	  " where affiliatedprods.derivative=".$prod["id"]." ORDER BY affiliatedprods.type");
	while($tmp=mysql_fetch_array($result)) {
    $tmp["prod"] = "original";
	  $affils[]=$tmp;
  }

	$result=mysql_query(
	  " SELECT affiliatedprods.type as type,".
	  " affiliatedprods.derivative as derivative,".
	  " affiliatedprods.original as original,".
	  " prods.name as name".
	  " from affiliatedprods".
	  " join prods on prods.id=affiliatedprods.derivative".
	  " where affiliatedprods.original=".$prod["id"]." ORDER BY affiliatedprods.type");
	while($tmp=mysql_fetch_array($result)) {
    $tmp["prod"] = "derivative";
	  $affils[]=$tmp;
  }

	$query="SELECT downloadlinks.id,downloadlinks.link,downloadlinks.type FROM downloadlinks WHERE downloadlinks.prod=".$prod["id"]." ORDER BY downloadlinks.type";
	$result = mysql_query($query);
	while($tmp=mysql_fetch_array($result)) {
	  $dl[]=$tmp;
	}

}

if(file_exists("screenshots/".$prod["id"].".jpg")) {
  $shotpath = "screenshots/".$prod["id"].".jpg";
} elseif(file_exists("screenshots/".$prod["id"].".gif")) {
  $shotpath = "screenshots/".$prod["id"].".gif";
} elseif(file_exists("screenshots/".$prod["id"].".png")) {
  $shotpath = "screenshots/".$prod["id"].".png";
}

?>
<br />
<? if(count($prod)>2): ?>
<table width="50%"><tr><td>
<table bgcolor="#000000" cellspacing="1" cellpadding="0" border="0" width="100%">
 <tr>
  <td>
   <table bgcolor="#000000" cellspacing="1" cellpadding="2" border="0" width="100%">
    <tr>
	    <td bgcolor="#224488" colspan="4">
      <table cellspacing="0" cellpadding="0" border="0" width="100%">
       <tr>
        <td>
         <b>
          <font size="+1"><?
          $s = $prod["name"];
          $s = stripslashes($s);
          $s = htmlspecialchars($s);
          $s = str_replace("&amp;#","&#",$s);
          echo $s;
          ?></font>
          <? if($prod["groupname1"]): ?>
           by
           <a href="groups.php?which=<? print($prod["group1"]); ?>"><? if ( (strlen($prod["groupname1"])<25) || !$prod["groupa1"]) {print($prod["groupname1"]);} else {print($prod["groupa1"]);} ?></a>
           <? if($prod["groupweb1"]): ?>
            [<a href="<? print($prod["groupweb1"]); ?>">web</a>]
           <? endif; ?>
          <? endif; ?>
          <? if($prod["groupname2"]): ?>
           &amp;
           <a href="groups.php?which=<? print($prod["group2"]); ?>"><? if ( (strlen($prod["groupname2"])<25) || !$prod["groupa2"]) {print($prod["groupname2"]);} else {print($prod["groupa2"]);} ?></a>
           <? if($prod["groupweb2"]): ?>
            [<a href="<? print($prod["groupweb2"]); ?>">web</a>]
           <? endif; ?>
          <? endif; ?>
          <? if($prod["groupname3"]): ?>
           &amp;
           <a href="groups.php?which=<? print($prod["group3"]); ?>"><? if ( (strlen($prod["groupname3"])<25) || !$prod["groupa3"]) {print($prod["groupname3"]);} else {print($prod["groupa3"]);} ?></a>
           <? if($prod["groupweb3"]): ?>
            [<a href="<? print($prod["groupweb3"]); ?>">web</a>]
           <? endif; ?>
          <? endif; ?>
         </b>
        </td>

        <td align="right" valign="bottom" nowrap>
         <b>
         <? if($SESSION_LEVEL=='administrator' || $SESSION_LEVEL=='moderator' || $SESSION_LEVEL=='gloperator'): ?>
           [<a href="editprod_light.php?which=<? print($which); ?>">edit</a>]
          <? endif; ?>

          <? if(file_exists("nfo/".$prod["id"].".nfo")): ?>
           <? if(filesize("nfo/".$prod["id"].".nfo")): ?>
            [<a href="nfo.php?which=<? print($prod["id"]);
            $query="select count(0) from prods_platforms, platforms WHERE platforms.name like 'Amiga%' and prods_platforms.platform=platforms.id and prods_platforms.prod=".$prod["id"];
	$result = mysql_query($query);
	if (mysql_result($result,0)>0) print("&amp;f=4"); ?>">nfo</a>]
            <? endif; ?>
          <? else: ?>
		   <? if($_SESSION["SCENEID_ID"]): ?>
	           [<small>
	            <a href="submitnfo.php?which=<? print($prod["id"]); ?>">+.nfo</a>
	           </small>]<br />
		   <? endif; ?>
          <? endif; ?>
         </b>
        </td>
       </tr>
      </table>
     </td>
    </tr>
    <tr bgcolor="#446688">
     <td rowspan="3" align="center" valign="center" nowrap>
      <? if($shotpath): ?>
       <? $mysize=GetImageSize($shotpath);

      	$query="SELECT screenshots.added as dt,users.nickname as nick FROM screenshots,users WHERE prod=".$prod["id"]." and users.id=screenshots.user";
      	$result = mysql_query($query);
    	  $ssuser=mysql_fetch_object($result);

       if($SESSION_LEVEL=='administrator' || $SESSION_LEVEL=='moderator' || $SESSION_LEVEL=='gloperator') {
       ?>
       <a href="submitsshot.php?which=<?=$prod["id"]?>"><img src="<? print($shotpath); ?>" <? print($mysize[3]); ?> border="0" title="screenshot added on the <?=substr($ssuser->dt,0,10)?> by <?=htmlentities($ssuser->nick)?>"></a><br />
       <?
       } else {
       ?>
       <img src="<? print($shotpath); ?>" <? print($mysize[3]); ?> border="0" title="screenshot added on the <?=substr($ssuser->dt,0,10)?> by <?=htmlentities($ssuser->nick)?>"><br />
       <?
       }
       ?>
      <? else: ?>
       no screenshot yet...<br />
	   <? if($_SESSION["SCENEID_ID"]): ?>
       <a href="submitsshot.php?which=<? print($prod["id"]); ?>">upload one</a><br />
	   <? endif; ?>
      <? endif; ?>
     </td>
     <td colspan="3" valign="top">
      <table border="0">
       <tr>
        <td nowrap valign="top">platform :</td>
        <td nowrap>
        <table cellspacing="0" cellpadding="0" border="0">
        <?
          for($i=0;$i<count($platforms);$i++) {
          	?>
         <tr>
           <td>
            <a href="prodlist.php?platform[]=<? print($platforms[$i]["name"]); ?>"><img src="gfx/os/<? print($platforms[$i]["icon"]); ?>" width="16" height="16" border="0" title="<? print($platforms[$i]["name"]); ?>"></a><br />
           </td>
           <td>&nbsp;</td>
           <td nowrap>
            <a href="prodlist.php?platform[]=<? print($platforms[$i]["name"]); ?>"><? print($platforms[$i]["name"]); ?></a><br />
           </td>
          </tr>
            <?
          }
        ?>
        </table>
        </td>
       </tr>
       <tr>
        <td nowrap valign="top">type :</td>
        <td nowrap>
        <table cellspacing="0" cellpadding="0" border="0">
        <? $typess = explode(",", $prod["type"]);
          for($i=0;$i<count($typess);$i++) { ?>
         <tr>
           <td>
            <a href="prodlist.php?type[]=<? print($typess[$i]); ?>"><img src="gfx/types/<? print($types[$typess[$i]]); ?>" width="16" height="16" border="0" title="<? print($typess[$i]); ?>"></a><br />
           </td>
           <td>&nbsp;</td>
           <td>
            <a href="prodlist.php?type[]=<? print($typess[$i]); ?>"><? print($typess[$i]); ?></a><br />
           </td>
          </tr>
         <? } ?>
        </table>
        </td>
       </tr>
       <? if($prod["invitation"]!=0): ?>
       <tr>
        <td nowrap>invit for :</td>
        <td nowrap>
         <a href="party.php?which=<? print($prod["invitation"]); ?>&when=<? print(sprintf("%02d",$prod["invitationyear"])); ?>"><? print($prod["invitationpartyname"]); ?></a> <? print($piyear); ?><br />
        </td>
       </tr>
       <? endif; ?>
       <? if($prod["boardID"]!=0): ?>
       <tr>
        <td nowrap>advertising for :</td>
        <td nowrap>
         <a href="bbses.php?which=<? print($prod["boardID"]); ?>"><? print($prod["boardname"]); ?></a><br />
        </td>
       </tr>
       <? endif; ?>
       <tr>
        <td nowrap>release date :</td>
        <td nowrap colspan="2">
         <?
          if(($prod["date"]!="0000-00-00")&&(strlen($prod["date"])>0)) {
          $rdate=explode("-",$prod["date"]);
          switch($rdate[1]) {
            case "01": $rmonth="january"; break;
            case "02": $rmonth="february"; break;
            case "03": $rmonth="march"; break;
            case "04": $rmonth="april"; break;
            case "05": $rmonth="may"; break;
            case "06": $rmonth="june"; break;
            case "07": $rmonth="july"; break;
            case "08": $rmonth="august"; break;
            case "09": $rmonth="september"; break;
            case "10": $rmonth="october"; break;
            case "11": $rmonth="november"; break;
            case "12": $rmonth="december"; break;
            default: $rmonth=""; break;
          }
         ?>
          <? print($rmonth); ?> <? print($rdate[0]); ?><br />
         <? } else { ?>
           <? if($_SESSION["SCENEID_ID"]): ?>
           <font color="#9999AA">n/a</font> [<a href="submitpartyinfo.php?which=<?=$prod["id"]?>">+</a>]<br />
           <? else: ?>
          <font color="#9999AA">n/a</font><br />
          <? endif; ?>
         <? } ?>
        </td>
       </tr>
       <? if(!($prod["party"]==1024)): ?>
       <tr>
        <td nowrap>release party :</td>
        <td nowrap>
         <? if(strlen($prod["partyname"])>0): ?>
          <a href="party.php?which=<? print($prod["party"]); ?>&when=<? print(sprintf("%02d",$prod["party_year"])); ?>"><? print($prod["partyname"]); ?></a> <? print($pyear); ?><br />
         <? else: ?>
           <? if($_SESSION["SCENEID_ID"]): ?>
           <font color="#9999AA">n/a</font> [<a href="submitpartyinfo.php?which=<?=$prod["id"]?>">+</a>]<br />
           <? else: ?> <font color="#9999AA">n/a</font>
           <? endif; ?>
         <? endif; ?>
       </td>
       </tr>
       <? if($prod["partycompo"]!="none"): ?>
       <tr>
        <td nowrap>compo :</td>
        <td nowrap>
         <? if($prod["partycompo"]):
             print($prod["partycompo"]."<br />");
          else:
            if($_SESSION["SCENEID_ID"]): ?>
           <font color="#9999AA">n/a</font> [<a href="submitpartyinfo.php?which=<?=$prod["id"]?>">+</a>]<br />
	   <? else: ?> <font color="#9999AA">n/a</font>
           <? endif; ?>
         <? endif; ?>
        </td>
       </tr>
       <tr>
        <td nowrap>ranked :</td>
        <td nowrap>
         <? if($prod["party_place"]):
           switch($prod["party_place"]) {
             	case 1:
     		case 21:
     		case 31:
     		case 41:
     		case 51:
     		case 61:
     		case 71:
     		case 81:
     		case 91: $placeadj="st";
             		print($prod["party_place"]."<font color=\"#CCCCCC\">".$placeadj."</font><br />");
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
             		print($prod["party_place"]."<font color=\"#CCCCCC\">".$placeadj."</font><br />");
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
             		print($prod["party_place"]."<font color=\"#CCCCCC\">".$placeadj."</font><br />");
             		break;
		case 97: print("disqualified<br />");
			break;
             	case 98: print("n/a<br />");
             		break;
		case 99: print("not shown<br />");
			break;
             	default: $placeadj="th";
             		print($prod["party_place"]."<font color=\"#CCCCCC\">".$placeadj."</font><br />");
             		break;
           }
         ?>
         <? else: ?>
           <? if($_SESSION["SCENEID_ID"]): ?>
           <font color="#9999AA">n/a</font> [<a href="submitpartyinfo.php?which=<?=$prod["id"]?>">+</a>]<br />
	   <? else: ?> <font color="#9999AA">n/a</font>
           <? endif; ?>
         <? endif; ?>
        </td>
       </tr>
       <? endif; ?>
       <? endif; ?>



       <? //otherparty
        for($i=0;$i<count($prodotherparties);$i++):

       	if(isset($prodotherparties[$i]["party_year"])) {
            $pyear = $prodotherparties[$i]["party_year"];
        }
      ?>
       <tr>
        <td nowrap>release party :</td>
        <td nowrap>
         <? if(strlen($prodotherparties[$i]["name"])>0): ?>
          <a href="party.php?which=<? print($prodotherparties[$i]["party"]); ?>&when=<? print(sprintf("%02d",$prodotherparties[$i]["party_year"])); ?>"><? print($prodotherparties[$i]["name"]); ?></a> <? print($pyear); ?><br />
         <? else: ?>
           <font color="#9999AA">n/a</font>
         <? endif; ?>
       </td>
       </tr>
       <? if($prodotherparties[$i]["partycompo"]!="none"): ?>
       <tr>
        <td nowrap>compo :</td>
        <td nowrap>
         <? if($prodotherparties[$i]["partycompo"]):
             print($prodotherparties[$i]["partycompo"]."<br />");
          else: ?>
          <? if($_SESSION["SCENEID_ID"]): ?>
           <font color="#9999AA">n/a</font> [<a href="submitotherpartyinfo.php?which=<?=$prod["id"]?>&what=<?=$prodotherparties[$i]["party"]?>">+</a>]<br />
	   <? else: ?> <font color="#9999AA">n/a</font>
           <? endif; ?>
         <? endif; ?>
        </td>
       </tr>
       <tr>
        <td nowrap>ranked :</td>
        <td nowrap>
         <? if($prodotherparties[$i]["party_place"]):
           switch($prodotherparties[$i]["party_place"]) {
             	case 1:
     		case 21:
     		case 31:
     		case 41:
     		case 51:
     		case 61:
     		case 71:
     		case 81:
     		case 91: $placeadj="st";
             		print($prodotherparties[$i]["party_place"]."<font color=\"#CCCCCC\">".$placeadj."</font><br />");
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
             		print($prodotherparties[$i]["party_place"]."<font color=\"#CCCCCC\">".$placeadj."</font><br />");
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
             		print($prodotherparties[$i]["party_place"]."<font color=\"#CCCCCC\">".$placeadj."</font><br />");
             		break;
		case 97: print("disqualified<br />");
			break;
             	case 98: print("n/a<br />");
             		break;
		case 99: print("not shown<br />");
			break;
             	default: $placeadj="th";
             		print($prodotherparties[$i]["party_place"]."<font color=\"#CCCCCC\">".$placeadj."</font><br />");
             		break;
           }
           else:
           if($_SESSION["SCENEID_ID"]): ?>
           <font color="#9999AA">n/a</font> [<a href="submitotherpartyinfo.php?which=<?=$prod["id"]?>&what=<?=$prodotherparties[$i]["party"]?>">+</a>]<br />
	   <? else: ?> <font color="#9999AA">n/a</font>
           <? endif; ?>
         <? endif; ?>
        </td>
       </tr>
       <? endif; ?>

       <? endfor; ?>



       <? if(count($affils)): ?>
       <tr>
        <td nowrap valign="top">related :</td>
        <td nowrap><? for($i=0;$i<count($affils);$i++): ?>
            <a href="prod.php?which=<? print($affils[$i][$affils[$i]["prod"]]); ?>"><?
            if ($affils[$i]["prod"]=="derivative")
              print($affilorig[$affils[$i]["type"]]);
            else
              print($affilinverse[$affils[$i]["type"]]);
            echo ": ".$affils[$i]["name"];
            ?></a><br /><? endfor; ?>
        </td>
       </tr>
       <? endif; ?>
      </table>
     </td>
    </tr>
    <? $pourcent=floor($prod["views"]*100/$max_views); ?>
    <tr>
     <td bgcolor="#557799">
	     <table border="0">
	     <tr>
	         <td><img src="gfx/rulez.gif" width="16" height="16" border="0" title="rulez">
	         </td>
	         <td>
	         <? if(!$prod["voteup"]) {print("0");} else {print($prod["voteup"]);} ?>
	         </td>
	     </tr>
	     <tr>
	         <td><img src="gfx/isok.gif" width="16" height="16" border="0" title="piggie">
	      	 </td>
	         <td>
	         <? if(!$prod["votepig"]) {print("0");} else {print($prod["votepig"]);} ?>
	         </td>
			 </tr>
	     <tr>
	         <td><img src="gfx/sucks.gif" width="16" height="16" border="0" title="sucks">
	      	 </td>
	         <td>
	         <? if(!$prod["votedown"]) {print("0");} else {print($prod["votedown"]);} ?>
	         </td>
			 </tr>
	     </table>
     </td>
     <td bgcolor="#446688" align="center" valign="center">
      <table border="0" width="100">
       <tr>
        <td>
         popularity :<br />
        </td>
        <td align="right" nowrap>
         <? print($pourcent); ?> %<br />
        </td>
       </tr>
       <tr>
        <td colspan="3">
         <? DoBar($pourcent,true); ?>
        </td>
       </tr>
       <?
       	if(count($sceneorgrecommends)):
        	print("<tr><td nowrap align=\"left\">");
		for($k=0;$k<count($sceneorgrecommends);$k++) {
		printf("<a href='./sceneorg.php#%s'><img src=\"gfx/sceneorg/%s.gif\" title=\"%s\" alt=\"%s\" border=\"0\"></a>",
		  $sceneorgrecommends[$k]["type"] == "viewingtip" ? substr($prod["date"],0,4) : substr($prod["date"],0,4).str_replace(" ","",$sceneorgrecommends[$k]["category"]),
		  $sceneorgrecommends[$k]["type"],
		  $sceneorgrecommends[$k]["category"],
		  $sceneorgrecommends[$k]["category"]);
		}
		print("<br /></td></tr>");
	endif;
	?>
      </table>
     </td>
    </tr>
    <tr>
         <td bgcolor="#557799">

<?     if($prod["voteavg"]>0)
		$thumbgfx="gfx/rulez.gif";
	elseif($prod["voteavg"]==0)
		$thumbgfx="gfx/isok.gif";
	else
		$thumbgfx="gfx/sucks.gif";

       if($prod["cdc"]>0) $cdcprint="<td>&nbsp;</td><td><img src=\"gfx/titles/coupdecoeur.gif\" width=\"16\" height=\"16\" border=\"0\" alt=\"cdc\"></td><td>&nbsp;</td><td>".$prod["cdc"]."</td>";
	else $cdcprint="";

	printf(
	  "<table cellspacing=\"0\" cellpadding=\"0\">".
	  "<tr>".
	  "<td><img src=\"".$thumbgfx."\" width=\"16\" height=\"16\" border=\"0\" alt=\"average rating\" align=\"left\"></td>".
	  "<td>&nbsp;</td>".
	  "<td>%.2f</td>".$cdcprint.
	  "</tr>".
	  "</table>\n", $prod["voteavg"]);

  echo "<div style='margin:10px auto; font-size:80%; text-align:center;'>";
  echo "alltime top: ";
  if ($prod["rank"])
    printf("#%d",$prod["rank"]);
  else
    echo "n/a";
  echo "</div>";
?>
     </td>

     <td bgcolor="#446688" align="right" valign="bottom" colspan="2" nowrap>

	<? if($prod["csdb"]): ?>
	[<a href="http://noname.c64.org/csdb/release/?id=<? print($prod["csdb"]); ?>">csdb</a>]<br />
          <? else: ?>
           <? if($check64): ?>
		<? if($_SESSION["SCENEID_ID"]): ?>
	           [<small>
	            <a href="submitprodcsdb.php?which=<? print($prod["id"]); ?>">+csdb</a>
	           </small>]<br />
		<? endif; ?>
          <? endif; ?>
         <? endif; ?>
         <? if($prod["sceneorg"]): ?>
	[<a href="http://scene.org/file.php?id=<? print($prod["sceneorg"]); ?>">sceneorg</a>]<br />
          <? else: ?><? /*if($_SESSION["SCENEID_ID"]): ?>
	           [<small>
	            <a href="submitprodsceneorg.php?which=<? print($prod["id"]); ?>">+sceneorg</a>
	           </small>]
		<? endif; */?>
	  <? endif; ?>
	<? if($prod["zxdemo"]): ?>
	[<a href="http://zxdemo.org/item.php?id=<? print($prod["zxdemo"]); ?>">zxdemo</a>]<br />
          <? else: ?>
           <? if($checkzx): ?>
		<? if($_SESSION["SCENEID_ID"]): ?>
	           [<small>
	           <a href="submitprodzxdemo.php?which=<? print($prod["id"]); ?>">+zxdemo</a>
	           </small>]<br />
		<? endif; ?>
          <? endif; ?>
         <? endif; ?>
         <span id="mainDownload">[<a id="mainDownloadLink" href="<?=$prod["download"]?>">download</a>]</span><br />
	 <? for($i=0;$i<count($dl);$i++): ?>
	  [<a href="<? print($dl[$i]["link"]); ?>"><? print($dl[$i]["type"]); ?></a>]<br />
	 <? endfor; ?>
	 [<a href="download.php?which=<?=$prod["id"]?>">mirrors...</a>]<br />
     </td>
    </tr>
    <tr>
     <td bgcolor="#6688AA" colspan=4" align="right">
      <table cellspacing="0" cellpadding="0">
       <tr>
        <td>added on the <? print(substr($prod["quand"],0,10)); ?> by <a href="user.php?who=<? print($prod["added"]); ?>"><? print($prod["nickname"]); ?></a></td>
        <td>&nbsp;<br /></td>
        <td><a href="user.php?who=<? print($prod["added"]); ?>"><img src="avatars/<? print($prod["avatar"]); ?>" width="16" height="16" border="0"></a></td>
       </tr>
      </table>
     </td>
    </tr>
   </table>
  </td>
 </tr>
</table>
<br />

<table bgcolor="#000000" cellspacing="1" cellpadding="0" border="0" width="100%">
 <tr>
  <td>
   <table bgcolor="#000000" cellspacing="1" cellpadding="2" border="0" width="100%">
    <tr>
     <td bgcolor="#224488">
      <b>popularity helper</b><br />
     </td>
    </tr>
    <tr>
	<td bgcolor="#446688" align="center">
      	 increase the popularity of this prod by spreading this URL:<br />
	<input type="text" value="http://www.pouet.net/prod.php?which=<? print($prod["id"]); ?>" size="50" readonly>
     </td>
    </tr>
   </table>
  </td>
 </tr>
</table>
<br />

<? if(count($comments)>0): ?>
<table id="prodcommenttable" bgcolor="#000000" cellspacing="1" cellpadding="0" border="0" width="100%">
 <tr>
  <td>
   <table bgcolor="#000000" cellspacing="1" cellpadding="2" border="0" width="100%">
    <tr>
     <td bgcolor="#224488">
      <b>comments</b><br />
     </td>
    </tr>
    <? for($i=0;$i<count($comments);$i++): ?>
    <tr class="cite-<?=$comments[$i]["who"]?>">
	<td bgcolor="#446688" id="c<?=$comments[$i]["id"]?>">
      <?
      if ( ( ($comments[$i]["level"]=='fakeuser') && $user["prodhidefakeuser"] ) || ( ($comments[$i]["level"]=='pr0nstahr') && $user["prodhidepornstar"] ) ||  ( ($comments[$i]["level"]=='annoyinguser') && $user["prodhideannoyinguser"] ) )
       { print("i'm a ".$comments[$i]["level"]." so i got shamelessly censored, if you're registered you can uncensor my level's posts <a href=\"customize.php\">here</a>"); }
       else { print(parse_message($comments[$i]["comment"])); }
       ?>
     </td>
    </tr>
    <tr class="cite-<?=$comments[$i]["who"]?>">
	   <td bgcolor="#6688AA" colspan="2" align="right">
      <table cellspacing="0" cellpadding="0" width="100%">
       <tr>
        <td width="100%">
        <? if($comments[$i]["rating"]==1): ?>
         <img src="gfx/rulez.gif" width="16" height="16" border="0" title="rulez">
        <? elseif($comments[$i]["rating"]==-1): ?>
         <img src="gfx/sucks.gif" width="16" height="16" border="0" title="sucks">
        <? endif; ?>
        <? if($comments[$i]["cdc"]==$prod["id"] && !$cdcdone[$comments[$i]["who"]]): ?>
         <img src="gfx/titles/coupdecoeur.gif" width="16" height="16" border="0" title="cdc">
        <? $cdcdone[$comments[$i]["who"]]=1; endif; ?>
        &nbsp;</td>
        <td nowrap>added on the <a href="prod.php?which=<?=$prod["id"]?>#c<?=$comments[$i]["id"]?>"><? print($comments[$i]["quand"]); ?></a> by <a href="user.php?who=<? print($comments[$i]["who"]); ?>"><? print($comments[$i]["nickname"]); ?></a></td>
        <td nowrap>&nbsp;</td>
        <td nowrap><a href="user.php?who=<? print($comments[$i]["who"]); ?>"><img src="avatars/<? print($comments[$i]["avatar"]); ?>" width="16" height="16" border="0" title="<? print($comments[$i]["nickname"]); ?>"></a></td>
       </tr>
      </table>
     </td>
    </tr>
    <? endfor; ?>
	<? $page_link="prod.php?which=$which&amp;howmanycomments=$howmanycomments&amp;page="; ?>
    <tr bgcolor="#224488">
     <td>
      <table width="100%" cellspacing="0" cellpadding="0" border="0">
       <tr>
	<? if(($page>=1)&&($comments_per_page>0)): ?>
        <td>
         <a href="<?=$page_link.($page-1)?>">
          <img src="gfx/flecheg.gif" border="0"><br />
         </a>
        </td>
        <td>&nbsp;</td>
        <td nowrap>
         <a href="<?=$page_link.($page-1)?>">
          <b>previous page</b><br />
         </a>
        </td>
	<? endif; ?>
	   <td width="100%" align="center">
	   <form name="howmanycomments" action="prod.php">
	    <table><tr>
		 <td nowrap>
			displaying
			<input type="hidden" name="which" value="<? print($which); ?>">
			<select name="howmanycomments" onChange="document.howmanycomments.submit();">
			<option value="0" <?=($howmanycomments==0)?'selected':''?>>none</option>
			<option value="25" <?=($howmanycomments==25)?'selected':''?>>25</option>
			<option value="50" <?=($howmanycomments==50)?'selected':''?>>50</option>
			<option value="100" <?=($howmanycomments==100)?'selected':''?>>100</option>
			<option value="-1" <?=($howmanycomments==-1)?'selected':''?>>all</option>
			</select>
			comments
			<? if($comments_per_page>0): ?>
			(<b><?=($page*$comments_per_page+1)?></b>-<b><?=min(($page+1)*$comments_per_page, $nb_comments)?></b>)
			<? endif; ?>
			out of <b><?=$nb_comments?></b>
			<? if($page>0): ?>(<a href="<?=$page_link?>0">go to first</a>)
			<?endif; ?>
		 </td>
		</tr></table>
		</form>
	   </td>
       <? if(((($page+1)*$comments_per_page)<$nb_comments)&&($comments_per_page>0)): ?>
        <td nowrap>
         <a href="<?=$page_link.($page+1)?>">
          <b>next page</b><br />
         </a>
        </td>
        <td>&nbsp;</td>
        <td>
         <a href="<?=$page_link.($page+1)?>">
          <img src="gfx/fleched.gif" border="0"><br />
         </a>
        </td>
       <? endif; ?>
       </tr>
	  </table>
	 </td>
	</tr>
   </table>
  </td>
 </tr>
</table>
<?
if (count($othercdc)) {
?>
<br/>
<table bgcolor="#000000" cellspacing="1" cellpadding="2" border="0" width="100%">
 <tr>
  <th>sneaky cdc's</th>
 </tr>
<?
foreach($othercdc as $v) {
  $query="SELECT nickname,avatar,id FROM users WHERE id=".$v;
  $result=mysql_query($query);
  $o = mysql_fetch_object($result);
  printf("<tr><td class='bg1'><a href='user.php?who=%d'><img src='avatars/%s' border='0'></a> <a href='user.php?who=%d'>%s</a></td></tr>",
    $o->id, $o->avatar, $o->id, $o->nickname);
}
?>
</table>
<?
}
?>
<? endif; ?>

<? if($howmanycomments == 0): ?>
<table bgcolor="#000000" cellspacing="1" cellpadding="0" border="0" width="100%">
 <tr>
  <td>
   <table bgcolor="#000000" cellspacing="1" cellpadding="2" border="0" width="100%">
    <tr>
	   <td bgcolor="#224488">
      <b>comments</b><br />
     </td>
    </tr>
    <tr>
     <td bgcolor="#446688" align="center">
		Comments are actually hidden.<br />
		<b><a href="prod.php?which=<?=$which?>&amp;howmanycomments=25">Click here to show the comments again.</a></b><br />
     </td>
    </tr>
   </table>
  </td>
 </tr>
</table>
<? endif; ?>

<br />

<table bgcolor="#000000" cellspacing="1" cellpadding="0" border="0" width="100%">
 <tr>
  <td>
   <table bgcolor="#000000" cellspacing="1" cellpadding="2" border="0" width="100%">
    <tr>
	   <td bgcolor="#224488">
      <b>submit changes</b><br />
     </td>
    </tr>
    <tr>
     <td bgcolor="#446688" align="center">
      if this prod is a fake, some info is false or the download link is broken,<br />
	  do not post about it in the comments, it will get lost.<br />
	  instead, <a href="topic.php?which=1024"><b>post</b></a> about it.<br />
     </td>
    </tr>
   </table>
  </td>
 </tr>
</table>
<br />

<? if($_SESSION["SCENEID_ID"]): ?>
<form action="add.php" method="post">
<input type="hidden" name="which" value="<? print($prod["id"]); ?>">
<input type="hidden" name="type" value="comment">
<table bgcolor="#000000" cellspacing="1" cellpadding="0" border="0" width="100%">
 <tr>
  <td>
   <table bgcolor="#000000" cellspacing="1" cellpadding="2" border="0" width="100%">
    <tr>
	   <td bgcolor="#224488">
      <b>add a comment</b><br />
     </td>
    </tr>
    <tr>
     <td bgcolor="#446688" align="center">
		<?
		// check if the logged user has already rated (not commented) this prod
		$query = 'SELECT count(0) FROM comments WHERE which='.$prod["id"].' AND who='.$_SESSION["SCENEID_ID"].' AND rating!=0';
		$result = mysql_query($query);
		if(!mysql_result($result,0)) :
		?>
      <table border="0">
       <tr>
		<td>this prod</td>
        <td align="right"><input type="radio" name="rating" value="rulez"></td>
        <td><img src="gfx/rulez.gif" width="16" height="16" border="0" title="rulez"></td>
		<td>rulez</td>
		<td align="right"><input type="radio" name="rating" value="isok" checked></td>
		<td><img src="gfx/isok.gif" width="16" height="16" border="0" title="is ok"></td>
		<td>is ok</td>
		<td align="right"><input type="radio" name="rating" value="sucks"></td>
		<td><img src="gfx/sucks.gif" width="16" height="16" border="0" title="sucks"></td>
		<td>sucks</td>
	   </tr>
	  </table>
	  <? else: ?>
	  <input type="hidden" name="rating" value="isok">
	  <? endif; ?>
<script language="JavaScript" type="text/javascript">
<!--
function checkWarning(cmt)
{
  document.getElementById("commentwarning").innerHTML = "";
  if (cmt.search(/youtube/i)!=-1 || cmt.search(/youtu\.be/i)!=-1)
  {
    document.getElementById("commentwarning").innerHTML="if you want to add a youtube link, go <a href='http://www.pouet.net/topic.php?which=1024'>here</a> instead!";
  }
  if (cmt.search(/soundcloud/i)!=-1 || cmt.search(/\.mp3/i)!=-1 || cmt.search(/\.ogg/i)!=-1)
  {
    document.getElementById("commentwarning").innerHTML="if you want to add a soundtrack link, go <a href='http://www.pouet.net/topic.php?which=1024'>here</a> instead!";
  }
}
//-->
</script>
      <textarea name="comment" cols="50" rows="5" onkeyup="checkWarning(this.value)"></textarea><br />
      <a href="faq.php#BB Code"><b>BB Code</b></a> is allowed here<br />
     </td>
    </tr>
    <tr>
     <td bgcolor="#6688AA" colspan="2" align="right">
<script language="JavaScript" type="text/javascript">
<!--
  document.write('<div id="commentwarning" style="float:left;padding-top:2px;padding-left:2px"></div>');
  document.write('<input type="image" src="gfx/preview.gif" onclick=\'return preview(this.form,"prod")\' border="0">');
//-->
</script>
      <input type="image" src="gfx/submit.gif" border="0"><br />
     </td>
    </tr>
   </table>
  </td>
 </tr>
</table>
</form>
<? else: ?>
<form action="login.php" method="post">
<table cellspacing="1" cellpadding="2" class="box">
 <tr><th>add a comment</th></tr>
 <tr bgcolor="#446688">
  <td nowrap align="center">
   You need to be logged in to add a comment :: <a href="account.php">register here</a><br />
   <input type="text" name="login" value="SceneID" size="15" maxlength="16" onfocus="this.value=''">
   <input type="password" name="password" value="password" size="15" onfocus="javascript:if(this.value=='password') this.value='';"><br />
  </td>
 </tr>
 <tr>
  <td bgcolor="#6688AA" align="right">
   <input type="image" src="gfx/submit.gif">
  </td>
 </tr>
</table>
</form>
<br />
<? endif; ?>
<br />
<?
//if ($SESSION_LEVEL=='administrator'):

$yearcheck = substr($prod["date"], 0, -6);

if ($sceneorgyear && $yearcheck==$sceneorgyear) {
//if (0) {

$result=mysql_query("SELECT * from awardscand_".$sceneorgyear." where user='".$_SESSION["SCENEID_ID"]."'");
$awardscand=mysql_fetch_assoc($result);
/*
if ( $prod["id"]==$awardscand["cat1"]
  || $prod["id"]==$awardscand["cat2"]
  || $prod["id"]==$awardscand["cat3"]
  || $prod["id"]==$awardscand["cat4"]
  || $prod["id"]==$awardscand["cat5"]
  || $prod["id"]==$awardscand["cat6"]
  || $prod["id"]==$awardscand["cat7"]
  || $prod["id"]==$awardscand["cat8"]
  || $prod["id"]==$awardscand["cat9"]
  || $prod["id"]==$awardscand["cat10"]
  || $prod["id"]==$awardscand["cat11"]) $nominate=true;
*/
?>
<form action="awardscandidates.php" method="post" name="awardscandidates">
<input type="hidden" name="prod" value="<?=$prod["id"]?>">
<input type="hidden" name="action" value="alter">
<table bgcolor="#000000" cellspacing="1" cellpadding="0" border="0" width="100%">
 <tr>
  <td>
   <table bgcolor="#000000" cellspacing="1" cellpadding="2" border="0" width="100%">
    <tr>
     <td bgcolor="#224488">
      <b>scene.org awards candidate</b><br />
     </td>
    </tr>
    <tr>
	<td bgcolor="#446688" align="center">
	 suggest this prod to the jury for a <a href="http://awards.scene.org">scene.org awards</a> category!
	 check your full suggestions <a href="awardscandidates.php">here</a><br />
	 <!--div style='background:#800;color:white;margin:10px;padding:10px'>suggestions will close on wednesday, january the 5th! vote now!</div-->
	<select name="cat[]" multiple="multiple" size="<?=count($awardscat[$yearcheck])?>">
	  <?
    foreach($awardscat[$yearcheck] as $x=>$name) {
    	if ($prod["id"]==$awardscand["cat".$x]) $is_selected=" selected";
    	 else $is_selected="";
    	print("<option value='".$x."' ".$is_selected.">".$name."</option>\n");
    }
	  ?>
     </td>
    </tr>
    <tr>
	  <td bgcolor="#6688AA" align="right" colspan="3">
	   <input type="image" src="gfx/submit.gif">
	  </td>
	 </tr>
   </table>
  </td>
 </tr>
</table>
</form>
<?
}
?>
</td></tr></table>


<? else: ?>
	<center><? print("the wrongly submitted prod that you are looking for has been lobsterxiced from our database!".
	                 "<br /> probably with a very good reason too! <br /> <br />*clack clack* (\/) o o (\/) *clack clack*"); ?></center>
<? endif; ?>
<br />
<? require("include/bottom.php"); ?>
