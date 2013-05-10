<?
require("include/top.php");

if(!isset($back)) $back = '/';

switch($e)
{
	case "password":
	case "Invalid password":
		$message="Invalid password.<br />Read the <a href=\"http://www.pouet.net/faq.php#15\">FAQ</a> if you want to get a new password.";
		break;
	case "login":
		$message="Bad username.";
		break;
	default:
		$message=htmlentities(stripslashes($e)); //"Undefined error.";
		break;
}
?>
<br>
<table bgcolor="#000000" cellspacing="1" cellpadding="0">
 <tr>
  <td>
   <table bgcolor="#000000" cellspacing="1" cellpadding="2">
    <tr>
     <td bgcolor="#224488" align="center" nowrap>
      <b>An error has occured:</b><br>
     </td>
    </tr>
    <tr>
     <td bgcolor="#557799">
	<?=$message?>
     </td>
    </tr>
    <tr>
     <td bgcolor="#446688" align="center">
      <a href="<?=htmlentities(stripslashes($back))?>"><b>get back</b></a><br>
     </td>
    </tr>
   </table>
  </td>
 </tr>
</table>
<br>
<? require("include/bottom.php"); ?>
