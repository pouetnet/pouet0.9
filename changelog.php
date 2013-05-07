<?
require("include/top.php");

$query="SELECT version,comment,quand FROM changelog ORDER BY quand DESC, id DESC";
$result=mysql_query($query);
while($tmp=mysql_fetch_array($result)) {
  $changes[]=$tmp;
}

?>
<br />
<table bgcolor="#000000" cellspacing="1" cellpadding="0" width="75%">
 <tr>
  <td>
   <table bgcolor="#000000" cellspacing="1" cellpadding="2">
    <tr>
     <td bgcolor="#224488" nowrap>
      <b>pouët.net todo list</b><br />
     </td>
    </tr>
    <tr>
     <td bgcolor="#557799">
      &nbsp;- add zone's nationality flags on user.php according to sceneID's new "country" user info field<br />
      &nbsp;- allow support to change users nationality on sceneID from pouet.net account.php<br />
      &nbsp;- fix add.php and update.php to do the vote math ignoring fakeusers votes<br />
      &nbsp;- paginate parties.php + allow year sort to be also reversed<br />
      &nbsp;- add a possibility to subscribe to certain prods/groups/bbs and receive an update by mail<br />
      &nbsp;- facilitate personalized css<br />
      &nbsp;- remodel BBS into sections<br />
      &nbsp;- populate stats.php with stats<br />
      &nbsp;- add cookie/ajax power to groups.php?which= and prodlist.php to show/hide some columns<br />
      &nbsp;- do a searchprodlist.php with a textbox input, a multichoice list of all types and platforms, and a way to select sortby up to 3 levels<br />
      &nbsp;- add more info on rss prodlistings (groups, type, platform)<br />
      &nbsp;- add automatic .rss browser linkage lines as suggested by p01 on bbstopic 2104<br />
      &nbsp;- improve lists.php<br />
      &nbsp;- add more detail to listitems types (visited parties vs organized parties for demology lists)<br />
      &nbsp;- add more listitems types for better demology (visited/organized parties, ex-groups, active/innactive users)<br />
      &nbsp;- add related lists info to user.php, group.php, prod.php and submit.php<br />
      &nbsp;- add automation to prodid links similar to webtv (for zxdemo, csdb, c64cracktros, plus4emucamp, scene.org fileid)<br />
      &nbsp;- update othernfo functionality some more<br />
      &nbsp;- add database fields to connect groups to parties/compos they organized (display them on groups.php?which=..)<br />
      &nbsp;- integrate demozoo style user and group credits<br />
      &nbsp;- allow gfx and music entries?!<br />
     </td>
    </tr>
    <tr>
     <td bgcolor="#224488" nowrap>
      <b>pouët.net changelog</b><br />
     </td>
    </tr>
    <? $c=0; ?>
    <? for($i=0;$i<count($changes);$i++): ?>
    <? if($changes[$i]["version"]!=$changes[$i-1]["version"]): ?>
    <tr>
     <? ($c%2)?print("<td bgcolor=\"#557799\">"):print("<td bgcolor=\"#446688\">"); ?>
      <b><? print($changes[$i]["version"]); ?></b>:<br />
    <? endif; ?>
      &nbsp; <? print(htmlentities("[".substr($changes[$i]["quand"],0,10)."] - ".$changes[$i]["comment"])); ?><br />
    <? if($changes[$i]["version"]!=$changes[$i+1]["version"]): ?>
     </td>
    </tr>
    <? $c++; ?>
    <? endif; ?>
    <? endfor; ?>
   </table>
  </td>
 </tr>
</table>
<br />
<? require("include/bottom.php"); ?>
