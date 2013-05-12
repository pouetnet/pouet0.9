<?php
if ($user["bottombar"] == 1)
{
    menu();
    print("<br />");
}

function get_long_git_commit()
{
	/** Returns the long hash of the commit of the current version */
	if (file_exists(LOCAL_COMMIT_FILE))
	{
		$commit = file_get_contents(LOCAL_COMMIT_FILE);
	}
	else
	{
		$commit = '';
	}

	return $commit;
}

function get_short_git_commit()
{
	/** Returns the short hash of the commit of the current version */
	$commit = get_long_git_commit();
	$commit = substr($commit, 0, 7);

	return $commit;
}

function is_upgrading()
{
	return file_exists(REMOTE_COMMIT_FILE);
}

?>
<a href="http://www.pouet.net">pou&euml;t.net</a>
<?php if (strlen(get_long_git_commit()) > 0): ?>
<a href="https://github.com/lra/pouet.net/commits/<?=get_long_git_commit()?>">0.9-<?=get_short_git_commit()?></a>
<?php else: ?>
0.9
<?php endif; ?>
<?php if (is_upgrading()): ?>
<abbr title="Deployment In Progress">DIP</abbr>
<?php endif; ?>
&copy; 2000-<?=date("Y")?> <a href="http://www.pouet.net/groups.php?which=5">mandarine</a> - hosted on <a href="http://www.scene.org/">scene.org</a><br />
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
