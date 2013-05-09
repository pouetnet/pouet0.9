<?
require("include/top.php");
require_once('recaptchalib.php');

// TODO remove this

// Get IM types
$result = mysql_query("DESC users im_type");
$row = mysql_fetch_row($result);
$reg = "/^enum\('(.*)'\)$/";
$tmp = preg_replace($reg,'\1',$row[1]);
$im_types = preg_split("/[']?,[']?/",$tmp);

$REGEXP_EMAIL = "^[a-zA-Z0-9][a-zA-Z0-9._-]*@[a-zA-Z0-9.-]+\\.[a-zA-Z]{2,4}$";

$errormessage = array();

if (isIPBanned()) die("no.");

if ($_POST["email"] && $_POST["nickname"]) {
  $nick = $_POST["nickname"];
  $nick = strip_tags($nick);
  $nick = trim($nick);

  ///////////////////////////////////////////////////////
  // USER ALREADY REGISTERED, UPDATE
  if($_SESSION["SCENEID_ID"]) {

    // user is registered and logged in

    $query ="UPDATE users SET ";

//    $s = mysql_query("select * from users where nickname='".$_POST["nickname"]."'");
//    $r = mysql_fetch_object($s);
    //$_POST["nickname"] = $nick;
//    if (!$r) // if nickname is taken
    $query.="nickname='".mysql_real_escape_string($nick)."', ";

    if (preg_match("/[^\\x20-\\x7f]/",$nick))
      $errormessage[] = "nick has invalid characters! (sorry, non-ascii characters are suspended for the time being)";

    if (strlen($nick) < 2)
      $errormessage[] = "nick too short!";

    $query.="udlogin='".mysql_real_escape_string($_POST["udlogin"])."', ";
    //if(strlen($level)==0) $query.="level='user', ";
    if((strlen($_POST["im_id"]) > 0) && (in_array($_POST["im_type"], $im_types))) {
      $query.="im_type='".$_POST["im_type"]."', ";
      $query.="im_id='".mysql_real_escape_string($_POST["im_id"])."', ";
    }
    else
    {
      $query.="im_type=NULL, ";
      $query.="im_id=NULL, ";
    }
    if(strlen($_POST["ojuice"])!=0) {
      $query.="ojuice=".((int)$_POST["ojuice"]).", ";
    }
    if(strlen($_POST["slengpung"])!=0) {
      $query.="slengpung=".((int)$_POST["slengpung"]).", ";
    }
    if(strlen($_POST["csdb"])!=0) {
      $query.="csdb=".((int)$_POST["csdb"]).", ";
    }
    if(strlen($_POST["zxdemo"])!=0) {
      $query.="zxdemo=".((int)$_POST["zxdemo"]).", ";
    }
    if(file_exists("avatars/".$_POST["avatar"]))
      $query.="avatar='".$_POST["avatar"]."' ";
    else
      $query.="avatar='zorglub.gif'";

    $query.="WHERE id=".$_SESSION["SCENEID_ID"];
    if (!count($errormessage)) {
      $sql = "";
      if ($_SESSION["SESSION_NICKNAME"] != $nick) {
        $sql = sprintf("insert into oldnicks (user,nick) values (%d,'%s')",$_SESSION["SCENEID_ID"],mysql_real_escape_string($_SESSION["SESSION_NICKNAME"]));
      }
      $_SESSION["SESSION_NICKNAME"]=$nick;
      $_SESSION["SESSION_AVATAR"]=$_POST["avatar"];
      mysql_query($query);
      unset($user);

      if ($sql) mysql_query($sql);

      $query = "SELECT cdc, timelock FROM users_cdcs WHERE user='".$_SESSION["SCENEID_ID"]."'";
      $result = mysql_query($query);
      while($tmp=mysql_fetch_array($result)){
        $cdc[] = $tmp;
      }

      $query="delete from users_cdcs where user=".$_SESSION["SCENEID_ID"];
      mysql_query($query);
      $uniquecdc = array();
      for ($i=0; $i<10; $i++) {
        $k = "cdc".$i;
        //echo $_POST[$k]." - ";
        $uniquecdc[] = (int)$_POST[$k];
      }
      $uniquecdc = array_unique($uniquecdc);

      foreach($uniquecdc as $v){
        if ($v > 0) {
          $flag = -1;
          for ($i=0; $i < count($cdc); $i++) {
            if ($cdc[$i]["cdc"] == $v) $flag = $i;
            //echo "[".$cdc[$i]["cdc"].",".$v.",".$flag."]";
            }
          if ($flag == -1) $query="insert into users_cdcs set cdc='".$v."', user='".$_SESSION["SCENEID_ID"]."', timelock=CURRENT_DATE";
           else $query="insert into users_cdcs set cdc='".$v."', user='".$_SESSION["SCENEID_ID"]."', timelock='".$cdc[$flag]["timelock"]."'";
          //echo $query."\n";
          mysql_query($query);
        }
      }
      unset($cdc);

      if (!preg_match("/".$REGEXP_EMAIL."/",$_POST["email"]))
        $errormessage[] = "invalid email address";

      $paramz = array(
         "userID" => $_SESSION["SCENEID_ID"],
         "firstname" => ($_POST["firstname"]),
         "lastname" => ($_POST["lastname"]),
         "nickname" => $nick, // question: do we need to set the sceneID nickname to the pouet one?
         "email" => $_POST["email"],
         "url" => $_POST["url"]
      );
      if ($_POST["password"] != $_POST["password2"]) {
        $errormessage[] = "Passwords dont match!";
      } else {
        if ($_POST["password"] && $_POST["password2"]) {
          $paramz["password"]  = md5($_POST["password"]);
          $paramz["password2"] = md5($_POST["password2"]);
        }
      }

      if (!count($errormessage)) {
        $returnvalue = $xml->parseSceneIdData("setUserInfoMD5", $paramz);

        if($returnvalue["returnvalue"]!=50)
        {
          $errormessage[] = $returnvalue["message"];
        }
        else
          $message = "modifications complete!";
      }
    }
  }
  else if ($_POST["password"] && $_POST["password2"] && $_POST["login"])
  {
    ///////////////////////////////////////////////////////
    // USER NOT REGISTERED, INSERT

    $resp = recaptcha_check_answer (RECAPTCHA_PRIV_KEY,
                                    $_SERVER["REMOTE_ADDR"],
                                    $_POST["recaptcha_challenge_field"],
                                    $_POST["recaptcha_response_field"]);

    if (!preg_match("/".$REGEXP_EMAIL."/",$_POST["email"]))
      $errormessage[] = "invalid email address";

    if (strlen($nick) < 2)
      $errormessage[] = "nick too short!";

    if ($_POST["login"] == $_POST["firstname"] && ($_POST["firstname"] == $_POST["lastname"] || $_POST["firstname"] == substr($_POST["lastname"],0,-2)))
      $errormessage[] = "yeah right.";

    if ($resp->is_valid && !count($errormessage)) {
      // user is not registered
      if ($_SERVER["REMOTE_ADDR"])
      {
        $query = "select id from users where level='banned' and lastip='".$_SERVER["REMOTE_ADDR"]."'";
        $result = mysql_query($query);
        $out = mysql_fetch_object($result);
        if ($out)
        {
          $_SESSION = null;
          $errormessage[]="your current ip belongs to a banned user. no account for you. if it's a shared ip, well, tough luck.";
        }

        if (strpos($_SERVER["REMOTE_ADDR"],"120.152.")===0 || strpos($_SERVER["REMOTE_ADDR"],"123.208.")===0)
        {
          $_SESSION = null;
          $errormessage[]="long story.";
        }
        if (strstr(gethostbyaddr($_SERVER["REMOTE_ADDR"]),"my-addr.com")!==false)
        {
          $_SESSION = null;
          $errormessage[]="long story.";
        }
        if (strstr(gethostbyaddr($_SERVER["REMOTE_ADDR"]),"go.vfserver.com")!==false)
        {
          $_SESSION = null;
          $errormessage[]="long story.";
        }
      } else {
        $_SESSION = null;
        $errormessage[]="you're wearing a proxy.. no account for you.";
      }
      if (!count($errormessage)) {
        $paramz = array(
          "login" => $_POST["login"],
          "firstname" => ($_POST["firstname"]),
          "lastname" => ($_POST["lastname"]),
          "nickname" => $nick,
          "email" => $_POST["email"],
          //"url" => $_POST["url"],
          "password" => md5($_POST["password"]),
          "password2" => md5($_POST["password2"]),
          "ip" => $_POST["REMOTE_ADDR"],
        );
        $returnvalue = $xml->parseSceneIdData("registerUserMD5", $paramz);

        if($returnvalue["returnvalue"]!=20)
        {
          $errormessage[] = $returnvalue["message"];
        }
        else if (!$returnvalue["user"]) {
          $errormessage[] = "WTF ERROR!";
        }
      }

    } else {
      $errormessage[]="wrong funny letters, sorry!";
    }
    if(!$errormessage)
    {
      $query= "INSERT users SET ";
      $query.="id=".$returnvalue["userID"].", ";
      if(strlen($nick)==0) {
        $query.="nickname='".$login."', ";
      }
      else
        $query.="nickname='".$nick."', ";
      if((strlen($im_id) > 0) && (in_array($im_type, $im_types))) {
        $query.="im_type='".$im_type."', ";
        $query.="im_id='".$im_id."', ";
      }
      else
      {
        $query.="im_type=NULL, ";
        $query.="im_id=NULL, ";
      }
      $query.="level='user', ";
      $query.="udlogin='".$udlogin."', ";
      $query.="avatar='".$avatar."', ";
      $query.="lastip='".$_SERVER["REMOTE_ADDR"]."', ";
      $query.="lasthost='".gethostbyaddr($_SERVER["REMOTE_ADDR"])."', ";
      if(strlen($_POST["ojuice"])!=0) {
        $query.="ojuice=".((int)$_POST["ojuice"]).", ";
      }
      if(strlen($_POST["slengpung"])!=0) {
        $query.="slengpung=".((int)$_POST["slengpung"]).", ";
      }
      if(strlen($_POST["csdb"])!=0) {
        $query.="csdb=".((int)$_POST["csdb"]).", ";
      }
      if(strlen($_POST["zxdemo"])!=0) {
        $query.="zxdemo=".((int)$_POST["zxdemo"]).", ";
      }
      $query.="quand=NOW()";
      mysql_query($query);
      $message = "registration complete! a confirmation mail will be sent to your address soon - you can't login until you confirmed your email address!";
    }
  } else {
    if (!$_POST["login"])
      $errormessage[] = "login is missing!";
    if (!$_POST["password"])
      $errormessage[] = "password is missing!";
    //$errormessage[] = "some required parameters are missing!";
  }
} else if ($_POST) {
  if (!$_POST["email"])
    $errormessage[] = "email address is missing!";
  if (!$_POST["nickname"])
    $errormessage[] = "nickname is missing!";
}
?>

<br/>
<form action="<?=$PHP_SELF?>" method="post" name="accountform">
<table bgcolor="#000000" cellspacing="1" cellpadding="0">
 <tr>
  <td>
   <table bgcolor="#000000" cellspacing="1" cellpadding="2">
    <tr>
     <td bgcolor="#224488" nowrap>
<?
if($_SESSION["SESSION"] && $_SESSION["SCENEID"]) {
  $query = "SELECT * FROM users WHERE id='".$_SESSION["SCENEID_ID"]."'";
  $result = mysql_query($query);
  $user=mysql_fetch_assoc($result);

  $userparams = array("userID" => $_SESSION["SCENEID_ID"]);

  $returnvalue = $xml->parseSceneIdData("getUserInfo", $userparams);
  if($returnvalue["returnvalue"]==10)
    $user = array_merge($user, $returnvalue["user"]);

?>
      <b>e-e-e-edit your account</b><br>
<? } else { ?>
      <b>create your account</b><br>
<? } ?>
     </td>
    </tr>

<? if (count($errormessage) || $message) { ?>
    <tr>
     <td bgcolor="#446688" style="font-weight: bold; padding: 10px; text-align:center;" class="bg1">
      <? if(count($errormessage)) { ?>
       there are some errors:<br>
       <br>
       <? for($i=0;$i<count($errormessage);$i++): ?>
        - <? print($errormessage[$i]); ?><br>
       <? endfor; ?>
       <br>
       <div align="center">
        please, correct them.<br>
        <a href="javascript:history.go(-1)">click here</a><br>
       </div>
      <? } else { ?>
       <?=$message?>
       <a href="index.php">have a nice stay!</a>
      <? } ?>
     </td>
    </tr>
<? }
if (!count($errormessage)) {
?>

    <tr>
     <td bgcolor="#446688">
      <table id='account'>
       <tr>
        <td align="right">login:<br></td>
<?if($_SESSION["SESSION"] && $_SESSION["SCENEID"]) {?>
        <td><b><?=htmlspecialchars($user["login"])?></b><br></td>
        <td><i>which word can you type very fast ?</i><br></td>
<?} else {?>
        <td><input type="text" name="login" maxlength="16" value='<?=htmlspecialchars($user["login"])?>'><br></td>
        <td><i>which word can you type very fast ?</i> [<font color="#FF8888"><b>req</b></font>]<br></td>
<?}?>
       </tr>
       <tr>
        <td align="right">password:<br></td>
        <td><input type="password" name="password"><br></td>
        <td><i>the most complicated one ?</i> [<font color="#FF8888"><b>req</b></font>]<br></td>
       </tr>
       <tr>
        <td align="right">password again:<br></td>
        <td><input type="password" name="password2"><br></td>
        <td><i>don't try to be original there</i> [<font color="#FF8888"><b>req</b></font>]<br></td>
       </tr>
       <tr>
        <td align="right">firstname:<br></td>
        <td><input type="text" name="firstname" value='<?=htmlspecialchars(utf8_decode($user["firstname"]))?>'><br></td>
        <td><i>which name your mother gave you ?</i><br></td>
       </tr>
       <tr>
        <td align="right">lastname:<br></td>
        <td><input type="text" name="lastname" value='<?=htmlspecialchars(utf8_decode($user["lastname"]))?>'><br></td>
        <td><i>and your father ?</i><br></td>
       </tr>
       <tr>
        <td align="right">email:<br></td>
        <td><input type="text" name="email" value='<?=htmlspecialchars($user["email"])?>'><br></td>
        <td><i>to be subscribed to 16 spammed newsletters a week</i> [<font color="#FF8888"><b>req</b></font>]<br></td>
       </tr>
<?
if($_SESSION["SESSION"] && $_SESSION["SCENEID"]) {
?>
       <tr>
        <td align="right">website:<br></td>
        <td><input type="text" name="url" value='<?=htmlspecialchars($user["url"]?$user["url"]:"http://")?>'><br></td>
        <td><i>want some hits ?</i><br></td>
       </tr>
<?
}
?>
       <tr>
        <td align="right">nickname:<br></td>
        <td><input type="text" name="nickname" maxlength="16" value='<?=htmlspecialchars($user["nickname"])?>'><br></td>
        <td><i>how do you look on IRC ?</i> [<font color="#FF8888"><b>req</b></font>]<br></td>
       </tr>
       <tr>
        <td align="right">instant messenger type:<br></td>
        <td>
          <select name="im_type">
          <option></option>
          <?
          for($i=0;$i<count($im_types);$i++) {
            if($user['im_type']==$im_types[$i]) {
              $is_selected = " selected";
            } else {
              $is_selected = "";
            }
            print("<option value=\"".$im_types[$i]."\"".$is_selected.">".$im_types[$i]."</option>\n");
          }
          ?>
          </select><br />
        </td>
        <td><i>the one you really use</i><br></td>
      </tr>
       <tr>
        <td align="right">instant messenger id:<br></td>
        <td>
          <input type="text" name="im_id" value='<?=htmlspecialchars($user["im_id"])?>'><br>
        </td>
        <td><i>buuuuuuuuuuuuuuuu .... hiho !</i><br></td>
       </tr>
       <tr>
        <td align="right">UD login:<br></td>
        <td><input type="text" name="udlogin" value='<?=htmlspecialchars($user["udlogin"])?>'><br></td>
        <td><i>your login on UD - <a href="ud.php">explained here</a></i><br></td>
       </tr>
       <tr>
        <td align="right">avatar: [<font color="#FF8888"><b>req</b></font>]&nbsp;</td>
        <td colspan="2">
         <table><tr>
         <td>
<?
  function mycmp($a,$b) { return strcasecmp($a,$b); }
  $entry = glob("./avatars/*.gif");
  usort($entry,"mycmp");
  if (!$user["avatar"]) {
    $r = $entry[array_rand($entry)];
    $user["avatar"] = str_replace("./avatars/","",$r);
  }
?>
         <img src="avatars/<?=$user["avatar"]?>" name="avatr">
  </td>
  <td>
  <select name="avatar" onChange="document.avatr.src='avatars/'+this.options[this.selectedIndex].value">
  <?
  foreach($entry as $e) {
    $e = str_replace("./avatars/","",$e);
    print("<option value=\"".$e."\"".($user["avatar"]==$e?" selected":"").">".$e."</option>\n");
  }
  ?>
  </select>
  <script language="JavaScript" type="text/javascript">
  <!--
  function randomizeAvatar() {
    var a = document.getElementsByName('avatar')[0];
    if (!a) return;
    a.selectedIndex = Math.floor( Math.random() * a.options.length );
    document.avatr.src='avatars/'+a.options[a.selectedIndex].value
  }
  //-->
  </script>
  (<a href="javascript:popupAvatarSelector('accountform','avatar');">select</a>)
  (<a href="javascript:randomizeAvatar();">random</a>)
  </td>
  </td>
  </tr></table>
        </td>
       </tr>
       <tr>
        <td><br></td>
        <td colspan="2">you can also upload your <i>personal</i> avatar <a href="avatar.php">here</a> ;)<br><br></td>
       </tr>

       <?
        $query = "SELECT cdc, (CURRENT_DATE - timelock) as time, prods.name FROM users_cdcs LEFT JOIN prods ON prods.id = users_cdcs.cdc WHERE user='".$_SESSION["SCENEID_ID"]."'";
        $result = mysql_query($query);
        while($tmp=mysql_fetch_array($result)){
          $cdc[] = $tmp;
        }

        $minglop=32;
        $lockdays=-1;
        for ($ik=1; $ik < 10; $ik++) {
          $minglop=$minglop*2;
          if($user["glops"]>=$minglop) {
          ?>
       <tr>
        <td align="right">coup de coeur <? print($ik); ?><br></td>

        <td><? if ($cdc[$ik-1]["time"]=='' || $cdc[$ik-1]["time"]>$lockdays){ ?><input type="text" name="cdc<? print($ik); ?>" value="<? print($cdc[$ik-1]["cdc"]); ?>"><br></td><td><i>prod id (<?=$cdc[$ik-1]["name"]?>)</i><br></td>
        <? }else{ ?><input type="text" name="cdc<? print($ik); ?>" value="<? print($cdc[$ik-1]["cdc"]); ?>" disabled><br></td><td><i>locked for the next <? print($lockdays-$cdc[$ik-1]["time"]); ?> days</i><br></td></td><input type="hidden" name="cdc<? print($ik); ?>" value="<? print($cdc[$ik-1]["cdc"]); ?>">
        <? } ?>


       </tr>
       <?
          }
       }
       ?>
       <tr>
        <td align="right">ojuice:<br></td>
        <td><input type="text" name="ojuice" value="<? print($user["ojuice"]); ?>"><br></td>
        <td><i>your <a href="http://ojuice.net" target=_blank>ojuice</a> id, if you had one (we miss you OJ ;( *snif*)</i><br></td>
       </tr>
       <tr>
        <td align="right">slengpung:<br></td>
        <td><input type="text" name="slengpung" value="<? print($user["slengpung"]); ?>"><br></td>
        <td><i>your <a href="http://www.slengpung.com" target=_blank>slengpung</a> id, if you have one</i><br></td>
       </tr>
       <tr>
        <td align="right">csdb:<br></td>
        <td><input type="text" name="csdb" value="<? print($user["csdb"]); ?>"><br></td>
        <td><i>your <a href="http://noname.c64.org/csdb/" target=_blank>csdb</a> id, if you have one</i><br></td>
       </tr>
       <tr>
        <td align="right">zxdemo:<br></td>
        <td><input type="text" name="zxdemo" value="<? print($user["zxdemo"]); ?>"><br></td>
        <td><i>your <a href="http://zxdemo.org/" target=_blank>zxdemo</a> id, if you have one</i><br></td>
       </tr>
<?if(!($_SESSION["SESSION"] && $_SESSION["SCENEID"])) {?>
       <tr>
        <td align="right">captcha thing:<br></td>
        <td>
<?
echo recaptcha_get_html(RECAPTCHA_PUB_KEY);
?>
        </td>
        <td>real sceners are proficient in the skill of reading letters</td>
       </tr>
<?}?>
      </table>
     </td>
    </tr>
    <tr>
     <td bgcolor="#6688AA" align="right">
      <input type="image" src="gfx/submit.gif" style="border: 0px"><br>
     </td>
    </tr>
<? } ?>
   </table>
  </td>
 </tr>
</table>
</form>
<br/>
<? require("include/bottom.php"); ?>
