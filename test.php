hoo.
<?
/*
session_start();

echo "SESSION:<br>";
while(list($k,$v)=each($_SESSION))
  echo "$k: $v<br>";
*/

echo "COOKIE:<br>";
while(list($k,$v)=each($_COOKIE))
  echo "$k: $v<br>";
?>