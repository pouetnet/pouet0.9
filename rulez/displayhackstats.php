<?
session_start();
if (!($SESSION_LEVEL=='administrator' || $SESSION_LEVEL=='moderator' || $SESSION_LEVEL=='gloperator'))
  die("OMG");

require("../include/top.php");

$year = $sceneorgyear;

?>
<style type="text/css">
.view {
  background: black;
  border-collapse: separate;
  border-spacing: 1px;
  border: 1px solid black;
  margin: 10px;
  width: 500px;
}
.view td {
  margin: 0px;
  padding: 2px;
}
.view img {
  vertical-align: text-top;
  margin-right: 2px;
}
.view th {
  font-size: 120%;
  padding: 5px;
  text-align: center;
}
#container {
  width: 750px;
  margin: 0px auto;
}
</style>

<div id='container'>
<?
$data = array();

$r = mysql_query("SELECT COUNT(*) as c,left(date,4) AS month FROM prods WHERE FIND_IN_SET(type,'demo')>0 OR FIND_IN_SET(type,'invitation')>0 GROUP BY month ORDER BY month") or die(mysql_error());
while ($o = mysql_fetch_object($r)) { $data[$o->month]["demo"] = $o->c; }

$r = mysql_query("SELECT COUNT(*) as c,left(date,4) AS month FROM prods WHERE FIND_IN_SET(type,'4k')>0 GROUP BY month ORDER BY month") or die(mysql_error());
while ($o = mysql_fetch_object($r)) { $data[$o->month]["4k"] = $o->c; }

$r = mysql_query("SELECT COUNT(*) as c,left(date,4) AS month FROM prods WHERE FIND_IN_SET(type,'64k')>0 GROUP BY month ORDER BY month") or die(mysql_error());
while ($o = mysql_fetch_object($r)) { $data[$o->month]["64k"] = $o->c; }

$r = mysql_query("SELECT COUNT(*) as c,left(date,4) AS month FROM prods JOIN prods_platforms ON prods.id = prods_platforms.prod AND prods_platforms.platform = 68 GROUP BY month ORDER BY month") or die(mysql_error());
while ($o = mysql_fetch_object($r)) { $data[$o->month]["win"] = $o->c; }

$r = mysql_query("SELECT COUNT(*) as c,left(date,4) AS month FROM prods JOIN prods_platforms ON prods.id = prods_platforms.prod AND prods_platforms.platform = 66 GROUP BY month ORDER BY month") or die(mysql_error());
while ($o = mysql_fetch_object($r)) { $data[$o->month]["linux"] = $o->c; }

$r = mysql_query("SELECT COUNT(*) as c,left(date,4) AS month FROM prods JOIN prods_platforms ON prods.id = prods_platforms.prod AND prods_platforms.platform = 76 GROUP BY month ORDER BY month") or die(mysql_error());
while ($o = mysql_fetch_object($r)) { $data[$o->month]["c64"] = $o->c; }

$r = mysql_query("SELECT COUNT(*) as c,left(date,4) AS month FROM prods JOIN prods_platforms ON prods.id = prods_platforms.prod AND prods_platforms.platform = 90 GROUP BY month ORDER BY month") or die(mysql_error());
while ($o = mysql_fetch_object($r)) { $data[$o->month]["flash"] = $o->c; }

$r = mysql_query("SELECT COUNT(*) as c,left(date,4) AS month FROM prods JOIN prods_platforms ON prods.id = prods_platforms.prod AND prods_platforms.platform = 98 GROUP BY month ORDER BY month") or die(mysql_error());
while ($o = mysql_fetch_object($r)) { $data[$o->month]["js"] = $o->c; }

$r = mysql_query("SELECT COUNT(*) as c,left(date,4) AS month FROM prods JOIN prods_platforms ON prods.id = prods_platforms.prod AND (prods_platforms.platform = 83 OR prods_platforms.platform = 139) GROUP BY month ORDER BY month") or die(mysql_error());
while ($o = mysql_fetch_object($r)) { $data[$o->month]["mac"] = $o->c; }

$r = mysql_query("SELECT COUNT(*) as c,left(date,4) AS month FROM prods JOIN prods_platforms ON prods.id = prods_platforms.prod AND (prods_platforms.platform = 71 OR prods_platforms.platform = 73) GROUP BY month ORDER BY month") or die(mysql_error());
while ($o = mysql_fetch_object($r)) { $data[$o->month]["amiga"] = $o->c; }

$r = mysql_query("SELECT COUNT(*) as c,left(date,4) AS month FROM prods JOIN prods_platforms ON prods.id = prods_platforms.prod AND (prods_platforms.platform = 67 OR prods_platforms.platform = 69) GROUP BY month ORDER BY month") or die(mysql_error());
while ($o = mysql_fetch_object($r)) { $data[$o->month]["dos"] = $o->c; }

$r = mysql_query("SELECT COUNT(*) as c,left(date,4) AS month FROM prods JOIN prods_platforms ON prods.id = prods_platforms.prod AND (prods_platforms.platform IN (80,126,132,70,72,96,117,109)) GROUP BY month ORDER BY month") or die(mysql_error());
while ($o = mysql_fetch_object($r)) { $data[$o->month]["atari"] = $o->c; }


ksort($data);

$keys = array();
foreach($data as $k=>$v) {
//  var_dump(array_keys($v));
  $keys = array_unique(array_merge($keys,array_keys($v)));
  //if (substr($k,-3,3)=="-00") unset($data[$k]);
}
echo "<table class='view'>\n";
echo "<tr>\n";
echo " <th>year</th>\n";
foreach($keys as $q)
  echo " <th>".$q."</th>\n";
echo "</tr>\n";
foreach($data as $k=>$v) {
  printf("<tr class='bg%d'>\n",($n++&1)+1);
  echo "<td>".$k."</td>\n";
  foreach($keys as $q)
    echo "<td>".(int)$v[$q]."</td>\n";
  echo "</tr>\n";
}
echo "</table>\n";

$sq = "SELECT DISTINCT LEFT(date,7) AS month, group1 FROM prods WHERE group1 >0 UNION SELECT DISTINCT LEFT(date,7) AS month, group2 FROM prods WHERE group2 >0 UNION SELECT DISTINCT LEFT(date,7) AS month, group3 FROM prods WHERE group3 >0";
$r = mysql_query("SELECT month,count(*) as c FROM (".$sq.") as t GROUP BY month ORDER BY month") or die(mysql_error());

echo "<table class='view'>\n";
echo "<tr>\n";
echo " <th>month</th>\n";
echo " <th>groups</th>\n";
echo "</tr>\n";
while ($o = mysql_fetch_object($r)) {
  if (substr($o->month,-3,3)=="-00") continue;
  printf("<tr class='bg%d'>\n",($n++&1)+1);
  echo "<td>".$o->month."</td>\n";
  echo "<td>".$o->c."</td>\n";
  echo "</tr>\n";
}
echo "</table>\n";

$sq = "SELECT DISTINCT LEFT(date,4) AS year, group1 FROM prods WHERE group1 >0 UNION SELECT DISTINCT LEFT(date,4) AS year, group2 FROM prods WHERE group2 >0 UNION SELECT DISTINCT LEFT(date,4) AS year, group3 FROM prods WHERE group3 >0";
$r = mysql_query("SELECT year,count(*) as c FROM (".$sq.") as t GROUP BY year ORDER BY year") or die(mysql_error());

echo "<table class='view'>\n";
echo "<tr>\n";
echo " <th>year</th>\n";
echo " <th>groups</th>\n";
echo "</tr>\n";
while ($o = mysql_fetch_object($r)) {
  //if (substr($o->month,-3,3)=="-00") continue;
  printf("<tr class='bg%d'>\n",($n++&1)+1);
  echo "<td>".$o->year."</td>\n";
  echo "<td>".$o->c."</td>\n";
  echo "</tr>\n";
}
echo "</table>\n";

$sq = "SELECT who,LEFT(quand,7) AS month FROM comments GROUP BY month,who";
$r = mysql_query("SELECT month,count(*) as c FROM (".$sq.") as t GROUP BY month ORDER BY month") or die(mysql_error());

echo "<table class='view'>\n";
echo "<tr>\n";
echo " <th>month</th>\n";
echo " <th>users</th>\n";
echo "</tr>\n";
while ($o = mysql_fetch_object($r)) {
  printf("<tr class='bg%d'>\n",($n++&1)+1);
  echo "<td>".$o->month."</td>\n";
  echo "<td>".$o->c."</td>\n";
  echo "</tr>\n";
}
echo "</table>\n";

$r = mysql_query("select party_year,count(*) as c from (select distinct party,party_year from prods) as t group by party_year") or die(mysql_error());

echo "<table class='view'>\n";
echo "<tr>\n";
echo " <th>year</th>\n";
echo " <th>parties</th>\n";
echo "</tr>\n";
while ($o = mysql_fetch_object($r)) {
  printf("<tr class='bg%d'>\n",($n++&1)+1);
  echo "<td>".$o->party_year."</td>\n";
  echo "<td>".$o->c."</td>\n";
  echo "</tr>\n";
}
echo "</table>\n";

?>
</div>
<?
require("../include/bottom.php");
?>
