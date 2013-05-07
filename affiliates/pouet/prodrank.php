<?
require("top.php");

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

require("bottom.php");
?>
