<?
require("include/top.php");

function lettermenu($pattern) {
  print("[ ");
  if($pattern=="#") {
    print("<b>#</b>");
  } else {
    print("<a href=\"userlist.php?pattern=%23\">#</a>");
  }
  for($i=1;$i<=26;$i++) {
    print(" | ");
    if($pattern==chr(96+$i)) {
      print("<b>".chr(96+$i)."</b>");
    } else {
      print("<a href=\"userlist.php?pattern=".chr(96+$i)."\">".chr(96+$i)."</a>");
    }
  }
  print(" ]<br>\n");
}

function goodfleche($wanted,$current) {
  if($wanted==$current) {
    $fleche="fleche1a";
  } else {
    $fleche="fleche1b";
  }
  return $fleche;
}

$users_per_page=$user["userlistusers"];

if (!$order) $order="name";

$pattern=$_REQUEST['pattern'];
if(($order=="name")&&(!$pattern)) {
  $pattern=chr(mt_rand(96,122));
  if($pattern==chr(96)) {
    $pattern="#";
  }
}

if(($order=="name")&&($pattern=="#")) {
  $sqlwhere="(nickname LIKE '0%')||(nickname LIKE '1%')||(nickname LIKE '2%')||(nickname LIKE '3%')||(nickname LIKE '4%')||(nickname LIKE '5%')||(nickname LIKE '6%')||(nickname LIKE '7%')||(nickname LIKE '8%')||(nickname LIKE '9%')";
} else {
  $sqlwhere="nickname LIKE '".$pattern."%'";
}
switch($order) {
	  case "name":
	  	$query="SELECT id,nickname,quand,level,avatar,glops FROM users WHERE (".$sqlwhere.") ORDER BY nickname";
	  	$cquery="SELECT count(0) FROM users WHERE (".$sqlwhere.") ORDER BY nickname";
	        break;
	  case "age": 
	  	$query="SELECT id,nickname,quand,level,avatar,glops FROM users ORDER BY quand";
	  	if(($page<=0)||(!$page)) {
		  $page=1;
		}
		$query.=" LIMIT ".(($page-1)*$users_per_page).",$users_per_page";
	  	$cquery="SELECT count(0) FROM users ORDER BY quand";
	  	break;
	  case "glops": 
	  	$query="SELECT id,nickname,quand,level,avatar,glops FROM users ORDER BY glops DESC";
	  	if(($page<=0)||(!$page)) {
		  $page=1;
		}
		$query.=" LIMIT ".(($page-1)*$users_per_page).",$users_per_page";
	  	$cquery="SELECT count(0) FROM users ORDER BY glops DESC";
	  	break;
	  case "level": 
	  	$query="SELECT id,nickname,quand,level,avatar,glops FROM users ORDER BY level ASC, quand";
	  	if(($page<=0)||(!$page)) {
		  $page=1;
		}
		$query.=" LIMIT ".(($page-1)*$users_per_page).",$users_per_page";
	  	$cquery="SELECT count(0) FROM users ORDER BY quand";
	  	break;
	  default: 
	  	$query="SELECT id,nickname,quand,level,avatar,glops FROM users WHERE (".$sqlwhere.") ORDER BY nickname";
	  	$cquery="SELECT count(0) FROM users WHERE (".$sqlwhere.") ORDER BY nickname";
	        break;
	}
$result = mysql_query($query);
while($tmp = mysql_fetch_array($result)) {
  $users[]=$tmp;
}

$result=mysql_query($cquery);
$nbusers=mysql_result($result,0);

$sortlink="userlist.php?page=".$page."&order=";

$result=mysql_query("SELECT MAX(glops) FROM users");
$maxglops=mysql_result($result,0);

/*
// best glopper
for($i=0;$i<count($users);$i++) {
  if($users[$i]['glops']>$maxglops) {
	  $maxglops=$users[$i]['glops'];
  }
}*/
/*
for($i=0;$i<count($users);$i++) {
  $result=mysql_query("SELECT count(0) FROM prods WHERE added=".$users[$i]["id"]);
  $users[$i]["prods"]=mysql_result($result,0);
  if($users[$i]["prods"]>$maxprods) {
	  $maxprods=$users[$i]["prods"];
  }
  $result=mysql_query("SELECT count(0) FROM groups WHERE added=".$users[$i]["id"]);
  $users[$i]["groups"]=mysql_result($result,0);
  if($users[$i]["groups"]>$maxgroups) {
	  $maxgroups=$users[$i]["groups"];
  }
  $result=mysql_query("SELECT count(0) FROM parties WHERE added=".$users[$i]["id"]);
  $users[$i]["party"]=mysql_result($result,0);
  if($users[$i]["party"]>$maxparty) {
	  $maxparty=$users[$i]["party"];
  }
  $result=mysql_query("SELECT count(0) FROM comments WHERE who=".$users[$i]["id"]);
  $users[$i]["comments"]=mysql_result($result,0);
  if($users[$i]["comments"]>$maxcomments) {
	  $maxcomments=$users[$i]["comments"];
  }
  $users[$i]["total"]=4*$users[$i]["prods"]+3*$users[$i]["groups"]+2*$users[$i]["party"]+1*$users[$i]["comments"];
  if($users[$i]["total"]>$maxtotal) {
	  $maxtotal=$users[$i]["total"];
  }
}

if(!$maxprods)$maxprods=1;
if(!$maxgroups)$maxgroups=1;
if(!$maxparty)$maxparty=1;
if(!$maxcomments)$maxcomments=1;
if(!$maxtotal)$maxtotal=1;
*/
if(!$maxglops)$maxglops=1;
?>
<br>
<table bgcolor="#000000" cellspacing="1" cellpadding="0" border="0">
 <tr>
  <td>
   <table bgcolor="#000000" cellspacing="1" cellpadding="2" border="0">
    <tr bgcolor="#224488">
     <th align="center">
      <table><tr>
       <td>
        <a href="<? print($sortlink); ?>name"><img src="gfx/<? print(goodfleche("name",$order)); ?>.gif" width="13" height="12" border="0"></a><br>
       </td>
       <td>
        <a href="<? print($sortlink); ?>name"><b>nickname</b></a>
       </td>
      </tr></table>
     </th>
     <th align="center">
      <table><tr>
       <td>
        <a href="<? print($sortlink); ?>age"><img src="gfx/<? print(goodfleche("age",$order)); ?>.gif" width="13" height="12" border="0"></a><br>
       </td>
       <td>
        <a href="<? print($sortlink); ?>age"><b>age</b></a>
       </td>
      </tr></table>
     </th>
     <th align="center">
      <table><tr>
       <td>
        <a href="<? print($sortlink); ?>level"><img src="gfx/<? print(goodfleche("level",$order)); ?>.gif" width="13" height="12" border="0"></a><br>
       </td>
       <td>
        <a href="<? print($sortlink); ?>level"><b>level</b></a>
       </td>
      </tr></table>
     </th>
     <th align="center">
      <table><tr>
       <td>
        <a href="<? print($sortlink); ?>glops"><img src="gfx/<? print(goodfleche("glops",$order)); ?>.gif" width="13" height="12" border="0"></a><br>
       </td>
       <td>
        <a href="<? print($sortlink); ?>glops"><b>glöps</b></a>
       </td>
      </tr></table>
     </th>
	 <? /* ?>
     <th align="center">
      <b>prods added</b><br>
     </th>
     <th align="center">
      <b>groups added</b><br>
     </th>
     <th align="center">
      <b>party added</b><br>
     </th>
     <th align="center">
      <b>comments added</b><br>
     </th>
     <th align="center">
      <b>total</b><br>
     </th>
	 <? */ ?>
    </tr>
   <?
   for($i=0;$i<count($users);$i++) {
     if($i%2) {
       print("<tr bgcolor=\"#446688\">");
     } else {
       print("<tr bgcolor=\"#557799\">");
     }
     $myyear=substr($users[$i]["quand"],0,4);
     $mymonth=substr($users[$i]["quand"],5,2);
     $myday=substr($users[$i]["quand"],8,2);
     $myhour=substr($users[$i]["quand"],11,2);
     $myminute=substr($users[$i]["quand"],14,2);
     $mysecond=substr($users[$i]["quand"],17,2);
     $age=time()-mktime($myhour,$myminute,$mysecond,$mymonth,$myday,$myyear);
     $nbdays=floor($age/(60*60*24));
     // $nbtotal[$users[$i]["id"]]=$nbprods[$users[$i]["id"]]+$nbgroups[$users[$i]["id"]]+$nbnews[$users[$i]["id"]]+$nbcomments[$users[$i]["id"]];
     ?>
      <td>
       <table cellspacing="0" cellpadding="0">
        <tr>
         <td>
          <a href="user.php?who=<? print($users[$i]["id"]); ?>">
           <img src="avatars/<? print($users[$i]["avatar"]); ?>" width="16" height="16" border="0"><br>
          </a>
         </td>
         <td>
          <img src="gfx/z.gif" width="3" height="1"><br>
         </td>
         <td>
          <a href="user.php?who=<? print($users[$i]["id"]); ?>">
           <? print($users[$i]["nickname"]); ?><br>
          </a>
         </td>
        </tr>
       </table>
      </td>
      <td align="right"><? print($nbdays." days"); ?></td>
      <td align="right"><? print($users[$i]["level"]); ?></td>
	  <?
	  $xglops=floor($users[$i]["glops"]*100/$maxglops);
      /*
	  $xprods=floor($users[$i]["prods"]*100/$maxprods);
	  $xgroups=floor($users[$i]["groups"]*100/$maxgroups);
	  $xparty=floor($users[$i]["party"]*100/$maxparty);
	  $xcomments=floor($users[$i]["comments"]*100/$maxcomments);
	  $xtotal=floor($users[$i]["total"]*100/$maxtotal);
	  ?>
      <td><? DoBar($xprods); ?></td>
      <td><? DoBar($xgroups); ?></td>
      <td><? DoBar($xparty); ?></td>
      <td><? DoBar($xcomments); ?></td>
      <td><? DoBar($xtotal); ?></td>
	  <? */ ?>
      <td><? DoBar($xglops,false,$users[$i]["glops"]); ?></td>
     </tr>
     <?
   }
    if ($order=="name"): ?>
    <tr bgcolor="#224488">
     <th colspan="4" align="center">
      <? lettermenu($pattern); ?>
     </th>
    </tr>
    <? else:
    
    $nextlink="userlist.php?order=".$order."&page=";
   ?>
    <tr bgcolor="#224488">
     <td colspan="12">
      <table width="100%" cellspacing="0" cellpadding="0" border="0">
       <tr>
       <? if($page>=2): ?>
        <td>
         <a href="<? print($nextlink.($page-1)); ?>">
          <img src="gfx/flecheg.gif" border="0"><br>
         </a>
        </td>
        <td>&nbsp;</td>
        <td nowrap>
         <a href="<? print($nextlink.($page-1)); ?>">
          <b>previous page</b><br>
         </a>
        </td>
       <? endif; ?>
        <form action="userlist.php">
        <input type="hidden" name="type" value="<? print($type); ?>">
        <input type="hidden" name="platform" value="<? print($platform); ?>">
        <td width="50%" align="right">
        <input type="hidden" name="order" value="<? print($order); ?>">
        <select name="page">
        <? for($i=1;($i-1)<=($nbusers/$users_per_page);$i++): ?>
        <? if($i==$page): ?>
        <option value="<? print($i); ?>" selected><? print($i); ?></option>
        <? else: ?>
        <option value="<? print($i); ?>"><? print($i); ?></option>
        <? endif; ?>
        <? endfor; ?>
        </select><br>
        </td>
        <td>&nbsp;</td>
        <td width="50%">
        <input type="image" src="gfx/submit.gif" border="0"><br>
        </td>
        </form>
       <? if(($page*$users_per_page)<=$nbusers): ?>
        <td nowrap>
         <a href="<? print($nextlink.($page+1)); ?>">
          <b>next page</b><br>
         </a>
        </td>
        <td>&nbsp;</td>
        <td>
         <a href="<? print($nextlink.($page+1)); ?>">
          <img src="gfx/fleched.gif" border="0"><br>
         </a>
        </td>
       <? endif; ?>
       </tr>
      </table>
     </td>
    </tr>
   
    
    
    <? endif; ?>
   </table>
  </td>
 </tr>
</table>
<br>
<? require("include/bottom.php"); ?>
