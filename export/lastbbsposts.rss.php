<?
header("Content-type: text/xml");
include('../include/auth.php');

function xmlentities ( $string )
{
   return str_replace ( array ( '&', '"', "'", '<', '>' ), array ( '&amp;' , '&quot;', '&apos;' , '&lt;' , '&gt;' ), $string );
}

$nl = "\n";
$t = "\t";

if(!$howmany||$howmany<0||$howmany>100)
	$howmany=10;

unset($output);

// opening DB
$dbl = mysql_connect($db['host'], $db['user'], $db['password']);
if(!$dbl) {
	die('SQL error... sorry ! ^^; I\'m on it !');
}
mysql_select_db($db['database'],$dbl);

$output = '<?xml version="1.0" encoding="UTF-8"?>'.$nl;
$output.= '<rss version="2.0">'.$nl;
$output.= $t.'<channel>'.$nl;
$output.= $t.$t.'<title>pouet.net</title>'.$nl;
$output.= $t.$t.'<link>http://pouet.net/</link>'.$nl;
$output.= $t.$t.'<description>your online demoscene resource</description>'.$nl;
$output.= $t.$t.'<language>en-us</language>'.$nl;
$output.= $t.$t.'<copyright>Copyright 2000-'.date('Y').' Mandarine</copyright>'.$nl;
$output.= $t.$t.'<managingEditor>webmaster@pouet.net</managingEditor>'.$nl;
$output.= $t.$t.'<webMaster>webmaster@pouet.net</webMaster>'.$nl;
// lastBuildDate
$query = 'SELECT lastpost FROM bbs_topics ORDER BY lastpost DESC LIMIT 1';
$result = mysql_query($query);
$lastBuildDate = date('D, d M Y H:i:s',strtotime(mysql_result($result, 0)));
$output.= $t.$t.'<lastBuildDate>'.$lastBuildDate.' CST</lastBuildDate>'.$nl;
$output.= $t.$t.'<generator>pouet.net</generator>'.$nl;
$output.= $t.$t.'<docs>http://backend.userland.com/rss</docs>'.$nl;
$output.= $t.$t.'<ttl>60</ttl>'.$nl;
// logo
$output.= $t.$t.'<image>'.$nl;
$output.= $t.$t.$t.'<url>http://pouet.net/gfx/buttons/pouet.gif</url>'.$nl;
$output.= $t.$t.$t.'<title>pouet.net</title>'.$nl;
$output.= $t.$t.$t.'<link>http://pouet.net/</link>'.$nl;
$output.= $t.$t.$t.'<width>88</width>'.$nl;
$output.= $t.$t.$t.'<height>31</height>'.$nl;
$output.= $t.$t.$t.'<description>your online demoscene resource</description>'.$nl;
$output.= $t.$t.'</image>'.$nl;

// getting data
unset($posts);
$query ='SELECT id,topic,lastpost FROM bbs_topics ORDER BY lastpost DESC LIMIT '.$howmany;
$result = mysql_query($query);
while($row = mysql_fetch_assoc($result))
	$topics[] = $row;

// publishing each prod as <item>
foreach($topics as $topic)
{
	$output.= $t.$t.'<item>'.$nl;
	$output.= $t.$t.$t.'<title>'.xmlentities(utf8_encode($topic['topic'])).'</title>'.$nl;
	$output.= $t.$t.$t.'<link>http://pouet.net/topic.php?which='.$topic['id'].'</link>'.$nl;
	$output.= $t.$t.$t.'<guid isPermaLink="false">'.$topic['id'].'</guid>'.$nl;
	$output.= $t.$t.$t.'<pubDate>'.date('D, d M Y H:i:s',strtotime($topic['lastpost'])).' CST</pubDate>'.$nl;
	$output.= $t.$t.'</item>'.$nl;
}

$output.= $t.'</channel>'.$nl;
$output.= '</rss>'.$nl;

// closing DB
if (isset($dbl)) {
	mysql_close($dbl);
}

// print('<pre>'.$nl);
// print(htmlentities($output));
// print('</pre>'.$nl);

print($output);
?>
