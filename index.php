<?
require("include/top.php");
require("include/libbb.php");
require("include/awardscategories.inc.php");
require_once('lib/lastrss/lastRSS.php');

if ($user["indexoneliner"]>0)
{
	include(TMP_FOLDER.'/onelines.cache.inc');
	$onelines = array_slice($onelines, 0, $user["indexoneliner"]);
}

// create lastRSS object
$rss = new lastRSS;

// setup transparent cache
$rss->cache_dir = TMP_FOLDER;
$rss->cache_time = 5*60; // in seconds
$rss->CDATA = 'strip';
$rss->date_format = 'Y-m-d';

// load some RSS file
$rs = @$rss->GetCached('http://bitfellas.org/e107_plugins/rss_menu/rss.php?1.2');

// latest added prods
if ($user["indexlatestadded"]>0)
{
	include(TMP_FOLDER.'/latest_demos.cache.inc');
	$latest_demos = array_slice($latest_demos, 0, $user["indexlatestadded"]);
}

// latest released prods
if ($user["indexlatestreleased"]>0)
{
	include(TMP_FOLDER.'/latest_released_prods.cache.inc');
	$latest_released_prods = array_slice($latest_released_prods, 0, $user["indexlatestreleased"]);
}

// debut calcul top demos
if ($user["indextopprods"]>0)
{
	include(TMP_FOLDER.'/top_demos.cache.inc');
	$top_demos = array_slice($top_demos, 0, $user["indextopprods"]);
}

if ($user["indextopkeops"]>0)
{
	include(TMP_FOLDER.'/top_keops.cache.inc');
	$top_keops = array_slice($top_keops, 0, $user["indextopkeops"]);
}

// latest commented prods
if ($user["indexlatestcomments"]>0)
{
	include(TMP_FOLDER.'/latest_comments.cache.inc');
	$latest_comments = array_slice($latest_comments, 0, $user["indexlatestcomments"]);
}

// submitters
if ($user["indextopglops"]>0)
{
	$query="SELECT id,nickname,avatar,glops FROM users ORDER BY glops DESC LIMIT ".$user["indextopglops"];
	$result=mysql_query($query);
	while($tmp=mysql_fetch_assoc($result))
		$submitters[]=$tmp;
}

// stats
if ($user["indexstats"]>0)
{
	if (rand(0,11)==0) create_stats_cache();
	include(TMP_FOLDER.'/stats.cache.inc');
}

// cdc
if ($user["indexcdc"]>0)
{
$query="SELECT cdc.which,prods.name,prods.type,prods.group1,prods.group2,prods.group3 FROM cdc,prods WHERE cdc.quand<=NOW() AND cdc.which=prods.id ORDER BY cdc.quand DESC LIMIT 1";
$result=mysql_query($query);
$cdc=mysql_fetch_assoc($result);
if(strlen($cdc["name"])>32)
	$cdc["name"]=substr($cdc["name"],0,32)."...";
}

if ($cdc["group1"]):
	$query="select name,acronym from groups where id='".$cdc["group1"]."'";
	$result=mysql_query($query);
	while($tmp = mysql_fetch_array($result)) {
	  $cdc["groupname1"]=$tmp["name"];
	  $cdc["groupacron1"]=$tmp["acronym"];
	 }
endif;
if ($cdc["group2"]):
	$query="select name,acronym from groups where id='".$cdc["group2"]."'";
	$result=mysql_query($query);
	while($tmp = mysql_fetch_array($result)) {
	  $cdc["groupname2"]=$tmp["name"];
	  $cdc["groupacron2"]=$tmp["acronym"];
	 }
endif;
if ($cdc["group3"]):
	$query="select name,acronym from groups where id='".$cdc["group3"]."'";
	$result=mysql_query($query);
	while($tmp = mysql_fetch_array($result)) {
	  $cdc["groupname3"]=$tmp["name"];
	  $cdc["groupacron3"]=$tmp["acronym"];
	 }
endif;

if (strlen($cdc["groupname1"].$cdc["groupname2"].$cdc["groupname3"])>27):
	if (strlen($cdc["groupname1"])>10 && $cdc["groupacron1"]) $cdc["groupname1"]=$cdc["groupacron1"];
	if (strlen($cdc["groupname2"])>10 && $cdc["groupacron2"]) $cdc["groupname2"]=$cdc["groupacron2"];
	if (strlen($cdc["groupname3"])>10 && $cdc["groupacron3"]) $cdc["groupname3"]=$cdc["groupacron3"];
endif;

$query="select platforms.name from prods_platforms, platforms where prods_platforms.prod='".$cdc["which"]."' and platforms.id=prods_platforms.platform";
$result=mysql_query($query);
$check=0;
$cdc["platform"]="";
while($tmp = mysql_fetch_array($result)) {
  if ($check>0) $cdc["platform"].=",";
  $check++;
  $cdc["platform"].=$tmp["name"];
}
//print("->".$cdc["platform"]);

//$sql = 'SELECT * FROM prods AS p1 LEFT JOIN prods AS p2 ON p1.invitation = p2.party AND p1.invitationyear = p2.party_year WHERE p1.invitation > 0 AND p2.id IS NULL AND p1.invitationyear >= 2007 ORDER BY p1.date DESC LIMIT 0, 30';

if ($user["indexlatestparties"]>0)
{
	include(TMP_FOLDER.'/latest_released_parties.cache.inc');
	$latest_parties = array_slice($latest_released_parties, 0, $user["indexlatestparties"]);
}

?>

<br />

<table width="100%"><tr>
<td width="2%"><br /></td>
<td valign="top" width="20%">

<?
$sceneOrgDown = true;
if (time() - filemtime(SCENE_ORG_CHECK_FILE) > 60 * 15)
{
  file_put_contents(SCENE_ORG_CHECK_FILE, file_get_contents('http://www.scene.org/'));
}
$sceneOrgDown = !file_get_contents(SCENE_ORG_CHECK_FILE);

if ($sceneOrgDown) {
?>
<table cellspacing="1" cellpadding="2" class="box">
 <tr><th colspan="3">your account</th></tr>
 <tr bgcolor="#446688">
  <td align="center" colspan="3" style='padding:10px;'>
   sorry guys, scene.org (and consequently, sceneID) is <a href="http://www.isup.me/scene.org">down</a> for some reason :(
   i added some automagical code to check it periodically whether it comes back up,
   but until then you have to make do with read-only-pouet.
   <!--besides it's 3 days before <a href="http://www.revision-party.net/">revi</a><a href="http://www.gathering.org/tg11/en/">thering</a>, so go back to coding.-->
   in the meantime, you could perhaps try making a demo about it.
   <br/>
   --hugs,
   <br/>
   garg
  </td>
 </tr>
</table>
<br/>
<?
} else {
if($_SESSION["SESSION"]&&$_SESSION["SCENEID"]): ?>
<table cellspacing="1" cellpadding="2" class="box">
 <tr><th colspan="3">your account</th></tr>
 <tr bgcolor="#446688">
  <td nowrap align="center" colspan="3">
   you are logged in as<br />
   <a href="user.php?who=<?=$_SESSION["SCENEID_ID"]?>"><img src="avatars/<?=$_SESSION["SESSION_AVATAR"]?>" width="16" height="16" border="0" title="<?=$_SESSION["SESSION_NICKNAME"]?>" alt="<?=$_SESSION["SESSION_NICKNAME"]?>"></a>
   <a href="user.php?who=<?=$_SESSION["SCENEID_ID"]?>"><b><?=$_SESSION["SESSION_NICKNAME"]?></b></a><br />
  </td>
 </tr>
 <tr><td class="bottom"><a href="account.php">account</a></td>
     <td class="bottom"><a href="customize.php">customize</a></td>
     <td class="bottom"><a href="logout.php">logout</a></td>
 </tr>
</table>
<br />
<? else: ?>
<form action="login.php" method="post">
<table cellspacing="1" cellpadding="2" class="box">
 <tr><th>your account</th></tr>
 <tr bgcolor="#446688">
  <td nowrap align="center">
   <input type="text" name="login" value="SceneID" size="15" maxlength="16" onfocus="this.value=''"><br />
   <input type="password" name="password" value="password" size="15" onfocus="javascript:if(this.value=='password') this.value='';"><br />
   <input type="checkbox" name="permanent">login for 1 year<br />
   <a href="account.php">register here</a><br />
   <!-- <span style="color:#f88">login will be down while scene.org is updating</span> -->
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
<?
endif;
}
?>

<? if ($user["indexcdc"]>0): ?>
<table cellspacing="1" cellpadding="2" class="box">
 <tr><th><img class="icon" src="gfx/titles/coupdecoeur.gif" title="coup de coeur" alt="coup de couer">coup de coeur</th></tr>
 <tr bgcolor="#446688">
  <td width="100%" nowrap>
  <table cellspacing="0" cellpadding="0"><tr><td align="left" width="100%" valign="top" nowrap>
<?
        if ($user["indextype"]>0):
   	$typess = explode(",", $cdc["type"]);
         for($kkk=0;$kkk<count($typess);$kkk++) {
        ?><img src="gfx/types/<?=$types[$typess[$kkk]]?>" width="16" height="16" border="0" title="<?=$typess[$kkk]?>" alt="<?=$typess[$kkk]?>"><? } endif; ?>
        <a href="prod.php?which=<?=$cdc["which"]?>"><b><?=strtolower(str_replace("\'", "'", htmlentities(stripslashes($cdc["name"]))))?></b></a>
        </td>
        <? if ($user["indexplatform"]>0): ?>
   	<td align="right" nowrap valign="top">
   	<? $platformss = explode(",", $cdc["platform"]);
   	$platformss = array_slice($platformss,0,3,true);
 	for($kkk=0;$kkk<count($platformss);$kkk++) {
        ?><img src="gfx/os/<?=$os[$platformss[$kkk]]?>" width="16" height="16" border="0" title="<?=$platformss[$kkk]?>" alt="<?=$platformss[$kkk]?>"><? } ?><br /></td><? endif; ?>
	</tr></table>
   <? if(strlen($cdc["groupname1"])>0): ?>
   :: <a href="groups.php?which=<?=$cdc["group1"]?>"><?=strtolower(str_replace("\'", "'", htmlentities(stripslashes($cdc["groupname1"]))))?></a>
   <? endif; ?>
   <? if(strlen($cdc["groupname2"])>0): ?>
   :: <a href="groups.php?which=<?=$cdc["group2"]?>"><?=strtolower(str_replace("\'", "'", htmlentities(stripslashes($cdc["groupname2"]))))?></a>
   <? endif; ?>
   <? if(strlen($cdc["groupname3"])>0): ?>
   :: <a href="groups.php?which=<?=$cdc["group3"]?>"><?=strtolower(str_replace("\'", "'", htmlentities(stripslashes($cdc["groupname3"]))))?></a>
   <? endif; ?>
  </td>
 </tr>
 <tr><td class="bottom"><a href="sceneorg.php">scene.org awards</a> :: <a href="cdc.php">more</a>...</td></tr>
</table>
<br />
<? endif; ?>

<? if ($user["indexlatestadded"]>0): ?>
<table cellspacing="1" cellpadding="2" class="box">
 <tr><th colspan="2">latest added prods</th></tr>
<?
for($i=0;$i<count($latest_demos);$i++) {
  if($i%2) {
    print('<tr class="bg1">');
  } else {
    print('<tr class="bg2">');
  }
  ?>
  <td width="100%" nowrap>
    <table cellspacing="0" cellpadding="0"><tr><td align="left" width="100%" valign="top" nowrap>
<?
        if ($user["indextype"]>0):
   	$typess = explode(",", $latest_demos[$i]["type"]);
         for($kkk=0;$kkk<count($typess);$kkk++) {
        ?><img src="gfx/types/<?=$types[$typess[$kkk]]?>" width="16" height="16" border="0" title="<?=$typess[$kkk]?>" alt="<?=$typess[$kkk]?>"><? } endif; ?>
        <a href="prod.php?which=<?=$latest_demos[$i]["id"]?>"><b><?=strtolower(str_replace("\'", "'", htmlentities(stripslashes($latest_demos[$i]["name"]))))?></b></a>
        </td>
   	<? if ($user["indexplatform"]>0): ?>
   	<td align="right" nowrap valign="top"><?
  	$platformss = explode(",", $latest_demos[$i]["platform"]);
  	$platformss = array_slice($platformss,0,3,true);
 	for($kkk=0;$kkk<count($platformss);$kkk++) { ?><img src="gfx/os/<?=$os[$platformss[$kkk]]?>" width="16" height="16" border="0" title="<?=$platformss[$kkk]?>" alt="<?=$platformss[$kkk]?>"><? } ?><br /></td><? endif; ?>
	</tr></table>
   <? if(strlen($latest_demos[$i]["groupname1"])>0): ?>
   :: <a href="groups.php?which=<?=$latest_demos[$i]["group1"]?>"><?=strtolower(str_replace("\'", "'", htmlentities(stripslashes($latest_demos[$i]["groupname1"]))))?></a>
   <? endif; ?>
   <? if(strlen($latest_demos[$i]["groupname2"])>0): ?>
   :: <a href="groups.php?which=<?=$latest_demos[$i]["group2"]?>"><?=strtolower(str_replace("\'", "'", htmlentities(stripslashes($latest_demos[$i]["groupname2"]))))?></a>
   <? endif; ?>
   <? if(strlen($latest_demos[$i]["groupname3"])>0): ?>
   :: <a href="groups.php?which=<?=$latest_demos[$i]["group3"]?>"><?=strtolower(str_replace("\'", "'", htmlentities(stripslashes($latest_demos[$i]["groupname3"]))))?></a>
   <? endif; ?>
  </td>
  <? if ($user["indexwhoaddedprods"]>0): ?>
  <td align="right" valign="top">
  	<a href="user.php?who=<?=$latest_demos[$i]["added"]?>" title="<?=$latest_demos[$i]["nickname"]?>"><img class="icon" src="avatars/<?=$latest_demos[$i]["avatar"]?>" title="<?=$latest_demos[$i]["nickname"]?>" alt="<?=$latest_demos[$i]["nickname"]?>"></a>
  </td>
  <? endif; ?>
  </tr>
  <? } ?>
 <tr><td class="bottom" colspan="2"><a href="prodlist.php?order=added">more</a>...</td></tr>
</table>
<br />
<? endif; ?>

<? if ($user["indexlatestreleased"]>0): ?>
<table cellspacing="1" cellpadding="2" class="box">
 <tr><th>latest released prods</th></tr>
<?
for($i=0;$i<count($latest_released_prods);$i++) {
  if($i%2) {
    print('<tr class="bg1">');
  } else {
    print('<tr class="bg2">');
  }
  ?>
    <td width="100%" nowrap>
    <table cellspacing="0" cellpadding="0"><tr><td align="left" width="100%" valign="top" nowrap>
<?
        if ($user["indextype"]>0):
   	$typess = explode(",", $latest_released_prods[$i]["type"]);
         for($kkk=0;$kkk<count($typess);$kkk++) {
        ?><img src="gfx/types/<?=$types[$typess[$kkk]]?>" width="16" height="16" border="0" title="<?=$typess[$kkk]?>" alt="<?=$typess[$kkk]?>"><? } endif; ?>
        <a href="prod.php?which=<?=$latest_released_prods[$i]["id"]?>"><b><?=strtolower(str_replace("\'", "'", htmlentities(stripslashes($latest_released_prods[$i]["name"]))))?></b></a>
        </td>
   	<? if ($user["indexplatform"]>0): ?>
   	<td align="right" nowrap valign="top">
   	<? $platformss = explode(",", $latest_released_prods[$i]["platform"]);
   	$platformss = array_slice($platformss,0,3,true);
 	for($kkk=0;$kkk<count($platformss);$kkk++) {
        ?><img src="gfx/os/<?=$os[$platformss[$kkk]]?>" width="16" height="16" border="0" title="<?=$platformss[$kkk]?>" alt="<?=$platformss[$kkk]?>"><? } ?><br /></td><? endif; ?>
	</tr></table>
   <? if(strlen($latest_released_prods[$i]["groupname1"])>0): ?>
   :: <a href="groups.php?which=<?=$latest_released_prods[$i]["group1"]?>"><?=strtolower(str_replace("\'", "'", htmlentities(stripslashes($latest_released_prods[$i]["groupname1"]))))?></a>
   <? endif; ?>
   <? if(strlen($latest_released_prods[$i]["groupname2"])>0): ?>
   :: <a href="groups.php?which=<?=$latest_released_prods[$i]["group2"]?>"><?=strtolower(str_replace("\'", "'", htmlentities(stripslashes($latest_released_prods[$i]["groupname2"]))))?></a>
   <? endif; ?>
   <? if(strlen($latest_released_prods[$i]["groupname3"])>0): ?>
   :: <a href="groups.php?which=<?=$latest_released_prods[$i]["group3"]?>"><?=strtolower(str_replace("\'", "'", htmlentities(stripslashes($latest_released_prods[$i]["groupname3"]))))?></a>
   <? endif; ?>
  </td>
  </tr>
<? } ?>
 <tr><td class="bottom"><a href="prodlist.php?order=release">more</a>...</td></tr>
</table>
<br />
<? endif; ?>

<? if ($user["indextopprods"]>0): ?>
<table cellspacing="1" cellpadding="2" class="box">
 <tr><th>top of the month</th></tr>
<?
for($i=0;$i<count($top_demos);$i++) {
  if($i%2) {
    print('<tr class="bg1">');
  } else {
    print('<tr class="bg2">');
  }
  ?>
    <td width="100%" nowrap>
    <table cellspacing="0" cellpadding="0"><tr><td align="left" width="100%" valign="top" nowrap>
<?
        if ($user["indextype"]>0):
   	$typess = explode(",", $top_demos[$i]["type"]);
         for($kkk=0;$kkk<count($typess);$kkk++) {
        ?><img src="gfx/types/<?=$types[$typess[$kkk]]?>" width="16" height="16" border="0" title="<?=$typess[$kkk]?>" alt="<?=$typess[$kkk]?>"><? } endif; ?>
        <a href="prod.php?which=<?=$top_demos[$i]["id"]?>"><b><?=strtolower(str_replace("\'", "'", htmlentities(stripslashes($top_demos[$i]["name"]))))?></b></a>
   	</td>
   	<? if ($user["indexplatform"]>0): ?>
   	<td align="right" nowrap valign="top">
   	<? $platformss = explode(",", $top_demos[$i]["platform"]);
   	$platformss = array_slice($platformss,0,3,true);
 	for($kkk=0;$kkk<count($platformss);$kkk++) {
        ?><img src="gfx/os/<?=$os[$platformss[$kkk]]?>" width="16" height="16" border="0" title="<?=$platformss[$kkk]?>" alt="<?=$platformss[$kkk]?>"><? } ?><br /></td><? endif; ?>
	</tr></table>
   <? if(strlen($top_demos[$i]["groupname1"])>0): ?>
   :: <a href="groups.php?which=<?=$top_demos[$i]["group1"]?>"><?=strtolower(str_replace("\'", "'", htmlentities(stripslashes($top_demos[$i]["groupname1"]))))?></a>
   <? endif; ?>
   <? if(strlen($top_demos[$i]["groupname2"])>0): ?>
   :: <a href="groups.php?which=<?=$top_demos[$i]["group2"]?>"><?=strtolower(str_replace("\'", "'", htmlentities(stripslashes($top_demos[$i]["groupname2"]))))?></a>
   <? endif; ?>
   <? if(strlen($top_demos[$i]["groupname3"])>0): ?>
   :: <a href="groups.php?which=<?=$top_demos[$i]["group3"]?>"><?=strtolower(str_replace("\'", "'", htmlentities(stripslashes($top_demos[$i]["groupname3"]))))?></a>
   <? endif; ?>
  </td>
  </tr>
  <? } ?>
 <tr><td class="bottom"><a href="toplist.php">more</a>...</td></tr>
</table>
<br />
<? endif; ?>

<? if ($user["indextopkeops"]>0): ?>
<table cellspacing="1" cellpadding="2" class="box">
 <tr><th>all-time top</th></tr>
<?
for($i=0;$i<count($top_keops);$i++) {
  if($i%2) {
    print('<tr class="bg1">');
  } else {
    print('<tr class="bg2">');
  }
  ?>
    <td width="100%" nowrap>
    <table cellspacing="0" cellpadding="0"><tr><td align="left" width="100%" valign="top" nowrap>
<?
        if ($user["indextype"]>0):
   	$typess = explode(",", $top_keops[$i]["type"]);
         for($kkk=0;$kkk<count($typess);$kkk++) {
        ?><img src="gfx/types/<?=$types[$typess[$kkk]]?>" width="16" height="16" border="0" title="<?=$typess[$kkk]?>" alt="<?=$typess[$kkk]?>"><? } endif; ?>
        <a href="prod.php?which=<?=$top_keops[$i]["id"]?>"><b><?=strtolower(str_replace("\'", "'", htmlentities(stripslashes($top_keops[$i]["name"]))))?></b></a>
   	</td>
   	<? if ($user["indexplatform"]>0): ?>
   	<td align="right" nowrap valign="top">
   	<? $platformss = explode(",", $top_keops[$i]["platform"]);
   	$platformss = array_slice($platformss,0,3,true);
 	for($kkk=0;$kkk<count($platformss);$kkk++) {
        ?><img src="gfx/os/<?=$os[$platformss[$kkk]]?>" width="16" height="16" border="0" title="<?=$platformss[$kkk]?>" alt="<?=$platformss[$kkk]?>"><? } ?><br /></td><? endif; ?>
	</tr></table>
   <? if(strlen($top_keops[$i]["groupname1"])>0): ?>
   :: <a href="groups.php?which=<?=$top_keops[$i]["group1"]?>"><?=strtolower(str_replace("\'", "'", htmlentities(stripslashes($top_keops[$i]["groupname1"]))))?></a>
   <? endif; ?>
   <? if(strlen($top_keops[$i]["groupname2"])>0): ?>
   :: <a href="groups.php?which=<?=$top_keops[$i]["group2"]?>"><?=strtolower(str_replace("\'", "'", htmlentities(stripslashes($top_keops[$i]["groupname2"]))))?></a>
   <? endif; ?>
   <? if(strlen($top_keops[$i]["groupname3"])>0): ?>
   :: <a href="groups.php?which=<?=$top_keops[$i]["group3"]?>"><?=strtolower(str_replace("\'", "'", htmlentities(stripslashes($top_keops[$i]["groupname3"]))))?></a>
   <? endif; ?>
  </td>
  </tr>
  <? } ?>
 <tr><td class="bottom"><a href="top10.php">more</a>...</td></tr>
</table>
<br />
<? endif; ?>


</td>

<td width="3%"><br /></td>
<td valign="top" width="50%" align="right">

<?
//include_once("breakpoint_donate.inc.php");
?>
<? if ($user["indexoneliner"]>0): ?>
<script language="JavaScript" type="text/javascript">
<!--
function validateOneliner(s) {
  if (s.indexOf("[url")!=-1) {
    alert("BBCode doesn't work in the oneliner!");
    return false;
  }
  return true;
}
//-->
</script>
<form action="add.php" method="post" onsubmit="return validateOneliner(this.message.value);">
<input type="hidden" name="type" value="oneliner">
<table cellspacing="1" cellpadding="2" class="box">
 <tr><th colspan="2"><img class="icon" src="gfx/titles/talk.gif" title="talk" alt="talk">the so famous pou&euml;t.net oneliner</th></tr>
 <? for($i=count($onelines)-1;$i>=0;$i--): ?>
 <tr class="cite-<?=$onelines[$i]["who"]?>">
 <?
  if($i%2) {
    print('<td class="bg1" colspan="2">');
  } else {
    print('<td class="bg2" colspan="2">');
  }
 ?>
 <table cellspacing="0" cellpadding="0"><tr><td>
   <a href="user.php?who=<?=$onelines[$i]["who"]?>" title="<?=$onelines[$i]["nickname"]?>"><img class="icon" src="avatars/<?=$onelines[$i]["avatar"]?>" title="<?=$onelines[$i]["nickname"]?>" alt="<?=$onelines[$i]["nickname"]?>"></a>
   </td>
   <td>&nbsp;</td>
   <td>
   <?=better_wordwrap(str_replace("\'", "'", parseUrl( htmlentities( stripslashes($onelines[$i]["message"]) ),NULL )),60,"\n")?>
   </td></tr></table>
  </td>
 </tr>
 <? endfor; ?>
 <?

//$funnytext = "have fun";
//$funnytext = "get a cookie coz u'll need one to post";
//$funnytext = "demo my ipod me beautiful!";
//$funnytext = "bbcode and unicode doesnt work on oneliner";
//$funnytext = "Most people including myself have some sensibility";
//$funnytext = "### song, people dancing ###";
//$funnytext = "PANTS OFF!";
//$funnytext = "The world may now !";
//$funnytext = "Captain: I'm in Mensa.";
//$funnytext = "SHOW US YOUR";
//$funnytext = "remember: NO CAPES!";
//$funnytext = "NO THURSDAY ARRIVALS!";
//$funnytext = "if garfield was a criminal, we would purchase him until afghanistan.";
//$funnytext = "crashes indeed.. but wow! NOOON..";
$funnytext = "time is to unicode on the onliner";

if($_SESSION["SESSION"]): ?>
 <tr>
  <td align="center" class="bg3">
   <table cellspacing="0" cellpadding="0">
    <tr>
	 <td><input type="text" name="message" id="message" value="<?=htmlspecialchars($funnytext)?>" size="50" onfocus="if (this.value=='<?=addslashes($funnytext)?>') this.value=''"></td>
	 <td><input type="image" src="gfx/submit.gif"></td>
	</tr>
   </table>
  </td>
  <td class="bottom">
   <b><a href="oneliner.php">more</a>...</b><br />
  </td>
 </tr>
 <? else: ?>
 <tr><td class="bottom" colspan="2"><a href="oneliner.php">more</a>...</td></tr>
 <? endif; ?>
</table>
</form>
<br />

<? endif;

if ($sceneorgyear && $_SESSION["SCENEID_ID"])
{
  $result=mysql_query("select * from awardscand_".$sceneorgyear." where user=".(int)$_SESSION["SCENEID_ID"]);
  if (!mysql_num_rows($result))
  {
  ?>
  <table cellspacing="1" cellpadding="2" class="box">
   <tr><th colspan="<?=$row?>"><img class="icon" src='http://www.pouet.net/gfx/sceneorg/awardwinner.gif'/>&nbsp;scene.org awards <?=$sceneorgyear?></th></tr>
   <tr>
    <td class="bg2" style='padding: 5px;'>
    you still haven't suggested anything for <a href='http://awards.scene.org/'>the <?=$sceneorgyear?> edition of the scene.org awards</a>!
    go <a href="awardscandidates.php">here</a> to learn more about the categories and submit demos individually,
    or browse some more <a href='prodlist.php?year=<?=$sceneorgyear?>'>demos released in <?=$sceneorgyear?></a>
    and submit them to a category using the voting form at the bottom of their prodpage!
    <br/>
    <b>remember:</b> think of prods the jury wouldn't normally think of! (and please stop voting asd as best newcomer, best 4k and best oldskool. it's not funny.)
    </td>
   </tr>
  </table>
  <br/>
  <?
  }
}

 if ($user["indexbbstopics"]>0):

echo "<!--";
var_dump($user);
echo "-->";

$query  = "SELECT bbs_topics.id, bbs_topics.topic, bbs_topics.firstpost, bbs_topics.lastpost, bbs_topics.userfirstpost, bbs_topics.userlastpost, bbs_topics.count, ";
$query .= " bbs_topics.category, users1.nickname as nickname_1, users1.avatar as avatar_1, users2.nickname as nickname_2, users2.avatar as avatar_2 FROM bbs_topics ";
$query .= " LEFT JOIN users as users2 on users2.id=bbs_topics.userfirstpost ";
$query .= " LEFT JOIN users as users1 on users1.id=bbs_topics.userlastpost ";

if ($user["indexbbsnoresidue"])
  $query .= " WHERE bbs_topics.category != 1 ";

$query .= " ORDER BY bbs_topics.lastpost desc ";
$query .= " LIMIT ".$user["indexbbstopics"];

$result=mysql_query($query);
while($tmp=mysql_fetch_assoc($result)) {
  $topics[]=$tmp;
}
$row = 4;
if (canSeeBBSCategories()) {
  $row = 5;
}
?>
<table cellspacing="1" cellpadding="2" class="box">
 <tr><th colspan="<?=$row?>">the oldskool pou&euml;t.net bbs</th></tr>
 <? for($i=0;$i<count($topics);$i++): ?>
 <?
  if($i%2) {
    $bgcolor='1';
  } else {
    $bgcolor='2';
  }
 ?>
 <tr class="cite-<?=$topics[$i]["userfirstpost"]?>">
  <td class="bg<?=$bgcolor?>"><a href="user.php?who=<?=$topics[$i]["userfirstpost"]?>" title="<?=$topics[$i]["nickname_2"]?>"><img src="avatars/<?=$topics[$i]["avatar_2"]?>" width="16" height="16" border="0" title="<?=$topics[$i]["nickname_2"]?>" alt="<?=$topics[$i]["nickname_2"]?>"></a></td>
<?
if (canSeeBBSCategories()) {
  printf("<td class='bg%d threadcat'>%s</td>\n",$bgcolor,$thread_categories[$topics[$i]["category"]]);
}
?>
  <td class="bg<?=$bgcolor?>" width="100%"><a href="topic.php?which=<?=$topics[$i]["id"]?>"><b><?=htmlcleanonerow($topics[$i]["topic"])?></b></a></td>
  <td class="bg<?=$bgcolor?>" align="right" nowrap>&nbsp;<?=$topics[$i]["count"]?>&nbsp;</td>
  <td class="bg<?=$bgcolor?>"><a href="user.php?who=<?=$topics[$i]["userlastpost"]?>" title="<?=$topics[$i]["nickname_1"]?>"><img src="avatars/<?=$topics[$i]["avatar_1"]?>" width="16" height="16" border="0" title="<?=$topics[$i]["nickname_1"]?>" alt="<?=$topics[$i]["nickname_1"]?>"></a></td>
 </tr>
 <? endfor; ?>
 <tr><td class="bottom" colspan="<?=$row?>"><a href="bbs.php">more</a>...</td></tr>
</table>
<br />
<? endif;

/*
<div style='background:#800;border:2px solid red;color:white;margin:10px;padding:10px; text-align:center;font-weight:bold;'>
remember: <a href='awardscandidates.php' style='color:#f88;'>suggestions for the 2010 scene.org awards</a> will close on wednesday, january the 5th! vote now!
</div>
*/
?>


<? include_once("include/customnews.php"); ?>
<? if ($user["indexojnews"]>0):
if ($user["indexojnews"] > $rs['items_count']) $user["indexojnews"] = $rs['items_count'];
for($i=0;$i<$user["indexojnews"];$i++): ?>
<table cellspacing="1" cellpadding="2" class="box">
 <tr>
  <th>
    <img class="icon" src="gfx/titles/bitfellas.gif" title="bitfellas news" alt="bitfellas news">&nbsp;
   <?
    if($rs['items'][$i]['link']) print("<a href=\"".$rs['items'][$i]['link']."\">");
    print(utf8_decode($rs['items'][$i]['title']));
    if($rs['items'][$i]['link']) print("</a>");
   ?>
  </th>
 </tr>
 <tr>
  <td bgcolor="#446688">
   <?=(utf8_decode($rs['items'][$i]['description']))?>
  </td>
 </tr>
 <tr>
  <td bgcolor="#6688AA" colspan="2" align="right">
   lobstregated at <a href="http://www.bitfellas.org/">BitFellas.org</a> on <?=$rs['items'][$i]['pubDate']?>
  </td>
 </tr>
</table>
<br />
<? endfor; ?>
<? endif; ?>
</td>

<td width="3%"><br /></td>
<td valign="top" width="20%">
<? if ($user["indexsearch"]>0): ?>
<form action="search.php">
<table cellspacing="1" cellpadding="2" class="box">
 <tr><th><img class="icon" src="gfx/titles/search.gif" title="search" alt="search">search box</th></tr>
 <tr bgcolor="#557799">
  <td align="center">
   <input type="text" name="what" size="25">
  </td>
 </tr>
 <tr>
  <td bgcolor="#446688" align="center">
   <table cellspacing="0" cellpadding="0">
    <tr>
     <td><input id="search_prod" type="radio" name="type" value="prod" checked></td>
     <td><label for="search_prod">prod</label></td>
     <td><input id="search_group" type="radio" name="type" value="group"></td>
     <td><label for="search_group">group</label></td>
     <td><input id="search_party" type="radio" name="type" value="party"></td>
     <td><label for="search_party">party</label></td>
     <td><input id="search_board" type="radio" name="type" value="board"></td>
     <td><label for="search_board">board</label></td>
     <td><input id="search_user" type="radio" name="type" value="user"></td>
     <td><label for="search_user">user</label></td>
     <td><input id="search_bbs" type="radio" name="type" value="bbs"></td>
     <td><label for="search_bbs">bbs</label></td>
    </tr>
   </table>
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

<? if ($user["indexstats"]>0): ?>
<table cellspacing="1" cellpadding="2" class="box">
 <tr>
  <th><img class="icon" src="gfx/titles/stats.gif" title="stats" alt="stats">some stats</th>
  <td align="right" bgcolor="#224488" nowrap><b>-24h</b></td>
 </tr>
 <tr>
  <td bgcolor="#446688">
   <?=$nb_demos?> <a href="prodlist.php">prods</a>
  </td>
  <td bgcolor="#557799" align="right" nowrap>
   + <?=$inc_demos?>
  </td>
 </tr>
 <tr>
  <td bgcolor="#557799">
   <?=$nb_groups?> <a href="groups.php">groups</a>
  </td>
  <td bgcolor="#446688" align="right" nowrap>
   + <?=$inc_groups?>
  </td>
 </tr>
 <tr>
  <td bgcolor="#446688">
   <?=$nb_parties?> <a href="parties.php">parties</a>
  </td>
  <td bgcolor="#557799" align="right" nowrap>
   + <?=$inc_parties?>
  </td>
 </tr>
 <tr>
  <td bgcolor="#557799">
   <?=$nb_bbses?> <a href="bbses.php">boards</a>
  </td>
  <td bgcolor="#446688" align="right" nowrap>
   + <?=$inc_bbses?>
  </td>
 </tr>
 <tr>
  <td bgcolor="#446688">
   <?=$nb_users?> <a href="userlist.php">users</a>
  </td>
  <td bgcolor="#557799" align="right" nowrap>
   + <?=$inc_users?>
  </td>
 </tr>
 <tr>
  <td bgcolor="#557799">
   <?=$nb_comments?> <a href="comments.php">comments</a>
  </td>
  <td bgcolor="#446688" align="right" nowrap>
   + <?=$inc_comments?>
  </td>
 </tr>
</table>
<br />
<? endif; ?>

<?
if ($user["indexlinks"]>0):
$query="SELECT type,img,url,alt FROM buttons WHERE dead = 0 ORDER BY RAND() LIMIT 1";
$result=mysql_query($query);
$button=mysql_fetch_assoc($result);
?>
<table cellspacing="1" cellpadding="2" class="box">
 <tr><th><?=$button["type"]?></th></tr>
 <tr bgcolor="#446688">
  <td align="center">
   <a href="<?=$button["url"]?>"><img src="gfx/buttons/<?=$button["img"]?>" border="0" title="<?=$button["alt"]?>" alt="<?=$button["alt"]?>"></a>
  </td>
 </tr>
 <tr><td class="bottom"><a href="buttons.php">more</a>...</td></tr>
</table>
<br />
<? endif; ?>

<? if ($user["indexlatestcomments"]>0): ?>
<table cellspacing="1" cellpadding="2" class="box">
 <tr><th colspan="2">latest comments added</th></tr>
<?
for($i=0;$i<count($latest_comments);$i++) {
  if($i%2) {
    print("<tr bgcolor=\"#446688\" class=\"cite-".$latest_comments[$i]["who"]."\">");
  } else {
    print("<tr bgcolor=\"#557799\" class=\"cite-".$latest_comments[$i]["who"]."\">");
  }
  ?>
    <td width="100%" nowrap>
    <table cellspacing="0" cellpadding="0"><tr><td align="left" width="100%" valign="top" nowrap>
<?
        if ($user["indextype"]>0):
        $typess = explode(",", $latest_comments[$i]["type"]);
        for($kkk=0;$kkk<count($typess);$kkk++) {
        ?><img src="gfx/types/<?=$types[$typess[$kkk]]?>" width="16" height="16" border="0" title="<?=$typess[$kkk]?>" alt="<?=$typess[$kkk]?>"><? } endif; ?>
        <a href="prod.php?which=<?=$latest_comments[$i]["id"]?>"><b><?=strtolower(str_replace("\'", "'", htmlentities(stripslashes($latest_comments[$i]["name"]))))?></b></a>
   	</td>
        <? if ($user["indexplatform"]>0): ?>
   	<td align="right" nowrap valign="top">
   	<? $platformss = explode(",", $latest_comments[$i]["platform"]);
   	$platformss = array_slice($platformss,0,3,true);
 	for($kkk=0;$kkk<count($platformss);$kkk++) {
        ?><img src="gfx/os/<?=$os[$platformss[$kkk]]?>" width="16" height="16" border="0" title="<?=$platformss[$kkk]?>" alt="<?=$platformss[$kkk]?>"><? } ?><br /></td><? endif; ?>
	</tr></table>
   <? if(strlen($latest_comments[$i]["groupname1"])>0): ?>
   :: <a href="groups.php?which=<?=$latest_comments[$i]["group1"]?>"><?=strtolower(str_replace("\'", "'", htmlentities(stripslashes($latest_comments[$i]["groupname1"]))))?></a>
   <? endif; ?>
   <? if(strlen($latest_comments[$i]["groupname2"])>0): ?>
   :: <a href="groups.php?which=<?=$latest_comments[$i]["group2"]?>"><?=strtolower(str_replace("\'", "'", htmlentities(stripslashes($latest_comments[$i]["groupname2"]))))?></a>
   <? endif; ?>
   <? if(strlen($latest_comments[$i]["groupname3"])>0): ?>
   :: <a href="groups.php?which=<?=$latest_comments[$i]["group3"]?>"><?=strtolower(str_replace("\'", "'", htmlentities(stripslashes($latest_comments[$i]["groupname3"]))))?></a>
   <? endif; ?>
  </td>
  <? if ($user["indexwhocommentedprods"]>0): ?>
  <td align="right" valign="top">
  	<a href="user.php?who=<?=$latest_comments[$i]["who"]?>" title="<?=$latest_comments[$i]["nickname"]?>"><img class="icon" src="avatars/<?=$latest_comments[$i]["avatar"]?>" title="<?=$latest_comments[$i]["nickname"]?>" alt="<?=$latest_comments[$i]["nickname"]?>"></a>
  </td>
  <? endif; ?>
  </tr>
  <? } ?>
 <tr><td class="bottom" colspan="2"><a href="comments.php">more</a>...</td></tr>
</table>
<br />
<? endif; ?>

<? if ($user["indexlatestparties"]>0): ?>
<table cellspacing="1" cellpadding="2" class="box">
 <tr><th colspan="2">latest parties</th></tr>
<?
for($i=0;$i<count($latest_parties);$i++) {
	if($i%2) {
		print("<tr bgcolor=\"#446688\">");
	} else {
		print("<tr bgcolor=\"#557799\">");
	}
  ?>
  <td align="left" width="100%" valign="top" nowrap>
    <a href="party.php?which=<?=$latest_parties[$i]["id"]?>&when=<?=sprintf("%02d",$latest_parties[$i]["party_year"])?>"><?=strtolower(str_replace("\'", "'", htmlentities(stripslashes($latest_parties[$i]["name"]." ".sprintf("%02d",$latest_parties[$i]["party_year"])))))?></a>
<?
  $when2d = sprintf("%02d",$latest_parties[$i]["party_year"]%100);
  if(file_exists("results/".$latest_parties[$i]["id"]."_".$when2d.".txt")){ ?>
  [<a href="results.php?which=<?=$latest_parties[$i]["id"]?>&when=<?=$when2d?>">results</a>]
  <? } ?>
  </td>
  <td align="right" valign="top" nowrap><? print($latest_parties[$i]["prodcount"]); ?><br /></td>
  </tr>
  <? } ?>
 <tr><td class="bottom" colspan="2"><a href="parties.php">more</a>...</td></tr>
</table>
<br />
<? endif; ?>


<table cellspacing="1" cellpadding="2" class="box">
 <tr><th colspan="2"><img class="icon" src="gfx/titles/dpnet.gif" title="demoparty.net" alt="demoparty.net">&nbsp;upcoming parties</th></tr>
<?
$rs = $rss->GetCached('http://feeds.feedburner.com/demoparty/parties');

//debuglog(var_export($rs,true));

for($i=0;$i<5;$i++) {
	if($i%2) {
		print("<tr bgcolor=\"#446688\">");
	} else {
		print("<tr bgcolor=\"#557799\">");
	}
	$st = strtotime($rs['items'][$i]['demopartynet:startDate']);
	$et = strtotime($rs['items'][$i]['demopartynet:endDate']);
	$sd = strtolower( date("M j",$st) );
	$ed = strtolower( date("M j",$et) );
	$form = "";
	if ($sd == $ed)
	  $form = $sd;
	else if (substr($sd,0,3)==substr($ed,0,3))
	  $form = $sd . " - " . substr($ed,4);
	else
	  $form = $sd . " - " . $ed;
	$dist = (int)ceil( ($st - time()) / 60 / 60 / 24 );
  ?>
  <td align="left" valign="top" nowrap>
    <a href="<?=$rs['items'][$i]['link']?>"><?=strtolower($rs['items'][$i]['demopartynet:title'])?></a>
  </td>
  <td align="right"><?
  echo $form;
  if ($dist == 0) echo " (today!)";
  else if ($dist == 1) echo " (tomorrow)";
  else echo " (".$dist." days)";
  ?></td>
  <? } ?>
 <tr><td class="bottom" colspan="2"><a href="http://www.demoparty.net">more at demoparty.net</a>...</td></tr>
</table>
<br />

<? if ($user["indextopglops"]>0): ?>
<table cellspacing="1" cellpadding="2" class="box">
 <tr><th>top of the gl&ouml;ps</th></tr>
<?
for($i=0;$i<count($submitters);$i++) {
    if($i%2) {
      print("<tr bgcolor=\"#446688\">");
    } else {
      print("<tr bgcolor=\"#557799\">");
    }
    ?>
    <td nowrap>
     <a href="user.php?who=<?=$submitters[$i]["id"]?>" title="<?=$submitters[$i]["nickname"]?>"><img src="avatars/<?=$submitters[$i]["avatar"]?>" width="16" height="16" border="0" title="<?=$submitters[$i]["nickname"]?>" alt="<?=$submitters[$i]["nickname"]?>"></a>
     <a href="user.php?who=<?=$submitters[$i]["id"]?>"><b><?=$submitters[$i]["nickname"]?></b></a>
     <br />
     :: <?=$submitters[$i]["glops"]?> gl&ouml;ps
    </td>
    </tr>
    <?
}
?>
<tr><td class="bottom"><a href="userlist.php?order=glops">more</a>...</td></tr>
</table>
<? endif; ?>
</td>
<td width="2%"><br /></td>
</tr></table>
<br />
<? require("include/bottom.php"); ?>
