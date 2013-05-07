<?
require("include/top.php");
require("include/libbb.php");

if ($user["indexoneliner"]>0)
{
	include('include/onelines.cache.inc');
	$onelines = array_slice($onelines, 0, $user["indexoneliner"]);
}

/*if ($user["indexojnews"]>0)
{
$query="SELECT * FROM ojnews ORDER BY quand DESC LIMIT ".$user["indexojnews"];
$result=mysql_query($query);
while($tmp=mysql_fetch_assoc($result)) {
  $tmp["newstype"]="ojnews";
	$ojnews[]=$tmp;
}

$query="SELECT * FROM news ORDER BY quand DESC LIMIT ".$user["indexojnews"];
$result=mysql_query($query);
while($tmp=mysql_fetch_assoc($result)) {
  $tmp["newstype"]="pouetnews";
  $mynews[]=$tmp;
}

$oj=0;
$po=0;
for($i=0;$i<count($ojnews);$i++) {
  if($ojnews[$oj]["quand"]>$mynews[$po]["quand"]) {
    $ojnews[$oj]["content"]=stripslashes(urldecode($ojnews[$oj]["content"]));
    $ojnews[$oj]["title"]=stripslashes(urldecode($ojnews[$oj]["title"]));
    $ojnews[$oj]["authornick"]=stripslashes(urldecode($ojnews[$oj]["authornick"]));
    $ojnews[$oj]["authorgroup"]=stripslashes(urldecode($ojnews[$oj]["authorgroup"]));
    $news[]=$ojnews[$oj];
    $oj++;
  } else {
	$query="SELECT nickname,avatar FROM users WHERE id=".$mynews[$po]["who"];
	$result=mysql_query($query);
	$tmp=mysql_fetch_assoc($result);
	$mynews[$po]["nickname"]=$tmp["nickname"];
	$mynews[$po]["avatar"]=$tmp["avatar"];
    $news[]=$mynews[$po];
    $po++;
  }
}
}*/

include './lastRSS.php';

// create lastRSS object
$rss = new lastRSS; 

// setup transparent cache
$rss->cache_dir = './cache'; 
$rss->cache_time = 5*60; // in seconds
$rss->CDATA = 'strip'; 
$rss->date_format = 'Y-m-d'; 

// load some RSS file
$rs = $rss->GetCached('http://bitfellas.org/e107_plugins/rss_menu/rss.php?1.2');
//if ($rs = $rss->get('http://bitfellas.org/e107_plugins/rss_menu/rss.php?1.2')) {
/*
$rss_url = 'http://bitfellas.org/e107_plugins/rss_menu/rss.php?1.2';
if ($rs = $rss->get($f)) {
}
else {
	printf('Error: RSS file not found...');
}
*/
// latest added prods
if ($user["indexlatestadded"]>0)
{
	include('include/latest_demos.cache.inc');
	$latest_demos = array_slice($latest_demos, 0, $user["indexlatestadded"]);
}

// latest released prods
if ($user["indexlatestreleased"]>0)
{
	include('include/latest_released_prods.cache.inc');
	$latest_released_prods = array_slice($latest_released_prods, 0, $user["indexlatestreleased"]);
}

// debut calcul top demos
if ($user["indextopprods"]>0)
{
	include('include/top_demos.cache.inc');
	$top_demos = array_slice($top_demos, 0, $user["indextopprods"]);
}

if ($user["indextopkeops"]>0)
{
	include('include/top_keops.cache.inc');
	$top_keops = array_slice($top_keops, 0, $user["indextopkeops"]);
}

// latest commented prods
if ($user["indexlatestcomments"]>0)
{
	include('include/latest_comments.cache.inc');
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
	include('include/stats.cache.inc');
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
	include('include/latest_released_parties.cache.inc');
	$latest_parties = array_slice($latest_released_parties, 0, $user["indexlatestparties"]);
}

?>

<br />

<table><tr>
<td width="2%"><br /></td>
<td valign="top" width="20%">

<? 
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
     <td class="bottom"><a href="account2.php">custom</a></td>
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

<? if ($user["indexoneliner"]>0): ?>
<form action="add.php" method="post">
<input type="hidden" name="type" value="oneliner">
<table cellspacing="1" cellpadding="2" class="box">
 <tr><th colspan="2"><img class="icon" src="gfx/titles/talk.gif" title="talk" alt="talk">the so famous pouët.net oneliner</th></tr>
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
 <? if($_SESSION["SESSION"]): ?>
 <tr>
  <td align="center" class="bg3">
   <table cellspacing="0" cellpadding="0">
    <tr>
	 <td><input type="text" name="message" value="Most people including myself have some sensibility" size="50" onfocus="if (this.value=='Most people including myself have some sensibility') this.value=''"></td>
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
<? endif; ?>

<? if ($user["indexbbstopics"]>0): 
$query="SELECT bbs_topics.id, bbs_topics.topic, bbs_topics.firstpost, bbs_topics.lastpost, bbs_topics.userfirstpost, bbs_topics.userlastpost, bbs_topics.count, users1.nickname as nickname_1, users1.avatar as avatar_1, users2.nickname as nickname_2, users2.avatar as avatar_2 FROM bbs_topics LEFT JOIN users as users2 on users2.id=bbs_topics.userfirstpost LEFT JOIN users as users1 on users1.id=bbs_topics.userlastpost ORDER BY bbs_topics.lastpost desc LIMIT ".$user["indexbbstopics"];
$result=mysql_query($query);
while($tmp=mysql_fetch_assoc($result)) {
  $topics[]=$tmp;
}
?>
<table cellspacing="1" cellpadding="2" class="box">
 <tr><th colspan="4">the oldskool pouët.net bbs</th></tr>
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
  <td class="bg<?=$bgcolor?>" width="100%"><a href="topic.php?which=<?=$topics[$i]["id"]?>"><b><?=htmlcleanonerow($topics[$i]["topic"])?></b></a></td>
  <td class="bg<?=$bgcolor?>" align="right" nowrap>&nbsp;<?=$topics[$i]["count"]?>&nbsp;</td>
  <td class="bg<?=$bgcolor?>"><a href="user.php?who=<?=$topics[$i]["userlastpost"]?>" title="<?=$topics[$i]["nickname_1"]?>"><img src="avatars/<?=$topics[$i]["avatar_1"]?>" width="16" height="16" border="0" title="<?=$topics[$i]["nickname_1"]?>" alt="<?=$topics[$i]["nickname_1"]?>"></a></td>
 </tr>
 <? endfor; ?>
 <tr><td class="bottom" colspan="4"><a href="bbs.php">more</a>...</td></tr>
</table>
<br />
<? endif; ?>

<? include_once("include/customnews.php"); ?>
<? if ($user["indexojnews"]>0): 
if ($user["indexojnews"] > $rs['items_count']) $user["indexojnews"] = $rs['items_count'];
for($i=0;$i<$user["indexojnews"];$i++): ?>
<table cellspacing="1" cellpadding="2" class="box">
 <tr>
  <th>
    <img class="icon" src="gfx/titles/bitfellas.gif" title="bitfellas news" alt="bitfellas news">
   <? if($rs['items'][$i]['link']): ?><a href="<?=$rs['items'][$i]['link']?>"><? endif; ?>
   <? print("&nbsp;".utf8_decode($rs['items'][$i]['title'])); ?>
   <? if($rs['items'][$i]['link']): ?></a><? endif; ?>
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
     <td><input type="radio" name="type" value="prod" checked></td>
     <td>prod</td>
     <td><input type="radio" name="type" value="group"></td>
     <td>group</td>
     <td><input type="radio" name="type" value="party"></td>
     <td>party</td>
     <td><input type="radio" name="type" value="board"></td>
     <td>board</td>
     <td><input type="radio" name="type" value="user"></td>
     <td>user</td>
     <td><input type="radio" name="type" value="bbs"></td>
     <td>bbs</td>
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
$query="SELECT type,img,url,alt FROM buttons ORDER BY RAND() LIMIT 1";
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
    <a href="party.php?which=<?=$latest_parties[$i]["id"]?>&when=<?=sprintf("%02d",$latest_parties[$i]["party_year"])?>"><?=strtolower(str_replace("\'", "'", htmlentities(stripslashes($latest_parties[$i]["name"]." ".sprintf("%02d",$latest_parties[$i]["party_year"])))))?></a><br />
  </td>
  <td align="right" valign="top" nowrap><? print($latest_parties[$i]["prodcount"]); ?><br /></td>
  </tr>
  <? } ?>
 <tr><td class="bottom" colspan="2"><a href="parties.php">more</a>...</td></tr>
</table>
<br />
<? endif; ?>


<? if ($user["indextopglops"]>0): ?>
<table cellspacing="1" cellpadding="2" class="box">
 <tr><th>top of the glöps</th></tr>
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
     :: <?=$submitters[$i]["glops"]?> glöps
    </td>
    </tr>
<? } ?>
<tr><td class="bottom"><a href="userlist.php?order=glops">more</a>...</td></tr>
</table>
<? endif; ?>

<table cellspacing="1" cellpadding="2" class="box">
 <tr><th>latest web tv</th></tr>
<?

	function create_webtv_cache()
	{
		//load http://www.demoscene.tv/page.php?id=172&lang=uk&vsmaction=vod_list
		//parse dtv page, get first 10 pouet ids out
		//compare to cache, if diff check db info and insert if missing demoscene.tv link
		if ($fdtv = fopen("http://www.demoscene.tv/page.php?id=172&lang=uk&vsmaction=vod_list", "r"))
		{
			$contents = '';
			while (!feof($fdtv)) {
			  $contents .= fread($fdtv, 8192);
			}
			fclose($fdtv);
			
			$pstring = 'http://www.pouet.net/prod.php?which=';
			$dstring = 'http://www.demoscene.tv/prod.php?id_prod=';
			$pos = strpos($contents, $pstring);
			while ($pos != false)
			{
				$posend = strpos($contents, '"', $pos) - ($pos + strlen($pstring));
				$pouetid = substr($contents, $pos + strlen($pstring), $posend);
				//echo "check: " . $posend . " ->" . $pouetid . "<-";
				if ($pouetid != '')
				{
					//echo "in";
					$posid = strpos($contents, $dstring, $pos + $posend + strlen($dstring));
					$posend = strpos($contents, '"', $posid) - ($posid + strlen($dstring));
					$dtvid = substr($contents, $posid + strlen($dstring), $posend);
					
					echo "p: " . $pouetid . " d: " . $dtvid . "<br />";
				}
				$pos = strpos($contents, $pstring, $pos + strlen($pstring));
				//echo "pos: " . $pos . "<-";
			}
			
		}

		
		//load http://capped.tv/rss.php
		//parse all pouet ids
		//compare to cache, if diff check db info and insert if missing capped.tv link
		if ($fcapped = fopen("http://capped.tv/rss.php", "r"))
		{
			
		}
		fclose($fcapped);
		
		//create_cache_module("webtv", "SELECT prods.id,prods.name,prods.type,prods.group1,prods.group2,prods.group3 FROM prods WHERE prods.id = ....",1);
	}





	//$timedif = @(time() - filemtime('include/stats.webtv.inc'));
	//if ($timedif < 7200) 
	create_webtv_cache(); // 7200 = every 2 hours..
	include('include/stats.webtv.inc');

for($i=0;$i<count($webtv);$i++) {
  if($i%2) {
    print("<tr bgcolor=\"#446688\"");
  } else {
    print("<tr bgcolor=\"#557799\"");
  }
  ?>
    <td width="100%" nowrap>
    <table cellspacing="0" cellpadding="0"><tr><td align="left" width="100%" valign="top" nowrap>
<?        
        if ($user["indextype"]>0):
        $typess = explode(",", $webtv[$i]["type"]);
        for($kkk=0;$kkk<count($typess);$kkk++) {
        ?><img src="gfx/types/<?=$types[$typess[$kkk]]?>" width="16" height="16" border="0" title="<?=$typess[$kkk]?>" alt="<?=$typess[$kkk]?>"><? } endif; ?>
        <a href="prod.php?which=<?=$webtv[$i]["id"]?>"><b><?=strtolower(str_replace("\'", "'", htmlentities(stripslashes($webtv[$i]["name"]))))?></b></a>
   	</td>
        <? if ($user["indexplatform"]>0): ?>
   	<td align="right" nowrap valign="top">
   	<? $platformss = explode(",", $webtv[$i]["platform"]);
   	$platformss = array_slice($platformss,0,3,true);
 	for($kkk=0;$kkk<count($platformss);$kkk++) {
        ?><img src="gfx/os/<?=$os[$platformss[$kkk]]?>" width="16" height="16" border="0" title="<?=$platformss[$kkk]?>" alt="<?=$platformss[$kkk]?>"><? } ?><br /></td><? endif; ?>
	</tr></table>
   <? if(strlen($webtv[$i]["groupname1"])>0): ?>
   :: <a href="groups.php?which=<?=$webtv[$i]["group1"]?>"><?=strtolower(str_replace("\'", "'", htmlentities(stripslashes($webtv[$i]["groupname1"]))))?></a>
   <? endif; ?>
   <? if(strlen($webtv[$i]["groupname2"])>0): ?>
   :: <a href="groups.php?which=<?=$webtv[$i]["group2"]?>"><?=strtolower(str_replace("\'", "'", htmlentities(stripslashes($webtv[$i]["groupname2"]))))?></a>
   <? endif; ?>
   <? if(strlen($webtv[$i]["groupname3"])>0): ?>
   :: <a href="groups.php?which=<?=$webtv[$i]["group3"]?>"><?=strtolower(str_replace("\'", "'", htmlentities(stripslashes($webtv[$i]["groupname3"]))))?></a>
   <? endif; ?>
  </td>
  </tr>
  <? } ?>
</table>

</td>
<td width="2%"><br /></td>
</tr></table>
<br />
<? require("include/bottom.php"); ?>