<?
//return;
header("Content-type: text/plain");

printf("START %s - ",date("Y-m-d H:i:s"));

include('/home/hosted/pouet/sites/www.pouet.net/lastRSS.php');

// create lastRSS object
$rss = new lastRSS;

// setup transparent cache
$rss->cache_dir = '/home/hosted/pouet/sites/www.pouet.net/cache';
$rss->cache_time = 5*60; // in seconds
$rss->CDATA = 'strip';
$rss->date_format = 'Y-m-d';

$rss->get('http://bitfellas.org/e107_plugins/rss_menu/rss.php?1.2');

$rss->itemtags[] = "demopartynet:title";
$rss->itemtags[] = "demopartynet:date";
$rss->itemtags[] = "demopartynet:startDate";
$rss->itemtags[] = "demopartynet:endDate";

$r = $rss->get('http://feeds.feedburner.com/demoparty/parties');

//var_dump($rss);
//var_dump($r);

printf("END %s\n",date("Y-m-d H:i:s"));
?>
