<?
session_start();
if (!($SESSION_LEVEL=='administrator' || $SESSION_LEVEL=='moderator' || $SESSION_LEVEL=='gloperator'))
  die("OMG");

require("../include/top.php");
require("../include/awardscategories.inc.php");

$year = $sceneorgyear;
if ($_GET["forceyear"])
  $year = (int)$_GET["forceyear"];

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
$n = mysql_num_rows(mysql_query("select * from awardscand_".$year.""));
printf("<h1>%d total votes</h1>\n",$n);

foreach($awardscat[$year] as $x=>$name) {

  $sql = "SELECT prods.id,prods.name,prods.type,count(*) as c ";
  $sql .= " FROM awardscand_".$year." ";
  $sql .= " JOIN prods ON awardscand_".$year.".cat".$x."=prods.id ";
  $sql .= " GROUP BY prods.id ORDER BY c DESC,name";

  $r = mysql_query($sql) or die(mysql_error());
  echo "<table class='view'>\n";
  echo "<tr><th colspan='2'>#".$x." - ".$name."</th></tr>\n";
  while ($o = mysql_fetch_object($r)) {
    printf("<tr class='bg%d'>\n",($n++&1)+1);
    echo "<td>";
    foreach(explode(",",$o->type) as $v) {
      printf("<img src='../gfx/types/%s'/>",$types[$v]);
    }
    echo "<a href='../prod.php?which=".$o->id."'>".$o->name."</a>";
    echo "<td>".$o->c." votes</td>\n";
    echo "</tr>\n";
  }
  echo "</table>\n";
}
?>
</div>
<?
require("../include/bottom.php");
?>
