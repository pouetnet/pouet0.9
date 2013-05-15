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
$query = 'SELECT quand FROM prods ORDER BY quand DESC LIMIT 1';
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
unset($prods);
$query ='SELECT prods.id,prods.name,prods.date FROM prods ';
if ($platform) $query.=", prods_platforms, platforms ";
$query.="WHERE 1 ";
if ($platform) $query.="and prods_platforms.platform=platforms.id and platforms.name='".$platform."' and prods_platforms.prod=prods.id ";
if ($type) $query.='AND find_in_set("'.$type.'",prods.type)>0 ';
if ($group) $query.='AND (prods.group1="'.$group.'" OR prods.group2="'.$group.'" OR prods.group3="'.$group.'") ';
$query.='ORDER BY prods.date DESC, prods.quand DESC LIMIT '.$howmany;
$result = mysql_query($query);
while($row = mysql_fetch_assoc($result))
	$prods[] = $row;

// publishing each prod as <item>
foreach($prods as $p)
{
	$output.= $t.$t.'<item>'.$nl;
	$output.= $t.$t.$t.'<title>'.xmlentities(utf8_encode($p['name'])).'</title>'.$nl;
	$output.= $t.$t.$t.'<link>http://pouet.net/prod.php?which='.$p['id'].'</link>'.$nl;
	// enclosure tag
	unset($screenshot);
	if(file_exists('../screenshots/'.$p['id'].'.jpg')) {
		$screenshot = $p['id'].'.jpg';
	} elseif(file_exists('../screenshots/'.$p['id'].'.gif')) {
		$screenshot = $p['id'].'.gif';
	} elseif(file_exists('../screenshots/'.$p['id'].'.png')) {
		$screenshot = $p['id'].'.png';
	}
	if($screenshot)
	{
		$screenshot_size = filesize('../screenshots/'.$screenshot);
		$size = getimagesize('../screenshots/'.$screenshot);
		$screenshot_type = $size['mime'];
		$output.= $t.$t.$t.'<enclosure url="http://pouet.net/screenshots/'.$screenshot.'" length="'.$screenshot_size.'" type="'.$screenshot_type.'" />'.$nl;
	}
	$output.= $t.$t.$t.'<guid isPermaLink="false">'.$p['id'].'</guid>'.$nl;
	$output.= $t.$t.$t.'<pubDate>'.date('D, d M Y H:i:s',strtotime($p['date'])).' CST</pubDate>'.$nl;
	$output.= $t.$t.'</item>'.$nl;
}

$output.= $t.'</channel>'.$nl;
$output.= '</rss>'.$nl;

// closing DB
if (isset($dbl)) {
	mysql_close($dbl);
}

print($output);
?>
