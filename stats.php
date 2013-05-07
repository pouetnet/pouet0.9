<?
require("include/top.php");
// color_title #248
// color_bottom #68a
// color_bg1 #579
// color_bg1 #468

function convert_links($str) {
    $replace = '<a href="'.htmlentities('\\1').htmlentities('\\2').'">link</a>';
    $str = preg_replace("/(http:\/\/(.*)\/)[\S]*/", "<a href=\\1>\\1</a> ", $replace);
   return $str;
}

?>
<br />
<? //<img src="gfx/stats/prods_year.php" alt="number of prods per year" /><br />
//$thisone="http://www.verylongurl.com/fuckoff.php?youaregayandsmell=true&itssotrue=ithurts+sucks ";
//$that=preg_replace("/(http:\/\/(.*)\/)[\S]*/", "<a href=\\1>link</a> ", $thisone);
//print("->".$thisone."<-<br />->".$that."<-");
//http://au.php.net/manual/en/function.preg-replace.php
?>
<br />


<? require("include/bottom.php"); ?>
