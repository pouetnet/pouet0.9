<?
require("include/top.php");
// color_title #248
// color_bottom #68a
// color_bg1 #579
// color_bg1 #468


$sql = "SELECT *, (t.av / t.c) AS rat
FROM (
  SELECT users.id, users.avatar, users.nickname, sum( voteavg ) AS av, count( * ) AS c
  FROM prods
  LEFT JOIN users ON users.id = prods.added
  GROUP BY users.id
) AS t
WHERE c > 20
ORDER BY rat ".($_GET["good"]?" DESC":"")." LIMIT 50";

$r = mysql_query($sql);
echo "<table bgcolor='#000000' cellspacing='1' cellpadding='2' border='0' style='margin:10px;'>\n";
printf("<tr>\n");
printf(" <th>user</th>\n");
printf(" <th>prods</th>\n");
printf(" <th>avg</th>\n");
printf(" <th>rating</th>\n");
printf("</tr>\n");
while($o = mysql_fetch_object($r)) {
  echo " <tr>\n";
  printf(" <td class='bg2'>");
  printf("  <a href='user.php?who=%d'><img border='0' src='avatars/%s'/></a>",$o->id,$o->avatar);
  printf("  <a href='user.php?who=%d'>%s</a>",$o->id,htmlentities($o->nickname));
  printf(" </td>");
  printf(" <td class='bg2'>%d</td>",$o->c);
  printf(" <td class='bg2'>%.2f</td>",$o->av);
  printf(" <td class='bg2'>%.2f</td>",$o->rat);
  echo " </tr>\n";
}
echo "</table>\n";

require("include/bottom.php");
?>