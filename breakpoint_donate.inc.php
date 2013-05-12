<?
if (!$_SESSION["nodonate"]) {
?>
<!-- sigh... i feel guilty. -->
<div id="bpdonate">
<table cellspacing="1" cellpadding="2" class="box">
 <tr><th>
 <div style='float:right'><a href="removedonate.php">hide</a></div>
 donate to help breakpoint 2009!
 </th></tr>
 <tr bgcolor="#446688">
  <td align="center">
    if you can't afford to buy a supporter ticket, you can add any small amount to the donation pool via scene.org:
    <table>
    <tr>
    <td><img src="http://www.pouet.net/gfx/titles/coupdecoeur.gif"/></td>
    <td>
      <form action="https://www.paypal.com/cgi-bin/webscr" method="post" style='margin:10px'>
        <input type="hidden" name="cmd" value="_xclick">
        <input type="hidden" name="business" value="melwyn@scene.org">
        <input type="hidden" name="item_name" value="Breakpoint">
        <input type="hidden" name="no_note" value="1">
        <input type="hidden" name="currency_code" value="EUR">
        <input type="hidden" name="tax" value="0">
        <input type="image" src="https://www.paypal.com/images/x-click-but04.gif" border="0" name="submit" alt="Donate">
      </form>
    </td>
    <td><img src="http://www.pouet.net/gfx/titles/coupdecoeur.gif"/></td>
    </tr>
    </table>

<?
$fn = TMP_FOLDER."/bpbudget.inc";
if (!file_exists($fn) || time() - filemtime($fn) < 3600) {
	$f = @fopen("http://breakpoint.untergrund.net/broke_budget.php", 'r');
	$content = '';
	while (!feof($f)) {
		$content .= fgets($f, 4096);
	}
  fclose($f);

  $content = str_replace("<table","<table id='bpbudget'",$content);

  $f = fopen($fn,"w");
  fwrite($f,$content);
  fclose($f);
}
echo file_get_contents($fn);
?>

  </td>
 </tr>
 <tr>
  <td bgcolor="#6688AA" align="right">
   <b><a href="http://www.scene.org/donate_breakpoint.php">read more about it here</a>...</b>
  </td>
 </tr>
</table>
<br/>
</div>
<?
}
?>
