<?
$ADDITIONAL_CSS = ' <link rel="stylesheet" href="/include/style2.css" type="text/css">';

include_once("include/top.php");

$sql = "select groups.id,groups.name from groups "
    . "left join prods as p1 on p1.group1 = groups.id "
    . "left join prods as p2 on p2.group2 = groups.id "
    . "left join prods as p3 on p3.group3 = groups.id "
    . "left join groupsaka as g1 on g1.group1 = groups.id "
    . "left join groupsaka as g2 on g2.group2 = groups.id "
    . "where (p1.id is null and p2.id is null and p3.id is null and g1.group1 is null and g2.group1 is null)";
    
$r = mysql_query($sql) or die(mysql_error());

printf("<table class='pouettbl'>\n");
while($o = mysql_fetch_object($r)) {
  printf("<tr class='bg1'>\n");
  printf("<td><a href='groups.php?which=%d'>%s</a></td>\n",$o->id,$o->name);
  printf("</tr>\n");
}
printf("</table>\n");

include_once("include/bottom.php");
?>