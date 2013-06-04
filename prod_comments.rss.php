<?
include_once("include/misc.php");
conn_db();

header("Content-type: application/rss+xml; charset=utf-8");
echo '<'.'?xml version="1.0" encoding="UTF-8" ?'.'>'."\n";

$query ="SELECT * FROM prods WHERE id=".(int)$_GET["which"]." LIMIT 1";
$result = mysql_query($query);
$prod = mysql_fetch_array($result);

?>
<rss version="2.0" xmlns:atom="http://www.w3.org/2005/Atom">
	<channel>
		<title>Pouet - <?=$prod["name"]?></title>
		<link>http://pouet.net/prod.php?which=<?=$prod['id']?></link>
		<description>Pouet - <?=$prod["name"]?></description>
		<language>en-us</language>
		<docs>http://feedvalidator.org/docs/rss2.html</docs>

		<atom:link href="http://pouet.net/prod_comments.rss.php?which=<?=$prod['id']?>" rel="self" type="application/rss+xml" />

		<lastBuildDate><?=date("r")?></lastBuildDate>
		<copyright>Copyright 2008 Pouet.net</copyright>
		<ttl>60</ttl>

<?
$result = mysql_query("SELECT * FROM comments join users on users.id = comments.who WHERE comments.which='".$prod["id"]."' order by comments.quand desc");
while($o = mysql_fetch_object($result)) {
?>
		<item>
			<guid isPermaLink="false">pouetcomments_<?=$o->id?></guid>
			<title><?=htmlentities($o->nickname)?> (<?=$o->rating?>)</title>
      <link>http://pouet.net/prod.php?which=<?=$prod["id"]?></link>
			<description><![CDATA[<?=htmlentities($o->comment)?>]]></description>
			<pubDate><?=date("r",strtotime($o->quand))?></pubDate>
		</item>
<?
}
?>
	</channel>
</rss>
