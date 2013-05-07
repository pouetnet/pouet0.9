<?
if ($user["bottombar"]==1) { menu(); print("<br />"); }
$query="SELECT version FROM changelog ORDER BY quand DESC, id DESC LIMIT 1";
$result=mysql_query($query);
$lastversion=mysql_result($result,0);
?>
<a href="http://www.pouet.net">pou&euml;t.net</a> <a href="changelog.php"><? print($lastversion); ?></a> &copy; 2000-<?=date("Y")?> <a href="http://www.pouet.net/groups.php?which=5">mandarine</a> - hosted on <a href="http://www.scene.org/">scene.org</a><br />
send comments and bug reports to <a href="mailto:webmaster@pouet.net">webmaster@pouet.net</a>
- contribute on <a href="https://github.com/lra/pouet.net">GitHub</a><br />
<?
$endtime = microtime_float();
$totaltime = ($endtime - $starttime);
?>
page created in <? printf("%f",$totaltime); ?> seconds<?=($SESSION_LEVEL=='administrator' || $SESSION_LEVEL=='moderator' || $SESSION_LEVEL=='gloperator') && $numqueries?" with ".$numqueries." queries":""?>.<br />
<? mysql_close(); ?>
<br />
</div>

<script type="text/javascript">
var gaJsHost = (("https:" == document.location.protocol) ? "https://ssl." : "http://www.");
document.write(unescape("%3Cscript src='" + gaJsHost + "google-analytics.com/ga.js' type='text/javascript'%3E%3C/script%3E"));
</script>
<script type="text/javascript">
try {
var pageTracker = _gat._getTracker("UA-107561-5");
pageTracker._trackPageview();
} catch(err) {}</script>
</body>
</html>
