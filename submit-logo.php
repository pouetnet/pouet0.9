<?
require("include/top.php");
$logos_path = 'gfx/logos/';

unset($submitok);
// check the submitted data
//if($sshotfile&&($sshotfile!="none"))
if(is_uploaded_file($logofile))
{
  // check user account
  if(!$_SESSION["SESSION"]||!$_SESSION["SCENEID"])
	$errormessage[]="you need to be logged in first.";

	// check if the logo filename is already taken
  if(file_exists($logos_path.$logofile_name)) {
    $errormessage[]="this filename already exists on the server";
  }

  if(!preg_match("/^[a-z0-9_-]*\.[a-zA-Z]{3,4}$/",$logofile_name)) {
    $errormessage[]="please give a senseful filename devoid of dumb characters, kthx? (nothing but alphanumerics, dash and underscore is allowed)";
  }

  $fileinfo = GetImageSize($logofile);
  switch($fileinfo[2]) {
    case 1:$mytype=".gif";break;
    case 2:$mytype=".jpg";break;
    case 3:$mytype=".png";break;
    default: $errormessage[]="the logo is not a valid .gif/jpg or .png file"; break;
  }
  if($fileinfo[0]>700) {
    $errormessage[]="the width of the logo must not be greater than 700 pixels";
  }
  if($fileinfo[1]>200) {
    $errormessage[]="the height of the logo must not be greater than 200 pixels";
  }
  if(filesize($logofile) > 256 * 1024) {
    $errormessage[]="the size of the logo must not be greater than 256KB";
  }

  // if everything is ok
  if(!$errormessage)
    $submitok=true;
}

// move the submitted sshot to the appropriate location
if(!$submitok && file_exists($logofile)){
  unlink($logofile);
//  move_uploaded_file($sshotfile,"screenshots/".$which.$mytype);
//  copy($sshotfile, "screenshots/".$which.$mytype);
//  unlink($sshotfile);
}

if($submitok) {
	$ext = strrchr($logofile_name, '.');
	if($ext !== false)
	{
		$custom_name = substr($logofile_name, 0, -strlen($ext));
	}
	else
	{
		$custom_name = $logofile_name;
	}
	if(strlen($custom_name) > 250)
	{
		$custom_name = substr($custom_name, 0, 250);
	}

	$custom_name .= $mytype;
  copy($logofile, $logos_path.$custom_name);

  unlink($logofile);
  // reward the user
  $query ="INSERT INTO logos SET ";
  $query.="file='".$custom_name."', ";
  $query.="author1=".$_SESSION["SCENEID_ID"];
  mysql_query($query)
    or unlink($logos_path.$custom_name);
}

?>
<br>
<form action="<?=basename($_SERVER['PHP_SELF'])?>" method="post" enctype="multipart/form-data">
<table bgcolor="#000000" cellspacing="1" cellpadding="0"><tr><td>
<table bgcolor="#000000" cellspacing="1" cellpadding="2">
<? if($submitok): ?>
 <tr><th bgcolor="#224488">Your logo has been added</th></tr>
 <tr>
  <td bgcolor="#446688" align="center">
   feel free to add another one<br>
   <a href="logos.php">Vote for it now !</a><br>
  </td>
 </tr>
<? endif; ?>
<? if($errormessage): ?>
 <tr><th bgcolor="#224488">errors found</th></tr>
 <? for($i=0;$i<count($errormessage);$i++): ?>
  <? if($i%2): ?>
   <tr><td bgcolor="#557799">&nbsp;<b>- <font color="#FF8888"><? print($errormessage[$i]); ?></font></b></td></tr>
  <? else: ?>
   <tr><td bgcolor="#446688">&nbsp;<b>- <font color="#FF8888"><? print($errormessage[$i]); ?></font></b></td></tr>
  <? endif; ?>
 <? endfor; ?>
<? endif; ?>
 <tr>
  <th bgcolor="#224488">Send your logo</th>
 </tr>
 <tr>
  <td bgcolor="#446688">
   <table>
    <tr>
     <td>Your logo:</td>
     <td><input type="file" name="logofile"></td>
    </tr>
    <tr>
     <td colspan="2">
      <b>.gif</b> <b>.jpg</b> or <b>.png</b> width and height limit are <b>700</b>x<b>200</b><br />
			Its size must be < <b>128KB</b>.<br />
			<br />
			Don't forget to optimize your logo to fit well against the <a href="/gfx/trumpet.gif">pouet background</a><br />
			(by using transparency), or it will look like a noob picture.<br />
			<br />
      If you want to use PNG transparency, take in account that IE6 and older don't support it.<br />
			Like <a href="/user.php?who=1007">Gargaj</a> said:<br />
			<i>"just set your background color over transparency to the blue pouet background and<br />
			it will be only fucked up with IE if a trumpet wanders there."</i><br />
			<br />
			Before being displayed, your logo will be voted up or down by the whole Pouet community.<br />
			Don't blame us for not displaying it if it's lame, the scene is rude, and that's why we like it !<br />
     </td>
    </tr>
   </table>
  </td>
 </tr>
 <tr>
  <td bgcolor="#224488" align="right"><input type="image" src="gfx/submit.gif" style="border: 0px"></td>
 </tr>
</table>
</td></tr></table>
</form>
<br />
<? require("include/bottom.php"); ?>
