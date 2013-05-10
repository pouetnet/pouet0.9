<?
require("include/top.php");

print("<br />");

if ($SESSION_LEVEL=='administrator' || $SESSION_LEVEL=='moderator' || $SESSION_LEVEL=='gloperator' ||
    $SESSION_LEVEL=='user' || $SESSION_LEVEL=='pr0nstahr'):

if ($action=='alter')
{
	$r = mysql_query("select * from usersettings where id=".$_SESSION["SCENEID_ID"]."");
	if ($r)
  	mysql_query("insert into usersettings (id) values (".$_SESSION["SCENEID_ID"].")");

	if (strlen($topglops)!=0)
	{
	if ( (int)$topglops > 50 ) $query = "update usersettings set indextopglops=50 where id='".$_SESSION["SCENEID_ID"]."'";
	 else if ( (int)$topglops <= 0 ) $query = "update usersettings set indextopglops=0 where id='".$_SESSION["SCENEID_ID"]."'";
	  else $query = "update usersettings set indextopglops=".(int)$topglops." where id='".$_SESSION["SCENEID_ID"]."'";
	mysql_query($query);
	}

	if (strlen($topprods)!=0)
	{
	if ( (int)$topprods > 50 ) $query = "update usersettings set indextopprods=50 where id='".$_SESSION["SCENEID_ID"]."'";
	 else if ( (int)$topprods <= 0 ) $query = "update usersettings set indextopprods=0 where id='".$_SESSION["SCENEID_ID"]."'";
	  else $query = "update usersettings set indextopprods=".(int)$topprods." where id='".$_SESSION["SCENEID_ID"]."'";
	mysql_query($query);
	}

	if (strlen($topkeops)!=0)
	{
	if ( (int)$topkeops > 50 ) $query = "update usersettings set indextopkeops=50 where id='".$_SESSION["SCENEID_ID"]."'";
	 else if ( (int)$topkeops <= 0 ) $query = "update usersettings set indextopkeops=0 where id='".$_SESSION["SCENEID_ID"]."'";
	  else $query = "update usersettings set indextopkeops=".(int)$topkeops." where id='".$_SESSION["SCENEID_ID"]."'";
	mysql_query($query);
	}

	if (strlen($indexcdc)!=0)
	{
	if ( (int)$indexcdc >= 1 ) $query = "update usersettings set indexcdc=1 where id='".$_SESSION["SCENEID_ID"]."'";
	 else if ( (int)$indexcdc <= 0 ) $query = "update usersettings set indexcdc=0 where id='".$_SESSION["SCENEID_ID"]."'";
	mysql_query($query);
	}

	if (strlen($indexsearch)!=0)
	{
	if ( (int)$indexsearch >= 1 ) $query = "update usersettings set indexsearch=1 where id='".$_SESSION["SCENEID_ID"]."'";
	 else if ( (int)$indexsearch <= 0 ) $query = "update usersettings set indexsearch=0 where id='".$_SESSION["SCENEID_ID"]."'";
	mysql_query($query);
	}

	if (strlen($logos)!=0)
	{
	if ( (int)$logos >= 1 ) $query = "update usersettings set logos=1 where id='".$_SESSION["SCENEID_ID"]."'";
	 else if ( (int)$logos <= 0 ) $query = "update usersettings set logos=0 where id='".$_SESSION["SCENEID_ID"]."'";
	mysql_query($query);
	}

	if (strlen($topbar)!=0)
	{
	if ( (int)$topbar >= 1 ) $query = "update usersettings set topbar=1 where id='".$_SESSION["SCENEID_ID"]."'";
	 else if ( (int)$topbar <= 0 ) $query = "update usersettings set topbar=0 where id='".$_SESSION["SCENEID_ID"]."'";
	mysql_query($query);
	}

	if (strlen($bottombar)!=0)
	{
	if ( (int)$bottombar >= 1 ) $query = "update usersettings set bottombar=1 where id='".$_SESSION["SCENEID_ID"]."'";
	 else if ( (int)$bottombar <= 0 ) $query = "update usersettings set bottombar=0 where id='".$_SESSION["SCENEID_ID"]."'";
	mysql_query($query);
	}

	if (strlen($indexstats)!=0)
	{
	if ( (int)$indexstats >= 1 ) $query = "update usersettings set indexstats=1 where id='".$_SESSION["SCENEID_ID"]."'";
	 else if ( (int)$indexstats <= 0 ) $query = "update usersettings set indexstats=0 where id='".$_SESSION["SCENEID_ID"]."'";
	mysql_query($query);
	}

	if (strlen($indexlinks)!=0)
	{
	if ( (int)$indexlinks >= 1 ) $query = "update usersettings set indexlinks=1 where id='".$_SESSION["SCENEID_ID"]."'";
	 else if ( (int)$indexlinks <= 0 ) $query = "update usersettings set indexlinks=0 where id='".$_SESSION["SCENEID_ID"]."'";
	mysql_query($query);
	}

	if (strlen($oneliner)!=0)
	{
	if ( (int)$oneliner > 50 ) $query = "update usersettings set indexoneliner=50 where id='".$_SESSION["SCENEID_ID"]."'";
	 else if ( (int)$oneliner <= 0 ) $query = "update usersettings set indexoneliner=0 where id='".$_SESSION["SCENEID_ID"]."'";
	  else $query = "update usersettings set indexoneliner=".(int)$oneliner." where id='".$_SESSION["SCENEID_ID"]."'";
	mysql_query($query);
	}

	if (strlen($latestadded)!=0)
	{
	if ( (int)$latestadded > 50 ) $query = "update usersettings set indexlatestadded=50 where id='".$_SESSION["SCENEID_ID"]."'";
	 else if ( (int)$latestadded <= 0 ) $query = "update usersettings set indexlatestadded=0 where id='".$_SESSION["SCENEID_ID"]."'";
	  else $query = "update usersettings set indexlatestadded=".(int)$latestadded." where id='".$_SESSION["SCENEID_ID"]."'";
	mysql_query($query);
	}

	if (strlen($latestreleased)!=0)
	{
	if ( (int)$latestreleased > 50 ) $query = "update usersettings set indexlatestreleased=50 where id='".$_SESSION["SCENEID_ID"]."'";
	 else if ( (int)$latestreleased <= 0 ) $query = "update usersettings set indexlatestreleased=0 where id='".$_SESSION["SCENEID_ID"]."'";
	  else $query = "update usersettings set indexlatestreleased=".(int)$latestreleased." where id='".$_SESSION["SCENEID_ID"]."'";
	mysql_query($query);
	}

	if (strlen($ojnews)!=0)
	{
	if ( (int)$ojnews > 50 ) $query = "update usersettings set indexojnews=50 where id='".$_SESSION["SCENEID_ID"]."'";
	 else if ( (int)$ojnews <= 0 ) $query = "update usersettings set indexojnews=0 where id='".$_SESSION["SCENEID_ID"]."'";
	  else $query = "update usersettings set indexojnews=".(int)$ojnews." where id='".$_SESSION["SCENEID_ID"]."'";
	mysql_query($query);
	}

	if (strlen($latestcomments)!=0)
	{
	if ( (int)$latestcomments > 50 ) $query = "update usersettings set indexlatestcomments=50 where id='".$_SESSION["SCENEID_ID"]."'";
	 else if ( (int)$latestcomments <= 0 ) $query = "update usersettings set indexlatestcomments=0 where id='".$_SESSION["SCENEID_ID"]."'";
	  else $query = "update usersettings set indexlatestcomments=".(int)$latestcomments." where id='".$_SESSION["SCENEID_ID"]."'";
	mysql_query($query);
	}

	if (strlen($latestparties)!=0)
	{
	if ( (int)$latestparties > 50 ) $query = "update usersettings set indexlatestparties=50 where id='".$_SESSION["SCENEID_ID"]."'";
	 else if ( (int)$latestparties <= 0 ) $query = "update usersettings set indexlatestparties=0 where id='".$_SESSION["SCENEID_ID"]."'";
	  else $query = "update usersettings set indexlatestparties=".(int)$latestparties." where id='".$_SESSION["SCENEID_ID"]."'";
	mysql_query($query);
	}

	if (strlen($bbstopics)!=0)
	{
	if ( (int)$bbstopics > 50 ) $query = "update usersettings set indexbbstopics=50 where id='".$_SESSION["SCENEID_ID"]."'";
	 else if ( (int)$bbstopics <= 0 ) $query = "update usersettings set indexbbstopics=0 where id='".$_SESSION["SCENEID_ID"]."'";
	  else $query = "update usersettings set indexbbstopics=".(int)$bbstopics." where id='".$_SESSION["SCENEID_ID"]."'";
	mysql_query($query);
	}

	if (strlen($topicposts)!=0)
	{
	if ( (int)$topicposts > 100 ) $query = "update usersettings set topicposts=100 where id='".$_SESSION["SCENEID_ID"]."'";
	 else if ( (int)$topicposts <= 10 ) $query = "update usersettings set topicposts=10 where id='".$_SESSION["SCENEID_ID"]."'";
	  else $query = "update usersettings set topicposts=".(int)$topicposts." where id='".$_SESSION["SCENEID_ID"]."'";
	mysql_query($query);
	}

	if (strlen($bbsbbstopics)!=0)
	{
	if ( (int)$bbsbbstopics > 100 ) $query = "update usersettings set bbsbbstopics=100 where id='".$_SESSION["SCENEID_ID"]."'";
	 else if ( (int)$bbsbbstopics <= 10 ) $query = "update usersettings set bbsbbstopics=10 where id='".$_SESSION["SCENEID_ID"]."'";
	  else $query = "update usersettings set bbsbbstopics=".(int)$bbsbbstopics." where id='".$_SESSION["SCENEID_ID"]."'";
	mysql_query($query);
	}

	if (strlen($prodlistprods)!=0)
	{
	if ( (int)$prodlistprods > 100 ) $query = "update usersettings set prodlistprods=100 where id='".$_SESSION["SCENEID_ID"]."'";
	 else if ( (int)$prodlistprods <= 10 ) $query = "update usersettings set prodlistprods=10 where id='".$_SESSION["SCENEID_ID"]."'";
	  else $query = "update usersettings set prodlistprods=".(int)$prodlistprods." where id='".$_SESSION["SCENEID_ID"]."'";
	mysql_query($query);
	}

	if (strlen($userlistusers)!=0)
	{
	if ( (int)$userlistusers > 100 ) $query = "update usersettings set userlistusers=100 where id='".$_SESSION["SCENEID_ID"]."'";
	 else if ( (int)$userlistusers <= 10 ) $query = "update usersettings set userlistusers=10 where id='".$_SESSION["SCENEID_ID"]."'";
	  else $query = "update usersettings set userlistusers=".(int)$userlistusers." where id='".$_SESSION["SCENEID_ID"]."'";
	mysql_query($query);
	}

	if (strlen($searchprods)!=0)
	{
	if ( (int)$searchprods > 100 ) $query = "update usersettings set searchprods=100 where id='".$_SESSION["SCENEID_ID"]."'";
	 else if ( (int)$searchprods <= 10 ) $query = "update usersettings set searchprods=10 where id='".$_SESSION["SCENEID_ID"]."'";
	  else $query = "update usersettings set searchprods=".(int)$searchprods." where id='".$_SESSION["SCENEID_ID"]."'";
	mysql_query($query);
	}

	if (strlen($userlogos)!=0)
	{
	if ( (int)$userlogos > 20 ) $query = "update usersettings set userlogos=20 where id='".$_SESSION["SCENEID_ID"]."'";
	 else if ( (int)$userlogos <= 0 ) $query = "update usersettings set userlogos=0 where id='".$_SESSION["SCENEID_ID"]."'";
	  else $query = "update usersettings set userlogos=".(int)$userlogos." where id='".$_SESSION["SCENEID_ID"]."'";
	mysql_query($query);
	}

	if (strlen($userprods)!=0)
	{
	if ( (int)$userprods > 100 ) $query = "update usersettings set userprods=100 where id='".$_SESSION["SCENEID_ID"]."'";
	 else if ( (int)$userprods < -1 ) $query = "update usersettings set userprods=0 where id='".$_SESSION["SCENEID_ID"]."'";
	  else $query = "update usersettings set userprods=".(int)$userprods." where id='".$_SESSION["SCENEID_ID"]."'";
	mysql_query($query);
	}

	if (strlen($usergroups)!=0)
	{
	if ( (int)$usergroups > 100 ) $query = "update usersettings set usergroups=100 where id='".$_SESSION["SCENEID_ID"]."'";
	 else if ( (int)$usergroups < -1 ) $query = "update usersettings set usergroups=0 where id='".$_SESSION["SCENEID_ID"]."'";
	  else $query = "update usersettings set usergroups=".(int)$usergroups." where id='".$_SESSION["SCENEID_ID"]."'";
	mysql_query($query);
	}

	if (strlen($userparties)!=0)
	{
	if ( (int)$userparties > 100 ) $query = "update usersettings set userparties=100 where id='".$_SESSION["SCENEID_ID"]."'";
	 else if ( (int)$userparties < -1 ) $query = "update usersettings set userparties=0 where id='".$_SESSION["SCENEID_ID"]."'";
	  else $query = "update usersettings set userparties=".(int)$userparties." where id='".$_SESSION["SCENEID_ID"]."'";
	mysql_query($query);
	}

	if (strlen($userscreenshots)!=0)
	{
	if ( (int)$userscreenshots > 100 ) $query = "update usersettings set userscreenshots=100 where id='".$_SESSION["SCENEID_ID"]."'";
	 else if ( (int)$userscreenshots < -1 ) $query = "update usersettings set userscreenshots=0 where id='".$_SESSION["SCENEID_ID"]."'";
	  else $query = "update usersettings set userscreenshots=".(int)$userscreenshots." where id='".$_SESSION["SCENEID_ID"]."'";
	mysql_query($query);
	}

	if (strlen($usernfos)!=0)
	{
	if ( (int)$usernfos > 100 ) $query = "update usersettings set usernfos=100 where id='".$_SESSION["SCENEID_ID"]."'";
	 else if ( (int)$usernfos < -1 ) $query = "update usersettings set usernfos=0 where id='".$_SESSION["SCENEID_ID"]."'";
	  else $query = "update usersettings set usernfos=".(int)$usernfos." where id='".$_SESSION["SCENEID_ID"]."'";
	mysql_query($query);
	}

	if (strlen($usercomments)!=0)
	{
	if ( (int)$usercomments > 100 ) $query = "update usersettings set usercomments=100 where id='".$_SESSION["SCENEID_ID"]."'";
	 else if ( (int)$usercomments < -1 ) $query = "update usersettings set usercomments=0 where id='".$_SESSION["SCENEID_ID"]."'";
	  else $query = "update usersettings set usercomments=".(int)$usercomments." where id='".$_SESSION["SCENEID_ID"]."'";
	mysql_query($query);
	}

	if (strlen($userrulez)!=0)
	{
	if ( (int)$userrulez > 100 ) $query = "update usersettings set userrulez=100 where id='".$_SESSION["SCENEID_ID"]."'";
	 else if ( (int)$userrulez < -1 ) $query = "update usersettings set userrulez=0 where id='".$_SESSION["SCENEID_ID"]."'";
	  else $query = "update usersettings set userrulez=".(int)$userrulez." where id='".$_SESSION["SCENEID_ID"]."'";
	mysql_query($query);
	}

	if (strlen($usersucks)!=0)
	{
	if ( (int)$usersucks > 100 ) $query = "update usersettings set usersucks=100 where id='".$_SESSION["SCENEID_ID"]."'";
	 else if ( (int)$usersucks < -1 ) $query = "update usersettings set usersucks=0 where id='".$_SESSION["SCENEID_ID"]."'";
	  else $query = "update usersettings set usersucks=".(int)$usersucks." where id='".$_SESSION["SCENEID_ID"]."'";
	mysql_query($query);
	}

	if (strlen($commentshours)!=0)
	{
	if ( (int)$commentshours > 72 ) $query = "update usersettings set commentshours=72 where id='".$_SESSION["SCENEID_ID"]."'";
	 else if ( (int)$commentshours < 2 ) $query = "update usersettings set commentshours=2 where id='".$_SESSION["SCENEID_ID"]."'";
	  else $query = "update usersettings set commentshours=".(int)$commentshours." where id='".$_SESSION["SCENEID_ID"]."'";
	mysql_query($query);
	}

	if (strlen($commentsnamecut)!=0)
	{
	if ( (int)$commentsnamecut > 100 ) $query = "update usersettings set commentsnamecut=100 where id='".$_SESSION["SCENEID_ID"]."'";
	 else if ( (int)$commentsnamecut < 20 ) $query = "update usersettings set commentsnamecut=20 where id='".$_SESSION["SCENEID_ID"]."'";
	  else $query = "update usersettings set commentsnamecut=".(int)$commentsnamecut." where id='".$_SESSION["SCENEID_ID"]."'";
	mysql_query($query);
	}

	if (strlen($topichidefakeuser)!=0)
	{
	if ( (int)$topichidefakeuser >= 1 ) $query = "update usersettings set topichidefakeuser=1 where id='".$_SESSION["SCENEID_ID"]."'";
	 else if ( (int)$topichidefakeuser <= 0 ) $query = "update usersettings set topichidefakeuser=0 where id='".$_SESSION["SCENEID_ID"]."'";
	mysql_query($query);
	}

	if (strlen($prodhidefakeuser)!=0)
	{
	if ( (int)$prodhidefakeuser >= 1 ) $query = "update usersettings set prodhidefakeuser=1 where id='".$_SESSION["SCENEID_ID"]."'";
	 else if ( (int)$prodhidefakeuser <= 0 ) $query = "update usersettings set prodhidefakeuser=0 where id='".$_SESSION["SCENEID_ID"]."'";
	mysql_query($query);
	}

	if (strlen($indextype)!=0)
	{
	if ( (int)$indextype >= 1 ) $query = "update usersettings set indextype=1 where id='".$_SESSION["SCENEID_ID"]."'";
	 else if ( (int)$indextype <= 0 ) $query = "update usersettings set indextype=0 where id='".$_SESSION["SCENEID_ID"]."'";
	mysql_query($query);
	}

	if (strlen($indexplatform)!=0)
	{
	if ( (int)$indexplatform >= 1 ) $query = "update usersettings set indexplatform=1 where id='".$_SESSION["SCENEID_ID"]."'";
	 else if ( (int)$indexplatform <= 0 ) $query = "update usersettings set indexplatform=0 where id='".$_SESSION["SCENEID_ID"]."'";
	mysql_query($query);
	}

	if (strlen($indexwhoaddedprods)!=0)
	{
	if ( (int)$indexwhoaddedprods >= 1 ) $query = "update usersettings set indexwhoaddedprods=1 where id='".$_SESSION["SCENEID_ID"]."'";
	 else if ( (int)$indexwhoaddedprods <= 0 ) $query = "update usersettings set indexwhoaddedprods=0 where id='".$_SESSION["SCENEID_ID"]."'";
	mysql_query($query);
	}

	if (strlen($indexwhocommentedprods)!=0)
	{
	if ( (int)$indexwhocommentedprods >= 1 ) $query = "update usersettings set indexwhocommentedprods=1 where id='".$_SESSION["SCENEID_ID"]."'";
	 else if ( (int)$indexwhocommentedprods <= 0 ) $query = "update usersettings set indexwhocommentedprods=0 where id='".$_SESSION["SCENEID_ID"]."'";
	mysql_query($query);
	}

	if (strlen($displayimages)!=0)
	{
	 if ( (int)$displayimages >= 1 ) $query = "update usersettings set displayimages=1 where id='".$_SESSION["SCENEID_ID"]."'";
	 else if ( (int)$displayimages <= 0 ) $query = "update usersettings set displayimages=0 where id='".$_SESSION["SCENEID_ID"]."'";
	 mysql_query($query);
	}

	if (strlen($indexbbsnoresidue)!=0)
	{
	 if ( (int)$indexbbsnoresidue >= 1 ) $query = "update usersettings set indexbbsnoresidue=1 where id='".$_SESSION["SCENEID_ID"]."'";
	 else if ( (int)$indexbbsnoresidue <= 0 ) $query = "update usersettings set indexbbsnoresidue=0 where id='".$_SESSION["SCENEID_ID"]."'";
	 mysql_query($query);
	}


	$query = "SELECT * FROM usersettings WHERE id='".$_SESSION["SCENEID_ID"]."'";
	$result = mysql_query($query);
	$user=mysql_fetch_assoc($result);
}
else
{
	$query = "SELECT * FROM usersettings WHERE id='".$_SESSION["SCENEID_ID"]."'";
	$result = mysql_query($query);
	$user=mysql_fetch_assoc($result);
}

?>

<form action="customize.php" method="post">
<input type="hidden" name="action" value="alter">
<table bgcolor="#000000" cellspacing="1" cellpadding="0"><tr><td>
<table bgcolor="#000000" cellspacing="1" cellpadding="2">
 <tr>
 <th colspan="2" bgcolor="#224488">custom?olobstormaziabletic 7004+ super</th>
 <tr>
  <td bgcolor="#557799" >logos</td>
  <td bgcolor="#557799">
	<select name="logos">
		<option value="1"<? if ($user["logos"] == 1) echo " selected"; ?>>Displayed</option>
		<option value="0"<? if ($user["logos"] == 0) echo " selected"; ?>>Hidden</option>
	</select>
  </td>
 </tr>
 <tr>
  <td bgcolor="#557799" >top bar</td>
  <td bgcolor="#557799">
	<select name="topbar">
		<option value="1"<? if ($user["topbar"] == 1) echo " selected"; ?>>Displayed</option>
		<option value="0"<? if ($user["topbar"] == 0) echo " selected"; ?>>Hidden</option>
	</select>
  </td>
 </tr>
 <tr>
  <td bgcolor="#557799" >bottom bar</td>
  <td bgcolor="#557799">
	<select name="bottombar">
		<option value="1"<? if ($user["bottombar"] == 1) echo " selected"; ?>>Displayed</option>
		<option value="0"<? if ($user["bottombar"] == 0) echo " selected"; ?>>Hidden</option>
	</select>
  </td>
 </tr>
 <tr>
  <td bgcolor="#557799" >/ top glops</td> <td bgcolor="#557799"><input type="text" name="topglops" value="<? print($user["indextopglops"]); ?>"></td>
 </tr>
 <tr>
  <td bgcolor="#557799" >/ top prods (recent)</td> <td bgcolor="#557799"><input type="text" name="topprods" value="<? print($user["indextopprods"]); ?>"></td>
 </tr>
 <tr>
  <td bgcolor="#557799" >/ top prods (all-time)</td> <td bgcolor="#557799"><input type="text" name="topkeops" value="<? print($user["indextopkeops"]); ?>"></td>
 </tr>
 <tr>
  <td bgcolor="#557799" >/ oneliner</td> <td bgcolor="#557799"><input type="text" name="oneliner" value="<? print($user["indexoneliner"]); ?>"></td>
 </tr>
 <tr>
  <td bgcolor="#557799" >/ cdc</td>
  <td bgcolor="#557799">
	<select name="indexcdc">
		<option value="1"<? if ($user["indexcdc"] == 1) echo " selected"; ?>>Displayed</option>
		<option value="0"<? if ($user["indexcdc"] == 0) echo " selected"; ?>>Hidden</option>
	</select>
  </td>
 </tr>
 <tr>
  <td bgcolor="#557799" >/ search</td>
  <td bgcolor="#557799">
	<select name="indexsearch">
		<option value="1"<? if ($user["indexsearch"] == 1) echo " selected"; ?>>Displayed</option>
		<option value="0"<? if ($user["indexsearch"] == 0) echo " selected"; ?>>Hidden</option>
	</select>
  </td>
 </tr>
 <tr>
  <td bgcolor="#557799" >/ stats</td>
  <td bgcolor="#557799">
	<select name="indexstats">
		<option value="1"<? if ($user["indexstats"] == 1) echo " selected"; ?>>Displayed</option>
		<option value="0"<? if ($user["indexstats"] == 0) echo " selected"; ?>>Hidden</option>
	</select>
  </td>
 </tr>
 <tr>
  <td bgcolor="#557799" >/ links</td>
  <td bgcolor="#557799">
	<select name="indexlinks">
		<option value="1"<? if ($user["indexlinks"] == 1) echo " selected"; ?>>Displayed</option>
		<option value="0"<? if ($user["indexlinks"] == 0) echo " selected"; ?>>Hidden</option>
	</select>
  </td>
 </tr>
 <tr>
  <td bgcolor="#557799" >/ latest added</td> <td bgcolor="#557799"><input type="text" name="latestadded" value="<? print($user["indexlatestadded"]); ?>"></td>
 </tr>
 <tr>
  <td bgcolor="#557799" >/ latest released</td> <td bgcolor="#557799"><input type="text" name="latestreleased" value="<? print($user["indexlatestreleased"]); ?>"></td>
 </tr>
 <tr>
  <td bgcolor="#557799" >/ bitfellas news</td> <td bgcolor="#557799"><input type="text" name="ojnews" value="<? print($user["indexojnews"]); ?>"></td>
 </tr>
 <tr>
  <td bgcolor="#557799" >/ latest comments</td> <td bgcolor="#557799"><input type="text" name="latestcomments" value="<? print($user["indexlatestcomments"]); ?>"></td>
 </tr>
 <tr>
  <td bgcolor="#557799" >/ latest parties</td> <td bgcolor="#557799"><input type="text" name="latestparties" value="<? print($user["indexlatestparties"]); ?>"></td>
 </tr>
 <tr>
  <td bgcolor="#557799" >/ bbs topics</td> <td bgcolor="#557799"><input type="text" name="bbstopics" value="<? print($user["indexbbstopics"]); ?>"></td>
 </tr>
 <tr>
  <td bgcolor="#557799" >/ platform icons</td>
  <td bgcolor="#557799">
	<select name="indexplatform">
		<option value="1"<? if ($user["indexplatform"] == 1) echo " selected"; ?>>Displayed</option>
		<option value="0"<? if ($user["indexplatform"] == 0) echo " selected"; ?>>Hidden</option>
	</select>
  </td>
 </tr>
 <tr>
  <td bgcolor="#557799" >/ type icons</td>
  <td bgcolor="#557799">
	<select name="indextype">
		<option value="1"<? if ($user["indextype"] == 1) echo " selected"; ?>>Displayed</option>
		<option value="0"<? if ($user["indextype"] == 0) echo " selected"; ?>>Hidden</option>
	</select>
  </td>
 </tr>
 <tr>
  <td bgcolor="#557799" >/ who added prods</td>
  <td bgcolor="#557799">
	<select name="indexwhoaddedprods">
		<option value="1"<? if ($user["indexwhoaddedprods"] == 1) echo " selected"; ?>>Displayed</option>
		<option value="0"<? if ($user["indexwhoaddedprods"] == 0) echo " selected"; ?>>Hidden</option>
	</select>
  </td>
 </tr>
 <tr>
  <td bgcolor="#557799" >/ who commented prods</td>
  <td bgcolor="#557799">
	<select name="indexwhocommentedprods">
		<option value="1"<? if ($user["indexwhocommentedprods"] == 1) echo " selected"; ?>>Displayed</option>
		<option value="0"<? if ($user["indexwhocommentedprods"] == 0) echo " selected"; ?>>Hidden</option>
	</select>
  </td>
 </tr>
 <tr>
  <td bgcolor="#557799" >bbs.php bbs topics</td> <td bgcolor="#557799"><input type="text" name="bbsbbstopics" value="<? print($user["bbsbbstopics"]); ?>"></td>
 </tr>
 <tr>
  <td bgcolor="#557799" >prodlist.php prods</td> <td bgcolor="#557799"><input type="text" name="prodlistprods" value="<? print($user["prodlistprods"]); ?>"></td>
 </tr>
 <tr>
  <td bgcolor="#557799" >userlist.php users</td> <td bgcolor="#557799"><input type="text" name="userlistusers" value="<? print($user["userlistusers"]); ?>"></td>
 </tr>
 <tr>
  <td bgcolor="#557799" >search.php prods</td> <td bgcolor="#557799"><input type="text" name="searchprods" value="<? print($user["searchprods"]); ?>"></td>
 </tr>
 <tr>
  <td bgcolor="#557799" >user.php logos</td> <td bgcolor="#557799"><input type="text" name="userlogos" value="<? print($user["userlogos"]); ?>"></td>
 </tr>
 <tr>
  <td bgcolor="#557799" >user.php prods</td> <td bgcolor="#557799"><input type="text" name="userprods" value="<? print($user["userprods"]); ?>"></td>
 </tr>
 <tr>
  <td bgcolor="#557799" >user.php groups</td> <td bgcolor="#557799"><input type="text" name="usergroups" value="<? print($user["usergroups"]); ?>"></td>
 </tr>
 <tr>
  <td bgcolor="#557799" >user.php parties</td> <td bgcolor="#557799"><input type="text" name="userparties" value="<? print($user["userparties"]); ?>"></td>
 </tr>
 <tr>
  <td bgcolor="#557799" >user.php screenshots</td> <td bgcolor="#557799"><input type="text" name="userscreenshots" value="<? print($user["userscreenshots"]); ?>"></td>
 </tr>
 <tr>
  <td bgcolor="#557799" >user.php nfos</td> <td bgcolor="#557799"><input type="text" name="usernfos" value="<? print($user["usernfos"]); ?>"></td>
 </tr>
 <tr>
  <td bgcolor="#557799" >user.php comments</td> <td bgcolor="#557799"><input type="text" name="usercomments" value="<? print($user["usercomments"]); ?>"></td>
 </tr>
 <tr>
  <td bgcolor="#557799" >user.php rulez</td> <td bgcolor="#557799"><input type="text" name="userrulez" value="<? print($user["userrulez"]); ?>"></td>
 </tr>
 <tr>
  <td bgcolor="#557799" >user.php sucks</td> <td bgcolor="#557799"><input type="text" name="usersucks" value="<? print($user["usersucks"]); ?>"></td>
 </tr>
 <tr>
  <td bgcolor="#557799" >comments.php hours</td> <td bgcolor="#557799"><input type="text" name="commentshours" value="<? print($user["commentshours"]); ?>"></td>
 </tr>
 <tr>
  <td bgcolor="#557799" >comments.php name cut</td> <td bgcolor="#557799"><input type="text" name="commentsnamecut" value="<? print($user["commentsnamecut"]); ?>"></td>
 </tr>
 <tr>
  <td bgcolor="#557799" >topic.php posts</td> <td bgcolor="#557799"><input type="text" name="topicposts" value="<? print($user["topicposts"]); ?>"></td>
 </tr>
 <tr>
  <td bgcolor="#557799" >topic.php hide fakeuser</td>
  <td bgcolor="#557799">
	<select name="topichidefakeuser">
		<option value="1"<? if ($user["topichidefakeuser"] == 1) echo " selected"; ?>>Hidden</option>
		<option value="0"<? if ($user["topichidefakeuser"] == 0) echo " selected"; ?>>Displayed</option>
	</select>
  </td>
 </tr>
 <tr>
  <td bgcolor="#557799" >prod.php hide fakeuser</td>
  <td bgcolor="#557799">
	<select name="prodhidefakeuser">
		<option value="1"<? if ($user["prodhidefakeuser"] == 1) echo " selected"; ?>>Hidden</option>
		<option value="0"<? if ($user["prodhidefakeuser"] == 0) echo " selected"; ?>>Displayed</option>
	</select>
  </td>
 </tr>
 <tr>
  <td bgcolor="#557799" >[img][/img] tags should be displayed as...</td>
  <td bgcolor="#557799">
	<select name="displayimages">
		<option value="1"<? if ($user["displayimages"] == 1) echo " selected"; ?>>images</option>
		<option value="0"<? if ($user["displayimages"] == 0) echo " selected"; ?>>links</option>
	</select>
  </td>
 </tr>
 <tr>
  <td bgcolor="#557799" >residue threads on the front page are...</td>
  <td bgcolor="#557799">
	<select name="indexbbsnoresidue">
		<option value="1"<? if ($user["indexbbsnoresidue"] == 1) echo " selected"; ?>>hidden</option>
		<option value="0"<? if ($user["indexbbsnoresidue"] == 0) echo " selected"; ?>>shown</option>
	</select>
  </td>
 </tr>
 <tr>
  <td bgcolor="#6688AA" align="right" colspan="2">
   <input type="image" src="gfx/submit.gif">
  </td>
 </tr>
</table>
</td></tr></table>
</form>

<? else: ?>

<table width="20%"><tr><td>
<form action="login.php" method="post">
<table cellspacing="1" cellpadding="2" class="box">
 <tr bgcolor="#446688">
  <td align="center">
   <input type="text" name="login" value="SceneID" size="15" maxlength="16" onfocus="this.value=''">
   <input type="password" name="password" value="password" size="15" onfocus="javascript:if(this.value=='password') this.value='';"><br />
  </td>
 </tr>
 <tr>
  <td bgcolor="#6688AA" align="right">
   <input type="image" src="gfx/submit.gif">
  </td>
 </tr>
</table>
</form>
</td></tr></table>

<? endif; ?>
<br />
<? require("include/bottom.php"); ?>
