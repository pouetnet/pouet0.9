<?
require("inc/top.php");

if($which)
{
	// $query = "SELECT name,group1,group2,platform FROM prods WHERE id=".$which;
	$query = '
		SELECT name,group1,group2
		FROM prods
		WHERE id='.$which.'
	';
	$result=mysql_query($query);
	$prod=mysql_fetch_array($result);

	$query = '
		SELECT platforms.name
		FROM platforms, prods_platforms
		WHERE
			prods_platforms.prod = '.$which.' AND
			platforms.id = prods_platforms.platform
	';
	$result=mysql_query($query);
	$platforms=mysql_fetch_assoc($result);
}

if($prod["group1"])
{
  $query="SELECT name FROM groups WHERE id=".$prod["group1"];
  $result=mysql_query($query);
  $prod["group1"]=mysql_result($result,0);
} else $prod["group1"]="";

if($prod["group2"])
{
  $query="SELECT name FROM groups WHERE id=".$prod["group2"];
  $result=mysql_query($query);
  $prod["group2"]=mysql_result($result,0);
} else $prod["group2"]="";

if(file_exists("../../screenshots/".$which.".gif"))
  $prod["sshot"]="gif";
elseif(file_exists("../../screenshots/".$which.".jpg"))
  $prod["sshot"]="jpg";
elseif(file_exists("../../screenshots/".$which.".png"))
  $prod["sshot"]="png";

print($prod["name"]."\r\n");
print($prod["group1"]."\r\n");
print($prod["group2"]."\r\n");
print($prod["sshot"]."\r\n");
if(is_array($platforms)) {
	foreach($platforms as $p) {
		print($platforms['name']."\r\n");
	}
}

require("inc/bottom.php");
?>
