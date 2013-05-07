<?php

/**
* Project:     SceneID Library: a simple SceneID communication library in PHP
* File:        sceneidlib.config.php.dist - example configuration file for the
*              SceneID Library.
*
* @author      Matti Palosuo <melwyn@scene.org>, Nicolas Leveille <knos@scene.org>, Gergely Szelei <gargajcns@gmail.com>
* @version     0.41b
*
* 200707: Matti Palosuo <melwyn@scene.org>
*         Merged changes from Gargaj's library using socket connections
*         for data exchange.
* 200701: Nicolas Leveille <knos@scene.org>
*  	  SceneID now works over SSL. For portal class 1-2, a
*         certificate / private key pair is required.
*
*         We will be start requiring full SSL support pretty soon, so update!
*
*         SSL support requires the CURL module.
*/

// test.scene.org does not work through SSL. SSL specific tests must thus
// be done on the main server, which MUST be called www.scene.org
//$sceneIdURL = "http://test.scene.org/sceneid.php";
//$sceneIdURL = "https://www.scene.org/sceneid.php";
//$sceneIdPortalLogin    = <portal-login>;
//$sceneIdPortalPassword = md5(<portal-password>);

$sceneIdURL = $sceneidUrl;
//$sceneidUrl = "http://test.scene.org:8080/sceneid.php";
$sceneIdPortalLogin = $sceneidLogin;
$sceneIdPortalPassword = $sceneidPassword;

$sceneIdUseSocket = FALSE; // if TRUE, tries to open connection to SceneID through sockets (experimental)
                           // if FALSE, uses normal file open - recommended if not restricted by PHP security
                           // settings

// required for the strict mode, otherwise won't be used if not set
// $sceneIdPortalSSLCA          = "private/sceneorg-ca-cert.pem";

// optional, only required for registration / modifications (portal class 1-2)
// it is very important for those files, especially the key, to be
// unavailable to others. The directory should thus be protected from http
// accesses.
//
// $sceneIdPortalSSLCertificate = <portal-pem-certificate>
// $sceneIdPortalSSLPrivateKey  = <portal-pem-private-key>

?>
