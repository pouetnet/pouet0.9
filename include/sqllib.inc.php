<?

include_once("auth.php");


class SQLLib {

  function connect() {
    mysql_connect($db['host'],$db['user'],$db['password']);
    mysql_select_db($db['database']);
  }

  function disconnect() {
    mysql_close();
  }

  function query($cmd) {
    SQLLib::connect();

    $r = mysql_query($cmd);
    if(!$r) die("<pre>\nMYSQL ERROR:\nQuery: ".$cmd."\nError: ".mysql_error());

    SQLLib::disconnect();
    return $r;
  }

  function selectRows($cmd) {
    $r = SQLLib::query($cmd);
    $a = Array();
    while($o = mysql_fetch_object($r)) $a[]=$o;
    return $a;
  }

  function selectRow($cmd) {
    $r = SQLLib::query($cmd);
    $a = mysql_fetch_object($r);
    return $a;
  }

  function insertRow($table,$o) {
    if (is_object($o)) $a = get_object_vars($o);
    else if (is_array($o)) $a = $o;
    $keys = Array();
    $values = Array();
    foreach($a as $k=>$v) {
      $keys[]=$k;
      if ($v!==NULL) $values[]='"'.$v.'"';
      else           $values[]='""';
    }

    $cmd = sprintf("insert %s (%s) values (%s)",
      $table,implode(",",$keys),implode(",",$values));

    $r = SQLLib::query($cmd);

    //return mysql_insert_id($r);
  }

  function updateRow($table,$o,$where) {
    if (is_object($o)) $a = get_object_vars($o);
    else if (is_array($o)) $a = $o;
    $set = Array();
    foreach($a as $k=>$v) {
      if ($v!==NULL)
        $set[] = sprintf("%s=\"%s\"",$k,$v);
    }
    $cmd = sprintf("update %s set %s where %s",
      $table,implode(",",$set),$where);
    SQLLib::query($cmd);
  }

}

?>
