<?
require("top.php");

$query="SELECT author1,author2,vote_count FROM logos";
$result=mysql_query($query);
while($tmp=mysql_fetch_assoc($result)) {
	// count only voted up logos
	if($tmp['vote_count'] > 0)
	{
		if($tmp["author1"])
			$totals[$tmp["author1"]]+=20;
		if($tmp["author2"])
			$totals[$tmp["author2"]]+=20;
	}
}
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

require("bottom.php");
?>
