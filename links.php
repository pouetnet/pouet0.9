<?
require("include/top.php");

$userstogreet[0]=69;
$userstogreet[1]=11;
$userstogreet[2]=10;
$userstogreet[3]=87;
$userstogreet[4]=207;
$userstogreet[5]=271;
$userstogreet[6]=108;
$userstogreet[7]=221;

for($i=0;$i<count($userstogreet);$i++) {
 $query="SELECT id,nickname,avatar FROM users WHERE id=".$userstogreet[$i];
 $result=mysql_query($query);
 $users[]=mysql_fetch_array($result);
}
?>
<table cellspacing="0" cellpadding="0" border="0">
 <tr>
  <td colspan="2"><br></td>
  <td><img src="gfx/z.gif" width="5" height="1"><br></td>
  <td bgcolor="#FFFFFF"><img src="gfx/z.gif" width="1" height="1"><br></td>
  <td><img src="gfx/z.gif" width="5" height="1"><br></td>
  <td colspan="2"><br></td>
 </tr>
<?
$result = mysql_query("SELECT * FROM links");
while($link = mysql_fetch_row($result)) {
  print("<tr>\n");
  print("<td><br></td>\n");
  print("<td align=\"right\"><a href=\"".$link[1]."\">".$link[1]."</a><br></td>\n");
  print("<td><img src=\"gfx/z.gif\" width=\"5\" height=\"1\"><br></td>\n");
  print("<td bgcolor=\"#FFFFFF\"><img src=\"gfx/z.gif\" width=\"1\" height=\"1\"><br></td>\n");
  print("<td><img src=\"gfx/z.gif\" width=\"5\" height=\"1\"><br></td>\n");
  print("<td>".$link[2]."<br></td>\n");
  print("<td><br></td>\n");
  print("</tr>\n");
}
?>
 <tr>
  <td colspan="2"><br></td>
  <td><img src="gfx/z.gif" width="5" height="1"><br></td>
  <td bgcolor="#FFFFFF"><img src="gfx/z.gif" width="1" height="1"><br></td>
  <td><img src="gfx/z.gif" width="5" height="1"><br></td>
  <td colspan="2"><br></td>
 </tr>
 <tr>
  <td colspan="7" bgcolor="#FFFFFF">
   <img src="gfx/z.gif" width="1" height="1"><br>
  </td>
 </tr>
 <tr>
  <td bgcolor="#FFFFFF">
   <img src="gfx/z.gif" width="1" height="1"><br>
  </td>
  <td colspan="5" align="center">
   <table><tr><td>
    <br>
    the pouët.net team would like to thank the following people:<br>
    <br>
    <ul>
     <li>
      <a href="user.php?who=<? print($users[0]["id"]); ?>"><? print($users[0]["nickname"]); ?></a>
      for the two nice logo he has done for us
     </li>
     <li>
      <a href="user.php?who=<? print($users[1]["id"]); ?>"><? print($users[1]["nickname"]); ?></a>
      for the well designed pouët.net button and icon
     </li>
     <li>
      <a href="user.php?who=<? print($users[2]["id"]); ?>"><? print($users[2]["nickname"]); ?></a>
      for the domain name inspiration
     </li>
     <li>
      <a href="mailto:yes@ojuice.net">yes</a> &amp; <a href="user.php?who=<? print($users[3]["id"]); ?>"><? print($users[3]["nickname"]); ?></a>
      for our partnership
     </li>
     <li>
      <a href="user.php?who=<? print($users[4]["id"]); ?>"><? print($users[4]["nickname"]); ?></a>
      for his nice pouët.net logo
     </li>
     <li>
      <a href="user.php?who=<? print($users[5]["id"]); ?>"><? print($users[5]["nickname"]); ?></a>
      and <a href="http://fra.planet-d.net">fra</a>
      for totoro !
     </li>
     <li>
      <a href="user.php?who=<? print($users[6]["id"]); ?>"><? print($users[6]["nickname"]); ?></a>
      for the spanish goblin logo!
     </li>
     <li>
      <a href="user.php?who=<? print($users[7]["id"]); ?>"><? print($users[7]["nickname"]); ?></a>
      your logo and <a href="http://www.calodox.org/demoo/">demoo!</a> rulez ;)
     </li>
    </ul>
   </td></tr></table>
  </td>
  <td bgcolor="#FFFFFF">
   <img src="gfx/z.gif" width="1" height="1"><br>
  </td>
 </tr>
 <tr>
  <td colspan="7" bgcolor="#FFFFFF">
   <img src="gfx/z.gif" width="1" height="1"><br>
  </td>
 </tr>
 <tr>
  <td colspan="2"><br></td>
  <td><img src="gfx/z.gif" width="5" height="1"><br></td>
  <td bgcolor="#FFFFFF"><img src="gfx/z.gif" width="1" height="1"><br></td>
  <td><img src="gfx/z.gif" width="5" height="1"><br></td>
  <td colspan="2"><br></td>
 </tr>
</table>

<? require("include/bottom.php"); ?>
