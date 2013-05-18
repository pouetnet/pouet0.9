<?
require("include/top.php");
require("include/libbb.php");

function SQLToDate($sqldate) {
  global $months;

  $txtdate=substr($sqldate,-2);
  $txtdate.=" ";
  $txtdate.=$months[sprintf("%d",substr($sqldate,5,2))];
  $txtdate.=" ";
  $txtdate.=substr($sqldate,0,4);

  return $txtdate;
}

$usercustom=$user;

if ($com) $com = mysql_real_escape_string($com);
if (!$type) $type = "diskmag";
 else $type = mysql_real_escape_string($type);
if (!$platform) $platform = "Windows";
 else $platform = mysql_real_escape_string($platform);

$groups = array();
$platforms = array();

// latest commented prods
$query="SELECT prods.id,prods.name,prods.group1,prods.group2,prods.group3,prods.type,comments.who,";
if ($com) $query.="comments.comment,";
$query.="comments.rating,users.nickname,users.avatar,UNIX_TIMESTAMP()-UNIX_TIMESTAMP(comments.quand) as difftime, ".
" g1.name as groupn1,g1.acronym as groupacron1, ".
" g2.name as groupn2,g2.acronym as groupacron2, ".
" g3.name as groupn3,g3.acronym as groupacron3, ".
" GROUP_CONCAT(platforms.name) as platform ".
" FROM prods JOIN comments JOIN users JOIN prods_platforms JOIN platforms ".
" LEFT JOIN groups AS g1 ON prods.group1 = g1.id".
" LEFT JOIN groups AS g2 ON prods.group2 = g2.id".
" LEFT JOIN groups AS g3 ON prods.group3 = g3.id".
" WHERE FIND_IN_SET('".$type."',prods.type)".
" AND comments.which=prods.id ".
" AND comments.who=users.id ".
" AND prods_platforms.prod=prods.id ".
" AND prods_platforms.platform=platforms.id ";
if ($platform) $query.=" AND platforms.name = '".$platform."'";
$query .= " GROUP BY comments.id".
" ORDER BY comments.quand DESC";
if (!$com) $com = 25;
$query.=" LIMIT ".$com;

//echo $query;
//debuglog($query);
$result = mysql_query_debug($query);
while($tmp = mysql_fetch_assoc($result)) {
  //if(strlen($tmp["name"])>$usercustom["commentsnamecut"])
  //	$tmp["name"]=substr($tmp["name"],0,$usercustom["commentsnamecut"])."...";
  $comments[] = $tmp;
}
for ($i=0; $i<count($comments); $i++):
/*
	if ($comments[$i]["group1"]):
	  if ($groups[$comments[$i]["group1"]]) {
		  $comments[$i]["groupn1"]=$groups[$comments[$i]["group1"]]["name"];
		  $comments[$i]["groupacron1"]=$groups[$comments[$i]["group1"]]["acronym"];
	  } else {
  		$query="select name,acronym from groups where id='".$comments[$i]["group1"]."'";
  		$result=mysql_query_debug($query);
  		while($tmp = mysql_fetch_array($result)) {
  		  $comments[$i]["groupn1"]=$tmp["name"];
  		  $comments[$i]["groupacron1"]=$tmp["acronym"];
  		  $groups[$comments[$i]["group1"]]["name"] = $tmp["name"];
  		  $groups[$comments[$i]["group1"]]["acronym"] = $tmp["acronym"];
  		 }
		}
	endif;
	if ($comments[$i]["group2"]):
		$query="select name,acronym from groups where id='".$comments[$i]["group2"]."'";
		$result=mysql_query_debug($query);
		while($tmp = mysql_fetch_array($result)) {
		  $comments[$i]["groupn2"]=$tmp["name"];
		  $comments[$i]["groupacron2"]=$tmp["acronym"];
		 }
	endif;
	if ($comments[$i]["group3"]):
		$query="select name,acronym from groups where id='".$comments[$i]["group3"]."'";
		$result=mysql_query_debug($query);
		while($tmp = mysql_fetch_array($result)) {
		  $comments[$i]["groupn3"]=$tmp["name"];
		  $comments[$i]["groupacron3"]=$tmp["acronym"];
		 }
	endif;
*/
	if (strlen($comments[$i]["groupn1"].$comments[$i]["groupn2"].$comments[$i]["groupn3"])>27):
		if (strlen($comments[$i]["groupn1"])>10 && $comments[$i]["groupacron1"]) $comments[$i]["groupn1"]=$comments[$i]["groupacron1"];
		if (strlen($comments[$i]["groupn2"])>10 && $comments[$i]["groupacron2"]) $comments[$i]["groupn2"]=$comments[$i]["groupacron2"];
		if (strlen($comments[$i]["groupn3"])>10 && $comments[$i]["groupacron3"]) $comments[$i]["groupn3"]=$comments[$i]["groupacron3"];
	endif;

/*
	if ($platforms[$comments[$i]["id"]]) {
	  $comments[$i]["platform"] = $platforms[$comments[$i]["id"]];
	} else {
  	$query="select platforms.name from prods_platforms, platforms where prods_platforms.prod='".$comments[$i]["id"]."' and platforms.id=prods_platforms.platform";
  	$result=mysql_query_debug($query);
  	$check=0;
  	$comments[$i]["platform"]="";
  	while($tmp = mysql_fetch_array($result)) {
  	  if ($check>0) $comments[$i]["platform"].=",";
  	  $check++;
  	  $comments[$i]["platform"].=$tmp["name"];
  	 }
  	if(strlen($comments[$i]["platform"])>20)
    	$comments[$i]["platform"]=substr($comments[$i]["platform"],0,18)."...";
    $platforms[$comments[$i]["id"]] = $comments[$i]["platform"];
  }
*/
endfor;



for($i=0;$i<count($comments);$i++) {
  // trasformation secondes en lisible
  $comments[$i]["t_hours"]=floor($comments[$i]["difftime"]/3600);
  $comments[$i]["t_minutes"]=floor(($comments[$i]["difftime"]-3600*$comments[$i]["t_hours"])/60);
  $comments[$i]["t_seconds"]=$comments[$i]["difftime"]-3600*$comments[$i]["t_hours"]-60*$comments[$i]["t_minutes"];
}

?>
<br>
<table bgcolor="#000000" cellspacing="1" cellpadding="0">
 <tr>
  <td>
   <table bgcolor="#000000" cellspacing="1" cellpadding="2">
    <tr>
     <td bgcolor="#224488" colspan="6" align="center">
       <? if (!$com): ?><b>comments added in the last <?=$usercustom["commentshours"]?> hours</b><br />
       <? else: ?><b>last <?=$com?> comments added in the last <?=$usercustom["commentshours"]?> hours</b><br />
       <? endif; ?>
	 </td>
    </tr>
    <tr>
     <th>
      <b><img src="gfx/rulez.gif" alt="rating"></b><br>
     </th>
     <th>
      <b>name</b><br>
     </th>
     <th>
      <b>group</b><br>
     </th>
     <th>
      <b>platform</b><br>
     </th>
     <th>
      <b>time</b><br>
     </th>
     <th>
      <b>user</b><br>
     </th>
    </tr>
    <? $comm=$com;
       if (!$com) $comm = count($comments); ?>
    <? for($i=0;$i<$comm;$i++): ?>
    <? $tdcolor=($i%2)?"#557799":"#446688"; ?>
    <? if ($com): ?><tr class="cite-<?=$comments[$i]["who"]?>"><td bgcolor="<?=$tdcolor?>" align="left" colspan="6"><?  print(parse_message($comments[$i]["comment"])); ?><br /></td></tr><? endif; ?>
    <tr class="cite-<?=$comments[$i]["who"]?>">
      <td bgcolor="<?=$tdcolor?>" align="left">
       <?
     	switch((int)($comments[$i]["rating"]))
             {
             	case 1: print("<img src=\"gfx/rulez.gif\" alt=\"rulez!\">");
             		break;
             	case -1: print("<img src=\"gfx/sucks.gif\" alt=\"sucks!\">");
             		break;
             	default: print("<img src=\"gfx/isok.gif\" alt=\"oink!\">");
             }
       ?>
      </td>
      <td bgcolor="<?=$tdcolor?>">
	<b><a href="prod.php?which=<? print($comments[$i]["id"]); ?>"><? print(htmlentities(stripslashes($comments[$i]["name"]))); ?></a></b></td>
      <td bgcolor="<?=$tdcolor?>">
	<a href="groups.php?which=<?=$comments[$i]["group1"]?>"><? print(stripslashes($comments[$i]["groupn1"])); ?></a>
	<? if ($comments[$i]["group2"]) {printf(" :: ");} ?><a href="groups.php?which=<?=$comments[$i]["group2"]?>"><? print(stripslashes($comments[$i]["groupn2"])); ?></a>
	<? if ($comments[$i]["group3"]) {printf(" :: ");} ?><a href="groups.php?which=<?=$comments[$i]["group3"]?>"><? print(stripslashes($comments[$i]["groupn3"])); ?></a></td>
      <td bgcolor="<?=$tdcolor?>">
	<?
  $platforms = explode(",", $comments[$i]["platform"]);
	for($kkk=0;$kkk<count($platforms);$kkk++) {
	  ?><a href="prodlist.php?platform[]=<?=$platforms[$kkk]?>"><img src="gfx/os/<?=$os[$platforms[$kkk]]?>" width="16" height="16" border="0" title="<?=$platforms[$kkk]?>"></a><?
	}
	?></td>
      <td bgcolor="<?=$tdcolor?>"  align="right">
	<? print($comments[$i]["t_hours"]."h ".$comments[$i]["t_minutes"]."m ".$comments[$i]["t_seconds"]); ?>s ago</td>
      <td bgcolor="<?=$tdcolor?>" align="left">
       <table cellspacing="0" cellpadding="0">
        <tr>
         <td>
 	  <a href="user.php?who=<?=$comments[$i]["who"]?>"><img src="avatars/<?=$comments[$i]["avatar"]?>" width="16" height="16" border="0" title="<?=$comments[$i]["nickname"]?>"></a><br />
         </td>
         <td>
          <img src="gfx/z.gif" width="3" height="1"><br />
         </td>
         <td>
	  <a href="user.php?who=<?=$comments[$i]["who"]?>"><?=$comments[$i]["nickname"]?></a><br />
         </td>
        </tr>
       </table>
      </td>
     </tr>
    <?
    flush();
    endfor; ?>
   </table>
  </td>
 </tr>
</table>
<br />
<? require("include/bottom.php"); ?>
