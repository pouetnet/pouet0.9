<?
@session_start();
$_SESSION["nodonate"]=$_GET["reset"]?false:true;
header("Location: /");
?>
