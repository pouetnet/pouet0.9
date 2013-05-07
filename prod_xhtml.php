<?
include_once("include/sqllib.inc.php");
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
  "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
<head>
 <title>pouet.net :: your online demoscene resource</title>
 <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-2" />
 <link rel="stylesheet" type="text/css" href="include/style2.css" media="screen" />
</head>
<body>

<div id="header">
<?
$query  = "";
$query .= "SELECT logos.file,logos.author1,logos.author2,logosu1.nickname as nickname1,logosu2.nickname as nickname2 ";
$query .= " FROM logos ";
$query .= " LEFT JOIN users as logosu1 on logosu1.id=logos.author1 ";
$query .= " LEFT JOIN users as logosu2 on logosu2.id=logos.author2 ";
$query .= " WHERE logos.vote_count>0 ORDER by rand()";
$logo = SQLLib::selectRow($query);
?>
  <a href="."><img src="gfx/logos/<?=$logo->file?>" alt="done by <?=$logo->nickname1?><?=$logo->nickname2?" and ".$logo->nickname2:""?>"/></a>
  <p>logo done by <?
printf('<a href="user.php?who=%d">%s</a>',$logo->author1,$logo->nickname1);
if($logo->nickname2)
  printf(' and <a href="user.php?who=%d">%s</a>',$logo->author2,$logo->nickname2);
?> :: experimental xhtml+css layout</p>
</div>

<div class="menu">
<ul>
 <li><a href="#">A<span>ccount</span></a></li>
 <li><a href="#">C<span>ustom</span></a></li>
 <li><a href="#">P<span>rods</span></a></li>
 <li><a href="#">R<span>andom</span></a></li>
 <li><a href="#">G<span>roups</span></a></li>
 <li><a href="#">P<span>arties</span></a></li>
 <li><a href="#">B<span>oards</span></a></li>
 <li><a href="#">U<span>sers</span></a></li>
 <li><a href="#">S<span>earch</span></a></li>
 <li><a href="#">B<span>BS</span></a></li>
 <li><a href="#">U<span>D</span></a></li>
 <li><a href="#">F<span>aq</span></a></li>
 <li><a href="#">S<span>ubmit</span></a></li>
 <li>&nbsp;</li>
</ul>
</div>

<?
if ($_GET["which"])
  $condition = sprintf(" where prods.id=%d",$_GET["which"]);
else
  $condition = sprintf(" order by rand()");
  
$query  = "select *,prods.id as id,prods.name as name, ";
$query .= " g1.id as g1id, g1.name as g1name, g1.acronym as g1acr, g1.web as g1web, ";
$query .= " g2.id as g2id, g2.name as g2name, g2.acronym as g2acr, g2.web as g2web, ";
$query .= " g3.id as g3id, g3.name as g3name, g3.acronym as g3acr, g3.web as g3web, ";
$query .= " prods.added as added ";
$query .= " from prods ";
$query .= " LEFT JOIN groups as g1 on prods.group1 = g1.id ";
$query .= " LEFT JOIN groups as g2 on prods.group2 = g2.id ";
$query .= " LEFT JOIN groups as g3 on prods.group3 = g3.id ";
$query .= $condition;
    
$prod = SQLLib::selectRow($query);

if(file_exists("screenshots/".$prod->id.".jpg")) {
  $shotpath = "screenshots/".$prod->id.".jpg";
} elseif(file_exists("screenshots/".$prod->id.".gif")) {
  $shotpath = "screenshots/".$prod->id.".gif";
} elseif(file_exists("screenshots/".$prod->id.".png")) {
  $shotpath = "screenshots/".$prod->id.".png";
}

?>
<div id="content"><table id="tbl_prodbox" class="pouettbl">
<tr>
<th colspan="3">
 <span id="title"><big><?=$prod->name?></big><?
 $a = array();
 if ($prod->g1id) {
   $s = sprintf("<a href='groups.php?which=%d'>%s</a>",$prod->g1id,$prod->g1name);
   if ($prod->g1web) $s.=sprintf(" [<a href='%s'>web</a>]",$prod->g1web);
   $a[] = $s;
 }
 if ($prod->g2id) {
   $s = sprintf("<a href='groups.php?which=%d'>%s</a>",$prod->g2id,$prod->g2name);
   if ($prod->g2web) $s.=sprintf(" [<a href='%s'>web</a>]",$prod->g2web);
   $a[] = $s;
 }
 if ($prod->g3id) {
   $s = sprintf("<a href='groups.php?which=%d'>%s</a>",$prod->g3id,$prod->g3name);
   if ($prod->g3web) $s.=sprintf(" [<a href='%s'>web</a>]",$prod->g3web);
   $a[] = $s;
 }
 if (count($a)) echo " by ".implode(" &amp; ",$a);
 ?></span>

<?
 $nfopath = "nfo/".$prod->id.".nfo";
 if(file_exists($nfopath) && filesize($nfopath)) {
?>
 <span id="otherlinks">[<a href="nfo.php?which=<?=$prod->id?>">nfo</a>]</span>
<?
}
?>
</th>
</tr>
<tr>
 <td rowspan="3" class="cenmid"><?
if ($shotpath) {
?>
 <img src="<?=$shotpath?>" alt="screenshot"/></td>
<?
} else {
?>
 no screenshot yet.
<?
}
?>
 <td colspan="2" class="cenmid">
  <table id="stattable">
   <tr>
    <td>platform :</td>
    <td>Amiga ECS</td>
   </tr>
   <tr>
    <td>type :</td>
    <td><?=$prod->type?></td>
   </tr>
   <tr>
    <td>release date :</td>
    <td>december 1993</td>
   </tr>
   <tr>
    <td>release party :</td>
    <td><a href="#">The Party</a> 1993</td>
   </tr>
   <tr>
    <td>compo :</td>
    <td>amiga demo</td>
   </tr>
   <tr>
    <td>ranked :</td>
    <td>3rd</td>
   </tr>
  </table>
 </td>
</tr>
<tr>
 <td class="r2">
   <img src="http://www.pouet.net/gfx/rulez.gif" alt="rulez" /> <?=$prod->voteup?><br/>
   <img src="http://www.pouet.net/gfx/isok.gif"  alt="piggy" /> <?=$prod->votepig?><br/>
   <img src="http://www.pouet.net/gfx/sucks.gif" alt="sucks" /> <?=$prod->votedown?>
 </td>
 <td class="cenmid">
  popularity : 50%<br/>
  <div class="outerbar">
  <div class="innerbar" style="width: 50%">
  &nbsp;<span>50%</span>  
  </div>

  </div>
 </td>
</tr>
<tr>
 <td class="r2">
   <img src="http://www.pouet.net/gfx/<?=$prod->voteavg>0?"rulez":"sucks"?>.gif" alt="average <?=$prod->voteavg>0?"rulez":"sucks"?>" /> <?=$prod->voteavg?><br/>
 </td>
 <td id="links">
    [<a href="http://amiga.nvg.org/warlock/adf/s/Sanity/Arte.adf.gz">download</a>]<br />
    [<a href="http://www.scene.org/file.php?file=/mirrors/amidemos/arte.zip">video</a>]<br />
    [<a href="download.php?which=1477">mirrors...</a>]<br /> 
 </td>
</tr>
<tr class="footn">
 <td colspan="3">added on the 2001-03-05 by OSTYL <img src="http://www.pouet.net/gfx/isok.gif" alt="piggy" class="avatar"/></td>
</tr>

</table>

<table id="tbl_popularity" class="pouettbl">
<tr><th>popularity helper</th></tr>
<tr><td>increase the popularity of this prod by spreading this URL:<br/>
<input type="text" value="http://www.pouet.net/prod.php?which=<?=$prod->id?>" size="50" readonly="readonly" />
</td></tr>
</table>

<table id="tbl_comments" class="pouettbl">
<tr><th>comments</th></tr>

<?
$query = "";
$query .= "select *,users.id as userid from comments, users where users.id = comments.who";
$query .= sprintf(" and comments.which=%d order by comments.quand",$prod->id);
$comments = SQLLib::selectRows($query);
foreach ($comments as $c) {
?>
<tr><td><?=htmlentities($c->comment)?></td></tr>
<tr><td class="footn">
<span class="vote <?=$c->rating>0?"rulez":($c->rating<0?"sucks":"")?>"><span><?=$c->rating?></span></span>
added on the <?=$c->quand?> by <a href="user.php?who=<?=$c->userid?>"><?=$c->nickname?></a>
<img src="http://www.pouet.net/avatars/<?=$c->avatar?>" alt="<?=$c->nickname?>" class="avatar" />
</td></tr>
<?
}
?>
<tr><td class="numcomments">
<form action="prod.php" method="get">
displaying

<input type="hidden" name="which" value="1477" />
<select name="howmanycomments" onchange="document.howmanycomments.submit();">
<option value="0" >none</option>
<option value="25" >25</option>
<option value="50" >50</option>
<option value="100" >100</option>
<option value="-1" selected="selected">all</option>
</select>
comments out of <b>130</b>
</form>

</td></tr>
</table>

<table id="tbl_changes" class="pouettbl">
<tr><th>submit changes</th></tr>
<tr><td>if this prod is a fake, some info is false or the download link is broken,<br/>
do not post about it in the comments, it will get lost.<br/>
instead,
<a href="mailto:pouet@neuromatrice.net?subject=about%20prod%20number%201477">email</a> or
<a href="topic.php?which=1024">post</a> about it.

</td></tr>
</table>
</div>

<div class="menu">
<ul>
 <li><a href="#">A<span>ccount</span></a></li>
 <li><a href="#">C<span>ustom</span></a></li>
 <li><a href="#">P<span>rods</span></a></li>

 <li><a href="#">R<span>andom</span></a></li>
 <li><a href="#">G<span>roups</span></a></li>
 <li><a href="#">P<span>arties</span></a></li>
 <li><a href="#">B<span>oards</span></a></li>
 <li><a href="#">U<span>sers</span></a></li>

 <li><a href="#">S<span>earch</span></a></li>
 <li><a href="#">B<span>BS</span></a></li>
 <li><a href="#">U<span>D</span></a></li>
 <li><a href="#">F<span>aq</span></a></li>
 <li><a href="#">S<span>ubmit</span></a></li>

 <li>&nbsp;</li>
</ul>
</div>

<div id="footer">
pouët.net <a href="changelog.php">1.2.3</a> &copy; 2000-2007 <a href="http://www.pouet.net/groups.php?which=5">mandarine</a> - hosted on <a href="http://www.scene.org/">scene.org</a><br />
send comments and bug reports to <a href="mailto:webmaster@pouet.net">webmaster@pouet.net</a><br />

page created in 0.00681495666504 seconds. 
</div>

</body>
</html>
