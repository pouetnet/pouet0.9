<?

setcookie("SCENEID_COOKIE","", time() - 3600, "/", "scene.org");
unset($_COOKIE["SCENEID_COOKIE"]);

//print_r($_COOKIE);
while(list($k,$v)=each($_COOKIE))
  echo "$k: $v<br>";
?>
