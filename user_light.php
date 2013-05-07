<?
	require("include/top.php");

	$who = $_GET["who"];

$usercustom=$user;
//var_dump($usercustom);

if(!is_numeric($who)) {
  $result = mysql_query_debug("SELECT id FROM users");
  while($tmp = mysql_fetch_row($result)) {
    $ids[]=$tmp[0];
  }
  $who=$ids[mt_rand(0,count($ids)-1)];
}

function htmlcleanonerow($inhtml){
  $inhtml= str_replace( "<", "&" . "lt;", $inhtml);
  $inhtml= str_replace( ">", "&" . "gt;", $inhtml);
  $inhtml= str_replace( "\"", "&" . "quot;", $inhtml);
  $inhtml= str_replace( "\n", " ", $inhtml);
  return $inhtml;
}


$timetest = microtime_float();



$result = mysql_query_debug("SELECT * FROM users WHERE id=".$who);
$user = mysql_fetch_array($result);

$sceneIDData = array();
if (!$user["sceneIDData"] || (time() - strtotime($user["sceneIDLastRefresh"])) > 60 * 60 * 12)
{
  $returnvalue = $xml->parseSceneIdData("getUserInfo", array("userID" => $who));

  if(is_array($returnvalue["user"])&&is_array($user)) {
    $r = $returnvalue["user"];
    $sceneIDData = $r;
  }
  mysql_query("update users set sceneIDLastRefresh = now(), sceneIDData='".mysql_real_escape_string(serialize($returnvalue["user"]))."' where id=".$who);
} else {
  $sceneIDData = unserialize( $user["sceneIDData"] );
}
unset( $sceneIDData["nickname"] );
$user = array_merge($user, $sceneIDData);



debuglog(var_export($returnvalue,true));
$time["getuserdata"] = microtime_float() - $timetest;

$timetest = microtime_float();

// total thumb ups / down
$result=mysql_query_debug("SELECT rating, SUM(rating) AS total FROM comments WHERE who=".$user["id"]." GROUP BY rating");
while($tmp=mysql_fetch_array($result)) {
	if($tmp["rating"]==1) $total_ups=$tmp["total"];
	else if($tmp["rating"]==-1) $total_downs=-1*$tmp["total"];
}

// glöps count
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

// average rating
$query="SELECT SUM(rating)/count(0) FROM comments WHERE who=".$user["id"];
$result=mysql_query_debug($query);
$avg_rating=mysql_result($result,0);

$time["averages"] = microtime_float() - $timetest;

//$glops=4*count($prods)+3*count($groups)+2*count($partys)+count($comments)+$ud;

// Count only the logos of this user which have been voted in by the pouet users
$result=mysql_query_debug("SELECT count(0) FROM logos WHERE author1=".$user["id"]." || author2=".$user["id"]);
$nblogos = mysql_result($result,0);
$result=mysql_query_debug("SELECT count(0) FROM logos WHERE vote_count>0 and (author1=".$user["id"]." || author2=".$user["id"].")");
$nb_good_logos = mysql_result($result,0);
/*
$nb_good_logos = 0;
if ($logos) foreach($logos as $l)
{
	if($l['vote_count'] > 0)
	{
		$nb_good_logos++;
	}
}
*/
$glops=20*$nb_good_logos+2*$nbprods+$nbgroups+$nbparties+$nbscreenshots+$nbnfos+$ud+$nbcomments;

$timetest = microtime_float();
if($user["id"]==$_SESSION["SCENEID_ID"])
{
	mysql_query_debug("UPDATE users SET glops=".$glops." WHERE id=".$user["id"]);
}
$time["update"] = microtime_float() - $timetest;

debuglog(var_export($time,true));
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
         <b><? print($glops); ?></b> <font color="#9999AA">glöps</font><br>
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
		  <? print($user["nickname"]); ?><br>
		 </td>
		</tr>
	<? endif; ?>
	<? if(strlen($user["firstname"])): ?>
		<tr>
		 <td>firstname:<br></td>
		 <td>
		  <? print($user["firstname"]); ?><br>
		 </td>
		</tr>
	<? endif; ?>
	<? if(strlen($user["lastname"])): ?>
   		 <tr>
		  <td>lastname:<br></td>
		  <td>
		   <? print($user["lastname"]); ?><br>
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
		<? if(session_is_registered("SESSION")&&strcasecmp($xml->hidden, "yes")): ?>
			<a href="mailto:<? print($user["email"]); ?>"><? print($user["email"]); ?></a><br>
		<? else: ?>
			<font color="#9999AA">hidden</font><br>
		<? endif; ?>
		 </td>
		 </tr>
	<? endif; ?>
	<? if((strlen($user["url"])>0)&&($user["url"]!="http://")): ?>
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
		 <? print($user["im_id"]."<br>"); ?>
            	 </td>
		</tr>
         <? endif; ?>
         <? if($user["ojuice"]): ?>
         	<tr>
		 <td>ojuice:<br></td>
		 <td>
			<? print("<a href=\"http://www.ojuice.net/".$user["ojuice"]."/nick.htm\" target=_blank>profile</a><br>"); ?>
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
		 <td colspan="2"><b>[</b><a href="demoblog.php?who=<? print($who);?>">demoblog view</a><b>]</b><br /></td>
       		</tr>
	   </table>
	</td>
        <td align="right" nowrap>
         <img src="avatars/<? print($user["avatar"]); ?>" width="160" height="160"><br>
        </td>
       </tr>

<?
if($_GET["show"]) {
  switch($_GET["show"]) {
    case "prods": {
?>
    <tr bgcolor="#224488">
     <td colspan="2">
      latest added prods <font color="#9999AA"><? print($nbprods." x 2 = ".($nbprods*2)) ?> glöps</font>
     </td>
    </tr>
<?
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
    while($prods = mysql_fetch_assoc($result)) {
    ?>
    <tr bgcolor="#446688">
     <td colspan="2">
      <table cellspacing="0" cellpadding="0" border="0">
       <tr>
        <td valign="top" nowrap>
        <? 
         $typess = explode(",", $prods["type"]);
         for($kkk=0;$kkk<count($typess);$kkk++) {
         ?><a href="prodlist.php?type[]=<?=$typess[$kkk]?>"><img src="gfx/types/<?=$types[$typess[$kkk]]?>" border="0" title="<?=$typess[$kkk]?>" alt="<?=$typess[$kkk]?>"></a><?
         } ?><br />
        </td>
        <td>&nbsp;</td>
        <td width="100%">
         <a href="prod.php?which=<? print($prods["id"]); ?>">
          <? print(stripslashes($prods["name"])); ?>
         </a>
         <?
             if ($prods["groupname1"]) print(" by <a href=\"groups.php?which=".stripslashes($prods["group1"])."\">".stripslashes($prods["groupname1"])."</a>");
             if ($prods["groupname2"]) print(" and <a href=\"groups.php?which=".stripslashes($prods["group2"])."\">".stripslashes($prods["groupname2"])."</a>");
             if ($prods["groupname3"]) print(" and <a href=\"groups.php?which=".stripslashes($prods["group3"])."\">".stripslashes($prods["groupname3"])."</a>");
          ?><br />
        </td>
        <td align="right" valign="top" nowrap>
        <? $platformss = explode(",", $prods["platform"]);
         for($kkk=0;$kkk<count($platformss);$kkk++) { ?><a href="prodlist.php?platform[]=<? print($platformss[$kkk]); ?>"><img src="gfx/os/<? print($os[$platformss[$kkk]]); ?>" width="16" height="16" border="0" title="<? print($platformss[$kkk]); ?>" alt="<? print($platformss[$kkk]); ?>"></a><? } ?><br />
        </td>
       </tr>
      </table>
     </td>
    </tr>
<?   
    }
    }
  }
} else {
?>

    <? if($nblogos): ?>
    <tr bgcolor="#224488">
     <td colspan="2">
      added <?=$nblogos?> logos <font color="#9999AA"><? print($nb_good_logos." x 20 = ".($nb_good_logos*20)) ?> glöps</font><br>
     </td>
    </tr>
    <? endif; ?>

    <? if($nbprods>0): ?>
    <tr bgcolor="#224488">
     <td colspan="2">
      latest added prods <font color="#9999AA"><? print($nbprods." x 2 = ".($nbprods*2)) ?> glöps</font>
      [<a href="user_light.php?who=<?=(int)$_GET["who"]?>&amp;show=prods">show</a>]<br>
     </td>
    </tr>
    <? endif; ?>
    
    <? if($nbgroups>0): ?>
    <tr bgcolor="#224488">
     <td colspan="2">
      latest added groups <font color="#9999AA"><? print($nbgroups); ?> glöps</font><br>
     </td>
    </tr>
    <? endif; ?>

    <? if($nbscreenshots>0): ?>
    <tr bgcolor="#224488">
     <td colspan="2">
      latest added screenshots <font color="#9999AA"><? print($nbscreenshots); ?> glöps</font><br>
     </td>
    </tr>
    <? endif; ?>

    <? if($nbnfos>0): ?>
    <tr bgcolor="#224488">
     <td colspan="2">
      latest added nfos <font color="#9999AA"><? print($nbnfos); ?> glöps</font><br>
     </td>
    </tr>
    <? endif; ?>

    <? if($nbcomments>0): ?>
    <tr bgcolor="#224488">
     <td colspan="2">
      latest 1st comments <font color="#9999AA"><? print($nbcomments); ?> glöps</font><br>
     </td>
    </tr>
    <? endif; ?>

    <? if ($total_ups): ?>    
    <tr bgcolor="#224488">
     <td colspan="2">
      top thumb up agreers (total <img src="gfx/rulez.gif"> <?=$total_ups?>)<br>
     </td>
    </tr>
    <? endif; ?>
    
    <? if ($total_downs): ?>    
    <tr bgcolor="#224488">
     <td colspan="2">
      top thumb down agreers (total <img src="gfx/sucks.gif"> <?=$total_downs?>)<br/>
     </td>
    </tr>
    <? endif; ?>

<?
}
?>

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
