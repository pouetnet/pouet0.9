<?

require("include/top.php");
require("include/libbb.php");

$who = (int)$_GET["who"];
//$linebypage = $_GET["lines"];

$usercustom=$user;

if ($com) $linebypage=$com; else $linebypage=$com=25;

$query="SELECT count(0) FROM prods where prods.added=".$who;
$result=mysql_query($query);
$nbmsg=mysql_result($result,0);

if(!$page) $page=1;

if(!is_numeric($who)) {
  $result = mysql_query("SELECT id FROM users");
  while($tmp = mysql_fetch_row($result)) {
    $ids[]=$tmp[0];
  }
  $who=$ids[mt_rand(0,count($ids)-1)];
}

$result = mysql_query_debug("SELECT * FROM users WHERE id=".$who);
$user = mysql_fetch_array($result);

$sceneIDData = array();
if (!$user["sceneIDData"] || (time() - strtotime($user["sceneIDLastRefresh"])) < 60 * 60)
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


$query="SELECT prods.id,prods.name,prods.group1,prods.group2,prods.group3,prods.type,prods.quand,users.nickname,users.avatar,UNIX_TIMESTAMP()-UNIX_TIMESTAMP(prods.quand) as difftime FROM prods JOIN users WHERE prods.added=users.id and prods.added=".$who;
$query.=" ORDER BY prods.quand DESC";
$query.=" LIMIT ".(($page-1)*$linebypage).",".$linebypage;
//if ($com) $query.=" LIMIT ".$com; else $query.=" LIMIT 12";
$result = mysql_query($query);
while($tmp = mysql_fetch_assoc($result)) {
  $comments[] = $tmp;
}
for ($i=0; $i<count($comments); $i++):
	if ($comments[$i]["group1"]):
		$query="select name,acronym from groups where id='".$comments[$i]["group1"]."'";
		$result=mysql_query($query);
		while($tmp = mysql_fetch_array($result)) {
		  $comments[$i]["groupn1"]=$tmp["name"];
		  $comments[$i]["groupacron1"]=$tmp["acronym"];
		 }
	endif;
	if ($comments[$i]["group2"]):
		$query="select name,acronym from groups where id='".$comments[$i]["group2"]."'";
		$result=mysql_query($query);
		while($tmp = mysql_fetch_array($result)) {
		  $comments[$i]["groupn2"]=$tmp["name"];
		  $comments[$i]["groupacron2"]=$tmp["acronym"];
		 }
	endif;
	if ($comments[$i]["group3"]):
		$query="select name,acronym from groups where id='".$comments[$i]["group3"]."'";
		$result=mysql_query($query);
		while($tmp = mysql_fetch_array($result)) {
		  $comments[$i]["groupn3"]=$tmp["name"];
		  $comments[$i]["groupacron3"]=$tmp["acronym"];
		 }
	endif;

	if (strlen($comments[$i]["groupn1"].$comments[$i]["groupn2"].$comments[$i]["groupn3"])>27):
		if (strlen($comments[$i]["groupn1"])>10 && $comments[$i]["groupacron1"]) $comments[$i]["groupn1"]=$comments[$i]["groupacron1"];
		if (strlen($comments[$i]["groupn2"])>10 && $comments[$i]["groupacron1"]) $comments[$i]["groupn2"]=$comments[$i]["groupacron2"];
		if (strlen($comments[$i]["groupn3"])>10 && $comments[$i]["groupacron1"]) $comments[$i]["groupn3"]=$comments[$i]["groupacron3"];
	endif;

	$query="select platforms.name from prods_platforms, platforms where prods_platforms.prod='".$comments[$i]["id"]."' and platforms.id=prods_platforms.platform";
	$result=mysql_query($query);
	$check=0;
	$comments[$i]["platform"]="";
	while($tmp = mysql_fetch_array($result)) {
	  if ($check>0) $comments[$i]["platform"].=",";
	  $check++;
	  $comments[$i]["platform"].=$tmp["name"];
	 }
endfor;
//print("<br />->".count($comments)."<-");
for($i=0;$i<count($comments);$i++) {
  // trasformation secondes en lisible
  $comments[$i]["t_hours"]=floor($comments[$i]["difftime"]/3600);
  $comments[$i]["t_minutes"]=floor(($comments[$i]["difftime"]-3600*$comments[$i]["t_hours"])/60);
  $comments[$i]["t_seconds"]=$comments[$i]["difftime"]-3600*$comments[$i]["t_hours"]-60*$comments[$i]["t_minutes"];
}

$query="SELECT prods.id,prods.name,prods.type,prods.group1,prods.group2,prods.group3 FROM users_cdcs, prods WHERE users_cdcs.cdc=prods.id and users_cdcs.user=".$who;
$result=mysql_query($query);
while($tmp=mysql_fetch_array($result)) {
  $cdc[]=$tmp;
}
for ($i=0; $i<count($cdc); $i++):
	if ($cdc[$i]["group1"]):
		$query="select name,acronym from groups where id='".$cdc[$i]["group1"]."'";
		$result=mysql_query($query);
		while($tmp = mysql_fetch_array($result)) {
		  $cdc[$i]["groupname1"]=$tmp["name"];
		  $cdc[$i]["groupacron1"]=$tmp["acronym"];
		 }
	endif;
	if ($cdc[$i]["group2"]):
		$query="select name,acronym from groups where id='".$cdc[$i]["group2"]."'";
		$result=mysql_query($query);
		while($tmp = mysql_fetch_array($result)) {
		  $cdc[$i]["groupname2"]=$tmp["name"];
		  $cdc[$i]["groupacron2"]=$tmp["acronym"];
		 }
	endif;
	if ($cdc[$i]["group3"]):
		$query="select name,acronym from groups where id='".$cdc[$i]["group3"]."'";
		$result=mysql_query($query);
		while($tmp = mysql_fetch_array($result)) {
		  $cdc[$i]["groupname3"]=$tmp["name"];
		  $cdc[$i]["groupacron3"]=$tmp["acronym"];
		 }
	endif;

	if (strlen($cdc[$i]["groupname1"].$cdc[$i]["groupname2"].$cdc[$i]["groupname3"])>27):
		if (strlen($cdc[$i]["groupname1"])>10) $cdc[$i]["groupname1"]=$cdc[$i]["groupacron1"];
		if (strlen($cdc[$i]["groupname2"])>10) $cdc[$i]["groupname2"]=$cdc[$i]["groupacron2"];
		if (strlen($cdc[$i]["groupname3"])>10) $cdc[$i]["groupname3"]=$cdc[$i]["groupacron3"];
	endif;

	/*$query="select platforms.name from prods_platforms, platforms where prods_platforms.prod='".$cdc[$i]["id"]."' and platforms.id=prods_platforms.platform";
	$result=mysql_query($query);
	$check=0;
	$cdc[$i]["platform"]="";
	while($tmp = mysql_fetch_array($result)) {
	  if ($check>0) $cdc[$i]["platform"].=", ";
	  $check++;
	  $cdc[$i]["platform"].=$tmp["name"];
	 }*/
endfor;

// average rating
$query="SELECT SUM(rating)/count(0) FROM comments WHERE who=".$user["id"];
$result=mysql_query($query);
$avg_rating=mysql_result($result,0);

?>
<br />
<? if ($user["level"]): ?>
<table bgcolor="#000000" cellspacing="1" cellpadding="0" border="0" width="75%">
 <tr>
  <td>
   <table bgcolor="#000000" cellspacing="1" cellpadding="2" border="0" width="100%">
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
         <b><? print($user["glops"]); ?></b> <font color="#9999AA">gl?ps</font><br>
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
      echo recaptcha_mailhide_html(RECAPTCHA_PUB_KEY, RECAPTCHA_PRIV_KEY, $user["email"]);
		endif;
		?>
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
		 <td><b>[</b><a href="user.php?who=<? print($who);?>">glopper view</a><b>]</b><br /></td>
       		</tr>
	   </table>
	</td>
        <td align="right" nowrap>
         <img src="avatars/<? print($user["avatar"]); ?>" width="160" height="160"><br>
        </td>
       </tr>
  </table>
  </td>
  </tr>
</table>

    <? if (count($comments)>0): ?>
    	<br />
	<table width="75%" bgcolor="#000000" cellspacing="1" cellpadding="0" border="0">
	 <tr>
	  <td>
	   <table bgcolor="#000000" cellspacing="1" cellpadding="2" border="0" width="100%">
    <? for($i=0;$i<count($comments);$i++): ?>
	<? $trcolor=($i%2)?"#446688":"#557799"; ?>
	    <tr bgcolor="#224488">
	     <td colspan="2">
      <table cellspacing="0" cellpadding="0" border="0">
       <tr>
        <td valign="top" nowrap>
        <? $platformss = explode(",", $comments[$i]["platform"]);
         for($kkk=0;$kkk<count($platformss);$kkk++) { ?><a href="prodlist.php?platform=<? print($platformss[$kkk]); ?>"><img src="gfx/os/<? print($os[$platformss[$kkk]]); ?>" width="16" height="16" border="0" title="<? print($platformss[$kkk]); ?>" alt="<? print($platformss[$kkk]); ?>"></a><? } ?><br />
        </td>
        <td valign="top">&nbsp;</td>
        <td valign="top" nowrap>
        <? $typess = explode(",", $comments[$i]["type"]);
         for($kkk=0;$kkk<count($typess);$kkk++) { ?><a href="prodlist.php?type=<? print($typess[$kkk]); ?>"><img src="gfx/types/<? print($types[$typess[$kkk]]); ?>" width="16" height="16" border="0" title="<? print($typess[$kkk]); ?>" alt="<? print($typess[$kkk]); ?>"></a><? } ?><br />
        </td>
        <td valign="top">&nbsp;</td>
        <td width="100%" valign="top">
         <a href="prod.php?which=<? print($comments[$i]["id"]); ?>">
         <? print(stripslashes($comments[$i]["name"])); ?></a>
         <?
             if ($comments[$i]["groupn1"]) print(" by <a href=\"groups.php?which=".stripslashes($comments[$i]["group1"])."\">".stripslashes($comments[$i]["groupn1"])."</a>");
             if ($comments[$i]["groupn2"]) print(" :: <a href=\"groups.php?which=".stripslashes($comments[$i]["group2"])."\">".stripslashes($comments[$i]["groupn2"])."</a>");
             if ($comments[$i]["groupn3"]) print(" :: <a href=\"groups.php?which=".stripslashes($comments[$i]["group3"])."\">".stripslashes($comments[$i]["groupn3"])."</a>");
             ?><br />
        </td>
        <td align="right" valign="top" nowrap>
        <? print("added on the ".substr($comments[$i]["quand"],0,10)); ?><br />
         </td>
       </tr>
      </table>
     </td>
    </tr>
    <? endfor; ?>

    <? $passedlink="demoglop.php?who=".$who;
    if ($com) $passedlink.="&amp;com=".$com; ?>
    <tr bgcolor="#224488">
     <td>
      <table width="100%" cellspacing="0" cellpadding="0" border="0">
       <tr>
       <? if($page>=2): ?>
        <td>
         <a href="<? print($passedlink."&amp;page=".($page-1)) ?>">
          <img src="gfx/flecheg.gif" border="0"><br />
         </a>
        </td>
        <td>&nbsp;</td>
        <td nowrap>
         <a href="<? print($passedlink."&amp;page=".($page-1)) ?>">
          <b>previous page</b><br />
         </a>
        </td>
       <? endif; ?>
        <form action="demoglop.php">
        <input type="hidden" name="who" value="<? print($who); ?>">
        <input type="hidden" name="com" value="<? print($com); ?>">
        <td width="50%" align="right">
        <select name="page">
        <? for($i=1;$i<=round($nbmsg/$linebypage);$i++): ?>
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
        <input type="image" src="gfx/submit.gif" border="0"><br />
        </td>
        </form>
       <? if($page<round($nbmsg/$linebypage)): ?>
        <td nowrap>
         <a href="<? print($passedlink."&amp;page=".($page+1)) ?>">
          <b>next page</b><br />
         </a>
        </td>
        <td>&nbsp;</td>
        <td>
         <a href="<? print($passedlink."&amp;page=".($page+1)) ?>">
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
    <? endif; ?>

<? else: ?>
	<center><? print("aiiiiiiiiii cookiiieee\n la cuenta de este utilizador ha sido comida de una langosta ciega guapa<br />*clicki clicki clacki* (\/) - - (\/) *clacki clacki clicki*<br />ciega ciega ciega frikki!!"); ?></center>
<? endif; ?>
<br />
<?
$user["bottombar"]=$usercustom["bottombar"];
 require("include/bottom.php"); ?>
