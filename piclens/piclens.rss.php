<?
include_once("../include/misc.php");
conn_db();

header("Content-type: application/rss+xml; charset=utf-8");
echo '<'.'?xml version="1.0" encoding="UTF-8" ?'.'>'."\n";

$query ="SELECT * FROM prods order by quand desc limit 500";
$result = mysql_query($query) or die(mysql_error());

?>
<rss version="2.0"
  xmlns:media="http://search.yahoo.com/mrss/"
  xmlns:atom="http://www.w3.org/2005/Atom">
    <channel>
  		<title>Pouet.net Piclens feed</title>
  		<link>http://pouet.net</link>
  		<description>Pouet.net Piclens feed</description>
  		<language>en-us</language>
  		<docs>http://feedvalidator.org/docs/rss2.html</docs>

      <atom:icon>http://pouet.net/favicon.ico</atom:icon>
  		<atom:link href="http://pouet.net/piclens/piclens.rss.php" rel="self" type="application/rss+xml" />

<?
while($o = mysql_fetch_object($result)) {

$shotpath = "";
if(file_exists("../screenshots/".$o->id.".jpg")) {
  $shotpath = "screenshots/".$o->id.".jpg";
} elseif(file_exists("../screenshots/".$o->id.".gif")) {
  $shotpath = "screenshots/".$o->id.".gif";
} elseif(file_exists("../screenshots/".$o->id.".png")) {
  $shotpath = "screenshots/".$o->id.".png";
}
if ($shotpath) {
?>
        <item>
            <title><?=htmlspecialchars(utf8_encode($o->name))?></title>
            <guid isPermaLink="false">pouet_piclens_<?=$o->id?></guid>
            <link>http://pouet.net/prod.php?which=<?=$o->id?></link>
            <media:thumbnail url="http://pouet.net/<?=$shotpath?>"/>
            <media:content url="http://pouet.net/<?=$shotpath?>"/>
        </item>
<?
}
}
?>
    </channel>
</rss>
