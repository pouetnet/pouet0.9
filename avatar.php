 <?
require("include/top.php");

unset($submitok);

if(is_uploaded_file($userfile))
{
  //$tmpfile=tempnam("tmp","avatar");
  //rename($userfile,$tmpfile);

  $size=GetImageSize($userfile);
  if($size[0]!=16) {
    $errormessage[]="the width must be equal to 16 pixels";
  }
  if($size[1]!=16) {
    $errormessage[]="the height must be equal to 16 pixels";
  }
  if($size[2]!=1) {
    $errormessage[]="the file must be a .gif file";
  }
  if(filesize($userfile)>4096) {
    $errormessage[]="the file size must be lower than 4Kb";
  }
	$ext = strrchr($userfile_name, '.');
	if($ext !== false)
	{
		$custom_name = substr($userfile_name, 0, -strlen($ext));
	}
	else
	{
		$custom_name = $userfile_name;
	}
	$custom_name = strtolower($custom_name);
	if(strlen($custom_name) > 250)
	{
		$custom_name = substr($custom_name, 0, 250);
	}
	$custom_name .= '.gif';

  if(!preg_match("/^[a-z0-9_-]*\.gif$/",$custom_name)) {
    $errormessage[]="please give a senseful filename devoid of dumb characters, kthx? (nothing but alphanumerics, dash and underscore is allowed)";
  }

  if(file_exists("avatars/".$custom_name)) {
    $errormessage[]="this filename already exists on the server";
  }
  // if everything is ok
  if(!$errormessage)
    $submitok=true;
}

if(!$submitok&&file_exists($userfile)){
  unlink($userfile);
}

if($submitok) {
	copy($userfile,"avatars/".$custom_name);
  unlink($userfile);
  /*
  // reward the user
  $query ="INSERT INTO avatars SET ";
  $query.="avatar='".$userfile_name."',";
  $query.="user=".$user["id"].",";
  $query.="added=NOW()";
  mysql_query($query);
  */
}

if($errormessage) { ?>
<table>
<?
	for($i=0;$i<count($errormessage);$i++) {
		print("<tr><td>error:</td><td><font color=\"#FF8888\">".$errormessage[$i]."</font><br></td></tr>\n");
	}
?>
</table>
<? } ?>
<br>
<table width="50%"><tr><td valign="top">
<table bgcolor="#000000" cellspacing="1" cellpadding="0" border="0" width="100%">
 <tr>
  <td>
   <table bgcolor="#000000" cellspacing="1" cellpadding="2" border="0" width="100%">
    <tr bgcolor="#224488">
     <th><b>some of the currently available avatars</b></th>
    </tr>
    <tr bgcolor="#446688">
     <td>
      <table width="100%">
       <tr>
       <?
        $d = glob("avatars/*.gif");
        $grid = 16;
        $keyz = array_rand($d,$grid*$grid);
        for ($i=0; $i<$grid*$grid; $i++) {
          $entry = $d[$keyz[$i]];
          print("<td align=\"center\"><img src=\"".$entry."\" width=\"16\" height=\"16\"></td>\n");
          if(($i%$grid)==$grid-1) {
            print("</tr><tr>\n");
          }
        }
        /*
        while($entry=$d->read()) {
          $i++;
          print("<td align=\"center\"><img src=\"avatars/".$entry."\" width=\"16\" height=\"16\"><br></td>\n");
          if(!($i%8)) {
            print("</tr><tr>\n");
          }
        }
        $d->close();
        while($i%8) {
        $i++;
        print("<td><br></td>\n");
        }
        */
       ?>
       </tr>
      </table>
     </td>
    </tr>
   </table>
  </td>
 </tr>
</table>
<br>
<form action="avatar.php" enctype="multipart/form-data" method="post">
<table bgcolor="#000000" cellspacing="1" cellpadding="0" border="0" width="100%">
 <tr>
  <td>
   <table bgcolor="#000000" cellspacing="1" cellpadding="2" border="0" width="100%">
    <tr bgcolor="#224488">
     <th><b>upload a new avatar</b></th>
    </tr>
    <tr bgcolor="#557799">
     <td>
      <table cellspacing="0" cellpadding="0" width="100%">
       <tr>
        <td valign="bottom">
         in order to upload a new avatar, make sure to follow those rules:<br>
         <ul>
          <li>it's a <b>.gif</b> file</li>
          <li>it's <b>16</b> pixel width</li>
          <li>it's <b>16</b> pixel height</li>
          <li>it's <b>4</b>Kb max.</li>
          <li>the background color is set to <b>transparent</b></li>
         </ul>
         if everything's ok, you can upload your file by using the <b>browse</b> button below.<br>
         <br>
         then your avatar will be available with the others.<br>
         <br>
         <input type="file" name="userfile">
        </td>
        <td>
         <img src="gfx/example.gif" width="100" height="100"><br>
        </td>
       </tr>
      </table>
     </td>
    </tr>
    <tr bgcolor="#446688">
     <td align="right" colspan="2">
      <input type="image" src="gfx/submit.gif" border="0"><br>
     </td>
    </tr>
   </table>
  </td>
 </tr>
</table>
</form>
</td>
</tr></table>
<? require("include/bottom.php"); ?>
