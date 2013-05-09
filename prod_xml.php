<?php
echo "<?xml version=\"1.0\" encoding=\"UTF-8\" standalone=\"yes\" ?>\n";

require('include/auth.php');

$dbl = mysql_connect($db['host'], $db['user'], $db['password']);
if(!$dbl) {
	die('<pouet>\n\t<error>SQL error</error>\n</pouet>\n');
}
mysql_select_db($db['database'],$dbl);

$query ="SELECT * FROM prods WHERE id=".(int)$_GET["id"];
$result = mysql_query($query);
$prod = mysql_fetch_array($result);

$query="SELECT nickname,avatar FROM users WHERE id=".$prod["added"];
$result=mysql_query($query);
$tmp=mysql_fetch_array($result);
$prod["nickname"]=$tmp["nickname"];
$prod["avatar"]=$tmp["avatar"];

if($prod["party"])
{
  $query="SELECT name FROM parties WHERE id=".$prod["party"];
  $result=mysql_query($query);
  $prod["partyname"]=mysql_result($result,0);
}
if($prod["invitation"])
{
/*
  $typess = explode(",", $prod["type"]);
  for($i=0;$i<count($typess);$i++) {
    if ($typess[$i]=="bbstro") $bbstroalert = 1;
  }
  if ($bbstroalert) { $query="SELECT name FROM bbses WHERE id=".$prod["invitation"]; }
  else
*/
   { $query="SELECT name FROM parties WHERE id=".$prod["invitation"]; }
  $result=mysql_query($query);
  $prod["invitationpartyname"]=mysql_result($result,0);
}
if($prod["group1"])
{
  $query="SELECT name,web,acronym FROM groups WHERE id=".$prod["group1"];
  $result=mysql_query($query);
  $tmp=mysql_fetch_array($result);
  $prod["groupname1"]=$tmp["name"];
  $prod["groupweb1"]=$tmp["web"];
  $prod["groupacron1"]=$tmp["acronym"];
}
if($prod["group2"])
{
  $query="SELECT name,web,acronym FROM groups WHERE id=".$prod["group2"];
  $result=mysql_query($query);
  $tmp=mysql_fetch_array($result);
  $prod["groupname2"]=$tmp["name"];
  $prod["groupweb2"]=$tmp["web"];
  $prod["groupacron2"]=$tmp["acronym"];
}
if($prod["group3"])
{
  $query="SELECT name,web,acronym FROM groups WHERE id=".$prod["group3"];
  $result=mysql_query($query);
  $tmp=mysql_fetch_array($result);
  $prod["groupname3"]=$tmp["name"];
  $prod["groupweb3"]=$tmp["web"];
  $prod["groupacron3"]=$tmp["acronym"];
}

$query="select platforms.name, platforms.icon from prods_platforms, platforms where prods_platforms.prod='".$id."' and platforms.id=prods_platforms.platform";
$result = mysql_query($query);
while($tmp = mysql_fetch_array($result)) {
 $platforms[]=$tmp;
}



/*
// if we want other parties?
 $query="SELECT prodotherparty.party, prodotherparty.party_place, prodotherparty.party_year, prodotherparty.partycompo, parties.name FROM prodotherparty LEFT JOIN parties ON parties.id=prodotherparty.party WHERE prod=".$prod["id"];
 $result = mysql_query($query);
 while($tmp = mysql_fetch_array($result)) {
  $prodotherparties[]=$tmp;
 }

// we might need screenshots later on
if(file_exists("screenshots/".$prod["id"].".jpg")) {
  $shotpath = "screenshots/".$prod["id"].".jpg";
} elseif(file_exists("screenshots/".$prod["id"].".gif")) {
  $shotpath = "screenshots/".$prod["id"].".gif";
} elseif(file_exists("screenshots/".$prod["id"].".png")) {
  $shotpath = "screenshots/".$prod["id"].".png";
}
*/
echo "<pouet id=\"".$prod["id"]."\">\n".
     "\t<name>".stripslashes($prod["name"])."</name>\n".
     ($prod["group1"]?"\t<group id=\"".$prod["group1"]."\">".$prod["groupname1"]."</group>\n":"").
     ($prod["group2"]?"\t<group id=\"".$prod["group2"]."\">".$prod["groupname2"]."</group>\n":"").
     ($prod["group3"]?"\t<group id=\"".$prod["group3"]."\">".$prod["groupname3"]."</group>\n":"").
     ($prod["csdb"]?"\t<csdb id=\"".$prod["csdb"]."\" />\n":"").
     ($prod["zxdemo"]?"\t<zxdemo id=\"".$prod["zxdemo"]."\" />\n":"").
     ($prod["sceneorg"]?"\t<sceneorg id=\"".$prod["sceneorg"]."\" />\n":"");

// if(file_exists("nfo/".$prod["id"].".nfo"))
// not including file info at least now since it takes lots of space
// ...
// same for screenshots: print($shotpath); print($mysize[3]);

//$platforms = explode(",", $prod["platform"]);
for($i=0;$i<count($platforms);$i++)
{
  echo "\t<platform>".$platforms[$i]["name"]."</platform>\n";
}

$types = explode(",", $prod["type"]);
for($i=0;$i<count($types);$i++)
{
  echo "\t<type>".$types[$i]."</type>\n";
}

// do we want invitation extra info? perhaps later on...
// if($prod["invitation"]!=0):
// print($prod["invitationpartyname"]);

echo "\t<date>".$prod["date"]."</date>\n".
     "\t<party id=\"".$prod["party"]."\">".$prod["partyname"]."</party>\n".
     "\t<compo>".$prod["partycompo"]."</compo>\n".
     "\t<rank>".$prod["party_place"]."</rank>\n".

// other parties?
// for($i=0;$i<count($prodotherparties);$i++):

// affils?
// if(count($affils)):
// print($affils[$i]["prod2"]);
// print($affils[$i]["type"]);

     ($prod["download"]?"\t<download>".str_replace("&", "&amp;", $prod["download"])."</download>\n":"");
//     ($prod["download2"]?"\t<download>".str_replace("&", "&amp;", $prod["download2"])."</download>\n":"").
//     ($prod["download3"]?"\t<download>".str_replace("&", "&amp;", $prod["download3"])."</download>\n":"").
//     ($prod["download4"]?"\t<download>".str_replace("&", "&amp;", $prod["download4"])."</download>\n":"").
//     ($prod["download5"]?"\t<download>".str_replace("&", "&amp;", $prod["download5"])."</download>\n":"").
//     ($prod["video"]?"\t<video>".str_replace("&", "&amp;", $prod["video"])."</video>\n":"").
//     ($prod["source"]?"\t<source>".str_replace("&", "&amp;", $prod["source"])."</source>\n":"");

$query="SELECT downloadlinks.id,downloadlinks.link,downloadlinks.type FROM downloadlinks WHERE downloadlinks.prod=".$prod["id"]." ORDER BY downloadlinks.type";
	$result = mysql_query($query);
	while($tmp=mysql_fetch_array($result)) {
	  $dl[]=$tmp;
	}
for ($i=0;$i<count($dl);$i++):
	echo "\t<".$dl[$i]["type"].">".str_replace("&", "&amp;", $dl[$i]["link"])."</".$dl[$i]["type"].">\n";
endfor;

echo "</pouet>\n";

?>
