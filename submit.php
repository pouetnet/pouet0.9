<? require("include/top.php"); ?>

<br />
<table bgcolor="#000000" cellspacing="1" cellpadding="0">
<tr><td><table bgcolor="#000000" cellspacing="1" cellpadding="2">
 <tr>
  <th bgcolor="#224488">what do you want to do ?</th>
 </tr>
 <?php if(isset($_SESSION['SESSION'])): ?>
 <tr>
  <td bgcolor="#557799">
   <b>&nbsp;- <a href="submitprod.php">add a prod</a></b><br />
  </td>
 </tr>
 <tr>
  <td bgcolor="#446688">
   <b>&nbsp;- <a href="submitgroup.php">add a group</a></b><br />
  </td>
 </tr>
 <tr>
  <td bgcolor="#557799">
   <b>&nbsp;- <a href="submitparty.php">add a party</a></b><br />
  </td>
 </tr>
 <tr>
  <td bgcolor="#446688">
   <b>&nbsp;- <a href="submitsshot.php">upload a screenshot</a></b><br />
  </td>
 </tr>
 <tr>
  <td bgcolor="#557799">
   <b>&nbsp;- <a href="submitnfo.php">upload an .nfo file</a></b><br />
  </td>
 </tr>
 <tr>
  <td bgcolor="#446688">
   <b>&nbsp;- <a href="submitpartyweb.php">add party homepage url</a></b><br />
  </td>
 </tr>
 <tr>
  <td bgcolor="#557799">
   <b>&nbsp;- <a href="submitpartyresults.php">upload event results file</a></b><br />
  </td>
 </tr>
 <tr>
  <td bgcolor="#446688">
   <b>&nbsp;- <a href="submitpartylinks.php">add event prods download url</a></b><br />
  </td>
 </tr>
 <tr>
  <td bgcolor="#557799">
   <b>&nbsp;- <a href="submitpartyslengpung.php">add event slengpung id</a></b><br />
  </td>
 </tr>
 <tr>
  <td bgcolor="#446688">
   <b>&nbsp;- <a href="submitprodcsdb.php">add prod csdb id</a></b><br />
  </td>
 </tr>
 <tr>
  <td bgcolor="#557799">
   <b>&nbsp;- <a href="submitpartycsdb.php">add event csdb id</a></b><br />
  </td>
 </tr>
 <tr>
  <td bgcolor="#446688">
   <b>&nbsp;- <a href="submitgroupcsdb.php">add group csdb id</a></b><br />
  </td>
 </tr>
 <tr>
  <td bgcolor="#557799">
   <b>&nbsp;- <a href="submitprodzxdemo.php">add prod zxdemo id</a></b><br />
  </td>
 </tr>
 <tr>
  <td bgcolor="#446688">
   <b>&nbsp;- <a href="submitpartyzxdemo.php">add event zxdemo id</a></b><br />
  </td>
 </tr>
 <tr>
  <td bgcolor="#557799">
   <b>&nbsp;- <a href="submitgroupzxdemo.php">add group zxdemo id</a></b><br />
  </td>
 </tr>
 <tr>
  <td bgcolor="#446688">
   <b>&nbsp;- <a href="submitbbs.php">add bbs</a></b><br />
  </td>
 </tr>
 <tr>
  <td bgcolor="#557799">
   <b>&nbsp;- <a href="submitbbsaffils.php">add bbs/group affils</a></b><br />
  </td>
 </tr>
 <tr>
  <td bgcolor="#446688">
   <b>&nbsp;- <a href="submitothernfo.php">add a bbs or group infofile</a></b><br />
  </td>
 </tr>
 <tr>
  <td bgcolor="#557799">
   <b>&nbsp;- <a href="submit-logo.php">add a logo</a></b><br />
  </td>
 </tr>
 <tr>
  <td bgcolor="#446688">
   <b>&nbsp;- <a href="logos.php">vote on the logos</a></b><br />
  </td>
 </tr>
 <? endif; ?>
 <tr>
  <th bgcolor="#224488">free4all stuff</th>
 </tr>
 <tr>
  <td bgcolor="#446688">
   <b>&nbsp;- <a href="http://bitfellas.org/submitnews.php">add a news (via BitFellas)</a></b><br />
  </td>
 </tr>
 <tr>
  <td bgcolor="#557799">
   <b>&nbsp;- <a href="avatar.php">add an avatar</a></b><br />
  </td>
 </tr>
 <? if($SESSION_LEVEL=='administrator' || $SESSION_LEVEL=='moderator' || $SESSION_LEVEL=='gloperator'): ?>
 <tr>
  <th bgcolor="#224488">admin thingz0rz</th>
 </tr>
 <tr>
  <td bgcolor="#557799">
   <b>&nbsp;- <a href="submitprodaffils.php">add prod affiliation</a></b><br />
  </td>
 </tr>
 <tr>
  <td bgcolor="#446688">
   <b>&nbsp;- <a href="submitprodotherparty.php">add prod other party</a></b><br />
  </td>
 </tr>
 <tr>
  <td bgcolor="#557799">
   <b>&nbsp;- <a href="submitdownloadlinks.php">add download link of prod</a></b><br />
  </td>
 </tr>
 <tr>
  <td bgcolor="#446688">
   <b>&nbsp;- <a href="submitlist.php">create new list</a></b><br />
  </td>
 </tr>
 <? endif; ?>
 <?php if(isset($_SESSION['SESSION'])): ?>
<form action="login.php" method="post">
 <tr><th>login to add more stuff</th></tr>
 <tr bgcolor="#446688">
  <td nowrap align="center">
   <input type="text" name="login" value="SceneID" size="15" maxlength="16" onfocus="this.value=''"><br />
   <input type="password" name="password" value="password" size="15" onfocus="javascript:if(this.value=='password') this.value='';"><br />
   <a href="account.php">register here</a><br />
  </td>
 </tr>
 <tr>
  <td bgcolor="#6688AA" align="right">
   <input type="image" src="gfx/submit.gif">
  </td>
 </tr>
</form>
 <? endif; ?>
</table>
</td></tr></table>
<br />
<? require("include/bottom.php"); ?>
