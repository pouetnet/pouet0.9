<?
ob_start();

$sceneOrgDown = true;
if (time() - filemtime(SCENE_ORG_CHECK_FILE) > 60 * 15)
{
	file_put_contents(SCENE_ORG_CHECK_FILE, file_get_contents('http://www.scene.org/'));
}
$sceneOrgDown = !file_get_contents(SCENE_ORG_CHECK_FILE);

if ($sceneOrgDown) {
	?>
	<table cellspacing="1" cellpadding="2" class="box">
		<tr><th colspan="3">your account</th></tr>
		<tr bgcolor="#446688">
			<td align="center" colspan="3" style='padding:10px;'>
				sorry guys, scene.org (and consequently, sceneID) is <a href="http://www.isup.me/scene.org">down</a> for some reason :(
				i added some automagical code to check it periodically whether it comes back up,
				but until then you have to make do with read-only-pouet.
				<!--besides it's 3 days before <a href="http://www.revision-party.net/">revi</a><a href="http://www.gathering.org/tg11/en/">thering</a>, so go back to coding.-->
				in the meantime, you could perhaps try making a demo about it.
				<br/>
				--hugs,
				<br/>
				garg
			</td>
		</tr>
	</table>
	<br/>
<?
} else {
	if($_SESSION["SESSION"]&&$_SESSION["SCENEID"]): ?>
		<table cellspacing="1" cellpadding="2" class="box">
			<tr><th colspan="3">your account</th></tr>
			<tr bgcolor="#446688">
				<td nowrap align="center" colspan="3">
					you are logged in as<br />
					<a href="user.php?who=<?=$_SESSION["SCENEID_ID"]?>"><img src="avatars/<?=$_SESSION["SESSION_AVATAR"]?>" width="16" height="16" border="0" title="<?=$_SESSION["SESSION_NICKNAME"]?>" alt="<?=$_SESSION["SESSION_NICKNAME"]?>"></a>
					<a href="user.php?who=<?=$_SESSION["SCENEID_ID"]?>"><b><?=$_SESSION["SESSION_NICKNAME"]?></b></a><br />
				</td>
			</tr>
			<tr><td class="bottom"><a href="account.php">account</a></td>
				<td class="bottom"><a href="customize.php">customize</a></td>
				<td class="bottom"><a href="logout.php">logout</a></td>
			</tr>
		</table>
		<br />
	<? else: ?>
		<form action="login.php" method="post">
			<table cellspacing="1" cellpadding="2" class="box">
				<tr><th>your account</th></tr>
				<tr bgcolor="#446688">
					<td nowrap align="center">
						<input type="text" name="login" value="SceneID" size="15" maxlength="16" onfocus="this.value=''"><br />
						<input type="password" name="password" value="password" size="15" onfocus="javascript:if(this.value=='password') this.value='';"><br />
						<input type="checkbox" name="permanent">login for 1 year<br />
						<a href="account.php">register here</a><br />
						<!-- <span style="color:#f88">login will be down while scene.org is updating</span> -->
					</td>
				</tr>
				<tr>
					<td bgcolor="#6688AA" align="right">
						<input type="image" src="gfx/submit.gif">
					</td>
				</tr>
			</table>
		</form>
		<br />
	<?
	endif;
}

$output = ob_get_contents();
ob_clean();

return $output;
?>
