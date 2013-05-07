<pre>
<?
require("top.php");

$file = fopen ("http://www.grid.org/services/teams/team_members.htm?id=BC792A55-96EB-4170-9026-239438C3D04A&rsps=250", "r");
if (!$file) {
    echo "<p>Unable to open remote file.\n";
    exit;
}

$i=0;

while (!feof ($file)) {
  $line = fgets ($file, 1024);
  if(strstr($line,"<tr bgcolor=\"#FFFFFF\" onmouseover=\"javascript:style.background='#E5E5E5'\" onmouseout=\"javascript:style.background='#FFFFFF'\">")) {
    $line = fgets ($file, 1024);
    eregi("><b>(.*)</b></a></td>", $line, $out);
    $ud[$i]["login"]=$out[1];
    $line = fgets ($file, 1024);
    eregi("<td align=\"right\" nowrap=\"true\">(.*)</td>", $line, $out);
    $ud[$i]["joined"]=$out[1];
    $line = fgets ($file, 1024);
    eregi("<td align=\"right\">(.*)</td>", $line, $out);
    $ud[$i]["cputime"]=$out[1];
    $line = fgets ($file, 1024);
    eregi("<td align=\"right\">(.*)</td>", $line, $out);
    $ud[$i]["points"]=$out[1];
    $line = fgets ($file, 1024);
    eregi("<td align=\"right\">(.*)</td>", $line, $out);
    $ud[$i]["results"]=$out[1];
    $i++;
  }
}
fclose($file);

for($i=0;$i<count($ud);$i++)
{
 /*
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
 */
	// joined date transformation
	$jm=sprintf("%02d",substr($ud[$i]["joined"],0,2));
	$jd=sprintf("%02d",substr($ud[$i]["joined"],3,2));
	$jy=sprintf("%d",substr($ud[$i]["joined"],6,4));
	$ud[$i]["joined"]=$jy."-".$jm."-".$jd;

 $ud[$i]["results"]=str_replace(",","",$ud[$i]["results"]);
 $ud[$i]["points"]=str_replace(",","",$ud[$i]["points"]);
 /*
	// pouët.net account check
	$query="SELECT id,nickname,avatar FROM users WHERE udlogin='".$ud[$i]["login"]."'";
	$result=mysql_query($query);
	if(mysql_num_rows($result))
	{
		$ud[$i]["id"]=mysql_result($result,0,"id");
		$ud[$i]["nickname"]=mysql_result($result,0,"nickname");
		$ud[$i]["avatar"]=mysql_result($result,0,"avatar");
	}
 */
}
// print(count($ud)."\n");
for($i=0;$i<count($ud);$i++)
{
  $query="SELECT id FROM ud WHERE login='".$ud[$i]["login"]."'";
  $result=mysql_query($query);
  if(mysql_num_rows($result))
  {
    $id=mysql_result($result,0);
    $query="UPDATE ud SET ";
    $query.="joined='".$ud[$i]["joined"]."',";
    $query.="results=".$ud[$i]["results"].",";
    $query.="points=".$ud[$i]["points"].",";
    $query.="cputime='".$ud[$i]["cputime"]."' ";
    $query.="WHERE id=".$id;
  } else {
    $query="INSERT ud SET ";
    $query.="login='".$ud[$i]["login"]."',";
    $query.="joined='".$ud[$i]["joined"]."',";
    $query.="results=".$ud[$i]["results"].",";
    $query.="points=".$ud[$i]["points"].",";
    $query.="cputime='".$ud[$i]["cputime"]."'";
  }
//  print($query."\n");
  mysql_query($query);
}
?>
</pre>
<table border="1">
  <tr>
    <th>members</th>
    <th>joined</th>
    <th>results</th>
    <th>points</th>
    <th>cputime</th>
  </tr>
<? for($i=0;$i<count($ud);$i++): ?>
  <tr>
    <td><? print($ud[$i]["login"]); ?></td>
    <td><? print($ud[$i]["joined"]); ?></td>
    <td align="right"><? print($ud[$i]["results"]); ?></td>
    <td align="right"><? print($ud[$i]["points"]); ?></td>
    <td align="right"><? print($ud[$i]["cputime"]); ?></td>
  </tr>
<? endfor; ?>
</table>
<? require("bottom.php"); ?>
