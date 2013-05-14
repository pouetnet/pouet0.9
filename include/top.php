<?php
require_once($_SERVER['DOCUMENT_ROOT'].'/include/constants.php');

// lets send things zipped if we can..
function pouet_ob(&$bass)
{
	if(
		isset($_SERVER['HTTP_ACCEPT_ENCODING'])
		&& strstr($_SERVER['HTTP_ACCEPT_ENCODING'], 'gzip')
		&& extension_loaded('zlib')
	) {
		header("Content-Encoding: gzip");
		return ob_gzhandler($bass, 9);
	} else {
		return $bass;
	}
}
ob_start('pouet_ob');

error_reporting(0);
// ini_set('session.use_trans_sid',0);

// cookies stuff not related to account stuff
//
// keep the number of comments displayed for a prod in a cookie for a year
session_start();

$SESSION_LEVEL = $_SESSION["SESSION_LEVEL"];
if (($SESSION_LEVEL=='administrator' || $SESSION_LEVEL=='moderator' || $SESSION_LEVEL=='gloperator' || $_GET["debugmode"]))
  error_reporting(E_ALL & ~E_NOTICE);

//die("test");

if (isset($_GET['howmanycomments']))
{
  $z = time()+60*60*24*365;
//  printf("<!--%d (%08X) -->",time(),time());
//  printf("<!--%d (%08X) -->",$z,$z);
	setcookie('howmanycomments', $_GET['howmanycomments'], $z);
	$howmanycomments = $_GET['howmanycomments'];
} else {
	$howmanycomments = $_COOKIE['howmanycomments'];
}

include_once('misc.php');
include_once('auth.php');

#
# Tue Apr 22 17:15:12 CEST 2003 - jeffry:
#   Changed the pconnects to normal connects, because all the idle mysql
#   connections were killing the server in busy hours.. (every mysql process
#   takes about 50MB of resident memory :)
#
#$dbl = mysql_pconnect($db['host'], $db['user'], $db['password']);
conn_db();

$xml = new SceneID();

if($_SESSION["SCENEID"])
{
	if($_SESSION["SCENEID_IP"]!=$_SERVER["REMOTE_ADDR"])
	{
		//header("Location: http://$_SERVER[HTTP_HOST]/logout.php");
	}
}

//die("test");
refreshUserInfo();

$starttime = microtime_float();

$charset = "ISO-8859-1";
if ($_GET["charset"]) $charset = htmlentities($_GET["charset"]);

?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
  <meta http-equiv="Content-Type" content="text/html; charset=<?=$charset?>">
  <link rel="shortcut icon" href="/favicon.ico" type="image/x-icon">
  <link rel="canonical" href="http://www.pouet.net<?=$_SERVER["REQUEST_URI"]?>"/>
  <link rel="alternate" href="/piclens/piclens.rss.php" type="application/rss+xml" title="piclens thing" id="gallery" />
  <link rel="alternate" href="/export/lastprodsadded.rss.php" type="application/rss+xml" title="pouet - last prods added" />
  <link rel="alternate" href="/export/lastprodsreleased.rss.php" type="application/rss+xml" title="pouet - last prods released" />
  <link rel="alternate" href="/export/lastbbsposts.rss.php" type="application/rss+xml" title="pouet - last bbs posts" />
  <link rel="alternate" href="/export/last-prod-comments.rss.php" type="application/rss+xml" title="pouet - last comments" />
  <?

 $customquery="SELECT ";

 $settingfields="";

if (isset($which))
{
	$which = intval($which);
}
switch (basename($_SERVER['SCRIPT_FILENAME'])) {
	case 'index.php' :
		$customquery.="level from users where id='";
    $settingfields="logos, topbar, bottombar, indextopglops, indextopprods, indextopkeops, indexoneliner, indexlatestadded, indexlatestreleased, indexojnews, indexlatestcomments, indexbbstopics, indexcdc, indexsearch, indexlinks, indexstats, indextype, indexplatform, indexwhoaddedprods, indexwhocommentedprods, indexlatestparties, indexbbsnoresidue";
		print("<title>pouet.net :: your online demoscene resource</title>\n");
		break;
	case 'prod.php' :
		$customquery.="level from users where id='";
    $settingfields="logos, topbar, bottombar, prodhidefakeuser, displayimages";
		if ($which)
		{
		$query="SELECT prods.name as prod_name, groups.name as group_name, groups2.name as group_name2, groups3.name as group_name3 FROM prods LEFT JOIN groups ON groups.id=prods.group1 LEFT JOIN groups as groups2 ON groups2.id=prods.group2 LEFT JOIN groups as groups3 ON groups3.id=prods.group3 WHERE prods.id=$which";
		$result=mysql_query($query);
		$row = mysql_fetch_assoc($result);
		if($row['group_name'])
		{
			if($row['group_name3'])
			{
			print('<title>'.stripslashes($row['prod_name']).' by '.$row['group_name'].' &amp; '.$row['group_name2'].' &amp; '.$row['group_name3'].'</title>'."\n");
			}
			else if($row['group_name2'])
			{
			print('<title>'.stripslashes($row['prod_name']).' by '.$row['group_name'].' &amp; '.$row['group_name2'].'</title>'."\n");
			}
			else
			{
			print('<title>'.stripslashes($row['prod_name']).' by '.$row['group_name'].'</title>'."\n");
			}
		}
		else {
			print('<title>'.stripslashes($row['prod_name']).'</title>'."\n");
		}
		}
		else { print("<title>random prod</title>\n"); }
		break;
	case 'topic.php' :
		$customquery.="level from users where id='";
		$settingfields="logos, topbar, bottombar, topicposts, topichidefakeuser, displayimages";
		$query="SELECT topic FROM bbs_topics WHERE id=".$which;
		$result=mysql_query($query);
		$topictitle=mysql_result($result,0);
		print("<title>".$topictitle."</title>\n");
		break;
	case 'groups.php' :
		$customquery.="level from users where id='";
		$settingfields="logos, topbar, bottombar";
		if ($which)
		{
			$query="SELECT name FROM groups WHERE id=".$which;
			$result=mysql_query($query);
			$groupname=mysql_result($result,0);
		}
		print("<title>".($which?$groupname:"groups")."</title>\n");
		break;
	case 'party.php' :
		$customquery.="level from users where id='";
    $settingfields="logos, topbar, bottombar";
		$query="SELECT name FROM parties WHERE id=".$which;
		$result=mysql_query($query);
		$partyname=mysql_result($result,0);
		print("<title>".$partyname." ".$when."</title>\n");
		break;
	case 'results.php' :
		$customquery.="level from users where id='";
    $settingfields="logos, topbar, bottombar";
		$query="SELECT name FROM parties WHERE id=".$which;
		$result=mysql_query($query);
		$partyname=mysql_result($result,0);
		print("<title>results ".$partyname." ".$when."</title>\n");
		break;
	case 'comments.php' :
		$customquery.="level from users where id='";
    $settingfields="logos, topbar, bottombar, commentshours, commentsnamecut ";
		print("<title>comments</title>\n");
		break;
	case 'bbs.php' :
		$customquery.="level from users where id='";
    $settingfields="logos, topbar, bottombar, bbsbbstopics";
		print("<title>BBS</title>\n");
		break;
	case 'cdc.php' :
		$customquery.="level from users where id='";
    $settingfields="logos, topbar, bottombar";
		print("<title>coup de coeur</title>\n");
		break;
	case 'oneliner.php' :
		$customquery.="level from users where id='";
    $settingfields="logos, topbar, bottombar";
		print("<title>oneliner</title>\n");
		break;
	case 'prodlist.php' :
		$customquery.="level from users where id='";
    $settingfields="logos, topbar, bottombar, prodlistprods";
		print("<title>prodlist</title>\n");
		break;
	case 'search.php' :
		$customquery.="level from users where id='";
    $settingfields="logos, topbar, bottombar, searchprods";
		print("<title>search</title>\n");
		break;
	case 'top10.php' :
		$customquery.="level from users where id='";
    $settingfields="logos, topbar, bottombar";
		print("<title>top of the pops</title>\n");
		break;
	case 'faq.php' :
		$customquery.="level from users where id='";
    $settingfields="logos, topbar, bottombar";
		print("<title>faq</title>\n");
		break;
	case 'user_light.php' :
	case 'user.php' :
		$customquery.="level from users where id='";
        $settingfields="logos, topbar, bottombar, userlogos, userprods, usergroups, userparties, userscreenshots, usernfos, usercomments, userrulez, usersucks ";
		$query="SELECT level,nickname FROM users WHERE id=".$who;
		$result=mysql_query($query);
        if ($result)
        {
            $o=mysql_fetch_object($result);
            print("<title>".$o->nickname."</title>\n");
        }
		break;
	case 'userlist.php' :
		$customquery.="level from users where id='";
    $settingfields="logos, topbar, bottombar, userlistusers";
		print("<title>userlist</title>\n");
		break;
	case 'news.php' :
		$customquery.="level from users where id='";
    $settingfields="logos, topbar, bottombar ";
		print("<title>news</title>\n");
		break;
	case 'account.php' :
		$customquery.="level from users where id='";
		$settingfields="logos, topbar, bottombar";
		print("<title>account</title>\n");
		break;
	case 'customize.php' :
		$customquery.="level from users where id='";
		$settingfields="logos, topbar, bottombar";
		print("<title>custom?olobstormaziabletic 7004+ super</title>\n");
		break;
	case 'bbses.php' :
		$customquery.="level from users where id='";
		$settingfields="logos, topbar, bottombar";
		if ($which)
		{
  		$query="SELECT name FROM bbses WHERE id=".$which;
  		$result=mysql_query($query);
  		$bbsname=mysql_result($result,0);
  		print("<title>".$bbsname."</title>\n");
		}
		break;
	case 'lists.php' :
		$customquery.="level from users where id='";
		$settingfields="logos, topbar, bottombar";
		$query="SELECT name FROM lists WHERE id=".$which;
		$result=mysql_query($query);
        if ($result)
        {
            $listname = mysql_result($result, 0);
        }
		print("<title>".($listname?$listname:"pouet.net :: your online demoscene resource")."</title>\n");
		break;
	//case 'submitprod_js.php' :
	//<SCRIPT LANGUAGE="JavaScript" SRC="groups.js"></SCRIPT>
	//	break;
	default:
		$customquery.="level from users where id='";
		$settingfields="logos, topbar, bottombar";
		print("<title>pouet.net :: your online demoscene resource</title>\n");
}

$customid = $_SESSION["SCENEID_ID"];
if (!$customid)
  $customid = 11057;

$customquery.=$customid."'";
$result=mysql_query($customquery);
$user=mysql_fetch_assoc($result);

if($_SESSION["SESSION"]&&$_SESSION["SCENEID"]&&$_SESSION["SCENEID_ID"])
{
  $host = gethostbyaddr($_SERVER["REMOTE_ADDR"]);
  if ($host===".")
    $_SESSION = null;
  else
    mysql_query(sprintf('update users set lastip="%s", lasthost="%s", lastlogin="%s" where id=%d',
      $_SERVER["REMOTE_ADDR"],$host,date("Y-m-d H:i:s"),$_SESSION["SCENEID_ID"]));
}

if ($user['level']=='banned') $_SESSION = null;

if (isIPBanned()) $_SESSION = null;

$result=mysql_query(sprintf('select %s from usersettings where id=%d',$settingfields,$customid));
$usersettings=mysql_fetch_assoc($result);
if (!$usersettings) {
  $result=mysql_query(sprintf('select %s from usersettings where id=11057',$settingfields,$customid));
  $usersettings=mysql_fetch_assoc($result);
}
$user=array_merge($user,$usersettings);

//echo "-".$user["searchprods"];

 ?>
 <link rel="stylesheet" href="/include/style.css?cb=0" type="text/css">
<?
if(strstr($_SERVER['HTTP_USER_AGENT'], 'Firefox')) {
	echo ' <!-- conditional css for ff hax! -->'."\n";
	echo ' <link rel="stylesheet" href="/include/style-ff.css" type="text/css">'."\n";
}
if(strstr($_SERVER['HTTP_USER_AGENT'], 'Firefox/3.')) {
	echo ' <!-- conditional css for ff3 hax! -->'."\n";
	echo ' <link rel="stylesheet" href="/include/style-ff3.css" type="text/css">'."\n";
}
if(strstr($_SERVER['HTTP_USER_AGENT'], 'MSIE')) {
	echo ' <!-- conditional css for IE hax! -->'."\n";
	echo ' <link rel="stylesheet" href="/include/style-ie.css" type="text/css">'."\n";
}
if(strstr($_SERVER['HTTP_USER_AGENT'], 'Opera')) {
	echo ' <!-- conditional css for opera hax! -->'."\n";
	echo ' <link rel="stylesheet" href="/include/style-opera.css" type="text/css">'."\n";
}
echo $ADDITIONAL_CSS;
if (isset($_GET["OSSOM"])) {
?>
<style type="text/css">
*, td, th { text-decoration: blink; font-family: "comic sans ms", monospace; }
</style>
<?
}

?>
 <link rel="search" type="application/opensearchdescription+xml" href="opensearch_prod.xml" title="pout.net - prod search" />
 <meta name="description" content="pou?t.net - your online demoscene resource">
 <meta name="keywords" content="pou?t.net,256b,1k,4k,40k,64k,cracktro,demo,dentro,diskmag,intro,invitation,lobster sex,musicdisk,Amiga AGA,Amiga ECS,Amiga PPC,Amstrad CPC,Atari ST,BeOS,Commodore 64,Falcon,MS-Dos,Linux,MacOS,Windows">
 <script language="JavaScript" type="text/javascript" src="/include/script.js"></script>
</head>
<body background="gfx/trumpet.gif" bgcolor="#3A6EA5">
<br />
<div align="center">
<?

require('icons.php');

$months[1]="january";
$months[2]="february";
$months[3]="march";
$months[4]="april";
$months[5]="may";
$months[6]="june";
$months[7]="july";
$months[8]="august";
$months[9]="september";
$months[10]="october";
$months[11]="november";
$months[12]="december";

// printable numbers extensions
$nbext[0]="th";
$nbext[1]="st";
$nbext[2]="nd";
$nbext[3]="rd";
$nbext[4]="th";
$nbext[5]="th";
$nbext[6]="th";
$nbext[7]="th";
$nbext[8]="th";
$nbext[9]="th";

mt_srand((double)microtime()*1000000);

function logGloperatorAction($action,$id) {
  $sql = sprintf("insert gloperator_log (gloperatorid,action,itemid,date) values (%d,'%s',%d,'%s')",$_SESSION["SCENEID_ID"],$action,$id,date("Y-m-d H:i:s"));
  mysql_query($sql) or die(mysql_error());
}

function menu(){ ?>
<table cellspacing="0" cellpadding="0" border="0" width="100%">
 <tr>
  <td bgcolor="#FFFFFF"><img src="gfx/z.gif" width="1" height="1" alt=""><br /></td>
 </tr>
 <tr>
  <td align="center" bgcolor="#224488">
	  <ul class="nav">
		  <li>
			  <a href="account.php"><b>A<font color="#FFFFFF">ccount</font></b></a>
		  </li>
		  <li>
			  <a href="customize.php"><b>C<font color="#FFFFFF">ustom</font></b></a>
		  </li>
		  <li>
			  <a href="prodlist.php"><b>P<font color="#FFFFFF">rods</font></b></a>
		  </li>
		  <li>
			  <a href="random.php"><b>R<font color="#FFFFFF">andom</font></b></a>
		  </li>
		  <li>
			  <a href="groups.php"><b>G<font color="#FFFFFF">roups</font></b></a>
		  </li>
		  <li>
			  <a href="parties.php"><b>P<font color="#FFFFFF">arties</font></b></a>
		  </li>
		  <li>
			  <a href="bbses.php"><b>B<font color="#FFFFFF">oards</font></b></a>
		  </li>
		  <li>
			  <a href="userlist.php"><b>U<font color="#FFFFFF">sers</font></b></a>
		  </li>
		  <li>
			  <a href="search.php"><b>S<font color="#FFFFFF">earch</font></b></a>
		  </li>
		  <li>
			  <a href="bbs.php"><b>B<font color="#FFFFFF">BS</font></b></a>
		  </li>
		  <li>
			  <a href="lists.php"><b>L<font color="#FFFFFF">ists</font></b></a>
		  </li>
		  <li>
			  <a href="faq.php"><b>F<font color="#FFFFFF">aq</font></b></a>
		  </li>
		  <li>
			  <a href="submit.php"><b>S<font color="#FFFFFF">ubmit</font></b></a>
		  </li>
		  <li>&nbsp;</li> <!-- this is just here to ensure the display of the last white dot -->
	  </ul>
  </td>
 </tr>
 <tr><td><img src="gfx/z.gif" width="1" height="1" alt=""><br /></td></tr>
 <tr>
  <td bgcolor="#FFFFFF"><img src="gfx/z.gif" width="1" height="1" alt=""></td>
 </tr>
</table>
<?
}

/*
 <tr><td align="center" bgcolor="#000000" style="padding:2px;padding-bottom:4px;">
   <span style="color:#bbb">In memory of Tuo / Mandarine ^ Popsy Team</span>
 </td></tr>
*/
if ($user["logos"]==1)
{
$query = "SELECT logos.file,logos.author1,logos.author2,logosu1.nickname as nickname1,logosu2.nickname as nickname2 FROM logos LEFT JOIN users as logosu1 on logosu1.id=logos.author1 LEFT JOIN users as logosu2 on logosu2.id=logos.author2 WHERE logos.vote_count>0 order by rand() limit 1";
$result = mysql_query($query);

//mysql_data_seek($result, rand(0, mysql_num_rows($result)));
$logo = mysql_fetch_assoc($result);

//$logo['size'] = GetImageSize('/gfx/logos/'.$logo['file']);

?>
<a href="/">
 <img src="/gfx/logos/<? print($logo["file"]); ?>" border="0" alt="<? if($logo["nickname1"]): ?>done by <? print($logo["nickname1"]); ?><? endif; ?><? if($logo["nickname2"]): ?> and <? print($logo["nickname2"]); ?><? endif; ?>"><br />
</a>
<? if($logo["nickname1"]): ?>
logo done by
<a href="user.php?who=<? print($logo["author1"]); ?>"><? print($logo["nickname1"]); ?></a>
<? if($logo["nickname2"]): ?>
and
<a href="user.php?who=<? print($logo["author2"]); ?>"><? print($logo["nickname2"]); ?></a>
<? endif; endif;
} else { print("<a href=\"/\">back to pou&euml;t's home</a> "); }
?>
::
<?
$random_quotes = Array (
  'send your logos to <a href="/submit-logo.php">us</a> and be a popstar !',
  '<a href="logos.php">vote</a> for the logos you like and be a lamah !',
  'pou&euml;t.net is brought to you by <a href="http://www.pouet.net/groups.php?which=5">mandarine</a>',
  'pou&euml;t.net is hosted on the huge <a href="http://www.scene.org/">scene.org</a> servers',
/*
  'pout != scene && scene != pout',
  'help make KOOL DEMO-SHOCK to japanese brain',
  'i am not an atomic playboy',
  'glop me beautiful'
*/
  );
?>
<?=$random_quotes[array_rand($random_quotes)]?><br />
<br />
<? if ($user["topbar"]==1) menu(); ?>
