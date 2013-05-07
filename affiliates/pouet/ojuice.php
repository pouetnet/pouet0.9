<?
require("top.php");

/* marche pas encore */
$fd = fsockopen ("onds.ojuice.net",80);
fputs($fd,"GET /newspouet.php HTTP/1.1\nHost:onds.ojuice.net\n\n");
//while (!feof ($fd)) {
    $buffer = fgets($fd, 4096);
    echo $buffer;
//}
fclose ($fd);
/**/
?>

</pre>
<?
include("http://onds.ojuice.net/newspouet.php");

for($i=0;$i<=4;$i++) {
$query="REPLACE ojnews SET ";
$query.="id=".$ojnews[$i]["id"].", ";
$query.="title='".addslashes($ojnews[$i]["title"])."', ";
$query.="url='".$ojnews[$i]["url"]."', ";
$query.="quand='".$ojnews[$i]["when"]."', ";
$query.="authorid=".$ojnews[$i]["authorid"].", ";
$query.="authornick='".addslashes($ojnews[$i]["authornick"])."', ";
$query.="authorgroup='".addslashes($ojnews[$i]["authorgroup"])."', ";
$query.="content='".addslashes($ojnews[$i]["content"])."'";
mysql_query($query,$db);
//print("<hr>".$query."<br>");
}


require("bottom.php");
?>
