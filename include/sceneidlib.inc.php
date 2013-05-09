<?php

/**
* Project:     SceneID Library: a simple SceneID communication library in PHP
* File:        sceneidlib.inc.php - entrypoint for communicating between client
*              and the SceneID server.
*
* @author      Matti Palosuo <melwyn@scene.org>, Nicolas Leveille <knos@scene.org>, Gergely Szelei <gargajcns@gmail.com>
* @version     0.41c
* @date        9.7.2007
*
* 200701 Nicolas Leveille <knos@scene.org>
*   Updated to run over SSL, optionaly using certificates (to enable
*   registration capabilities, for portal class 1-2)
*/

@include_once("sceneidlib.config.php");

/*
 * Stream classes to chose between fopen and curl.
 *
 * CURL is mandatory for SSL access and certificate authentication.
 */
class Stream
{
  public function isAvailable () {
         return false;
  }

  public function read ($bytes) {
         // override in implementations
         return "";
  }
}

class FopenStream extends Stream
{
  public function __construct ($path) {
         $this->fileHandle = fopen ($path, "r");
         $this->hasMore = !!$this->fileHandle;
  }

  public function isAvailable () {
         return $this->hasMore;
  }

  public function read ($bytes) {
         $res = fread ($this->fileHandle, $bytes);
         $this->hasMore = !!$res;

         return $res;
  }

  var $fileHandle;
}

class CurlStream extends Stream
{
        /**
   * options can be:
   *   CURLOPT_CAINFO  : path for the CA certificate to use
   *     => implies peer verification
   *   CURLOPT_SSLCERT : path for the certificate to use
   *   CURLOPT_SSLKEY  : path for the key to use
   */
  public function __construct ($path, $options = array()) {
         $ch = curl_init ($path);
         curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1);


         if (isset ($options [CURLOPT_CAINFO])) {
           $mustVerifyPeer = true;
         } else {
           $mustVerifyPeer = false;
         }

         curl_setopt ($ch, CURLOPT_CAINFO, $options [CURLOPT_CAINFO]);
         curl_setopt ($ch, CURLOPT_SSL_VERIFYPEER, $mustVerifyPeer);

               if (isset ($options [CURLOPT_SSLCERT])) {
                        curl_setopt ($ch, CURLOPT_SSLCERT, $options [CURLOPT_SSLCERT]);
                        curl_setopt ($ch, CURLOPT_SSLKEY, $options [CURLOPT_SSLKEY]);
               }

         $this->data = curl_exec ($ch);
         $this->offset = 0;
         $this->hasMore = $this->data!==FALSE;
         if (!$this->hasMore) {
           trigger_error (curl_error($ch), E_USER_WARNING);
         }
         curl_close ($ch);
  }

  public function isAvailable () {
         return $this->hasMore;
  }

  public function read ($bytes) {
         $res = substr ($this->data, $this->offset, $bytes);
         $this->hasMore = !!$res;
         if ($this->hasMore) {
                 $this->offset += $bytes;
         } else {
                 $this->offset += strlen ($res);
         }

         return $res;
  }

  var $data;
  var $hasMore;
  var $offset;
}

class SceneID
{
  static $returnvalue;
  static $message;
  static $country;
  static $user;
  static $userID;
  static $usergroups;
  static $cookie;
  static $hidden;
  static $portals;
  static $portalID;
  static $file;
  static $fileID;
  static $xmlData;

  public function __construct()
  {
    // nothing to do for now
  }

  static function getSceneIdAnswer($rawurl) {
    $url = parse_url($rawurl);
    $f = fsockopen($url["host"],$url["port"]?$url["port"]:80);
    if (!$f) {
      return NULL;
    } else {
      $out  = "GET ".$url["path"]."?".$url["query"]." HTTP/1.1\r\n";
      $out .= "Host: ".$url["host"]."\r\n";
      $out .= "Connection: Close\r\n\r\n";

      fwrite($f, $out);
      $data = NULL;
      while (!feof($f)) $data .= fgets($f, 4096);
      fclose($f);
      list($header,$data) = explode("\r\n\r\n",$data,2);

      return $data;
    }
  }

  //private
   static function parseSceneIdData($command, $param=NULL)
  {
    global $sceneIdURL;
    global $sceneIdPortalLogin;
    global $sceneIdPortalPassword;
    global $sceneIdPortalSSLCA;
                global $sceneIdPortalSSLCertificate;
                global $sceneIdPortalSSLPrivateKey;
                global $sceneIdUseSocket;
    $params = "";

    // always reset possible old data
    SceneID::$user = NULL;
    SceneID::$usergroups = NULL;
    SceneID::$portals = NULL;
    SceneID::$cookie = NULL;
                SceneID::$file = NULL;

    if(is_array($param))
    {
      while(list($k,$v)=each($param))
      {
        $params .= "&".$k."=".urlencode(strip_tags($v));
      }
    }

    $file = $sceneIdURL."?portalLogin=".$sceneIdPortalLogin."&portalPassword=".$sceneIdPortalPassword."&command=".$command.$params;

    $xml_parser=xml_parser_create("UTF-8");

    $httpsPrefix = "https://";
    $mustUseCurl = 0 == strncmp ($file, $httpsPrefix, strlen ($httpsPrefix));

    if ($mustUseCurl) {
            $options = array ();
            if (isset ($sceneIdPortalSSLCA)) {
              $options [CURLOPT_CAINFO] = $sceneIdPortalSSLCA;
            }

            if (isset ($sceneIdPortalSSLCertificate)) {
                $options [CURLOPT_SSLCERT] = $sceneIdPortalSSLCertificate;
                $options [CURLOPT_SSLKEY]  = $sceneIdPortalSSLPrivateKey;

            }

            $stream = new CurlStream ($file, $options);
    }
    else if($sceneIdUseSocket)
    {
      xml_parse_into_struct($xml_parser, SceneID::getSceneIdAnswer($file), $vals, $index);
    } else {
        $stream = new FopenStream ($file);
    }

    if(!$sceneIdUseSocket)
    {
      if (!$stream->isAvailable()) return FALSE;
      //die("could not open XML input");

      SceneID::$xmlData = "";
      while ($data=$stream->read(4096))
      {
        SceneID::$xmlData .= $data;
        xml_parse_into_struct($xml_parser, $data, $vals, $index);
      }
      $returnvalue = -1;
      $curdata = "";
      $curlevel = 0;

      if(!strcasecmp($vals[0]["tag"], "sceneid"))
      {
        while(list($k,$v)=each($vals))
        {
          $curlevel = $v["level"];
          if($curdata=="user"&&$curlevel>2)
          {
            if(strtolower($v["tag"])=="email"&&$v["attributes"]["HIDDEN"]) {
              SceneID::$hidden = $v["attributes"]["HIDDEN"];
              SceneID::$user["hidden"] = $v["attributes"]["HIDDEN"];
            }
            else if(strtolower($v["tag"])=="emailhidden") {
              SceneID::$hidden = $v["value"];
              SceneID::$user["hidden"] = $v["value"];
            }
            else if(strtolower($v["tag"])=="country"&&$v["attributes"]["ID"])
            {
              SceneID::$country = $v["attributes"]["ID"];
              SceneID::$user["country"] = $v["attributes"]["ID"];
            }
            if(isset($v["value"]))
              SceneID::$user[strtolower($v["tag"])] = $v["value"];
            else
              SceneID::$user[strtolower($v["tag"])] = "";
          }
          if($curdata=="portal"&&$curlevel>3)
          {
            if(isset($v["value"]))
              SceneID::$portals[SceneID::$portalID][strtolower($v["tag"])] = $v["value"];
            else
              SceneID::$portals[SceneID::$portalID][strtolower($v["tag"])] = "";
          }
          if($curdata=="usergroups"&&$curlevel>2)
          {
            SceneID::$usergroups[$v["attributes"]["ID"]] = $v["value"];
          }
          if($curdata=="cookie"&&$curlevel>2)
          {
            SceneID::$cookie[strtolower($v["tag"])] = $v["value"];
          }
          if($curdata=="file"&&$curlevel>2)
          {
            SceneID::$file[strtolower($v["tag"])] = $v["value"];
          }
          if(!strcasecmp($v["tag"], "session"))
            SceneID::$session = $v["value"];
          if(!strcasecmp($v["tag"], "returnMessage"))
            SceneID::$message = $v["value"];
          if(!strcasecmp($v["tag"], "returnCode"))
            $returnvalue = $v["value"];
          while(list($k2,$v2)=each($v))
          {
            if(!strcasecmp("tag", $k2)&&!strcasecmp("user", $v2)&&!strcasecmp($v["type"], "open"))
            {
              $curdata = "user";
              SceneID::$userID = $v["attributes"]["ID"];
            }
            if(!strcasecmp("tag", $k2)&&!strcasecmp("user", $v2)&&!strcasecmp($v["type"], "close"))
            {
              $curdata = "";
            }
            if(!strcasecmp("tag", $k2)&&!strcasecmp("file", $v2)&&!strcasecmp($v["type"], "open"))
            {
              $curdata = "file";
              SceneID::$fileID = $v["attributes"]["ID"];
            }
            if(!strcasecmp("tag", $k2)&&!strcasecmp("file", $v2)&&!strcasecmp($v["type"], "close"))
            {
              $curdata = "";
            }
            if(!strcasecmp("tag", $k2)&&!strcasecmp("usergroups", $v2)&&!strcasecmp($v["type"], "open"))
            {
              $curdata = "usergroups";
            }
            if(!strcasecmp("tag", $k2)&&!strcasecmp("usergroups", $v2)&&!strcasecmp($v["type"], "close"))
            {
              $curdata = "";
            }
            if(!strcasecmp("tag", $k2)&&!strcasecmp("cookie", $v2)&&!strcasecmp($v["type"], "open"))
            {
              $curdata = "cookie";
            }
            if(!strcasecmp("tag", $k2)&&!strcasecmp("cookie", $v2)&&!strcasecmp($v["type"], "close"))
            {
              $curdata = "";
            }
            if(!strcasecmp("tag", $k2)&&!strcasecmp("portals", $v2)&&!strcasecmp($v["type"], "open"))
            {
              $curdata = "portals";
            }
            if(!strcasecmp("tag", $k2)&&!strcasecmp("portals", $v2)&&!strcasecmp($v["type"], "close"))
            {
              $curdata = "";
            }
            if(!strcasecmp("tag", $k2)&&!strcasecmp("portal", $v2)&&!strcasecmp($v["type"], "open"))
            {
              $curdata = "portal";
              SceneID::$portalID = $v["attributes"]["ID"];
            }
            if(!strcasecmp("tag", $k2)&&!strcasecmp("portal", $v2)&&!strcasecmp($v["type"], "close"))
            {
              $curdata = "";
            }
          }
        }
      }

      SceneID::$userID = SceneID::$user["id"];
      SceneID::$fileID = SceneID::$file["id"];

      $tmpArray = array("returnvalue" => $returnvalue, "message" => SceneID::$message);
      if(isset(SceneID::$userID))
        $tmpArray = array_merge($tmpArray, array("userID" => SceneID::$userID));
      if(isset(SceneID::$user))
        $tmpArray = array_merge($tmpArray, array("user" => SceneID::$user));
      if(isset(SceneID::$portals))
        $tmpArray = array_merge($tmpArray, array("portals" => SceneID::$portals));
      if(isset(SceneID::$file))
        $tmpArray = array_merge($tmpArray, array("file" => SceneID::$file));
      if(isset(SceneID::$cookie))
        $tmpArray = array_merge($tmpArray, array("cookie" => SceneID::$cookie));
      return $tmpArray;
    }
  }

  /* Functions for all the SceneID enabled sites (minor website / portal class 4 or less). */

  public static function loginUser($login, $password, $ip, $permanent=NULL, $externalid=NULL)
  {
    return SceneID::parseSceneIdData("loginUserMD5", array("login" => $login, "password" => md5($password),
                                   "ip" => $ip, "permanent" => $permanent, "externalid" => $externalid));
  }

  public static function logoutUser_UserID($userID)
  {
    return SceneID::logoutUser($userID);
  }

  public static function logoutUser_Login($login)
  {
    return SceneID::logoutUser(NULL, $login);
  }

  public static function logoutUser_Cookie($cookie)
  {
    return SceneID::logoutUser(NULL, NULL, $cookie);
  }

  public static function logoutUser($userID=NULL, $login=NULL, $cookie=NULL)
  {
    return SceneID::parseSceneIdData("logoutUser", array("userID" => $userID, "login" => $login, "cookie" => $cookie));
  }

  public static function getUserInfo_UserID($userID)
  {
    return SceneID::getUserInfo($userID);
  }

  public static function getUserInfo_Login($login)
  {
    return SceneID::getUserInfo(NULL, $login);
  }

  public static function getUserInfo_Cookie($cookie)
  {
    return SceneID::getUserInfo(NULL, NULL, $cookie);
  }

  public static function getUserInfo($userID=NULL, $login=NULL, $cookie=NULL)
  {
    return SceneID::parseSceneIdData("getUserInfo", array("userID" => $userID, "login" => $login, "cookie" => $cookie));
  }

  public static function getPortalList()
  {
    return SceneID::parseSceneIdData("getPortalList");
  }

  /* Functions for websites (portal class 3 or less) */

  public static function setUserInfo($userID, $email=NULL, $password=NULL, $password2=NULL,
                              $nickname=NULL, $firstname=NULL, $lastname=NULL,
                              $url=NULL, $showinfo=NULL, $birthdate=NULL, $country=NULL)
  {
    $params["userID"]     = $userID;
    $params["nickname"]   = $nickname;
    $params["firstname"]  = $firstname;
    $params["lastname"]   = $lastname;
    $params["email"]      = $email;
    $params["url"]        = $url;

    // do not update passwords unless the password field really has data
    if(strlen(trim($password)>0))
    {
      $params["password"]   = md5($password);
      $params["password2"]  = md5($password2);
    }
    $params["showinfo"]   = ($showinfo?1:0);
//    if ($_POST["birthdate_d"]!="dd"&&$_POST["birthdate_m"]!="mm"&&$_POST["birthdate_y"]!="yyyy")
//     $params["birthdate"] = $_POST["birthdate_y"]."-".$_POST["birthdate_m"]."-".$_POST["birthdate_d"];
    $params["birthdate"]  = $birthdate;
    $params["country"]    = $country;

    return SceneID::parseSceneIdData("setUserInfoMD5", $params);
  }

  public static function requestNewUserPassword_UserID($userID)
  {
    return SceneID::requestNewUserPassword($userID);
  }

  public static function requestNewUserPassword_Login($login)
  {
    return SceneID::requestNewUserPassword(NULL, $login);
  }

  public static function getFileInfo($fileID)
  {
    return SceneID::parseSceneIdData("getFileInfo", array("fileID" => $fileID));
  }

  public static function requestNewUserPassword($userID=NULL, $login=NULL)
  {
    return SceneID::parseSceneIdData("requestNewUserPassword", array("userID" => $userID, "login" => $login));
  }

  /* Functions for portals (portal class 2 or less) */

  public static function renewUserPassword($key)
  {
    return SceneID::parseSceneIdData("renewUserPassword", array("key" => $key));
  }

  public static function registerUser($login, $email, $password, $password2, $nickname=NULL, $firstname=NULL, $lastname=NULL,
                               $url=NULL, $showinfo=NULL, $birthdate=NULL, $country=NULL)
  {
    $params["login"]      = $login;
    $params["firstname"]  = $firstname;
    $params["lastname"]   = $lastname;
    $params["email"]      = $email;
    $params["url"]        = $url;
    $params["password"]   = md5($password);
    $params["password2"]  = md5($password2);

    $params["showinfo"]   = ($showinfo?1:0);
//    if ($_POST["birthdate_d"]!="dd"&&$_POST["birthdate_m"]!="mm"&&$_POST["birthdate_y"]!="yyyy")
//     $params["birthdate"] = $_POST["birthdate_y"]."-".$_POST["birthdate_m"]."-".$_POST["birthdate_d"];
    $params["birthdate"]  = $birthdate;
    $params["country"]    = $country;

    return SceneID::parseSceneIdData("registerUserMD5", $params);
  }

  /* Functions for SceneID servers (portal class 1) */

  // TODO:

  public static function synchronizeUserBase()
  {
    // now let's think about this more in the future
  }

}
?>
