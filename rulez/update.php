<?php
require_once('../include/constants.php');
require_once('../include/auth.php');

session_start();
if (!($SESSION_LEVEL=='administrator' || $SESSION_LEVEL=='moderator' || $SESSION_LEVEL=='gloperator'))
  die("OMG");
?><html>
<head>
 <title>test</title>
</head>
<body>
<hr>
<pre>
<?

function utf8_entity_decode($entity){
 $convmap = array(0x0, 0x10000, 0, 0xfffff);
 return mb_decode_numericentity($entity, $convmap, 'ISO-8859-1');
}

function create_cache_module($name, $query, $domore)
	{
		//print("->".$query."<-");
		$result = mysql_query($query) or die($query . " - " . mysql_error());

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

		$fp = fopen('../'.TMP_FOLDER.'/'.$name.'.cache.inc', "wb");
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
					//$v2 = preg_replace('/&#\d{2,5};/ue', "utf8_entity_decode('$0')", $v2 );
  				fwrite($fp, "\$".$name."[".$k."][\"".$k2."\"]=\"".$v2."\";\n");
				}
				fwrite($fp, "\n");
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

		$fp = fopen('../'.TMP_FOLDER.'/stats.cache.inc', "wb");
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

$options = array(
	'all',
	'ojuice',
	'glops',
	'topdemos',
	'logos',
	'bbstopics',
	'voteavg',
	'results',
	'cacheonelines',
	'cachelatestcomments',
	'cachetop_demos',
	'cachetop_keops',
	'cachelatest_demos',
	'cachestats',
	'cachecdclist',
	'allcaches',
	'webtv');

echo "options:\n";
foreach ($options as $v) {
	echo "- $v\n";
	// load $_GET['foo'] into $foo
	$$v = isset($_GET[$v]) ? $_GET[$v] : false;
}

// update ojuice news
if ($ojuice||$all)
{
	/* marche pas encore */
	$fd = fsockopen ("onds.ojuice.net",80);
	fputs($fd,"GET /newspouet.php HTTP/1.1\nHost:onds.ojuice.net\n\n");
	//while (!feof ($fd)) {
	    $buffer = fgets($fd, 4096);
	    echo $buffer;
	//}
	fclose ($fd);
	print("ojuice stage 1!<br />");
}
?>
</pre>
<?
if ($ojuice||$all)
{
	include("http://onds.ojuice.net/newspouet.php");
	print("ojuice stage 2!<br />");
}

$dbinfo=$db;
$db=mysql_connect($dbinfo['host'],$dbinfo['user'], $dbinfo['password']);
mysql_select_db($dbinfo['database'],$db);

if ($ojuice||$all)
{
	for($i=0;$i<=4;$i++) {
		$query="REPLACE ojnews SET ";
		$query.="id=".$ojnews[$i]["id"].", ";
		$query.="title='".addslashes($ojnews[$i]["title"])."', ";
		$query.="url='".$ojnews[$i]["url"]."', ";
		$query.="quand='".$ojnews[$i]["when"]."', ";
		$query.="authorid=".$ojnews[$i]["authorid"].", ";
		$query.="authornick='".addslashes($ojnews[$i]["authornick"])."', ";
		$query.="authorgroup='".addslashes($ojnews[$i]["authorgroup"])."', ";
		$query.="content='".addslashes($ojnews[$i]["content"])."'";
		mysql_query($query,$db);
		print("<hr>".$query."<br>");
	}
	print("ojuice stage 3!<br />");
}

// calculate users's glops
if ($glops||$all)
{
	unset($logos);
	$result=mysql_query("SELECT id,file,vote_count FROM logos"); // WHERE author1=".$user["id"]." || author2=".$user["id"]);
	while($tmp=mysql_fetch_array($result)) {
	  if ( ($tmp["author1"]) && ($tmp['vote_count'] > 0) ) $totals[$tmp["author1"]]+=20;
	  if ( ($tmp["author2"]) && ($tmp['vote_count'] > 0) ) $totals[$tmp["author2"]]+=20;
	}

/*	$query="SELECT author1,author2 FROM logos";
	$result=mysql_query($query);
	while($tmp=mysql_fetch_assoc($result)) {
		if($tmp["author1"])
			$totals[$tmp["author1"]]+=20;
		if($tmp["author2"])
			$totals[$tmp["author2"]]+=20;
	}*/
	$query="SELECT added FROM prods";
	$result=mysql_query($query);
	while($tmp=mysql_fetch_assoc($result)) {
	  $totals[$tmp["added"]]+=2;
	}
	$query="SELECT added FROM groups";
	$result=mysql_query($query);
	while($tmp=mysql_fetch_assoc($result)) {
	  $totals[$tmp["added"]]++;
	}
	$query="SELECT added FROM parties";
	$result=mysql_query($query);
	while($tmp=mysql_fetch_assoc($result)) {
	  $totals[$tmp["added"]]++;
	}
	$query="SELECT user FROM screenshots";
	$result=mysql_query($query);
	while($tmp=mysql_fetch_assoc($result)) {
	  $totals[$tmp["user"]]++;
	}
	$query="SELECT user FROM nfos";
	$result=mysql_query($query);
	while($tmp=mysql_fetch_assoc($result)) {
	  $totals[$tmp["user"]]++;
	}
	$query="SELECT adder FROM bbses";
	$result=mysql_query($query);
	while($tmp=mysql_fetch_assoc($result)) {
	  $totals[$tmp["adder"]]++;
	}

	$query="SELECT COUNT(DISTINCT which) as comments,who FROM comments GROUP BY who";
	$result=mysql_query($query);
	while($tmp=mysql_fetch_assoc($result)) {
	  $totals[$tmp["who"]]+=$tmp["comments"];
	}

	$query="SELECT users.id,ud.points FROM users,ud WHERE ud.login=users.udlogin";
	$result=mysql_query($query);
	while($tmp=mysql_fetch_assoc($result))
		$totals[$tmp["id"]]+=round($tmp["points"]/1000);

	reset($totals);
	for($i=0;$i<count($totals);$i++) {
		$query="UPDATE users SET glops=".$totals[key($totals)]." WHERE id=".key($totals);
		mysql_query($query);
		next($totals);
	}
	print("glops!<br />");
}

if ($topdemos||$all)
{
	// debut calcul top demos
	unset($total);
	$i=0;
	$query="SELECT id FROM prods ORDER BY views DESC";
	$result = mysql_query($query);
	while($tmp = mysql_fetch_assoc($result)) {
	  $total[$tmp["id"]]+=$i;
	  $i++;
	}

	$i=0;
	$query="SELECT prods.id,SUM(comments.rating) AS somme FROM prods,comments WHERE prods.id=comments.which GROUP BY prods.id ORDER BY somme DESC";
	$result = mysql_query($query);
	while($tmp = mysql_fetch_assoc($result)) {
	  $total[$tmp["id"]]+=$i;
	  $i++;
	}

	asort($total);

	$i=1;
	unset($tmp);
	unset($top_demos);
	while ((list ($key, $val)=each($total))) {
		$query="UPDATE prods SET rank=".$i." WHERE id=".$key;
		mysql_query($query);
		$i++;
	}

	$cachetop_keops=true;
	print("keops!<br />");
}


if ($logos||$all)
{
	//update logos vote_count
	$query="SELECT logos.id,SUM(logos_votes.vote) AS votes FROM logos,logos_votes WHERE logos_votes.logo=logos.id group by logos.id";
	$result=mysql_query($query);
	while($tmp=mysql_fetch_array($result)) {
	  $votes[]=$tmp;
	}
	for ($i=0;$i<count($votes);$i++)
	{
		$query="UPDATE logos SET vote_count=".$votes[$i]["votes"]." where id=".$votes[$i]["id"];
		$result = mysql_query($query);
	}
	print("logos!<br />");
}


//update vote info
if ($voteavg||$all)
{
	$query ="SELECT id FROM prods";
	$result = mysql_query($query);
	while($tmp = mysql_fetch_array($result)) {
	  $prods[]=$tmp;
	}

	print("updating ".count($prods)." prods<br />");
	for($j=0;$j<count($prods);$j++) {
			unset($commentss);
			unset($checktable);
			$rulez=0;
			$piggie=0;
			$sucks=0;
			$total=0;

				    	$query  = "SELECT comments.rating,comments.who FROM comments WHERE comments.which='".$prods[$j]["id"]."'";
					$result=mysql_query($query);
					while($tmp=mysql_fetch_array($result)) {
					  $commentss[]=$tmp;
					}
					for($i=0;$i<count($commentss);$i++)
					{
						if(!array_key_exists($commentss[$i]["who"], $checktable)||$commentss[$i]["rating"]!=0)
							$checktable[$commentss[$i]["who"]] = $commentss[$i]["rating"];
					}
					while(list($k,$v)=each($checktable))
					{
						if($v==1) $rulez++;
						else if($v==-1) $sucks++;
						else $piggie++;
						$total++;
					}

					if ($total!=0) $avg = sprintf("%.2f",(float)($rulez*1+$sucks*-1)/$total);
					 else $avg="0.00";
					$query="UPDATE prods SET voteup=".$rulez.", votepig=".$piggie.", votedown=".$sucks.", voteavg='".$avg."' where id=".$prods[$j]["id"];
					print($query."<br />");
					mysql_query($query);
	}
	print("voteavg!<br />");
}


//update bbs info
if ($bbstopics||$all)
{
	$query ="SELECT id FROM bbs_topics";
	$result = mysql_query($query);
	while($tmp = mysql_fetch_array($result)) {
	  $topics[]=$tmp;
	}

	print("updating ".count($topics)." bbs_topics<br />");
	for($i=0;$i<count($topics);$i++) {
				  $query="SELECT count(0) FROM bbs_posts WHERE topic=".$topics[$i]["id"];
				  $result=mysql_query($query);
				  $topics[$i]["replies"]=mysql_result($result,0)-1;
				  $query="SELECT added FROM bbs_posts WHERE topic=".$topics[$i]["id"]." ORDER BY added DESC LIMIT 1";
				  $result=mysql_query($query);
				  $topics[$i]["lastpost"]=mysql_result($result,0);

				  $query="SELECT added FROM bbs_posts WHERE topic=".$topics[$i]["id"]." ORDER BY added ASC LIMIT 1";
				  $result=mysql_query($query);
				  $topics[$i]["firstpost"]=mysql_result($result,0);

				  $query="SELECT author FROM bbs_posts WHERE topic=".$topics[$i]["id"]." ORDER BY added DESC LIMIT 1";
				  $result=mysql_query($query);
				  $topics[$i]["latest"]=mysql_result($result,0);
				  /*$query="SELECT nickname,avatar FROM users WHERE id=".$topics[$i]["latest"];
				  $result=mysql_query($query);
				  $topics[$i]["nickname_l"]=mysql_result($result,0,"nickname");
				  $topics[$i]["avatar_l"]=mysql_result($result,0,"avatar");*/

				  $query="SELECT author FROM bbs_posts WHERE topic=".$topics[$i]["id"]." ORDER BY added ASC LIMIT 1";
				  $result=mysql_query($query);
				  $topics[$i]["starter"]=mysql_result($result,0);
				  /*$query="SELECT nickname,avatar FROM users WHERE id=".$topics[$i]["starter"];
				  $result=mysql_query($query);
				  $topics[$i]["nickname"]=mysql_result($result,0,"nickname");
				  $topics[$i]["avatar"]=mysql_result($result,0,"avatar");*/

				$query="UPDATE bbs_topics SET lastpost='".$topics[$i]["lastpost"]."', firstpost='".$topics[$i]["firstpost"]."', userlastpost='".$topics[$i]["latest"]."', userfirstpost='".$topics[$i]["starter"]."', count='".$topics[$i]["replies"]."' where id=".$topics[$i]["id"];
				print($query."<br />");
				mysql_query($query);
	}
	print("bbstopics!<br />");
}

if ($cacheonelines||$all||$allcaches)
{
	create_cache_module("onelines", "SELECT oneliner.who,oneliner.message,users.nickname,users.avatar FROM oneliner,users WHERE oneliner.who=users.id ORDER BY oneliner.quand DESC LIMIT 50",0);
	print("cache oneliner!<br />");
}

if ($cachelatestcomments||$all||$allcaches)
{
	create_cache_module("latest_comments", "SELECT prods.id,prods.name,prods.type,prods.group1,prods.group2,prods.group3,comments.who,users.nickname,users.avatar FROM prods JOIN comments LEFT JOIN users ON users.id=comments.who WHERE comments.which=prods.id ORDER BY comments.quand DESC LIMIT 50",1);
	print("cache latest comments!<br />");
}

if ($cachetop_demos||$all||$allcaches)
{
	create_cache_module("top_demos", "SELECT prods.id, prods.name,prods.type,prods.group1,prods.group2,prods.group3 FROM prods WHERE prods.quand > DATE_SUB(sysdate(),INTERVAL '30' DAY) AND prods.quand < DATE_SUB(sysdate(),INTERVAL '0' DAY) ORDER BY (prods.views/((sysdate()-prods.quand)/100000)+prods.views)*prods.voteavg*prods.voteup desc LIMIT 50",1);
	print("cache topdemos!<br />");
}

if ($cachetop_keops||$all||$allcaches)
{
	create_cache_module("top_keops", "SELECT prods.id,prods.name,prods.type,prods.group1,prods.group2,prods.group3 FROM prods WHERE prods.rank!=0 ORDER BY prods.rank ASC LIMIT 50",1);
	print("cache top keops!<br />");
}

if ($cachecdclist||$all||$allcaches)
{
	create_cache_module("cdclist", "SELECT distinct prods.id as which,count(prods.id) as count,prods.name,prods.type,prods.group1,prods.group2,prods.group3 FROM users_cdcs JOIN prods on users_cdcs.cdc = prods.id group by prods.id order by count desc",1);
	print("cache cdc list!<br />");
}


if ($cachelatest_demos||$all||$allcaches)
{
	create_cache_module("latest_demos", "SELECT prods.id,prods.name,prods.type,prods.group1,prods.group2,prods.group3,prods.added,users.nickname,users.avatar FROM prods LEFT JOIN users ON users.id=prods.added ORDER BY prods.quand DESC LIMIT 50",1);
	create_cache_module("latest_released_prods", "SELECT prods.id,prods.name,prods.type,prods.group1,prods.group2,prods.group3 FROM prods ORDER BY prods.date DESC,prods.quand DESC LIMIT 50",1);
	create_cache_module("latest_released_parties", "select distinct parties.name, parties.id, prods.party_year, COUNT(prods.party) as prodcount from parties right join prods on prods.party=parties.id where parties.id!=1024 group by prods.party,prods.party_year order by prods.date desc, prods.id desc limit 50",0);
	print("cache latest demos!<br />");
	print("cache latest released prods!<br />");
	print("cache latest parties!<br />");
}

if ($cachestats||$all||$allcaches)
{
	create_stats_cache();
	print("cache stats!<br />");
}

if ($webtv||$all)
{
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
				$posid = strpos($contents, $dstring, $pos + $posend + strlen($dstring));
				$posend = strpos($contents, '"', $posid) - ($posid + strlen($dstring));
				$dtvid = substr($contents, $posid + strlen($dstring), $posend);
				//echo "p: " . $pouetid . " d: " . $dtvid . "<br />";

				//check db, update if needed
				$query = "select prod from downloadlinks where type like 'demoscene.tv' and prod = " . $pouetid;
				$result = mysql_query($query);
				while($tmp = mysql_fetch_array($result)) {
				  $prods[]=$tmp;
				}
				if (count($prods) == 0) {
					$query ="insert into downloadlinks set type = 'demoscene.tv', link = 'http://www.demoscene.tv/prod.php?id_prod=".$dtvid."', prod =". $pouetid;
					$result = mysql_query($query);
					echo "inserted, pouetid: " . $pouetid . " dtvid: " . $dtvid . "<br />";
				} else {
					echo "already existed, pouetid: " . $pouetid . " dtvid: " . $dtvid . "<br />";
				}
				unset($prods);
			}
			$pos = strpos($contents, $pstring, $pos + strlen($pstring));
		}
		print("demoscene.tv done!<br />");
	} else { print("demoscene.tv page is down!<br />"); }

	//if ($fcapped = fopen("http://capped.tv/rss.php?num=-1", "r")) // all
	if ($fcapped = fopen("http://capped.tv/rss.php", "r")) // latest
	{
		$contents = '';
		while (!feof($fcapped)) {
		  $contents .= fread($fcapped, 8192);
		}
		fclose($fcapped);

		$pstring = '<link>http://capped.tv/playeralt.php?vid=';
		$dstring = 'pouet.net/prod.php?which=';
		$pos = strpos($contents, $pstring);
		while ($pos != false)
		{
			$posend = strpos($contents, '</link>', $pos) - ($pos + strlen($pstring));
			$capped = substr($contents, $pos + strlen($pstring), $posend);
			//echo "posend: " . ($pos + $posend);
			$posid = strpos($contents, $dstring, ($pos + $posend));
			$posend = strpos($contents, '\'>Pouet</a>', $posid) - ($posid + strlen($dstring));
			$pouetid = substr($contents, $posid + strlen($dstring), $posend);
			//echo " posid: " . $posid . "p: " . $pouetid . " c: " . $capped . "<br />";

			$pos = strpos($contents, $pstring, $pos + strlen($pstring));

			if (!$pos || $posid < $pos)
			{
				unset($prods);
				//check db, update if needed
				$query = "select prod from downloadlinks where type like 'capped.tv' and prod = " . $pouetid;
				$result = mysql_query($query);
				while($tmp = mysql_fetch_array($result)) {
				  $prods[]=$tmp;
				}
				if (count($prods) == 0) {
					$query ="insert into downloadlinks set type = 'capped.tv', link = 'http://capped.tv/playeralt.php?vid=".$capped."', prod =". $pouetid;
					$result = mysql_query($query);
					echo "inserted, pouetid: " . $pouetid . " capped: " . $capped . "<br />";
				} else {
					echo "already existed, pouetid: " . $pouetid . " capped: " . $capped . "<br />";
				}
			} else { echo "no pouetid for capped: " . $capped . "<br />"; }

			//$pos = strpos($contents, $pstring, $pos + strlen($pstring));
		}
		print("capped.tv done!<br />");
	} else { print("capped.tv page is down!<br />"); }
	print("<br />webtv done!<br />");
}

mysql_close($db);

print("<hr><br />aiiiiiiiiiiiii cookie<br /><a href=\"/\">bolber a la vida loca</a><br />");
?>

</body>
