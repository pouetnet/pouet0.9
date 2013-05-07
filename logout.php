<?
require('include/misc.php');

session_start();

$sceneid = new SceneID();

$params = array ("userID" => $_SESSION["SESSION_ID"]);
$command = "logoutUser";

// NOTE ! WE'RE NOT ACTUALLY CALLING SCENEID NOW. USER DOESN'T REALLY LOGOUT!

session_unset();
session_destroy();

if($_COOKIE["SCENEID_COOKIE"]) 
{
//  setcookie("SCENEID_COOKIE","", time() - 3600, "/", "scene.org");
  setcookie("SCENEID_COOKIE","", time() - 3600, "/", "pouet.net");
  unset($_COOKIE["SCENEID_COOKIE"]);
}
if($_COOKIE["SCENEID_SESSION"]) 
{
//  setcookie("SCENEID_SESSION","", time() - 3600, "/", "scene.org");
  setcookie("SCENEID_SESSION","", time() - 3600, "/", "pouet.net");
  unset($_COOKIE["SCENEID_SESSION"]);
}

$sceneid->parseSceneIdData($command, $params);

header("Location: ".$HTTP_REFERER);
?>
