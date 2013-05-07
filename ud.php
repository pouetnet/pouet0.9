<?
require("include/top.php");

$colors[]="#224488";
$colors[]="#446688";
$colors[]="#557799";

$query="SELECT login,joined,results,points,cputime FROM ud ORDER BY points DESC";
$result=mysql_query($query);
while($tmp=mysql_fetch_array($result)) {
  $ud[]=$tmp;
}

for($i=0;$i<count($ud);$i++)
{
	// cputime transformation
	$ty=sprintf("%d",substr($ud[$i]["cputime"],0,1));
	$td=sprintf("%d",substr($ud[$i]["cputime"],2,3));
	$th=sprintf("%d",substr($ud[$i]["cputime"],6,2));
	$tm=sprintf("%d",substr($ud[$i]["cputime"],9,2));
	$ts=sprintf("%d",substr($ud[$i]["cputime"],12,2));
	$ud[$i]["cputime"]="";
	if($ty)	$ud[$i]["cputime"].=$ty." year".(($ty>1)?"s":"")." ";
	if($td)	$ud[$i]["cputime"].=$td." day".(($td>1)?"s":"")." ";
	if($th)	$ud[$i]["cputime"].=$th." hour".(($th>1)?"s":"")." ";
	if($tm)	$ud[$i]["cputime"].=$tm." minute".(($tm>1)?"s":"")." ";
	if($ts||(!$ty&&!$td&&!$th&&!$tm))	$ud[$i]["cputime"].=$ts." second".(($ts>1)?"s":"");

	// joined date transformation
	$jy=sprintf("%d",substr($ud[$i]["joined"],0,4));
	$jm=sprintf("%d",substr($ud[$i]["joined"],5,2));
	$jd=sprintf("%2d",substr($ud[$i]["joined"],8,2));
	$ud[$i]["joined"]=$jd." ".$months[$jm]." ".$jy;

	// glöps conversion
	$ud[$i]["glops"]=round($ud[$i]["points"]/1000);

	// pouët.net account check
	$query="SELECT id,nickname,avatar FROM users WHERE udlogin='".$ud[$i]["login"]."'";
	$result=mysql_query($query);
	if(mysql_num_rows($result))
	{
		$ud[$i]["id"]=mysql_result($result,0,"id");
		$ud[$i]["nickname"]=mysql_result($result,0,"nickname");
		$ud[$i]["avatar"]=mysql_result($result,0,"avatar");
	}
}
?>
<br>
<table bgcolor="#000000" cellspacing="1" cellpadding="0" width="75%"><tr><td>
<table bgcolor="#000000" cellspacing="1" cellpadding="2">
 <tr>
  <th bgcolor="<? print($colors[0]); ?>">the United Devices pouët.net team</th>
 </tr>
 <tr>
  <td bgcolor="<? print($colors[2]); ?>">
   <blockquote>
    The pouët.net UD team is a community project, along with United Devices, to help find a cure for cancer. The idea is simple: everyone downloads a program which analyses data about cells, and then sends it back to a HQ server for processing of results. Eventually, hopefully, someone will chance upon a result which will lead to a cure for cancer.<br>
    <br>
    Sceners are people with a heart too, so I created this team so that this kind of crazy people can find a team where they feel @ home.<br>
    <br>
    To thank our members, the points owned on UD are exchanged into glöps, the pouët.net currency.<br>
    <br>
    For those of you who are not part of the team yet:<br>
    <ul>
      <li>Download de UD client and start computing those molecules !</li>
      <li>Click on the "join this team" button on <a href="http://www.grid.org/services/teams/team.htm?id=BC792A55-96EB-4170-9026-239438C3D04A">this</a> page and join us.</li>
      <li>Then, <a href="account.php">create</a> or <a href="account.php">modify</a> your account and specify your UD login on pouët.net</li>
    </ul>
   </blockquote>
  </td>
 </tr>
 <tr>
  <td bgcolor="<? print($colors[1]); ?>" align="center">
   <a href="http://www.grid.org/services/teams/team.htm?id=BC792A55-96EB-4170-9026-239438C3D04A">team homepage</a>
   on
   <a href="http://www.ud.com/">United Devices</a><br>
  </td>
 </tr>
</table>
</td></tr></table>
<br>
<table bgcolor="#000000" cellspacing="1" cellpadding="0"><tr><td>
<table bgcolor="#000000" cellspacing="1" cellpadding="2">
 <tr>
  <th bgcolor="<? print($colors[0]); ?>" align="center">members</th>
  <th bgcolor="<? print($colors[0]); ?>" align="center">joined</th>
  <th bgcolor="<? print($colors[0]); ?>" align="center">points</th>
  <th bgcolor="<? print($colors[0]); ?>" align="center">results</th>
  <th bgcolor="<? print($colors[0]); ?>" align="center">cputime</th>
  <th bgcolor="<? print($colors[0]); ?>" align="center">glöps</th>
 </tr>
<? for($i=0;$i<count($ud);$i++): ?>
 <? $c=($i%2)?1:2; ?>
 <tr>
  <? if($ud[$i]["id"]&&$ud[$i]["nickname"]): ?>
  <td bgcolor="<? print($colors[$c]); ?>">
   <table cellspacing="0" cellpadding="0">
	<tr>
     <td>
      <a href="user.php?who=<? print($ud[$i]["id"]); ?>">
       <img src="avatars/<? print($ud[$i]["avatar"]); ?>" width="16" height="16" border="0" alt="<? print($ud[$i]["nickname"]); ?>"><br>
      </a>
     </td>
     <td>&nbsp;</td>
     <td>
      <a href="user.php?who=<? print($ud[$i]["id"]); ?>">
       <b><? print($ud[$i]["nickname"]); ?></b><br>
      </a>
     </td>
    </tr>
   </table>
  </td>
  <? else: ?>
  <td bgcolor="<? print($colors[$c]); ?>"><? print($ud[$i]["login"]); ?></td>
  <? endif; ?>
  <td bgcolor="<? print($colors[$c]); ?>" align="center"><? print($ud[$i]["joined"]); ?></td>
  <td bgcolor="<? print($colors[$c]); ?>" align="right"><? print($ud[$i]["points"]); ?></td>
  <td bgcolor="<? print($colors[$c]); ?>" align="right"><? print($ud[$i]["results"]); ?></td>
  <td bgcolor="<? print($colors[$c]); ?>" align="right"><? print($ud[$i]["cputime"]); ?></td>
  <td bgcolor="<? print($colors[$c]); ?>" align="right"><? print($ud[$i]["glops"]); ?></td>
 </tr>
<? endfor; ?>
</table>
</td></tr></table>
<br>
<? require("include/bottom.php"); ?>
