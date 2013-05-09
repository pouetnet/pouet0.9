<?
require("include/top.php");
// color_title #248
// color_bottom #68a
// color_bg1 #579
// color_bg1 #468

function dumplist($sql,$n) {
  $r = mysql_query($sql);
  echo "<table bgcolor='#000000' cellspacing='1' cellpadding='2' border='0' style='float:left; margin:10px;'>\n";
  echo " <tr><th colspan='2'>".$n."</th></tr>\n";
  while($o = mysql_fetch_object($r)) {
    echo " <tr>\n";
    printf(" <td class='bg2'>");
    printf("  <a href='user.php?who=%d'><img border='0' src='avatars/%s'/></a>",$o->id,$o->avatar);
    printf("  <a href='user.php?who=%d'>%s</a>",$o->id,htmlentities($o->nickname));
    printf(" </td>");
    printf(" <td class='bg2'>%d</td>",$o->c);
    echo " </tr>\n";
  }
  echo "</table>\n";
}

echo "<div style='width:60%; margin:10px auto;'>";
dumplist("SELECT count( * ) AS c, users.nickname, users.id, users.avatar
FROM bbs_posts
JOIN users ON users.id = bbs_posts.author
GROUP BY bbs_posts.author
ORDER BY c DESC  LIMIT 20","most bbs posts");

dumplist("SELECT count( * ) AS c, users.nickname, users.id, users.avatar
FROM bbs_topics
JOIN users ON users.id = bbs_topics.userfirstpost
GROUP BY bbs_topics.userfirstpost
ORDER BY c DESC  LIMIT 20","most bbs topics");

dumplist("SELECT count( * ) AS c, users.nickname, users.id, users.avatar
FROM comments
JOIN users ON users.id = comments.who
GROUP BY comments.who
ORDER BY c DESC  LIMIT 20","most prod comments");

dumplist("SELECT count( * ) AS c, users.nickname, users.id, users.avatar
FROM oneliner
JOIN users ON users.id = oneliner.who
GROUP BY oneliner.who
ORDER BY c DESC  LIMIT 20","most oneliners");
/*
dumplist("SELECT count( * ) AS c, users.nickname, users.id, users.avatar
FROM bbs_posts
JOIN users ON users.id = bbs_posts.author
WHERE bbs_posts.post LIKE '%[img%'
GROUP BY bbs_posts.author
ORDER BY c DESC  LIMIT 20","most bbs posts with images");
*/
echo "</div>";
echo "<br style='clear:both;'/>";
?>
<table>
  <tr>
    <td>users who haven't commented on any prods:</td>
    <td><?
$r = mysql_query("select count(*) as c from users left join comments on users.id = comments.who where comments.id is null limit 10;");
$o = mysql_fetch_object($r);
echo $o->c;
    ?></td>
  </tr>
  <tr>
    <td>users who haven't posted on the bbs:</td>
    <td><?
$r = mysql_query("select count(*) as c from users left join bbs_posts on users.id = bbs_posts.author where bbs_posts.id is null limit 10;");
$o = mysql_fetch_object($r);
echo $o->c;
    ?></td>
  </tr>
  <tr>
    <td>users who haven't wrote on either:</td>
    <td><?
$r = mysql_query("select count(*) as c from users left join comments on users.id = comments.who left join bbs_posts on users.id = bbs_posts.author where bbs_posts.id is null and comments.id is null limit 10;");
$o = mysql_fetch_object($r);
echo $o->c;
    ?></td>
  </tr>
</table>
<?
/*
  <tr>
    <td>users who only posted in the random image thread:</td>
    <td><?
$sql = "select count(*) as c from (select count(*) as c, bbs_posts.topic as tpc from users left join bbs_posts on users.id = bbs_posts.author group by bbs_posts.topic) as t where t.tpc=2735";
$r = mysql_query($sql) or die(mysql_error());
$o = mysql_fetch_object($r);
echo $o->c;
    ?></td>
  </tr>
*/
require("include/bottom.php"); ?>
