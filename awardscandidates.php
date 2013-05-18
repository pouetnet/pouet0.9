<?
require("include/top.php");
require("include/awardscategories.inc.php");

print("<br />");

//if ($SESSION_LEVEL=='administrator'):
// || $SESSION_LEVEL=='moderator' || $SESSION_LEVEL=='gloperator' || $SESSION_LEVEL=='user' || $SESSION_LEVEL=='pr0nstahr'):

//die("all done for now, see you next year!");

$year = $sceneorgyear;
if (!$year)
  die("all done for now, see you next year!");

if ($_SESSION["SCENEID_ID"])
{
  if ($action=='alter')
  {
  	$query = "SELECT * FROM awardscand_".$year." WHERE user='".$_SESSION["SCENEID_ID"]."'";
  	$result = mysql_query($query);
  	$uservote=mysql_fetch_assoc($result);

  	if (!$uservote)
  	{
  		$query = "insert into awardscand_".$year." set user='".$_SESSION["SCENEID_ID"]."'";
  		mysql_query($query);
  	}

  	$sqlc = array();
    $prod = (int)$prod;

  	if ($_POST["cat"]) {
  	  foreach ($_POST["cat"] as $v)
  		  $sqlc[(int)$v] = (int)$prod;
    }

  	foreach($awardscat[$year] as $x=>$name) {
  		if (isset($_POST["cat".$x]))
  		  $sqlc[$x] = (int)$_POST["cat".$x];
    }

    foreach ($sqlc as $k=>$v) {
      $v = (int)$v;
    	if ($v >= 0)
    	{
    	  $r = mysql_fetch_object(mysql_query(sprintf("select date from prods where id=%d",$v)));
    	  if (substr($r->date,0,4)!=$year) continue;

        $query = "update awardscand_".$year." set cat".$k."='".(int)$v."' where user='".$_SESSION["SCENEID_ID"]."'";
      	mysql_query($query);
    	}
    }
  }

	$query = "SELECT * FROM awardscand_".$year." WHERE user='".$_SESSION["SCENEID_ID"]."'";
	$result = mysql_query($query);
	$uservote=mysql_fetch_assoc($result);

	foreach($awardscat[$year] as $x=>$name) {
  	if ($uservote["cat".$x]) {
    	$query = "SELECT name FROM prods WHERE id='".$uservote["cat".$x]."'";
    	$result = mysql_query($query);
    	if ($result)
      	$uservote["name".$x]=mysql_result($result,0);
  	}
	}

/*if ($prod): ?>
<form action="">
linked prod id:<input type="text" name="which" value="<?=$prod?>" /><br />
</form><br />
<? endif; */

?>

<form action="awardscandidates.php" name="awardscandidates" method="post">
<input type="hidden" name="action" value="alter">
<table bgcolor="#000000" cellspacing="1" cellpadding="0"><tr><td>
<table bgcolor="#000000" cellspacing="1" cellpadding="2">
 <tr>
 <th colspan="3" bgcolor="#224488">scene.org awards candidate selection</th>
<?
foreach($awardscat[$year] as $x=>$name) {
?>
 <tr>
  <td bgcolor="#557799" ><?=$name?></td>
  <td bgcolor="#557799"><? print("<a href=\"prod.php?which=".$uservote["cat".$x]."\">".$uservote["name".$x]."</a>"); ?></td>
  <td bgcolor="#557799"><input type="text" name="cat<?=$x?>" value="<? print($uservote["cat".$x]); ?>">(<a href="javascript:popupProdLastYearSelector('awardscandidates','cat<?=$x?>');">select</a>)</td>
 </tr>
<?
}
?>
 <tr>
  <td bgcolor="#6688AA" align="right" colspan="3">
   <input type="image" src="gfx/submit.gif">
  </td>
 </tr>
</table>
</td></tr></table>
</form>
<br />

<table bgcolor="#000000" cellspacing="1" cellpadding="0" width='600'><tr><td>
<table bgcolor="#000000" cellspacing="1" cellpadding="2">
 <tr>
  <th bgcolor="#224488" align="center" colspan="2">
   <big>
   remember: the purpose of this selection to help the jury not to overlook prods;<br/>
   don't pick demos/intros that are obvious choices, pick something you think they might to forget!
   </big>
	 <!--div style='background:#800;color:white;margin:10px;padding:10px'>suggestions will close on wednesday, january the 5th! vote now!</div-->
  </td>
 </tr>
 <tr>
 <th colspan="2" bgcolor="#224488">category descriptions</th>
 <tr>
  <td bgcolor="#557799" >best demo</td>
  <td bgcolor="#557799">
  most outstanding realtime demo from <?=$year?>. a work that is solid on all fronts.
  </td>
 </tr>
 <tr>
  <td bgcolor="#557799" >best 64k</td>
  <td bgcolor="#557799">
  most outstanding 64k intro from <?=$year?>.
  </td>
 </tr>
 <tr>
  <td bgcolor="#557799" >best 4k</td>
  <td bgcolor="#557799">
  most outstanding 4k intro from <?=$year?>.
  </td>
 </tr>
<!--
 <tr>
  <td bgcolor="#557799" >best animation</td>
  <td bgcolor="#557799">
  the most outstanding animated (non-realtime) work or wild demo from <?=$year?>.
  </td>
 </tr>
 -->
 <tr>
  <td bgcolor="#557799" >best oldschool</td>
  <td bgcolor="#557799">
  most outstanding demo on an oldschool platform from <?=$year?>. the platform
  of the demo is limited to non-modern computers and alternative platforms
  that can be considered "low-end".
  </td>
 </tr>
 <tr>
  <td bgcolor="#557799" >best effects</td>
  <td bgcolor="#557799">
  the most innovative use of new effect or effects, pushing the
  boundaries of existing effects or making something amazing within the
  context of realtime graphics. best effects don't have to technically
  advanced, even if they often are, but the central theme is an
  effective or novel visual expression.
  </td>
 </tr>
 <tr>
  <td bgcolor="#557799" >best graphics</td>
  <td bgcolor="#557799">
  the most innovative use of graphics in a demo. this can be amazing 3d
  modelling, texturing, 2d / painted graphics, you name it.
  </td>
 </tr>
 <tr>
  <td bgcolor="#557799" >best soundtrack</td>
  <td bgcolor="#557799">
  the most outstanding soundtrack in a demo. a solid work both when it
  comes to craftsmanship (mixing, quality of sound) and originality.
  </td>
 </tr>
 <tr>
  <td bgcolor="#557799" >best direction</td>
  <td bgcolor="#557799">
    the most outstanding direction in a demo. innovative, fresh or simply
  impressive execution in combining the different parts into a single
  functional piece. this can be a well told story or an effect demo with
  excellent flow - there's different ways of doing superior direction.
  </td>
 </tr>
 <tr>
  <td bgcolor="#557799" >most original</td>
  <td bgcolor="#557799">
  most original work (in the context of demoscene). new ideas,
  innovative use of technology or new approach in direction or overall
  style. can include experimental works or doing the unexpected.
  </td>
 </tr>
 <tr>
  <td bgcolor="#557799" >breakthrough</td>
  <td bgcolor="#557799">
  a group (or artist) that raised their personal bar significantly
  during <?=$year?>. promising talents that might be tomorrows best demo makers.
  this doesn't need to be necessarily a newcomer group, rather than
  someone receiving deserved attention after rising above mediocrity.
  </td>
 </tr>
 <tr>
  <td bgcolor="#557799" >best technical achievement</td>
  <td bgcolor="#557799">
  most technically impressive work. this could be cramming lots of stuff
  into a really small space, doing crazy things on crazy platforms, or
  having complex and impressive rendering techniques in a demo.
  </td>
 </tr>
 <tr>
  <td bgcolor="#6688AA" align="center" colspan="2">
   Your nominations on these productions will not aggregate with other users nominations.<br />
   This list will only be used as a shortlist for the chosen scene.org awards jury members to consider as final nominees.<br/>
  </td>
 </tr>
</table>
</td></tr></table>

<br />
<?
}
else
{
?>
<style type="text/css">
.view {
  background: black;
  border-collapse: separate;
  border-spacing: 1px;
  border: 1px solid black;
  margin: 10px;
  width: 500px;
}
.view td {
  margin: 0px;
  padding: 2px;
  text-align: center;
}
.view img {
  vertical-align: text-top;
  margin-right: 2px;
}
.view th {
  font-size: 120%;
  padding: 5px;
  text-align: center;
}
#container {
  width: 750px;
  margin: 0px auto;
}
</style>

<div id='container'>
<table class='view'>
<tr>
  <th>you need to be logged in for this</th>
</tr>
<tr>
  <td class='bg1'>

    <form action="login.php" method="post">
       <input type="text" name="login" value="SceneID" size="15" maxlength="16" onfocus="this.value=''"><br />
       <input type="password" name="password" value="password" size="15" onfocus="javascript:if(this.value=='password') this.value='';"><br />
       <input type="checkbox" name="permanent">login for 1 year<br />
       <a href="account.php">register here</a><br />
       <input type="image" src="gfx/submit.gif">
    </form>

  </td>
</tr>
</table>
</div>
<?
}
 require("include/bottom.php"); ?>
