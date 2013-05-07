<html>
<head>
<title>pouët.net UD test</title>
</head>
<body>
<?
$file = fopen ("http://members.ud.com/services/teams/team.htm?id=BC792A55-96EB-4170-9026-239438C3D04A", "r");
if (!$file) {
    echo "<p>Unable to open remote file.\n";
    exit;
}

$i=0;

while (!feof ($file)) {
  $line = fgets ($file, 1024);
  if(strstr($line,"<tr bgcolor=\"#FFFFFF\" >")) {
    $line = fgets ($file, 1024);
    eregi("<td>(.*)</td>", $line, $out);
    $ud[$i]["login"]=$out[1];
    $line = fgets ($file, 1024);
    eregi("<td align=\"right\" nowrap=\"true\">(.*)-.*", $line, $out);
    $ud[$i]["joined"]=$out[1];
    $line = fgets ($file, 1024);
    eregi("<td align=\"right\">(.*)</td>", $line, $out);
    $ud[$i]["results"]=$out[1];
    $line = fgets ($file, 1024);
    eregi("<td align=\"right\">(.*)</td>", $line, $out);
    $ud[$i]["points"]=$out[1];
    $line = fgets ($file, 1024);
    eregi("<td align=\"right\">(.*)</td>", $line, $out);
    $ud[$i]["cputime"]=$out[1];
    $i++;
  }
}
fclose($file);
?>
<table border="1">
  <tr>
    <th colspan="5">Team Members Detail</th>
  </tr>
  <tr>
    <th>login</th>
    <th>joined</th>
    <th>results</th>
    <th>points</th>
    <th>cputime</th>
  </tr>
  <? for($i=0;$i<count($ud);$i++): ?>
  <tr>
    <td><? print($ud[$i]["login"]); ?></td>
    <td><? print($ud[$i]["joined"]); ?></td>
    <td><? print($ud[$i]["results"]); ?></td>
    <td><? print($ud[$i]["points"]); ?></td>
    <td><? print($ud[$i]["cputime"]); ?></td>
  </tr>
  <? endfor; ?>
</table>
</body>
</html>
