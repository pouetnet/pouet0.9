<?php
/*
 *  Simple json export for prods including all relevant data
 *
 *  Usage: /export/prods.php?prod_id=<PROD_ID>
 *  or
 *  Usage: /export/prods.php?from=<TIMESTAMP>&to=<TIMESTAMP>&limit=<LIMIT>&offset=<OFFSET>
 */

// TODO: add hash to check if a prod has been updated and just export updated information
// TODO: export cdc information for the comments
// TODO: zip/gzip the exported data to speed up transfer

header("Content-type: text/json");
include('../include/auth.php');

$export_comments = FALSE;

// get GET parameters
$from = (int)(isset($_GET['from'])? $_GET['from'] : time() - (7 * 86400)); // default: last week
$to = (int)(isset($_GET['to'])? $_GET['to'] : time()); // default: today

$limit = (int)(isset($_GET['limit'])? $_GET['limit'] : 10);
$offset = (int)(isset($_GET['offset'])? $_GET['offset'] : 0);
$prod_id = (int)(isset($_GET['prod_id'])? $_GET['prod_id'] : FALSE);

// Limit every request to a maximum of 100 exported prods
if ((int)$limit > 100)
{
    $limit = 100;
}

// opening DB
$dbl = mysql_connect($db['host'], $db['user'], $db['password']);

if (!$dbl)
{
	die('SQL error... sorry ! ^^; I\'m on it !');
}

mysql_select_db($db['database'], $dbl);

// prepare data
$output = new stdClass();
$output->info = new stdClass();
$output->info->title = 'pouet.net prod export';
$output->info->date = date('c');
$output->info->search_params = new stdClass();

if (!$prod_id)
{
    $output->info->search_params->limit = $limit;
    $output->info->search_params->offset = $offset;
    $output->info->search_params->from = $from;
    $output->info->search_params->to = $to;
}
else
{
    $output->info->search_params->prod_id = $prod_id;
}
$output->prods = array();

if ($prod_id)
{
    $query = "SELECT * FROM prods WHERE id=".(int)$prod_id;
}
else
{
    $query = "SELECT * FROM prods WHERE quand >= FROM_UNIXTIME(".$from.") AND quand <= FROM_UNIXTIME(".mysql_real_escape_string($to).") ORDER BY prods.quand DESC LIMIT ".(int)$limit.' OFFSET '.(int)$offset;
}

$result = mysql_query($query);

while (is_resource($result) && $row = mysql_fetch_object($result))
{
    $prod = new stdClass();
    $prod->title = utf8_encode($row->name);
    $prod->type = $row->type;
    $prod->id = $row->id;
    $prod->date = date('c', strtotime($row->quand));

    $prod->source_url = $row->source;

    $prod->statistics = new stdClass();
    $prod->statistics->positive_votes = $row->voteup;
    $prod->statistics->negative_votes = $row->votedown;
    $prod->statistics->neutral_votes = $row->votepig;
    $prod->statistics->average_votes = $row->voteavg;
    $prod->statistics->downloads = $row->downloads;


    $prod->screenshot = new stdClass();

    $screenshot = FALSE;
    if (file_exists('../screenshots/'.$row->id.'.jpg')) {
        $screenshot = $row->id.'.jpg';
    }
    elseif(file_exists('../screenshots/'.$row->id.'.gif'))
    {
        $screenshot = $row->id.'.gif';
    }
    elseif(file_exists('../screenshots/'.$row->id.'.png'))
    {
        $screenshot = $row->id.'.png';
    }

    if ($screenshot)
    {
        $screenshot_size = filesize($_SERVER["DOCUMENT_ROOT"].'/screenshots/'.$screenshot);
        $size = getimagesize($_SERVER["DOCUMENT_ROOT"].'/screenshots/'.$screenshot);
        $screenshot_type = $size['mime'];

        $prod->screenshot->url = 'http://pouet.net/screenshots/'.$screenshot;
        $prod->screenshot->filesize = $screenshot_size;
        $prod->screenshot->type = $size['mime'];
    }

    // get uploader
    $query = "SELECT nickname, avatar, id FROM users WHERE id=".$row->added;
    $r = mysql_query($query);

    if ($r && $uploader = mysql_fetch_object($r))
    {
        $prod->uploader = new stdClass();
        $prod->uploader->scene_id = $uploader->id;
        $prod->uploader->nickname = $uploader->nickname;
    }

    // get party name and year
    if ($row->party)
    {
        $query = "SELECT name FROM parties WHERE id=".$row->party;
        $r = mysql_query($query);

        if ($r && $party = mysql_fetch_object($r))
        {
            $prod->released_at = new stdClass();
            $prod->released_at->name = $party->name;
            $prod->released_at->year = $row->party_year;
            $prod->released_at->rank = $row->party_place;
            $prod->released_at->compo = $row->partycompo;
        }
    }

    // get invitation party and year
    if ($row->invitation)
    {
        $query = "SELECT name FROM parties WHERE id=".$row->invitation;
        $r = mysql_query($query);

        if ($r && $invitation_party = mysql_fetch_object($r))
        {
            $prod->invitation_for = new stdClass();
            $prod->invitation_for->name = $invitation_party->name;
            $prod->invitation_for->year = $row->invitationyear;
        }
    }

    // get board name
    if ($row->boardID)
    {
        $query = "SELECT name FROM bbses WHERE id=".$row->boardID;
        $r = mysql_query($query);

        if ($r && $board = mysql_fetch_object($r))
        {
            $prod->board = $board->name;
        }
    }

    // get groups
    $prod->groups = array();

    $g = array('group1', 'group2', 'group3');

    foreach ($g as $attribute)
    {
        if ($row->{$attribute})
        {
            $query = "SELECT name, web, acronym FROM groups WHERE id=".$row->{$attribute};
            $r = mysql_query($query);

            if ($r && $group = mysql_fetch_object($r))
            {
                $tmp_group = new stdClass();
                $tmp_group->name = $group->name;
                $tmp_group->website = $group->web;
                $tmp_group->acronym = $group->acronym;

                $prod->groups[] = $tmp_group;
            }
        }
    }

    // get parties where this prod was also released
    $prod->also_released_at = array();

    $query = "SELECT prodotherparty.party, prodotherparty.party_place as rank, prodotherparty.party_year as year, prodotherparty.partycompo as compo, parties.name as name FROM prodotherparty LEFT JOIN parties ON parties.id=prodotherparty.party WHERE prod=".$row->id;

    $r = mysql_query($query);
    if ($r)
    {
        while ($tmp = mysql_fetch_object($r))
        {
            $released_at = new stdClass();
            $released_at->name = $tmp->name;
            $released_at->rank = $tmp->rank;
            $released_at->year = $tmp->year;
            $released_at->compo = $tmp->compo;

            $prod->also_released_at[] = $released_at;
        }
    }

    // get platforms
    $prod->platforms = array();

    $query = "select platforms.name as platform, platforms.icon from prods_platforms, platforms where prods_platforms.prod='".$row->id."' and platforms.id=prods_platforms.platform";
    $r = mysql_query($query);

    if ($r)
    {
        while ($tmp = mysql_fetch_object($r))
        {
            $platform = new stdClass();
            $platform->name = $tmp->platform;

            $prod->platforms[] = $platform;
        }
    }

    // get affiliate links
    $prod->affiliate_links = array();

    $link = new stdClass();
    $link->name = 'pouet.net';
    $link->url = 'http://pouet.net/prod.php?which='.$row->id;

    $prod->affiliate_links[] = $link;

    if ($row->csdb > 0)
    {
        $link = new stdClass();
        $link->name = 'csdb';
        $link->url = 'http://noname.c64.org/csdb/release/?id='.$row->csdb;
        $prod->affiliate_links[] = $link;
    }

    if ($row->zxdemo > 0)
    {
        $link = new stdClass();
        $link->name = 'zxdemo';
        $link->url = 'http://zxdemo.org/item.php?id='.$row->zxdemo;
        $prod->affiliate_links[] = $link;
    }

    if ($row->sceneorg > 0)
    {
        $link = new stdClass();
        $link->name = 'scene.org';
        $link->url = 'http://scene.org/file.php?id='.$row->sceneorg;
        $prod->affiliate_links[] = $link;
    }

    // get coup-de-coeur count
    $r = mysql_query("SELECT count(0) from users_cdcs where cdc=".$row->id);
    $prod->coup_de_coeur = mysql_result($r, 0);

    $r = mysql_query("SELECT count(0) from cdc where which=".$row->id);
    $prod->coup_de_coeur += mysql_result($r, 0);

    // sceneorgrecommended / award nominations and winners
    $prod->recommendations = array();
    $r = mysql_query("SELECT * from sceneorgrecommended where prodid=".$row->id." ORDER BY type");
    while ($tmp = mysql_fetch_object($r))
    {
        $recommendation = new stdClass();
        $recommendation->type = $tmp->type;
        $recommendation->category = $tmp->category;

        $prod->recommendations[] = $recommendation;
    }

    // affiliated prods
    $prod->derived_from = array();
    $prod->derivatives = array();
    $r = mysql_query(
        " SELECT affiliatedprods.type as type,".
            " affiliatedprods.derivative as derivative,".
            " affiliatedprods.original as original,".
            " prods.name as name,".
            " prods.id as prod_id".
            " from affiliatedprods".
            " join prods on prods.id=affiliatedprods.original".
            " where affiliatedprods.derivative=".$row->id." ORDER BY affiliatedprods.type");

    while (is_resource($r) && $tmp = mysql_fetch_object($r))
    {
        $affiliated_prod = new stdClass();
        $affiliated_prod->name = $tmp->name;
        $affiliated_prod->id = $tmp->prod_id;

        $prod->derived_from[] = $affiliated_prod;
    }

    $r = mysql_query(
        " SELECT affiliatedprods.type as type,".
            " affiliatedprods.derivative as derivative,".
            " affiliatedprods.original as original,".
            " prods.name as name,".
            " prods.id as prod_id".
            " from affiliatedprods".
            " join prods on prods.id=affiliatedprods.derivative".
            " where affiliatedprods.original=".$row->id." ORDER BY affiliatedprods.type");

    while (is_resource($r) && $tmp = mysql_fetch_object($r))
    {
        $affiliated_prod = new stdClass();
        $affiliated_prod->name = $tmp->name;
        $affiliated_prod->id = $tmp->prod_id;

        $prod->derivatives[] = $affiliated_prod;
    }

    // get downloads
    $prod->downloads = array();

    $download = new stdClass();
    $download->type = 'default';
    $download->url = $row->download;
    $prod->downloads[] = $download;

    $query = "SELECT downloadlinks.id,downloadlinks.link as url, downloadlinks.type as type FROM downloadlinks WHERE downloadlinks.prod=".$row->id." ORDER BY downloadlinks.type";
    $r = mysql_query($query);

    while(is_resource($r) && $tmp = mysql_fetch_object($r))
    {
        $download = new stdClass();
        $download->type = $tmp->type;
        $download->url = $tmp->url;

        $prod->downloads[] = $download;
    }

    // get the comments and the associated data
	if ($export_comments) {
		$prod->comments = array();

		$query  = "SELECT comments.id as comment_id, comments.comment as comment, comments.rating as rating, comments.who, comments.quand as date, users.nickname as nickname, users.id as scene_id, users.avatar, users.level FROM comments, users WHERE comments.which='".$row->id."' AND users.id=comments.who ORDER BY comments.quand ASC";
		$r = mysql_query($query);

		if ($r)
		{
			while ($tmp = mysql_fetch_object($r))
			{
				$comment = new stdClass();
				$comment->comment = $tmp->comment;
				$comment->rating = $tmp->rating;
				$comment->date = $tmp->date;

				$comment->author = new stdClass();
				$comment->author->scene_id = $tmp->scene_id;
				$comment->author->nickname = $tmp->nickname;

				$prod->comments[] = $comment;
			}
		}

		/*
		$query  = "SELECT * from users_cdcs where users_cdcs.cdc='".$prod["id"]."'";
		$result=mysql_query($query);
		while($tmp=mysql_fetch_array($result)) {
			$cdcs[]=$tmp;
		}

		for($j=0; $j<count($cdcs); $j++)
		{
			for($i=0; $i<count($comments); $i++)
			{
				if ($cdcs[$j]["user"]==$comments[$i]["who"]){
					$comments[$i]["cdc"]=$cdcs[$j]["cdc"];
				}
			}
		}

		$query  = "SELECT * from users_cdcs left join comments on users_cdcs.user=comments.who AND users_cdcs.cdc = comments.which where users_cdcs.cdc='".$prod["id"]."' and comments.id IS NULL";
		$result=mysql_query($query);
		while($tmp=mysql_fetch_object($result)) {
			$othercdc[]=$tmp->user;
		}
		*/
	}

    $output->prods[] = $prod;
}

// closing DB
if (isset($dbl))
{
	mysql_close($dbl);
}

echo json_encode($output);
?>
