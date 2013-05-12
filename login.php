<?
require("include/misc.php");
require("include/auth.php");

if (isIPBanned()) die("no.");

$xml = new SceneID();

$db["link"] = mysql_connect($db["host"], $db["user"], $db["password"]) or die("Unable to connect");
mysql_select_db($db["database"]);

ob_start();
$returnvalue = $xml->parseSceneIdData("loginUserMD5", array(
               "login" => $_REQUEST["login"],
               "password" => md5($_REQUEST["password"]),
               "ip" => $_SERVER["REMOTE_ADDR"],
               "permanent" => ($_POST["permanent"]=="on"?"1":"0")));
$z = ob_get_clean();

$refer = $HTTP_REFERER ? $HTTP_REFERER : "/";

switch($returnvalue["returnvalue"])
{
	case 30: { // login successful

		if(authenticate($returnvalue["userID"]) != 1) // user found from pouet.net db
		{
		// no user found from pouet.net database, create a new one.
      $userparams = array("userID" => $returnvalue["userID"]);

      $returnvalue = $xml->parseSceneIdData("getUserInfo", $userparams);
      //if($returnvalue["returnvalue"]==10)
      $u = $returnvalue["user"];

      $query= "INSERT users SET ";
      $query.="id=".(int)$returnvalue["userID"].", ";
      $query.="nickname='".mysql_real_escape_string($u["nickname"] ? $u["nickname"] : $_REQUEST["login"])."', ";

      $entry = glob("./avatars/*.gif");
      $r = $entry[array_rand($entry)];
      $a = str_replace("./avatars/","",$r);

      $query.="avatar='".$a."', "; // todo
      $query.="lastip='".$_SERVER["REMOTE_ADDR"]."', ";
      $query.="lasthost='".gethostbyaddr($_SERVER["REMOTE_ADDR"])."', ";
      $query.="quand=NOW()";
      mysql_query($query);

      authenticate($returnvalue["userID"]);
		}


	  setcookie($returnvalue["cookie"]["name"],
	            $returnvalue["cookie"]["value"],
	            $returnvalue["cookie"]["expires"],
              $returnvalue["cookie"]["path"], ".pouet.net");
//		                  $cookie["path"], $cookie["domain"]);
//		                  $returnvalue["cookie["path"], "localhost");

		session_start();
		$_SESSION["SESSION"]=session_id();
		$_SESSION["SESSION_NICKNAME"]=$returnvalue["user"]["nickname"];
//			$_SESSION["SESSION_NICKNAME"]=$userinfo["nickname"];
		$_SESSION["SESSION_AVATAR"]=$userinfo["avatar"];
		$_SESSION["SESSION_LEVEL"]=$userinfo["level"];

// update new nickname into the local db:
		$query = "UPDATE users SET nickname='".$returnvalue["user"]["nickname"]."' WHERE id=".$returnvalue["user"]["id"];
		mysql_query($query);

		// for keeping sceneid alive without permanent login
		if(!$_SESSION["SCENEID"])
		{
			$_SESSION["SCENEID"]=session_id();
		  	$_SESSION["SCENEID_ID"]=$returnvalue["userID"];
		  	$_SESSION["SCENEID_LOGIN"]=$returnvalue["user"]["login"];
      		$_SESSION["SCENEID_IP"]=$_SERVER["REMOTE_ADDR"];
		}

		header("Location: ".$refer);
	} break;

	case NULL:
	case FALSE:
	case -1: {
		header("Location: error.php?e=".rawurlencode("Couldn't connect SceneID. :(")."&back=".$refer);
	} break;

	default: {
		header("Location: error.php?e=".rawurlencode($returnvalue["message"])."&back=".$refer);
	} break;
}
exit;

mysql_close($db["link"]);

function authenticate($id)
{
	global $userinfo;
	if (!$id) return -1;
	$query = sprintf("SELECT nickname,avatar,level FROM users WHERE id=%d",$id);
	$result = mysql_query($query);
	if (mysql_num_rows($result) == 1) {
		$userinfo["nickname"]=mysql_result($result,0,0);
		$userinfo["avatar"]=mysql_result($result,0,1);
		$userinfo["level"]=mysql_result($result,0,2);
		return 1;
	}
	else
		return -1;
}

?>
