<?
require("include/top.php");
require_once("include/misc.php");

if(!$_SESSION["SESSION"]||!$_SESSION["SCENEID"])
	$errormessage[]="you need to be logged in first.";

$title="An error has occured:";
$message=$_REQUEST["message"];

if (count($_GET)) die("nice try.");

$time=array();
switch ($_POST["type"]) {

	case "oneliner":
//	if($_SESSION["SCENEID_ID"]==19428): printf("im with stupid");
//	else:
		$url="index.php";
		$_REQUEST["message"] = trim($_REQUEST["message"]);
		if (!$_REQUEST["message"])
    	$errormessage[]="oh cut that out already";
		if (strpos($_REQUEST["message"],"pascalnet.net")!==FALSE && strpos($_REQUEST["message"],"pouet")!==FALSE)
    	$errormessage[]="&lt;/spam&gt;";
		if (strpos($_REQUEST["message"],"wod.untergrund.net")!==FALSE)
    	$errormessage[]="&lt;/spam&gt;";
    if($_SESSION["SCENEID_ID"]==78655)
      $errormessage[]="nope.";
		
		if($_REQUEST["message"]&&!$errormessage)
		{
	    $who=$_SESSION["SCENEID_ID"];
	    $query="SELECT who FROM oneliner ORDER BY quand DESC LIMIT 1";
	    $result=mysql_query($query);
	    $lastone=mysql_fetch_assoc($result);
	    $query="SELECT message FROM oneliner WHERE who = ".(int)$who." ORDER BY quand DESC LIMIT 1";
	    $result=mysql_query($query);
	    $lastmine=mysql_fetch_assoc($result);

	    if($lastone["who"]!=$who && trim($lastmine["message"])!=trim($message))
	    {
	      $title="You've successfully added the following oneline:";
        $query="INSERT INTO oneliner SET who=".$who.", quand=NOW(), message='".addslashes($message)."'";
        mysql_query($query);
        //$title="HOLD ON A SEC (i'm fixing shit.) --garg";
      }
	    else
	    {
	      $title="ERROR! DOUBLEPOST == ROB IS JARIG!";
	    }

	    create_cache_module("onelines", "SELECT oneliner.who,oneliner.message,users.nickname,users.avatar FROM oneliner,users WHERE oneliner.who=users.id ORDER BY oneliner.quand DESC LIMIT 50",0);
		}
//	endif;
	break;
	
	case "topic":
		$url="bbs.php";
		//if (stristr($topic,"random")!==FALSE)
		if (date("Y-m-d")=="2008-11-19")
  		$errormessage[] = "bbs is closed today. go use the other 5000 threads for a change.";
		
		if($topic&&$message&&!$errormessage)
		{
		    $query="SELECT topic FROM bbs_topics ORDER BY lastpost DESC LIMIT 1";
		    $result=mysql_query($query);
		    $lastone=mysql_fetch_assoc($result);
		    if($lastone["topic"]!=$topic)
		    	{
			    $title="You've successfully added the following topic:";
			    $query="INSERT bbs_topics SET topic='".$topic."',category='".(int)$_POST["category"]."',lastpost=NOW(),firstpost=NOW(),userlastpost=".$_SESSION["SCENEID_ID"].",userfirstpost=".$_SESSION["SCENEID_ID"];
			    mysql_query($query);
			    $lastid=mysql_insert_id();
			    $query="INSERT bbs_posts SET topic=".$lastid.",post='".addslashes($message)."',author=".$_SESSION["SCENEID_ID"].",added=NOW()";
			    mysql_query($query);
			}
		}
	break;

	case "post":
	  $which = (int)$which;
		$url="topic.php?which=".$which;
		if ($which==6618) die();
		if ($which==7465) die();
		if ($which==2735) die();
		
		if($which&&$message&&!$errormessage)
		{
		  if (strstr($message,"dvdvideotools")!==false)
		    die("FU!");
		  if (strstr($message,"netetrader.com")!==false)
		    die("FU!");
		
	    $query="SELECT author,topic,post FROM bbs_posts ORDER BY added DESC LIMIT 1";
	    $result=mysql_query($query);
	    $lastone=mysql_fetch_assoc($result);
	    $query="SELECT id FROM bbs_topics where id=".$which;
	    $result=mysql_query($query);
	    $secretbbs=mysql_fetch_assoc($result);
	    if( ($lastone["author"]==$_SESSION["SCENEID_ID"]) && ($lastone["topic"]==$which) && (strcmp($lastone["post"],addslashes($message))==0) )
	    {
	    }
	    else
	    {
	    	if ($secretbbs)
	    	{
	    		$query="SELECT count(0) FROM bbs_posts WHERE topic=".$which;
	    		$result=mysql_query($query);
  				$count=mysql_result($result,0);
  				// im not increasing its value couz we want count to have number of replies, not total of posts.
  				//$count++;
			    $title="You've successfully added the following bbs post:";
			    $query="UPDATE bbs_topics SET lastpost=NOW(),count=".$count.",userlastpost=".$_SESSION["SCENEID_ID"]." WHERE id=".$which;
			    mysql_query($query);
			    $query="INSERT bbs_posts SET topic=".$which.",post='".addslashes($message)."',author=".$_SESSION["SCENEID_ID"].",added=NOW()";
			    mysql_query($query);
  			}
	    }
		}
	break;

	case "comment":
		$url="prod.php?which=".$which;
		$message=$comment;
		if (strstr($comment,"freecliptv.samsonshome.de")!==false)
		  $errormessage[] = "please post video links to <a href='http://www.pouet.net/topic.php?which=1024'>this thread</a>";
		
		if (strstr($comment,"EmwW_6kUdHw")!==false)
		  $errormessage[] = "please post video links to <a href='http://www.pouet.net/topic.php?which=1024'>this thread</a> - also, your link has been removed from youtube.";

	//if($_SESSION["SCENEID_ID"]==2100)
//	$comment = "hi! i'm dubmood and i suck!\n\nps. i have a small penis.";
//if(!strcasecmp(trim($message),"Optimus, you have a small penis"))
//  $comment = "hi - i come from $REMOTE_ADDR";
		if($which&&trim($comment)&&$rating&&!$errormessage) {
			$query="SELECT who FROM comments where comment='".addslashes($comment)."' and who=".$_SESSION["SCENEID_ID"]." and which=".$which." ORDER BY quand DESC LIMIT 1";
			    $timestart = microtime_float();
		    	$result=mysql_query($query);
		    	$time["query1"] = microtime_float() - $timestart;
		    	$lastone=mysql_fetch_assoc($result);
		    	if($lastone["who"]!=$_SESSION["SCENEID_ID"])
		    	{
			    $title="You've successfully added the following comment:";
			    $query="SELECT count(0) FROM comments WHERE who=".$_SESSION["SCENEID_ID"]." AND which=".$which." AND rating!=0";
			    $timestart = microtime_float();
			    $result=mysql_query($query);
		    	$time["query2"] = microtime_float() - $timestart;
			    if(mysql_result($result,0))
			      $rating="isok";
			    switch($rating) {
			      case "rulez": $rating=1; break;
			      case "sucks": $rating=-1; break;
			      default: $rating=0;
			    }
			    $query="INSERT comments SET comment='".addslashes($comment)."',who=".$_SESSION["SCENEID_ID"].",which=".$which.",rating=".$rating.",quand=NOW()";
			    $timestart = microtime_float();
			    mysql_query($query);
		    	$time["query3"] = microtime_float() - $timestart;
			
			    //update vote info
			    	unset($commentss);
				//unset($checktable);
				$checktable = array();
				
				$rulez=0;
				$piggie=0;
				$sucks=0;
				$total=0;
			    	$query  = "SELECT comments.rating,comments.who FROM comments WHERE comments.which='".$which."'";
		    $timestart = microtime_float();
				$result=mysql_query($query);
	    	$time["query4"] = microtime_float() - $timestart;
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
				$query="UPDATE prods SET voteup=".$rulez.", votepig=".$piggie.", votedown=".$sucks.", voteavg='".$avg."' where id=".$which;
				//print($query);
		    $timestart = microtime_float();
				mysql_query($query);
	    	$time["query5"] = microtime_float() - $timestart;
			
			}
	    $timestart = microtime_float();
			//create_cache_module("latest_comments", "SELECT prods.id,prods.name,prods.type,prods.group1,prods.group2,prods.group3,comments.who,users.nickname,users.avatar FROM prods JOIN comments LEFT JOIN users ON users.id=comments.who WHERE comments.which=prods.id ORDER BY comments.quand DESC LIMIT 20",1);
			$sql = "SELECT prods.id,prods.name,prods.type,prods.group1,prods.group2,prods.group3,comments.who,users.nickname,users.avatar,".
             " g1.name as groupname1,g1.acronym as groupacron1, ".
             " g2.name as groupname2,g2.acronym as groupacron2, ".
             " g3.name as groupname3,g3.acronym as groupacron3, ".
             " GROUP_CONCAT(platforms.name) as platform ".
			       " FROM prods ".
			       " JOIN comments JOIN prods_platforms JOIN platforms ".
			       " LEFT JOIN users ON users.id=comments.who ".
             " LEFT JOIN groups AS g1 ON prods.group1 = g1.id".
             " LEFT JOIN groups AS g2 ON prods.group2 = g2.id".
             " LEFT JOIN groups AS g3 ON prods.group3 = g3.id".
             " AND prods_platforms.prod=prods.id ".
             " AND prods_platforms.platform=platforms.id ".
			       " WHERE comments.which=prods.id ".
             " GROUP BY comments.id ".
			       " ORDER BY comments.quand DESC LIMIT 20";
			//create_cache_module("latest_comments", $sql, 0);
      create_cache_module("latest_comments", "SELECT prods.id,prods.name,prods.type,prods.group1,prods.group2,prods.group3,comments.who,users.nickname,users.avatar FROM prods JOIN comments LEFT JOIN users ON users.id=comments.who WHERE comments.which=prods.id ORDER BY comments.quand DESC LIMIT 20",1);
    	$time["cache1"] = microtime_float() - $timestart;
	    $timestart = microtime_float();
			create_cache_module("top_demos", "SELECT prods.id, prods.name,prods.type,prods.group1,prods.group2,prods.group3 FROM prods WHERE prods.quand > DATE_SUB(sysdate(),INTERVAL '30' DAY) AND prods.quand < DATE_SUB(sysdate(),INTERVAL '0' DAY) ORDER BY (prods.views/((sysdate()-prods.quand)/100000)+prods.views)*prods.voteavg*prods.voteup desc LIMIT 50",1);
    	$time["cache2"] = microtime_float() - $timestart;
		}
	break;

	default:
		$url="index.php";
}

if($errormessage)
{
	unset($message);
	for($i=0;$i<count($errormessage);$i++)
		$message .= "- ".$errormessage[$i]."<br>";
}

debuglog(var_export($time,true));
?>
<br>
<table bgcolor="#000000" cellspacing="1" cellpadding="0">
 <tr>
  <td>
   <table bgcolor="#000000" cellspacing="1" cellpadding="2">
    <tr>
     <td bgcolor="#224488" align="center" nowrap>
      <b><?=$title?></b><br>
     </td>
    </tr>
    <tr>
     <td bgcolor="#557799">
	<? print(stripslashes( htmlspecialchars($message))); ?>
     </td>
    </tr>
    <tr>
     <td bgcolor="#446688" align="center">
      <a href="<?=$url?>"><b>get back</b></a><br>
     </td>
    </tr>
   </table>
  </td>
 </tr>
</table>
<br>
<? require("include/bottom.php"); ?>
