<?
@session_start();
$_SESSION["nodonate"]=$_GET["reset"]?false:true;
header("Location: http://$_SERVER[HTTP_HOST]/");
?>
