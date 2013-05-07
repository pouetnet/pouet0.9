<?
require("include/top.php");

$query="SELECT logos.*,users.nickname,users.avatar FROM logos,users WHERE users.id=logos.who";
$result=mysql_query($query);
while($tmp=mysql_fetch_array($result)) {
  $mylogos[]=$tmp;
}
?>
<br>
<table bgcolor="#000000" cellspacing="1" cellpadding="0" border="0">
 <tr>
  <td>
   <table bgcolor="#000000" cellspacing="1" cellpadding="2" border="0">
    <tr>
	   <td bgcolor="#224488">
      <b>logos</b><br>
     </td>
    </tr>
    <? for($i=0;$i<count($logos);$i++): ?>
    <tr>
	   <td bgcolor="#446688" align="center">
      <table bgcolor="#000000" cellspacing="1" cellpadding="0">
       <tr>
        <td bgcolor="#3A6EA5" background="/gfx/trumpet.gif">
         <img src="gfx/logos/<? print($mylogos[$i]["file"]); ?>" hspace="5" vspace="5"><br>
        </td>
       </tr>
      </table>
     </td>
    </tr>
    <tr>
	   <td bgcolor="#6688AA" align="right">
      <table cellspacing="0" cellpadding="0">
       <tr>
        <td>done by <a href="user.php?who=<? print($mylogos[$i]["who"]); ?>"><? print($mylogos[$i]["nickname"]); ?></a></td>
        <td>&nbsp;<br></td>
        <td><a href="user.php?who=<? print($mylogos[$i]["who"]); ?>"><img src="avatars/<? print($mylogos[$i]["avatar"]); ?>" width="16" height="16" border="0"></a></td>
       </tr>
      </table>
     </td>
    </tr>
    <? endfor; ?>
   </table>
  </td>
 </tr>
</table>
<br>
<? require("include/bottom.php"); ?>
