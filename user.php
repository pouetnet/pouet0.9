<?
	require("include/top.php");
	require("recaptchalib.php");

	$who = (int)$_GET["who"];

$usercustom=$user;

if(!is_numeric($who) || !$who) {
  $result = mysql_query_debug("SELECT id FROM users");
  while($tmp = mysql_fetch_row($result)) {
    $ids[]=$tmp[0];
  }
  $who=$ids[mt_rand(0,count($ids)-1)];
}


$timetest = microtime_float();

function htmlcleanonerow($inhtml){
  $inhtml= str_replace( "<", "&" . "lt;", $inhtml);
  $inhtml= str_replace( ">", "&" . "gt;", $inhtml);
  $inhtml= str_replace( "\"", "&" . "quot;", $inhtml);
  $inhtml= str_replace( "\n", " ", $inhtml);
  return $inhtml;
}


// get user data from sceneid
//debuglog(var_export($returnvalue,true));

$result = mysql_query_debug("SELECT * FROM users WHERE id=".$who);
$user = mysql_fetch_array($result);

$sceneIDData = array();
if (!$user["sceneIDData"] || (time() - strtotime($user["sceneIDLastRefresh"])) > 60 * 60 * 12)
{
  $returnvalue = $xml->parseSceneIdData("getUserInfo", array("userID" => $who));
  if ($returnvalue["returnvalue"]==35 && $user["glops"] == 0)
  {
    $r = mysql_query("select * from bbs_posts where author = ".mysql_real_escape_string($who));
    if (!mysql_num_rows($r))
    {
      echo "WARNING, this account is unverified and will get deleted.";
    }
  }
  if(is_array($returnvalue["user"])&&is_array($user)) {
    $r = $returnvalue["user"];
    $sceneIDData = $r;
  }
  mysql_query("update users set sceneIDLastRefresh = now(), sceneIDData='".mysql_real_escape_string(serialize($returnvalue["user"]))."' where id=".$who);
} else {
  $sceneIDData = unserialize( $user["sceneIDData"] );
}
unset( $sceneIDData["nickname"] );
if ($sceneIDData)
  $user = array_merge($user, $sceneIDData);


//mysql_query_debug("update users set nickname='".addslashes($user["nickname"])."' WHERE id=".$who);

//debuglog(var_export($returnvalue,true));
$time["getuserdata"] = microtime_float() - $timetest;

$timetest = microtime_float();

$query="SELECT prods.id,prods.name,prods.type,prods.group1,prods.group2,prods.group3,".
"g1.name as groupname1,g1.acronym as groupacron1,".
"g2.name as groupname2,g2.acronym as groupacron2,".
"g3.name as groupname3,g3.acronym as groupacron3 ".
" FROM users_cdcs, prods ".
" LEFT JOIN groups AS g1 ON prods.group1 = g1.id".
" LEFT JOIN groups AS g2 ON prods.group2 = g2.id".
" LEFT JOIN groups AS g3 ON prods.group3 = g3.id".
" WHERE users_cdcs.cdc=prods.id and users_cdcs.user=".$who;
$result=mysql_query_debug($query);
while($tmp=mysql_fetch_array($result)) {
  $cdc[]=$tmp;
}
for ($i=0; $i<count($cdc); $i++):
	if (strlen($cdc[$i]["groupname1"].$cdc[$i]["groupname2"].$cdc[$i]["groupname3"])>27):
		if (strlen($cdc[$i]["groupname1"])>10) $cdc[$i]["groupname1"]=$cdc[$i]["groupacron1"];
		if (strlen($cdc[$i]["groupname2"])>10) $cdc[$i]["groupname2"]=$cdc[$i]["groupacron2"];
		if (strlen($cdc[$i]["groupname3"])>10) $cdc[$i]["groupname3"]=$cdc[$i]["groupacron3"];
	endif;
endfor;

$time["cdc"] = microtime_float() - $timetest;

unset($logos);

$timetest = microtime_float();
$result=mysql_query_debug("SELECT id,file,vote_count FROM logos WHERE author1=".$user["id"]." || author2=".$user["id"]);
while($tmp=mysql_fetch_array($result)) {
  $logos[]=$tmp;
}

$result = mysql_query_debug("SELECT points FROM ud WHERE login='".$user["udlogin"]."'");
if(mysql_num_rows($result))
	$ud = round(mysql_result($result,0)/1000);

$time["logospoints"] = microtime_float() - $timetest;

$timetest = microtime_float();
$query="SELECT prods.id,prods.name,prods.type,prods.group1,prods.group2,prods.group3, ".
"g1.name as groupname1,g1.acronym as groupacron1,".
"g2.name as groupname2,g2.acronym as groupacron2,".
"g3.name as groupname3,g3.acronym as groupacron3 ".
" FROM prods ".
" LEFT JOIN groups AS g1 ON prods.group1 = g1.id".
" LEFT JOIN groups AS g2 ON prods.group2 = g2.id".
" LEFT JOIN groups AS g3 ON prods.group3 = g3.id".
" WHERE prods.added=".$user["id"]." ORDER BY prods.quand DESC LIMIT ".$usercustom["userprods"];
//$result=mysql_query_debug("SELECT prods.id,prods.name,prods.type,prods.group1,prods.group2,prods.group3 FROM prods WHERE prods.added=".$user["id"]." ORDER BY prods.quand DESC LIMIT ".$usercustom["userprods"]);
$result=mysql_query_debug($query);
while($tmp=mysql_fetch_array($result)) {
  $prods[]=$tmp;
}
for ($i=0; $i<count($prods); $i++):
	$query="select platforms.name from prods_platforms, platforms where prods_platforms.prod='".$prods[$i]["id"]."' and platforms.id=prods_platforms.platform";
	$result=mysql_query_debug($query);
	$check=0;
	$prods[$i]["platform"]="";
	while($tmp = mysql_fetch_array($result)) {
	  if ($check>0) $prods[$i]["platform"].=",";
	  $check++;
	  $prods[$i]["platform"].=$tmp["name"];
	 }
endfor;
$time["prods"] = microtime_float() - $timetest;

$timetest = microtime_float();
$result=mysql_query_debug("SELECT id,name FROM groups WHERE added=".$user["id"]." ORDER BY quand DESC LIMIT ".$usercustom["usergroups"]);
while($tmp=mysql_fetch_array($result)) {
  $groups[]=$tmp;
}

$result=mysql_query_debug("SELECT id,name FROM parties WHERE added=".$user["id"]." ORDER BY quand DESC LIMIT ".$usercustom["userparties"]);
while($tmp=mysql_fetch_array($result)) {
  $partys[]=$tmp;
}
$time["groupsparties"] = microtime_float() - $timetest;

$timetest = microtime_float();
$query="SELECT prods.id,prods.name,prods.type,prods.group1,prods.group2,prods.group3,comments.rating as rating, ".
"g1.name as groupname1,g1.acronym as groupacron1,".
"g2.name as groupname2,g2.acronym as groupacron2,".
"g3.name as groupname3,g3.acronym as groupacron3 ".
" FROM comments,prods ".
" LEFT JOIN groups AS g1 ON prods.group1 = g1.id".
" LEFT JOIN groups AS g2 ON prods.group2 = g2.id".
" LEFT JOIN groups AS g3 ON prods.group3 = g3.id".
" WHERE comments.who=".$user["id"]." and comments.which=prods.id GROUP BY prods.id ORDER BY comments.quand DESC LIMIT ".$usercustom["usercomments"];

//$result=mysql_query_debug("SELECT DISTINCT which FROM comments WHERE who=".$user["id"]." ORDER BY quand DESC LIMIT ".$usercustom["usercomments"]);
$result=mysql_query_debug($query);
while($tmp=mysql_fetch_array($result)) {
  $comments[]=$tmp;
}
for($i=0;$i<count($comments);$i++) {

	if (strlen($comments[$i]["groupname1"].$comments[$i]["groupname2"].$comments[$i]["groupname3"])>27):
		if (strlen($comments[$i]["groupname1"])>10 && $comments[$i]["groupacron1"]) $comments[$i]["groupname1"]=$comments[$i]["groupacron1"];
		if (strlen($comments[$i]["groupname2"])>10 && $comments[$i]["groupacron2"]) $comments[$i]["groupname2"]=$comments[$i]["groupacron2"];
		if (strlen($comments[$i]["groupname3"])>10 && $comments[$i]["groupacron3"]) $comments[$i]["groupname3"]=$comments[$i]["groupacron3"];
	endif;

	$query="select platforms.name from prods_platforms, platforms where prods_platforms.prod='".$comments[$i]["id"]."' and platforms.id=prods_platforms.platform";
	$result=mysql_query_debug($query);
	$check=0;
	$comments[$i]["platform"]="";
	while($tmp = mysql_fetch_array($result)) {
	  if ($check>0) $comments[$i]["platform"].=",";
	  $check++;
	  $comments[$i]["platform"].=$tmp["name"];
	 }

//  $result=mysql_query_debug("SELECT rating FROM comments WHERE which=".$tempwhich." and rating!=0 and who=".$user["id"]." LIMIT 1");
//  debuglog(mysql_error());
//  if(mysql_num_rows($result)) $comments[$i]["rating"]=mysql_result($result,0);
}
$time["comments"] = microtime_float() - $timetest;

$timetest = microtime_float();
$query="SELECT screenshots.prod,prods.id,prods.name,prods.type,prods.group1,prods.group2,prods.group3, ".
"g1.name as groupname1,g1.acronym as groupacron1,".
"g2.name as groupname2,g2.acronym as groupacron2,".
"g3.name as groupname3,g3.acronym as groupacron3 ".
" FROM screenshots,prods ".
" LEFT JOIN groups AS g1 ON prods.group1 = g1.id".
" LEFT JOIN groups AS g2 ON prods.group2 = g2.id".
" LEFT JOIN groups AS g3 ON prods.group3 = g3.id".
" WHERE screenshots.user=".$user["id"]." AND screenshots.prod=prods.id ORDER BY screenshots.added DESC LIMIT ".$usercustom["userscreenshots"];
$result=mysql_query_debug($query);
while($tmp=mysql_fetch_array($result)) {
  $screenshots[]=$tmp;
}

for ($i=0; $i<count($screenshots); $i++):
	if (strlen($screenshots[$i]["groupname1"].$screenshots[$i]["groupname2"].$screenshots[$i]["groupname3"])>27):
		if (strlen($screenshots[$i]["groupname1"])>10 && $screenshots[$i]["groupacron1"]) $screenshots[$i]["groupname1"]=$screenshots[$i]["groupacron1"];
		if (strlen($screenshots[$i]["groupname2"])>10 && $screenshots[$i]["groupacron2"]) $screenshots[$i]["groupname2"]=$screenshots[$i]["groupacron2"];
		if (strlen($screenshots[$i]["groupname3"])>10 && $screenshots[$i]["groupacron3"]) $screenshots[$i]["groupname3"]=$screenshots[$i]["groupacron3"];
	endif;

	$query="select platforms.name from prods_platforms, platforms where prods_platforms.prod='".$screenshots[$i]["id"]."' and platforms.id=prods_platforms.platform";
	$result=mysql_query_debug($query);
	$check=0;
	$screenshots[$i]["platform"]="";
	while($tmp = mysql_fetch_array($result)) {
	  if ($check>0) $screenshots[$i]["platform"].=",";
	  $check++;
	  $screenshots[$i]["platform"].=$tmp["name"];
	 }
endfor;
$time["screenshots"] = microtime_float() - $timetest;

$timetest = microtime_float();

$query="SELECT nfos.prod,prods.id,prods.name,prods.type,prods.group1,prods.group2,prods.group3, ".
"g1.name as groupname1,g1.acronym as groupacron1,".
"g2.name as groupname2,g2.acronym as groupacron2,".
"g3.name as groupname3,g3.acronym as groupacron3 ".
" FROM nfos,prods ".
" LEFT JOIN groups AS g1 ON prods.group1 = g1.id".
" LEFT JOIN groups AS g2 ON prods.group2 = g2.id".
" LEFT JOIN groups AS g3 ON prods.group3 = g3.id".
" WHERE nfos.user=".$user["id"]." AND nfos.prod=prods.id ORDER BY nfos.added DESC LIMIT ".$usercustom["usernfos"];
//"SELECT nfos.prod,prods.id,prods.name,prods.type,prods.group1,prods.group2,prods.group3 FROM nfos,prods WHERE nfos.user=".$user["id"]." AND nfos.prod=prods.id ORDER BY nfos.added DESC LIMIT ".$usercustom["usernfos"]
$result=mysql_query_debug($query);
while($tmp=mysql_fetch_array($result)) {
  $nfos[]=$tmp;
}
for ($i=0; $i<count($nfos); $i++) {
	if (strlen($nfos[$i]["groupname1"].$nfos[$i]["groupname2"].$nfos[$i]["groupname3"])>27):
		if (strlen($nfos[$i]["groupname1"])>10 && $nfos[$i]["groupacron1"]) $nfos[$i]["groupname1"]=$nfos[$i]["groupacron1"];
		if (strlen($nfos[$i]["groupname2"])>10 && $nfos[$i]["groupacron2"]) $nfos[$i]["groupname2"]=$nfos[$i]["groupacron2"];
		if (strlen($nfos[$i]["groupname3"])>10 && $nfos[$i]["groupacron3"]) $nfos[$i]["groupname3"]=$nfos[$i]["groupacron3"];
	endif;

	$query="select platforms.name from prods_platforms, platforms where prods_platforms.prod='".$nfos[$i]["id"]."' and platforms.id=prods_platforms.platform";
	$result=mysql_query_debug($query);
	$check=0;
	$nfos[$i]["platform"]="";
	while($tmp = mysql_fetch_array($result)) {
	  if ($check>0) $nfos[$i]["platform"].=",";
	  $check++;
	  $nfos[$i]["platform"].=$tmp["name"];
	 }
}

$time["nfos"] = microtime_float() - $timetest;

$query="SELECT * from bbs_topics WHERE userfirstpost=".$user["id"]." order by firstpost desc LIMIT 10";
$result=mysql_query_debug($query);
$topics=array();
while($tmp=mysql_fetch_array($result)) {
  $topics[]=$tmp;
}

$query="SELECT bbs_topics.id,bbs_topics.topic,bbs_topics.category FROM bbs_posts JOIN users ON users.id = bbs_posts.author JOIN bbs_topics ON bbs_posts.topic=bbs_topics.id WHERE users.id = ".$user["id"]." GROUP BY bbs_posts.topic ORDER BY bbs_posts.added DESC LIMIT 10";
$result=mysql_query_debug($query);
$topicposts=array();
while($tmp=mysql_fetch_array($result)) {
  $topicposts[]=$tmp;
}

$query="SELECT * from lists WHERE upkeeper=".$user["id"]." order by added desc LIMIT 10";
$result=mysql_query_debug($query);
$lists=array();
while($tmp=mysql_fetch_array($result)) {
  $lists[]=$tmp;
}

$timetest = microtime_float();

//$result=mysql_query_debug("select c2.who AS who,count(0) as c,users.nickname,users.avatar from comments c1, comments c2 left join users on users.id=c2.who where c1.rating = c2.rating and c1.rating=1 and c1.which=c2.which and c1.who=".$user["id"]." and c2.who!=".$user["id"]." group by c2.who order by c DESC LIMIT ".($usercustom["userrulez"]*4));
$result=mysql_query_debug("select c2.who AS who,count(0) as c,users.nickname,users.avatar from comments c1, comments c2 left join users on users.id=c2.who where c1.rating = c2.rating and c1.rating=1 and c1.which=c2.which and c1.who=".$user["id"]." group by c2.who order by c DESC LIMIT ".($usercustom["userrulez"]*4));
while($tmp=mysql_fetch_array($result)) {
  if ($tmp["who"]!=$user["id"])
    $rulez[$tmp["who"]]=$tmp;
}

//$result=mysql_query_debug("select c2.who AS who,count(0) as c,users.nickname,users.avatar from comments c1, comments c2 left join users on users.id=c2.who where c1.rating = c2.rating and c1.rating=-1 and c1.which=c2.which and c1.who=".$user["id"]." and c2.who!=".$user["id"]." group by c2.who order by c DESC LIMIT ".($usercustom["usersucks"]*2));
$result=mysql_query_debug("select c2.who AS who,count(0) as c,users.nickname,users.avatar from comments c1, comments c2 left join users on users.id=c2.who where c1.rating = c2.rating and c1.rating=-1 and c1.which=c2.which and c1.who=".$user["id"]." group by c2.who order by c DESC LIMIT ".($usercustom["usersucks"]*2));
while($tmp=mysql_fetch_array($result)) {
  if ($tmp["who"]!=$user["id"])
    $sucks[$tmp["who"]]=$tmp;
}

$time["rulezsucks"] = microtime_float() - $timetest;

$timetest = microtime_float();

// total thumb ups / down
$result=mysql_query_debug("SELECT rating, SUM(rating) AS total FROM comments WHERE who=".$user["id"]." GROUP BY rating");
while($tmp=mysql_fetch_array($result)) {
	if($tmp["rating"]==1) $total_ups=$tmp["total"];
	else if($tmp["rating"]==-1) $total_downs=-1*$tmp["total"];
}

// glops count
$result=mysql_query_debug("SELECT count(0) FROM prods WHERE added=".$user["id"]);
$nbprods = mysql_result($result,0);
$result=mysql_query_debug("SELECT count(0) FROM groups WHERE added=".$user["id"]);
$nbgroups = mysql_result($result,0);
$result=mysql_query_debug("SELECT count(0) FROM parties WHERE added=".$user["id"]);
$nbparties = mysql_result($result,0);
$result=mysql_query_debug("SELECT count(0) FROM screenshots WHERE user=".$user["id"]);
$nbscreenshots = mysql_result($result,0);
$result=mysql_query_debug("SELECT count(0) FROM nfos WHERE user=".$user["id"]);
$nbnfos = mysql_result($result,0);
$result=mysql_query_debug("SELECT COUNT(DISTINCT which) FROM comments WHERE who=".$user["id"]);
$nbcomments = mysql_result($result,0);
$result=mysql_query_debug("SELECT COUNT(*) FROM bbs_topics WHERE userfirstpost=".$user["id"]);
$nbtopics = mysql_result($result,0);
$result=mysql_query_debug("SELECT COUNT(*) FROM bbs_posts WHERE author=".$user["id"]);
$nbtopicposts = mysql_result($result,0);
$result=mysql_query_debug("SELECT COUNT(*) FROM lists WHERE upkeeper=".$user["id"]);
$nblists = mysql_result($result,0);

// average rating
$query="SELECT SUM(rating)/count(0) FROM comments WHERE who=".$user["id"];
$result=mysql_query_debug($query);
$avg_rating=mysql_result($result,0);

$time["averages"] = microtime_float() - $timetest;

//$glops=4*count($prods)+3*count($groups)+2*count($partys)+count($comments)+$ud;

// Count only the logos of this user which have been voted in by the pouet users
$nb_good_logos = 0;
if ($logos) foreach($logos as $l)
{
	if($l['vote_count'] > 0)
	{
		$nb_good_logos++;
	}
}
$glops=20*$nb_good_logos+2*$nbprods+$nbgroups+$nbparties+$nbscreenshots+$nbnfos+$ud+$nbcomments;

$timetest = microtime_float();
if($user["id"]==$_SESSION["SCENEID_ID"])
{
	mysql_query_debug("UPDATE users SET glops=".$glops." WHERE id=".$user["id"]);
}
$time["update"] = microtime_float() - $timetest;

//debuglog(var_export($time,true));
?>
<br>
<? if ($user["level"]): ?>
<table bgcolor="#000000" cellspacing="1" cellpadding="0" border="0">
 <tr>
  <td>
   <table bgcolor="#000000" cellspacing="1" cellpadding="2" border="0">
    <tr bgcolor="#224488">
     <td colspan="2">
      <table cellspacing="0" cellpadding="0" border="0" width="100%">
       <tr>
        <td>
         <img src="avatars/<? print($user["avatar"]); ?>" width="16" height="16"><br>
        </td>
        <td>&nbsp;</td>
        <td nowrap>
         <? if(strlen(trim($user["nickname"]))): ?>
          <b><? print($user["nickname"]); ?></b> information<br>
         <? else: ?>
          <b><? print($user["login"]); ?></b> information<br>
         <? endif; ?>
        </td>
        <td width="100%" align="right">
         <b><? print($glops); ?></b> <font color="#9999AA">gl&ouml;ps</font><br>
        </td>
       </tr>
      </table>
     </td>
    </tr>
    <tr bgcolor="#446688">
     <td valign="top" nowrap>
      <table cellspacing="0" cellpadding="0" border="0" width="100%">
       <tr>
         <td>level:<br></td>
	 <td><? print($user["level"]); ?><br></td>
	<? if(strlen($user["nickname"])): ?>
		<tr>
		 <td>nickname:<br></td>
		 <td>
		  <? print(utf8_decode($user["nickname"])); ?><br>
		 </td>
		</tr>
	<? endif; ?>
<?
if (($SESSION_LEVEL=='administrator' || $SESSION_LEVEL=='moderator' || $SESSION_LEVEL=='gloperator')) {
	 if(strlen($user["login"])): ?>
		<tr>
		 <td>login:<br></td>
		 <td>
		  <? print($user["login"]); ?><br>
		 </td>
		</tr>
	<? endif;
}
?>
	<? if(strlen($user["firstname"])): ?>
		<tr>
		 <td>firstname:<br></td>
		 <td>
		  <? print(utf8_decode($user["firstname"])); ?><br>
		 </td>
		</tr>
	<? endif; ?>
	<? if(strlen($user["lastname"])): ?>
   		 <tr>
		  <td>lastname:<br></td>
		  <td>
		   <? print(utf8_decode($user["lastname"])); ?><br>
		  </td>
		 </tr>
	<? endif; ?>
	<? if(strlen($user["country"])): ?>
		<tr>
		 <td>country:<br></td>
		 <td>
		  <? print($user["country"]); ?><br>
		 </td>
		</tr>
	<? endif; ?>
   	<? if(strlen($user["email"])): ?>
   		<tr>
   		 <td>
		email:<br>
		</td>
		 <td>
		<?
		if(($returnvalue["user"]["emailhidden"]!="no") && !($SESSION_LEVEL=='administrator' || $SESSION_LEVEL=='moderator' || $SESSION_LEVEL=='gloperator')):
			echo '<font color="#9999AA">hidden</font><br>';
		else:
      echo recaptcha_mailhide_html(RECAPTCHA_MAILHIDE_PUB_KEY, RECAPTCHA_MAILHIDE_PRIV_KEY, $user["email"]);
		endif;
		?>
		 </td>
		 </tr>
	<? endif; ?>
	<? if(( ($SESSION_LEVEL=='administrator' || $SESSION_LEVEL=='moderator' || $SESSION_LEVEL=='gloperator' || $glops>10))&&(strlen($user["url"])>0)&&($user["url"]!="http://")): ?>
		 <tr>
		  <td>
		   website:<br>
		  </td>
		 <td>
		  <a href="<? print($user["url"]); ?>"><? print($user["url"]); ?></a><br>
		 </td>
		</tr>
         <? endif; ?>
         <? if($user["im_id"] && $user["im_type"]): ?>
         	<tr>
		 <td><?=$user["im_type"]?>:<br></td>
		 <td nowrap>
		 <?php
			if ($user["im_type"] == "MSN")
			{
		    	echo recaptcha_mailhide_html(RECAPTCHA_MAILHIDE_PUB_KEY, RECAPTCHA_MAILHIDE_PRIV_KEY, $user["im_id"]);
			}
			else
			{
				echo $user["im_id"];
			}
		 ?>
            	 </td>
		</tr>
         <? endif; ?>
         <? if($user["slengpung"]): ?>
         	<tr>
		 <td>slengpung:<br></td>
		 <td>
            		<a href="http://www.slengpung.com/?userid=<?=$user["slengpung"]?>" target=_blank>pictures</a><br></td>
		 </td>
		</tr>
         <? endif; ?>
         <? if ($user["csdb"]): ?>
         	<tr>
		 <td>csdb:<br></td>
		 <td>
            		<a href="http://noname.c64.org/csdb/scener/?id=<?=$user["csdb"]?>" target=_blank>profile</a><br></td>
		 </td>
		</tr>
         <? endif; ?>
         <? if ($user["zxdemo"]): ?>
         	<tr>
		 <td>zxdemo:<br></td>
		 <td>
            		<a href="http://zxdemo.org/author.php?id=<?=$user["zxdemo"]?>" target=_blank>profile</a><br></td>
		 </td>
		</tr>
         <? endif; ?>

         <? for($i=0;$i<count($cdc);$i++): ?>
         	<tr>
		 <td>
            	  coup de coeur:<br>
		 </td>
		 <td nowrap>
			<a href="prod.php?which=<? print($cdc[$i]["id"]."\">".stripslashes($cdc[$i]["name"])); ?></a>
		         <?
		             if ($cdc[$i]["groupname1"]) print(" by <a href=\"groups.php?which=".stripslashes($cdc[$i]["group1"])."\">".stripslashes($cdc[$i]["groupname1"])."</a>");
		             if ($cdc[$i]["groupname2"]) print(" and <a href=\"groups.php?which=".stripslashes($cdc[$i]["group2"])."\">".stripslashes($cdc[$i]["groupname2"])."</a>");
		             if ($cdc[$i]["groupname3"]) print(" and <a href=\"groups.php?which=".stripslashes($cdc[$i]["group3"])."\">".stripslashes($cdc[$i]["groupname3"])."</a>");
		          ?><br>
		 </td>
		</tr>
	<? endfor; ?>
	   	<tr>
		 <td>average rating:<br></td>
		 <td>
			<?
			if($avg_rating>0)
				$thumbgfx="gfx/rulez.gif";
			elseif($avg_rating==0)
				$thumbgfx="gfx/isok.gif";
			else
				$thumbgfx="gfx/sucks.gif";
			 ?>
			 <img src="<?=$thumbgfx?>" width="16" height="16" border="0" alt="." align="left">&nbsp;<?=$avg_rating?><br>
		 </td>
 		</tr>
       		<tr>
		 <td colspan="2"><b>[</b><a href="demoblog.php?who=<? print($who);?>">demoblog view</a><b>]</b><br />
		 <? if ($SESSION_LEVEL=='administrator' || $SESSION_LEVEL=='moderator' || $SESSION_LEVEL=='gloperator'): ?>
		 <b>[</b><a href="demoglop.php?who=<? print($who);?>&amp;com=50">demoglop view</a><b>]</b><br />
		 <b>[</b><a href="/rulez/ip.php?user=<? print($who);?>">ip check</a><b>]</b><br />
		 <? endif; ?>
		 </td>
       		</tr>
	   </table>
	</td>
        <td align="right" nowrap>
         <img src="avatars/<? print($user["avatar"]); ?>" width="160" height="160" class='largeavatar'><br>
        </td>
       </tr>

    <? if(count($logos)>0): ?>
    <tr bgcolor="#224488">
     <td colspan="2">
      added logos <font color="#9999AA"><? print($nb_good_logos." x 20 = ".($nb_good_logos*20)) ?> gl&ouml;ps (downvoted logos are shown but are not rewarded)</font><br>
     </td>
    </tr>
    <? if ($usercustom["userlogos"]>0): ?>
    <? for($i=0;$i<count($logos);$i++): ?>
	<? $logos[$i]['size'] = GetImageSize('gfx/logos/'.$logos[$i]['file']); ?>
    <tr>
     <td bgcolor="#3A6EA5" align="center" colspan="2">
      <img src="gfx/logos/<?=$logos[$i]["file"]?>" <?=$logos[$i]['size'][3]?> border="0" alt="<?=$user["nickname"]?> have done this nice logo for pou?t.net !"><br>
     </td>
    </tr>
    <? endfor; ?>
    <? endif; ?>
    <? endif; ?>

    <? if(count($prods)>0): ?>
    <tr bgcolor="#224488">
     <td colspan="2">
      latest added prods <font color="#9999AA"><? print($nbprods." x 2 = ".($nbprods*2)) ?> gl&ouml;ps</font><br>
     </td>
    </tr>
    <? if ($usercustom["userprods"]>0): ?>
    <? for($i=0;$i<count($prods);$i++): ?>
    <tr bgcolor="#446688">
     <td colspan="2">
      <table cellspacing="0" cellpadding="0" border="0">
       <tr>
        <td valign="top" nowrap>
        <?
         $typess = explode(",", $prods[$i]["type"]);
         for($kkk=0;$kkk<count($typess);$kkk++) { ?><a href="prodlist.php?type[]=<? print($typess[$kkk]); ?>"><img src="gfx/types/<? print($types[$typess[$kkk]]); ?>" width="16" height="16" border="0" title="<? print($typess[$kkk]); ?>" alt="<? print($typess[$kkk]); ?>"></a><? } ?><br />
        </td>
        <td>&nbsp;</td>
        <td width="100%">
         <a href="prod.php?which=<? print($prods[$i]["id"]); ?>">
          <? print(stripslashes($prods[$i]["name"])); ?>
         </a>
         <?
             if ($prods[$i]["groupname1"]) print(" by <a href=\"groups.php?which=".stripslashes($prods[$i]["group1"])."\">".stripslashes($prods[$i]["groupname1"])."</a>");
             if ($prods[$i]["groupname2"]) print(" and <a href=\"groups.php?which=".stripslashes($prods[$i]["group2"])."\">".stripslashes($prods[$i]["groupname2"])."</a>");
             if ($prods[$i]["groupname3"]) print(" and <a href=\"groups.php?which=".stripslashes($prods[$i]["group3"])."\">".stripslashes($prods[$i]["groupname3"])."</a>");
          ?><br />
        </td>
        <td align="right" valign="top" nowrap>
        <? $platformss = explode(",", $prods[$i]["platform"]);
         for($kkk=0;$kkk<count($platformss);$kkk++) { ?><a href="prodlist.php?platform[]=<? print($platformss[$kkk]); ?>"><img src="gfx/os/<? print($os[$platformss[$kkk]]); ?>" width="16" height="16" border="0" title="<? print($platformss[$kkk]); ?>" alt="<? print($platformss[$kkk]); ?>"></a><? } ?><br />
        </td>
       </tr>
      </table>
     </td>
    </tr>
    <? endfor; ?>
    <? endif; ?>
    <? endif; ?>

    <? if(count($groups)>0): ?>
    <tr bgcolor="#224488">
     <td colspan="2">
      latest added groups <font color="#9999AA"><? print($nbgroups); ?> gl&ouml;ps</font><br>
     </td>
    </tr>
    <? if ($usercustom["usergroups"]>0): ?>
    <? for($i=0;$i<count($groups);$i++): ?>
    <tr bgcolor="#446688">
     <td colspan="2">
      <a href="groups.php?which=<? print($groups[$i]["id"]); ?>"><? print($groups[$i]["name"]); ?></a><br>
     </td>
    </tr>
    <? endfor; ?>
    <? endif; ?>
    <? endif; ?>

    <? if(count($partys)>0): ?>
    <tr bgcolor="#224488">
     <td colspan="2">
      latest added parties <font color="#9999AA"><? print($nbparties); ?> gl&ouml;ps</font><br>
     </td>
    </tr>
    <? if ($usercustom["userparties"]>0): ?>
    <? for($i=0;$i<count($partys);$i++): ?>
    <tr bgcolor="#446688">
     <td colspan="2">
      <? printf("<a href='party.php?which=%d'>%s</a>",$partys[$i]["id"],$partys[$i]["name"]); ?><br>
     </td>
    </tr>
    <? endfor; ?>
    <? endif; ?>
    <? endif; ?>

    <? if(count($screenshots)>0): ?>
    <tr bgcolor="#224488">
     <td colspan="2">
      latest added screenshots <font color="#9999AA"><? print($nbscreenshots); ?> gl&ouml;ps</font><br>
     </td>
    </tr>
    <? if ($usercustom["userscreenshots"]>0): ?>
    <? for($i=0;$i<count($screenshots);$i++): ?>
    <tr bgcolor="#446688">
     <td colspan="2">
      <table cellspacing="0" cellpadding="0" border="0">
       <tr>
        <td valign="top" nowrap>
        <? $typess = explode(",", $screenshots[$i]["type"]);
         for($kkk=0;$kkk<count($typess);$kkk++) { ?><a href="prodlist.php?type[]=<? print($typess[$kkk]); ?>"><img src="gfx/types/<? print($types[$typess[$kkk]]); ?>" width="16" height="16" border="0" title="<? print($typess[$kkk]); ?>" alt="<? print($typess[$kkk]); ?>"></a><? } ?><br />
        </td>
        <td>&nbsp;</td>
        <td width="100%">
         <a href="prod.php?which=<? print($screenshots[$i]["id"]); ?>">
          <? print(stripslashes($screenshots[$i]["name"])); ?>
         </a>
         <?
             if ($screenshots[$i]["groupname1"]) print(" by <a href=\"groups.php?which=".stripslashes($screenshots[$i]["group1"])."\">".stripslashes($screenshots[$i]["groupname1"])."</a>");
             if ($screenshots[$i]["groupname2"]) print(" and <a href=\"groups.php?which=".stripslashes($screenshots[$i]["group2"])."\">".stripslashes($screenshots[$i]["groupname2"])."</a>");
             if ($screenshots[$i]["groupname3"]) print(" and <a href=\"groups.php?which=".stripslashes($screenshots[$i]["group3"])."\">".stripslashes($screenshots[$i]["groupname3"])."</a>");
          ?><br />
        </td>
        <td align="right" valign="top" nowrap>
        <? $platformss = explode(",", $screenshots[$i]["platform"]);
         for($kkk=0;$kkk<count($platformss);$kkk++) { ?><a href="prodlist.php?platform[]=<? print($platformss[$kkk]); ?>"><img src="gfx/os/<? print($os[$platformss[$kkk]]); ?>" width="16" height="16" border="0" title="<? print($platformss[$kkk]); ?>" alt="<? print($platformss[$kkk]); ?>"></a><? } ?><br />
         </td>
       </tr>
      </table>
     </td>
    </tr>
    <? endfor; ?>
    <? endif; ?>
    <? endif; ?>

    <? if(count($nfos)>0): ?>
    <tr bgcolor="#224488">
     <td colspan="2">
      latest added nfos <font color="#9999AA"><? print($nbnfos); ?> gl&ouml;ps</font><br>
     </td>
    </tr>
    <? if ($usercustom["usernfos"]>0): ?>
    <? for($i=0;$i<count($nfos);$i++): ?>
    <tr bgcolor="#446688">
     <td colspan="2">
      <table cellspacing="0" cellpadding="0" border="0">
       <tr>
        <td valign="top" nowrap>
        <? $typess = explode(",", $nfos[$i]["type"]);
         for($kkk=0;$kkk<count($typess);$kkk++) { ?><a href="prodlist.php?type[]=<? print($typess[$kkk]); ?>"><img src="gfx/types/<? print($types[$typess[$kkk]]); ?>" width="16" height="16" border="0" title="<? print($typess[$kkk]); ?>" alt="<? print($typess[$kkk]); ?>"></a><? } ?><br />
        </td>
        <td valign="top" nowrap>&nbsp;</td>
        <td width="100%">
         <a href="prod.php?which=<? print($nfos[$i]["id"]); ?>">
          <? print(stripslashes($nfos[$i]["name"])); ?>
         </a>
         <?
             if ($nfos[$i]["groupname1"]) print(" by <a href=\"groups.php?which=".stripslashes($nfos[$i]["group1"])."\">".stripslashes($nfos[$i]["groupname1"])."</a>");
             if ($nfos[$i]["groupname2"]) print(" and <a href=\"groups.php?which=".stripslashes($nfos[$i]["group2"])."\">".stripslashes($nfos[$i]["groupname2"])."</a>");
             if ($nfos[$i]["groupname3"]) print(" and <a href=\"groups.php?which=".stripslashes($nfos[$i]["group3"])."\">".stripslashes($nfos[$i]["groupname3"])."</a>");
          ?><br>
        </td>
        <td align="right" valign="top" nowrap>
        <? $platformss = explode(",", $nfos[$i]["platform"]);
         for($kkk=0;$kkk<count($platformss);$kkk++) { ?><a href="prodlist.php?platform[]=<? print($platformss[$kkk]); ?>"><img src="gfx/os/<? print($os[$platformss[$kkk]]); ?>" width="16" height="16" border="0" title="<? print($platformss[$kkk]); ?>" alt="<? print($platformss[$kkk]); ?>"></a><? } ?><br />
         </td>
       </tr>
      </table>
     </td>
    </tr>
    <? endfor; ?>
    <? endif; ?>
    <? endif; ?>

    <? if(count($comments)>0): ?>
    <tr bgcolor="#224488">
     <td colspan="2">
      latest 1st comments <font color="#9999AA"><? print($nbcomments); ?> gl&ouml;ps</font><br>
     </td>
    </tr>
    <? if ($usercustom["usercomments"]>0): ?>
    <? for($i=0;$i<count($comments);$i++): ?>
    <tr bgcolor="#446688">
     <td colspan="2">
      <table cellspacing="0" cellpadding="0" border="0">
       <tr>
        <td valign="top" nowrap>
        <?
             switch((int)($comments[$i]["rating"]))
             {
             	case 1: print("<img src=\"gfx/rulez.gif\">");
             		break;
             	case -1: print("<img src=\"gfx/sucks.gif\">");
             		break;
             	default: print("<img src=\"gfx/isok.gif\">");
             }
        ?>
        </td>
        <td valign="top">&nbsp;</td>
        <td valign="top" nowrap>
        <? $typess = explode(",", $comments[$i]["type"]);
         for($kkk=0;$kkk<count($typess);$kkk++) { ?><a href="prodlist.php?type[]=<? print($typess[$kkk]); ?>"><img src="gfx/types/<? print($types[$typess[$kkk]]); ?>" width="16" height="16" border="0" title="<? print($typess[$kkk]); ?>" alt="<? print($typess[$kkk]); ?>"></a><? } ?><br />
        </td>
        <td valign="top">&nbsp;</td>
        <td width="100%" valign="top">
         <a href="prod.php?which=<? print($comments[$i]["id"]); ?>">
         <? print(stripslashes($comments[$i]["name"])); ?></a>
         <?
             if ($comments[$i]["groupname1"]) print(" by <a href=\"groups.php?which=".stripslashes($comments[$i]["group1"])."\">".stripslashes($comments[$i]["groupname1"])."</a>");
             if ($comments[$i]["groupname2"]) print(" and <a href=\"groups.php?which=".stripslashes($comments[$i]["group2"])."\">".stripslashes($comments[$i]["groupname2"])."</a>");
             if ($comments[$i]["groupname3"]) print(" and <a href=\"groups.php?which=".stripslashes($comments[$i]["group3"])."\">".stripslashes($comments[$i]["groupname3"])."</a>");
             ?><br />
        </td>
        <td align="right" valign="top" nowrap>
        <? $platformss = explode(",", $comments[$i]["platform"]);
         for($kkk=0;$kkk<count($platformss);$kkk++) { ?><a href="prodlist.php?platform[]=<? print($platformss[$kkk]); ?>"><img src="gfx/os/<? print($os[$platformss[$kkk]]); ?>" width="16" height="16" border="0" title="<? print($platformss[$kkk]); ?>" alt="<? print($platformss[$kkk]); ?>"></a><? } ?><br />
         </td>
       </tr>
      </table>
     </td>
    </tr>
    <? endfor; ?>
    <? endif; ?>
    <? endif; ?>

    <? if ($usercustom["userrulez"]>0): ?>
    <? if(count($rulez)>0): ?>
    <tr bgcolor="#224488">
     <td colspan="2">
      top thumb up agreers (total <img src="gfx/rulez.gif"> <?=$total_ups?>)<br>
     </td>
    </tr>
    <?
		$i=0;
		while(list($k,$v)=each($rulez))
    {
			if($i==0) $str = $rulez[$k]["who"];
			else $str .= ", ".$rulez[$k]["who"];
			$i++;
    }
		reset($rulez);
    $sql = "SELECT who, count(1) AS count FROM comments WHERE who IN (".$str.") AND rating=1 GROUP BY who LIMIT 0,".($usercustom["userrulez"]*4);
		$result=mysql_query_debug($sql);

		while($tmp=mysql_fetch_array($result)) {
			$total_ups_others[$tmp["who"]] = ($rulez[$tmp["who"]]["c"]/(($total_ups+$tmp["count"])/2));
		}

    arsort($total_ups_others);
		$i=0;
    while(list($k,$v)=each($total_ups_others)) { ?>
    <tr bgcolor="#446688">
     <td colspan="2">
      <table cellspacing="0" cellpadding="0" border="0">
       <tr>
        <td>
         <a href="user.php?who=<?=$rulez[$k]["who"]?>"><img src="avatars/<?=$rulez[$k]["avatar"]?>" width="16" height="16" border="0" title="<?=$rulez[$k]["nickname"]?>"></a>
        </td>
        <td>&nbsp;</td>
        <td>
         <a href="user.php?who=<?=$rulez[$k]["who"]?>"><?=$rulez[$k]["nickname"]?></a>
        </td>
        <td>&nbsp;</td>
        <td>
        <? printf($rulez[$k]["c"]." (%.2f)", $v); ?></td>
       </tr>
      </table>
     </td>
    </tr>
    <?
    if($i>=$usercustom["userrulez"]) break;
    $i++;
    } ?>
    <? endif; ?>
    <? endif; ?>



    <? if ($usercustom["usersucks"]>0): ?>
    <? if(count($sucks)>0): ?>
    <tr bgcolor="#224488">
     <td colspan="2">
      top thumb down agreers (total <img src="gfx/sucks.gif"> <?=$total_downs?>)<br/>
     </td>
    </tr>
    <?
		$i=0;
		while(list($k,$v)=each($sucks))
    {
			if($i==0) $str = $sucks[$k]["who"];
			else $str .= ", ".$sucks[$k]["who"];
			$i++;
    }
		reset($sucks);
    $sql = "SELECT who, count(1) AS count FROM comments WHERE who IN (".$str.") AND rating=-1 GROUP BY who LIMIT 0,".($usercustom["usersucks"]*2);
		$result=mysql_query_debug($sql);

		while($tmp=mysql_fetch_array($result)) {
			$total_dws_others[$tmp["who"]] = ($sucks[$tmp["who"]]["c"]/(($total_downs+$tmp["count"])/2));
		}

    arsort($total_dws_others);
		$i=0;
    while(list($k,$v)=each($total_dws_others)) { ?>
    <tr bgcolor="#446688">
     <td colspan="2">
      <table cellspacing="0" cellpadding="0" border="0">
       <tr>
        <td>
         <a href="user.php?who=<?=$sucks[$k]["who"]?>"><img src="avatars/<?=$sucks[$k]["avatar"]?>" width="16" height="16" border="0" title="<?=$sucks[$k]["nickname"]?>"></a>
        </td>
        <td>&nbsp;</td>
        <td>
         <a href="user.php?who=<?=$sucks[$k]["who"]?>"><?=$sucks[$k]["nickname"]?></a>
        </td>
        <td>&nbsp;</td>
        <td>
        <? printf($sucks[$k]["c"]." (%.2f)", $v); ?></td>
       </tr>
      </table>
     </td>
    </tr>
    <?
    if($i>=$usercustom["usersucks"]) break;
    $i++;
    } ?>
    <? endif; ?>
    <? endif; ?>


    <?
    if(count($lists)>0): ?>
    <tr bgcolor="#224488">
     <td colspan="2">
      lists kept up <font color="#9999AA"><? print($nblists); ?> in total</font>
     </td>
    </tr>
    <?
    while(list($k,$v)=each($lists)) { ?>
    <tr bgcolor="#446688">
     <td colspan="2">
       <a href="lists.php?which=<?=$lists[$k]["id"]?>"><? print(htmlcleanonerow($lists[$k]["name"])); ?></a>
     </td>
    </tr>
    <?
    } ?>
    <? endif; ?>

    <?
    if(count($topics)>0): ?>
    <tr bgcolor="#224488">
     <td colspan="2">
      bbs topics opened <font color="#9999AA"><? print($nbtopics); ?> in total</font>
     </td>
    </tr>
    <?
    while(list($k,$v)=each($topics)) { ?>
    <tr bgcolor="#446688">
     <td colspan="2">
       <a href="topic.php?which=<?=$topics[$k]["id"]?>"><? print(htmlcleanonerow($topics[$k]["topic"])); ?></a> (<?=$thread_categories[$topics[$k]["category"]]?>)
     </td>
    </tr>
    <?
    } ?>
    <? endif; ?>

    <?
    if(count($topicposts)>0): ?>
    <tr bgcolor="#224488">
     <td colspan="2">
      bbs posts added <font color="#9999AA"><? print($nbtopicposts); ?> in total</font>
     </td>
    </tr>
    <?
    while(list($k,$v)=each($topicposts)) { ?>
    <tr bgcolor="#446688">
     <td colspan="2">
       <a href="topic.php?which=<?=$topicposts[$k]["id"]?>"><? print(htmlcleanonerow($topicposts[$k]["topic"])); ?></a> (<?=$thread_categories[$topicposts[$k]["category"]]?>)
     </td>
    </tr>
    <?
    } ?>
    <? endif; ?>


	<tr bgcolor="#6688AA">
     <td align="right" colspan="2">
      account created on the <? print($user["quand"]); ?><br>
     </td>
    </tr>
   </table>
  </td>
 </tr>
</table>
<? else: ?>
	<center><? print("aiiiiiiiiii cookiiieee\n la cuenta de este utilizador ha sido comida de una langosta ciega guapa<br />*clicki clicki clacki* (\/) - - (\/) *clacki clacki clicki*<br />ciega ciega ciega frikki!!"); ?></center>
<? endif; ?>
<br>
<?
$user["bottombar"]=$usercustom["bottombar"];
 require("include/bottom.php"); ?>
