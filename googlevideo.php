<?
$ADDITIONAL_CSS = ' <link rel="stylesheet" href="/include/style2.css" type="text/css">';

include_once("include/top.php");

$r = mysql_query("select prods.id, prods.name, downloadlinks.link, downloadlinks.type from downloadlinks join prods on prods.id = downloadlinks.prod where link like '%video.google%'");

printf("<table class='pouettbl'>\n");
printf("<tr>\n");
printf("<th>prod</th>\n");
printf("<th>type</th>\n");
printf("<th>google video link</th>\n");
printf("</tr>\n");
while($o = mysql_fetch_object($r)) {
  printf("<tr class='bg1'>\n");
  printf("<td><a href='prod.php?which=%d'>%s</a></td>\n",$o->id,$o->name);
  printf("<td>%s</td>\n",$o->type);
  printf("<td><a href='%s'>%s</a></td>\n",$o->link,$o->link);
  printf("</tr>\n");
}
printf("</table>\n");

include_once("include/bottom.php");
?>