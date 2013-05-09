<?
header("Content-type: text/xml");
include('../include/auth.php');
require("../include/libbb.php");

function xmlentities ( $string )
{
   return str_replace ( array ( '&', '"', "'", '<', '>' ), array ( '&amp;' , '&quot;', '&apos;' , '&lt;' , '&gt;' ), $string );
}

$nl = "\n";
$t = "\t";
unset($output);
$output = '<?xml version="1.0" encoding="UTF-8"?>'.$nl;
$output.= '<xnfo standard="1.1" version="1" author="webmaster@pouet.net" mode="partial">'.$nl;


// opening DB
$dbl = mysql_connect($db['host'], $db['user'], $db['password']);
if(!$dbl) {
  die('SQL error... sorry ! ^^; I\'m on it !');
}
mysql_select_db($db['database'],$dbl);

// getting data
$query ='SELECT * FROM prods ';
if(!$which) {
  $query.='ORDER BY RAND()';
} else {
  $query.='WHERE id='.$which;
}
$query.=' LIMIT 1';
$result = mysql_query($query);
$prod = mysql_fetch_array($result);

$output.= $t.'<demo pouet_id="'.$prod['id'].'">'.$nl;
$output.= $t.$t.'<name>'.utf8_encode(xmlentities($prod['name'])).'</name>'.$nl;

// category
// list of XNFO hardcoded categories
$xnfo_categories = Array (
  '32b'         => Array('intro;32bytes',   'Intro 32 Bytes'),
  '64b'         => Array('intro;64bytes',   'Intro 64 Bytes'),
  '128b'        => Array('intro;128bytes',  'Intro 128 Bytes'),
  '256b'        => Array('intro;256bytes',  'Intro 256 Bytes'),
  '1k'          => Array('intro;1k',        'Intro 1 Kb'),
  '4k'          => Array('intro;4k',        'Intro 4 Kb'),
  '8k'          => Array('intro;8k',        'Intro 8 Kb'),
  '32k'         => Array('intro;40k',       'Intro 32 Kb'),
  '40k'         => Array('intro;40k',       'Intro 40 Kb'),
  '64k'         => Array('intro;64k',       'Intro 64 Kb'),
  '80k'         => Array('intro;80k',       'Intro 80 Kb'),
  '96k'         => Array('intro;96k',       'Intro 96 Kb'),
  '100k'        => Array('intro;100k',      'Intro 100 Kb'),
  '128k'        => Array('intro;128k',      'Intro 128 Kb'),
  '256k'        => Array('intro;256k',      'Intro 256 Kb'),
  'artpack'     => Array('artpack',         'ArtPack'),
  'bbstro'      => Array('bbstro',          'BBStro'),
  'cracktro'    => Array('cracktro',        'Cracktro'),
  'demo'        => Array('demo',            'Demo'),
  'demopack'    => Array('demopack',        'DemoPack'),
  'demotool'    => Array('demotool',        'DemoTool'),
  'dentro'      => Array('dentro',          'Dentro'),
  'diskmag'     => Array('diskmag',         'Diskmag'),
  'fastdemo'    => Array('fastdemo',        'Fastdemo'),
  'game'        => Array('game',            'Game'),
  'intro'       => Array('intro',           'Intro'),
  'invitation'  => Array('invitation',      'Invitation'),
  'liveact'     => Array('liveact',         'Live Act'),
  'musicdisk'   => Array('musicdisk',       'Musicdisk'),
  'report'      => Array('report',          'Party Report'),
  'slideshow'   => Array('slideshow',       'Slideshow'),
  'votedisk'    => Array('votedisk',        'Vote Disk'),
  'wild'        => Array('wild',            'Wild')
  );

$output.= $t.$t.'<category type="'.$xnfo_categories[$prod['type']][0].'">';
$output.= xmlentities(utf8_encode($xnfo_categories[$prod['type']][1]));
$output.= '</category>'.$nl;

// party info
if($prod['party'])
{
  $output.= $t.$t.'<release>'.$nl;
  $query = 'SELECT name,web FROM parties WHERE id='.$prod['party'];
  $result = mysql_query($query);
  $party = mysql_fetch_assoc($result);
  if($party['web'])
    $output.= $t.$t.$t.'<party url="'.$party['web'].'">'.xmlentities(utf8_encode($party['name'])).'</party>'.$nl;
  else
    $output.= $t.$t.$t.'<party>'.xmlentities(utf8_encode($party['name'])).'</party>'.$nl;
  if($prod['date'])
    $output.= $t.$t.$t.'<date>'.substr($prod['date'],0,10).'</date>'.$nl;
  if($prod['party_place'])
    $output.= $t.$t.$t.'<rank>'.$prod['party_place'].'</rank>'.$nl;
  if($prod['partycompo'])
    $output.= $t.$t.$t.'<compo>'.$prod['partycompo'].'</compo>'.$nl;
  $output.= $t.$t.'</release>'.$nl;
}
if($prod['date'])
  $output.= $t.$t.'<releaseDate>'.substr($prod['date'],0,10).'</releaseDate>'.$nl;

// groups info
if($prod['group1'] || $prod['group2'] || $prod['group3'])
{
  $output.= $t.$t.'<authors>'.$nl;
  if($prod['group1'])
  {
    $query = 'SELECT name FROM groups WHERE id='.$prod['group1'];
    $result = mysql_query($query);
    $group1 = mysql_result($result,0);
    $output.= $t.$t.$t.'<group pouet_id="'.$prod['group1'].'">'.xmlentities(utf8_encode($group1)).'</group>'.$nl;
  }
  if($prod['group2'])
  {
    $query = 'SELECT name FROM groups WHERE id='.$prod['group2'];
    $result = mysql_query($query);
    $group2 = mysql_result($result,0);
    $output.= $t.$t.$t.'<group pouet_id="'.$prod['group2'].'">'.xmlentities(utf8_encode($group2)).'</group>'.$nl;
  }
  if($prod['group3'])
  {
    $query = 'SELECT name FROM groups WHERE id='.$prod['group3'];
    $result = mysql_query($query);
    $group2 = mysql_result($result,0);
    $output.= $t.$t.$t.'<group pouet_id="'.$prod['group3'].'">'.xmlentities(utf8_encode($group2)).'</group>'.$nl;
  }

  $output.= $t.$t.'</authors>'.$nl;
}

$query="select platforms.name, platforms.icon from prods_platforms, platforms where prods_platforms.prod='".$which."' and platforms.id=prods_platforms.platform";
    $result = mysql_query($query);
    while($tmp = mysql_fetch_array($result)) {
       $platforms[]=$tmp;
    }

// platform info
// list of XNFO hardcoded platforms
$xnfo_platforms = Array (
  'Windows' => Array('win32', 'Windows'),
  'MS-Dos'  => Array('msdos', 'Ms-Dos'),
  'MS-Dos/gus'  => Array('msdos', 'Ms-Dos'),
  'Java'    => Array('java',  'Java'),
  'Flash'   => Array('flash', 'Flash'),
  'Linux'   => Array('linux', 'Linux'),
  'MacOS X' => Array('macosx',  'Mac OS X')
  );
if(count($platforms))
{
  $tag_support_opened = false;
  //$platforms = explode(",", $prod["platform"]);
  foreach($platforms as $p) {
    if(count($xnfo_platforms[$p["name"]])==2)
    {
      if(!$tag_support_opened)
      {
        $output.= $t.$t.'<support>'.$nl;
        $tag_support_opened = true;
      }
      $output.= $t.$t.$t.'<configuration>'.$nl;
      $output.= $t.$t.$t.$t.'<platform type="'.$xnfo_platforms[$p["name"]][0].'">'.xmlentities(utf8_encode($xnfo_platforms[$p["name"]][1])).'</platform>'.$nl;
      $output.= $t.$t.$t.'</configuration>'.$nl;
    }
  }
  if($tag_support_opened)
    $output.= $t.$t.'</support>'.$nl;
}


$output.= $t.$t.'<download>'.$nl;
$output.= $t.$t.$t.'<url type="download">'.xmlentities(utf8_encode($prod['download'])).'</url>'.$nl;

$result = mysql_query(sprintf("select * from downloadlinks where prod=%d",$which));
while($tmp = mysql_fetch_array($result)) {
  $output.= $t.$t.$t.'<url type="'.xmlentities(utf8_encode($tmp['type'])).'">'.xmlentities(utf8_encode($tmp['link'])).'</url>'.$nl;
}

$output.= $t.$t.'</download>'.$nl;

if(file_exists("../screenshots/".$prod["id"].".jpg")) {
  $shotpath = "http://www.pouet.net/screenshots/".$prod["id"].".jpg";
} elseif(file_exists("../screenshots/".$prod["id"].".gif")) {
  $shotpath = "http://www.pouet.net/screenshots/".$prod["id"].".gif";
} elseif(file_exists("../screenshots/".$prod["id"].".png")) {
  $shotpath = "http://www.pouet.net/screenshots/".$prod["id"].".png";
}

$output.= $t.$t.'<screenshot>'.$nl;
$output.= $t.$t.$t.'<url>'.xmlentities(utf8_encode($shotpath)).'</url>'.$nl;
$output.= $t.$t.'</screenshot>'.$nl;

$output.= $t.'</demo>'.$nl;
$output.= '</xnfo>'.$nl;

// closing DB
if (isset($dbl)) {
  mysql_close($dbl);
}

print($output);
?>
