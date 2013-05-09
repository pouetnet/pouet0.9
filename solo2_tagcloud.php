<?
require("include/top.php");

$poor_fucker = $_GET["user"] ? (int)$_GET["user"] : 3254;

if (!$_GET["tag"]) {

  $posts = array();
  if ($poor_fucker != -1) {
    $threshold = 5;
    $sql = "select * from users where id=".$poor_fucker."";
    $r = mysql_query($sql) or die(mysql_error());
    $usr = mysql_fetch_object($r);

    $r = mysql_query( sprintf("select comment from comments where who=%d",$poor_fucker) ) or die(mysql_error());
    while ($o = mysql_fetch_object($r)) $posts[] = $o->comment;

    if ($_GET["sitewide"]==1)
    {
      $r = mysql_query( sprintf("select message from oneliner where who=%d",$poor_fucker) ) or die(mysql_error());
      while ($o = mysql_fetch_object($r)) $posts[] = $o->message;

      $r = mysql_query( sprintf("select post from bbs_posts where author=%d",$poor_fucker) ) or die(mysql_error());
      while ($o = mysql_fetch_object($r)) $posts[] = $o->post;
    }
  }
  else
  {
    $threshold = 2000;
    $sql = "select * from comments";
    $r = mysql_query($sql) or die(mysql_error());

    $usr->avatar = "r.gif";
    $usr->nickname = "ALL USERS";
  }

  $tags = array();

  foreach ($posts as $post)
  {
    $a = preg_split("/[^a-zA-Z0-9]/",$post);
    foreach($a as $v) if (strlen(trim($v))>1)
      $tags[strtolower($v)] += 1;
  }

  // remove common words
  $common = unserialize(@file_get_contents("common_words.txt"));
  if (!$common) {
    $f = file_get_contents("http://en.wikipedia.org/w/index.php?title=Most_common_words_in_English&action=raw");
    echo preg_match_all("/^\| [0-9]+ \|\| ([a-zA-Z]+)$/m",$f,$matches);

    foreach ($matches[1] as $v)
      $common[] = strtolower($v);

    $common[] = "is";
    $common[] = "are";

    file_put_contents("common_words.txt",serialize($common));
  }

  foreach ($common as $v) unset($tags[$v]);

  $max = 0;
  $avg = 0;
  $avgn = 0;
  foreach ($tags as $v) { $max = max($max, $v); $avg += $v; $avgn++; }

  $avg = (int)($avg / $avgn);

  ksort($tags);
  //var_dump($tags);

  ?>
  <table bgcolor="#000000" cellspacing="1" cellpadding="0" border="0" width="75%" style="margin:5px">
   <tr>

    <td>
     <table bgcolor="#000000" cellspacing="1" cellpadding="2" border="0" width="100%">
      <tr bgcolor="#224488">
       <td align="center">
         <img src="avatars/<?=$usr->avatar?>"/>
         <b><?=$usr->nickname?></b> tagcloud (average: <?=$avg?> occurences of a single word)
       </td>
      </tr>
      <tr bgcolor="#446688">
       <td align="center">
  <?
  foreach ($tags as $t=>$v) if ($v > $threshold) {
    printf("<a href='%s?user=%d&amp;tag=%s' style='font-size:%dpx' title='%d occurences'>%s</a>\n",
      $_SERVER["PHP_SELF"],$poor_fucker,rawurlencode($t),($v*80/$max)+10,$v,$t);
  }
  ?>
      </td>
     </tr>
    </table>
    </td>

    </tr>
  </table>
  <?
} else {
//  $sql = "select * from comments where who=".$poor_fucker." and comment like '%".mysql_real_escape_string($_GET["tag"])."%'";
//  $r = mysql_query($sql) or die(mysql_error());
  echo "i'll do this later.";
}

require("include/bottom.php");

?>
