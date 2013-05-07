<?
require("include/top.php");

$query="SELECT id,question,answer,category FROM faq ORDER BY category ASC";
$result=mysql_query($query);
while($tmp=mysql_fetch_array($result)) {
  $faq[]=$tmp;
}
?>
<br>
<table bgcolor="#000000" cellspacing="1" cellpadding="0" width="75%">
 <tr>
  <td>
   <table bgcolor="#000000" cellspacing="1" cellpadding="2">
    <tr>
     <th bgcolor="#224488">the always incomplete pouët.net faq</th>
    </tr>
    <tr>
     <td bgcolor="#446688">
      <? for($i=0;$i<count($faq);$i++): ?>
       <? if($faq[$i]["category"]!=$faq[$i-1]["category"]): ?>
        <a href="#<? print($faq[$i]["category"]); ?>"><b><? print($faq[$i]["category"]); ?></b></a><br>
       <? endif; ?>
       &nbsp;--> <a href="#<? print($faq[$i]["id"]); ?>"><? print($faq[$i]["question"]); ?></a><br>
       <? if($faq[$i]["category"]!=$faq[$i+1]["category"]): ?>
        <br>
       <? endif; ?>
      <? endfor; ?>
     </td>
    </tr>
    <? for($i=0;$i<count($faq);$i++): ?>
    <? if($faq[$i]["category"]!=$faq[$i-1]["category"]): ?>
     <tr>
      <th bgcolor="#224488"><a name="<? print($faq[$i]["category"]); ?>"><? print($faq[$i]["category"]); ?></a></th>
     </tr>
    <? endif; ?>
    <tr>
     <td bgcolor="#446688">
      <a name="<? print($faq[$i]["id"]); ?>"><b>:: <? print($faq[$i]["category"]); ?> :: <? print($faq[$i]["question"]); ?></b></a>
     </td>
    </tr>
    <tr>
     <td bgcolor="#557799">
      <blockquote>
       <? print($faq[$i]["answer"]); ?>
      </blockquote>
     </td>
    </tr>
    <? endfor; ?>
   </table>
  </td>
 </tr>
</table>
<br>
<? require("include/bottom.php"); ?>
