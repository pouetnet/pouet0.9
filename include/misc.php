<?
include_once('auth.php');

// a very useful misc. include file added by melwyn - 19/12/2003

function isIPBanned() {
  if ($_SERVER["REMOTE_ADDR"]=="62.147.219.109") return true; // barti
  if ($_SERVER["REMOTE_ADDR"]=="85.238.79.35") return true; // mesesajt
  if ($_SERVER["REMOTE_ADDR"]=="67.159.44.138") return true; // w1.hidemyass.com
  if ($_SERVER["REMOTE_ADDR"]=="69.144.1.95") return true; // spammer
  if ($_SERVER["REMOTE_ADDR"]=="94.103.150.20") return true; // spammer
  if ($_SERVER["REMOTE_ADDR"]=="83.59.123.185") return true; // solo2

//  return false;

  if (strstr(gethostbyaddr($_SERVER["REMOTE_ADDR"]),"broad.pt.fj.dynamic.163data.com.cn")!==false)
    return true;

  if (strpos($_SERVER["REMOTE_ADDR"],"120.152.")===0 || strpos($_SERVER["REMOTE_ADDR"],"123.208.")===0) return true; // anakirob
  return false;
}

	/** create a cached include file out of database to help keeping the load down. */

	function create_cache_module($name, $query, $domore)
	{
		//print("->".$query."<-");
		$result = mysql_query($query) or debuglog($query." - ".mysql_error());

		while($tmp = mysql_fetch_assoc($result))
		{
//  		if(strlen($tmp["name"])>32)
//				$tmp["name"]=substr($tmp["name"],0,32)."...";
		  $data[] = $tmp;
		}

		if ($domore>0)
		{
			for ($i=0; $i<count($data); $i++):
				if ($data[$i]["group1"]):
					$query="select name,acronym from groups where id='".$data[$i]["group1"]."'";
		  			$result=mysql_query($query);
		  			while($tmp = mysql_fetch_array($result)) {
					  $data[$i]["groupname1"]=$tmp["name"];
					  $data[$i]["groupacron1"]=$tmp["acronym"];
					 }
  				endif;
  				if ($data[$i]["group2"]):
					$query="select name,acronym from groups where id='".$data[$i]["group2"]."'";
		  			$result=mysql_query($query);
		  			while($tmp = mysql_fetch_array($result)) {
					  $data[$i]["groupname2"]=$tmp["name"];
					  $data[$i]["groupacron2"]=$tmp["acronym"];
					 }
  				endif;
  				if ($data[$i]["group3"]):
					$query="select name,acronym from groups where id='".$data[$i]["group3"]."'";
		  			$result=mysql_query($query);
		  			while($tmp = mysql_fetch_array($result)) {
					  $data[$i]["groupname3"]=$tmp["name"];
					  $data[$i]["groupacron3"]=$tmp["acronym"];
					 }
  				endif;

  				if (strlen($data[$i]["groupname1"].$data[$i]["groupname2"].$data[$i]["groupname3"])>27):
  					if (strlen($data[$i]["groupname1"])>10 && $data[$i]["groupacron1"]) $data[$i]["groupname1"]=$data[$i]["groupacron1"];
  					if (strlen($data[$i]["groupname2"])>10 && $data[$i]["groupacron2"]) $data[$i]["groupname2"]=$data[$i]["groupacron2"];
  					if (strlen($data[$i]["groupname3"])>10 && $data[$i]["groupacron3"]) $data[$i]["groupname3"]=$data[$i]["groupacron3"];
  				endif;

				$query="select platforms.name from prods_platforms, platforms where prods_platforms.prod='".$data[$i]["id"]."' and platforms.id=prods_platforms.platform";
	  			$result=mysql_query($query);
	  			$check=0;
	  			$data[$i]["platform"]="";
	  			while($tmp = mysql_fetch_array($result)) {
				  if ($check>0) $data[$i]["platform"].=",";
				  $check++;
				  $data[$i]["platform"].=$tmp["name"];
				 }

			endfor;

		}

		$fp = fopen("include/".$name.".cache.inc", "wb");
		fwrite($fp, "<?\n");

		while(list($k,$v)=each($data))
		{
			if(is_array($v))
			{
				while(list($k2,$v2)=each($v))
				{
					if($k2=="name"&&strlen($v2)>27)
						$v2 = substr($v2,0,27)."...";
					//if($k2=="groupname1"&&strlen($v2)>20)
					//	$v2 = substr($v2,0,15)."...";
					$v2 = addslashes($v2);
					$v2 = str_replace("$","\\\$",$v2);
//          $v2 = html_entity_decode($v2);
          $v2 = preg_replace('~&#38;#([0-9]+);~e', 'chr("\\1")', $v2);

          fwrite($fp, "\$".$name."[".$k."][\"".$k2."\"]=\"".$v2."\";\n");
				}
			}
		}
    		fwrite($fp, "?>\n");
		fclose($fp);
	}



	function create_stats_cache()
	{
		$query="SELECT count(0) FROM prods";
		$result=mysql_query($query);
		$nb_demos=mysql_result($result,0);
		$query="SELECT count(0) FROM prods WHERE (UNIX_TIMESTAMP()-UNIX_TIMESTAMP(quand))<=3600*24";
		$result=mysql_query($query);
		$inc_demos=mysql_result($result,0);

		$query="SELECT count(0) FROM groups";
		$result=mysql_query($query);
		$nb_groups=mysql_result($result,0);
		$query="SELECT count(0) FROM groups WHERE (UNIX_TIMESTAMP()-UNIX_TIMESTAMP(quand))<=3600*24";
		$result=mysql_query($query);
		$inc_groups=mysql_result($result,0);

		$query="SELECT count(0) FROM parties";
		$result=mysql_query($query);
		$nb_parties=mysql_result($result,0);
		$query="SELECT count(0) FROM parties WHERE (UNIX_TIMESTAMP()-UNIX_TIMESTAMP(quand))<=3600*24";
		$result=mysql_query($query);
		$inc_parties=mysql_result($result,0);

		$query="SELECT count(0) FROM bbses";
		$result=mysql_query($query);
		$nb_bbses=mysql_result($result,0);
		$query="SELECT count(0) FROM bbses WHERE (UNIX_TIMESTAMP()-UNIX_TIMESTAMP(added))<=3600*24";
		$result=mysql_query($query);
		$inc_bbses=mysql_result($result,0);

		$query="SELECT count(0) FROM users";
		$result=mysql_query($query);
		$nb_users=mysql_result($result,0);
		$query="SELECT count(0) FROM users WHERE (UNIX_TIMESTAMP()-UNIX_TIMESTAMP(quand))<=3600*24";
		$result=mysql_query($query);
		$inc_users=mysql_result($result,0);

		$query="SELECT count(0) FROM comments";
		$result=mysql_query($query);
		$nb_comments=mysql_result($result,0);
		$query="SELECT count(0) FROM comments WHERE (UNIX_TIMESTAMP()-UNIX_TIMESTAMP(quand))<=3600*24";
		$result=mysql_query($query);
		$inc_comments=mysql_result($result,0);

		$fp = fopen("include/stats.cache.inc", "wb");
		fwrite($fp, "<?\n");

          	fwrite($fp, "\$nb_demos=\"".$nb_demos."\";\n");
          	fwrite($fp, "\$inc_demos=\"".$inc_demos."\";\n");
          	fwrite($fp, "\$nb_groups=\"".$nb_groups."\";\n");
          	fwrite($fp, "\$inc_groups=\"".$inc_groups."\";\n");
          	fwrite($fp, "\$nb_parties=\"".$nb_parties."\";\n");
          	fwrite($fp, "\$inc_parties=\"".$inc_parties."\";\n");
          	fwrite($fp, "\$nb_bbses=\"".$nb_bbses."\";\n");
          	fwrite($fp, "\$inc_bbses=\"".$inc_bbses."\";\n");
          	fwrite($fp, "\$nb_users=\"".$nb_users."\";\n");
          	fwrite($fp, "\$inc_users=\"".$inc_users."\";\n");
          	fwrite($fp, "\$nb_comments=\"".$nb_comments."\";\n");
          	fwrite($fp, "\$inc_comments=\"".$inc_comments."\";\n");

    		fwrite($fp, "?>\n");
		fclose($fp);
	}

include_once("sceneidlib.inc.php");

function microtime_float()
{
   list($usec, $sec) = explode(" ", microtime());
   return ((float)$usec + (float)$sec);
}

function debuglog($s) {
  global $SESSION_LEVEL;
  if (($SESSION_LEVEL=='administrator' || $SESSION_LEVEL=='moderator' || $SESSION_LEVEL=='gloperator'))
    printf("<!-- %s -->\n",$s);
}

$numqueries = 0;
function mysql_query_debug($s) {
  global $numqueries;
  $numqueries++;
  return mysql_query($s);
}

function SQLDate($sqldate)
{
  global $months,$nbext;

  $nbday=substr($sqldate,8,2);
  $nbmonth=substr($sqldate,5,2);
  $year=substr($sqldate,0,4);
  $time=substr($sqldate,-8);

	if($nbday>9)
		$day=sprintf("%d%s",$nbday,$nbext[0]);
	else
		$day=sprintf("%d%s",$nbday,$nbext[$nbday%10]);
  $month=$months[sprintf("%d",$nbmonth)];

  if(strlen($sqldate)>10)
    return $day." ".$month." ".$year." at ".$time;
  else
    return $day." ".$month." ".$year;
}

/*
 * wraps long words without cutting html tag arguments
 */
function better_wordwrap($str,$cols,$cut){
	$tag_open = '<';
	$tag_close = '>';
	$count = 0;
	$in_tag = 0;
	$str_len = strlen($str);
	$segment_width = 0;

	for ($i=1 ; $i<=$str_len ; $i++){
		if ($str[$i] == $tag_open) {
			$in_tag++;
		} elseif ($str[$i] == $tag_close) {
			if ($in_tag > 0) {
				$in_tag--;
				$segment_width = 0;
			}
		} else {
			if ($in_tag == 0) {
				if($str[$i] != ' ') {
					$segment_width++;
					if ($segment_width > $cols) {
						 $str = substr($str,0,$i).$cut.substr($str,$i,$str_len);
						 $i += strlen($cut);
						 $str_len = strlen($str);
						 $segment_width = 0;
					}
				} else {
					$segment_width = 0;
				}
			}
		}
	}
	return $str;
}

function parse_message($message)
{
//  return wordwrap(nl2br(stripslashes(bbencode(htmlentities($message),true))),80,' ',1);
  $s = better_wordwrap(nl2br(stripslashes(bbencode(htmlentities($message),true))),80,' ');
  //echo htmlentities($message);
  $s = str_replace("&amp;#","&#",$s);
  return $s;
}

function DoBar($percent,$fond=false,$alttag="") {
  $alttag = $alttag ? $alttag : $percent;
?>
<table cellspacing="0" cellpadding="0" border="0">
 <tr>
  <td>
   <font color="#000000">
    <img src="gfx/bar.gif" width="<? print($percent); ?>" height="16" border="1" title="<?=$alttag?>" alt="<?=$alttag?>"><br />
   </font>
  </td>
  <? if(($percent<100)&&$fond): ?>
  <td>
   <font color="#000000">
    <img src="gfx/black.gif" width="<? print(100-$percent); ?>" height="16" border="1" title="<?=$alttag?>" alt="<?=$alttag?>"><br />
   </font>
  </td>
  <? endif; ?>
 </tr>
</table>
<?
}

function conn_db() {
  global $db,$dbl;
  $dbl = mysql_connect($db['host'], $db['user'], $db['password']);
  if(!$dbl) {
  	/*
  	print('<pre>Debugging things : ');
  	print_r($db);
  	print("</pre>\n");
  	*/
  	die('SQL error... sorry ! ^^; I\'m on it ! (This might be a good time to go make some demos instead.)'."\n<!-- ".mysql_error()." -->");
  }
  mysql_select_db($db['database'],$dbl);
}

function cdcstack($n) {
  if ($n==1) {
?><img src="gfx/titles/coupdecoeur.gif" title="cdc" alt="cdc"><?
  } else {
?><img src="gfx/cdcstack_start.gif" title="<?=$n?> cdcs" alt="<?=$n?> cdcs"><?
for ($x=0; $x<$n-2; $x++) {
?><img src="gfx/cdcstack_loop.gif" title="<?=$n?> cdcs" alt="<?=$n?> cdcs"><?
}
?><img src="gfx/cdcstack_end.gif" title="<?=$n?> cdcs" alt="<?=$n?> cdcs"><?
  }
}

function refreshUserInfo() {
  global $userID,$xml;

//  $_SESSION = array();
//  return;

  if(!$_SESSION["SESSION_NICKNAME"]&&!$_SESSION["SESSION_LEVEL"]&&!$_SESSION["SESSION_AVATAR"]&&
    ($_COOKIE["SCENEID_COOKIE"]||($_COOKIE["SCENEID_SESSION"]&&$_SESSION["SCENEID_ID"])))
  {

    if ((time() - $_SESSION["lastUserInfoRefresh"]) < 5 * 60) return;
    $_SESSION["lastUserInfoRefresh"] = time();

  	$returnvalue = $xml->parseSceneIdData ("getUserInfo", array ("cookie" => $_COOKIE["SCENEID_COOKIE"]?$_COOKIE["SCENEID_COOKIE"]:$_COOKIE["SCENEID_SESSION"]));


  	$userID = $returnvalue["userID"];

  	if($returnvalue["returnvalue"]==10) // got user info, login
  	{
  		$query = "SELECT nickname,avatar,level FROM users WHERE id=$userID";
  		$result = mysql_query($query);
  		if (mysql_num_rows($result) == 1) {
  			$_SESSION["SESSION"]=session_id();
  			$_SESSION["SESSION_NICKNAME"]=mysql_result($result,0,0);
  			$_SESSION["SESSION_AVATAR"]=mysql_result($result,0,1);
  			$_SESSION["SESSION_LEVEL"]=mysql_result($result,0,2);

  			// for keeping sceneid alive without permanent login
  			if(!$_SESSION["SCENEID"])
  			{
  				$_SESSION["SCENEID"]=session_id();
	        $_SESSION["SCENEID_ID"]=$userID;
	        $_SESSION["SCENEID_ALIAS"]=$userinfo["alias"];
    			$_SESSION["SCENEID_IP"]=$_SERVER["REMOTE_ADDR"];
  			}
  		}
  		else // damn, it's a sceneid user with his/her first login to pouet!
  		{
  			if(substr_count($_SERVER["PHP_SELF"], "account.php")==0)
  			{
  				header("Location: http://www.pouet.net/account.php?regsceneid=1");
  //				header("Location: http://www.scene.org/~melwyn/www.pouet.net/account.php?regsceneid=1");
  			}
  		}
  	}
  }
}

$thread_categories = array(
  0 => "general",
  2 => "gfx",
  3 => "code",
  4 => "music",
  5 => "parties",
  6 => "offtopic",
  1 => "residue",
);

$affilorig = array(
  "remix" => "remixed in",
  "port" => "ported to",
  "final" => "final version",
  "pack" => "packed in",
  "related" => "related to",
);
$affilinverse = array(
  "remix" => "remix of",
  "port" => "ported from",
  "final" => "party version",
  "pack" => "includes",
  "related" => "related from",
);


function canSeeBBSCategories() {
  return true;
//  global $SESSION_LEVEL;
//  return $SESSION_LEVEL=='administrator' || $SESSION_LEVEL=='moderator' || $SESSION_LEVEL=='gloperator';
}

function canEditBBSCategories() {
  global $SESSION_LEVEL;
  return $SESSION_LEVEL=='administrator' || $SESSION_LEVEL=='moderator' || $SESSION_LEVEL=='gloperator';
}
?>
