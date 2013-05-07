<?
include_once("include/misc.php");
include_once("include/sqllib.inc.php");

$r = SQLLib::selectRows("select count(*) as c, substr(quand,1,7) as d from users group by d order by d");

include('postgraph.class.php'); 

$graph = new PostGraph(1920,1080);

$data = array();
foreach ($r as $o)
  $data[$o->d] = $o->c;
  
$graph->setData($data);
$graph->setXTextOrientation('vertical');

$graph->drawImage();
$graph->printImage();


?>