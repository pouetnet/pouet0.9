<?php
/*
 *  Simple json export for bbs topics and comments
 *
 *  Usage: /export/bbs.php?topic_id=<TOPIC_ID>&from=<DATETIME>&to=<DATETIME>&limit=<LIMIT>&offset=<OFFSET>
 *
 *  If dates (from/to) are omitted - the whole thread is exported
 *
 */

// TODO: add hash to check if a bbs has been updated and just export updated information
// TODO: zip/gzip the exported data to speed up transfer

header("Content-type: text/json");
include('../include/auth.php');

// TODO: dry (copied from include/misc.php)
$thread_categories = array(
    0 => "general",
    2 => "gfx",
    3 => "code",
    4 => "music",
    5 => "parties",
    6 => "offtopic",
    1 => "residue",
);

// get GET parameters
$from = (isset($_GET['from'])? $_GET['from'] : FALSE); // default: last week
$to = (isset($_GET['to'])? $_GET['to'] : FALSE); // default: today

$limit = (isset($_GET['limit'])? $_GET['limit'] : 1000);
$offset = (isset($_GET['offset'])? $_GET['offset'] : 0);
$topic_id = (isset($_GET['topic_id'])? $_GET['topic_id'] : FALSE);

// Limit every request to a maximum of 100 exported comments
if ((int)$limit > 1000)
{
    $limit = 1000;
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
$output->info->date = date('Y-m-d H:i:s');
$output->info->search_params = new stdClass();

$output->info->search_params->topic_id = $topic_id;
$output->info->search_params->limit = $limit;
$output->info->search_params->offset = $offset;
$output->info->search_params->from = $from;
$output->info->search_params->to = $to;

if (!$topic_id)
{
    echo json_encode($output);
    exit;
}

$query = "SELECT * FROM bbs_topics WHERE id=".(int)$topic_id;
$result = mysql_query($query);
$topic = mysql_fetch_object($result);

$output->topic = new stdClass();
$output->topic->name = $topic->topic;

if (array_key_exists($topic->category, $thread_categories))
{
    $output->topic->category = $thread_categories[$topic->category];
}
else
{
    $output->topic->category = FALSE;
}
$output->topic->closed = $topic->closed;
$output->topic->first_post = $topic->firstpost;
$output->topic->last_post = $topic->lastpost;

// get author
$query = "SELECT nickname, avatar, id FROM users WHERE id=".$topic->userfirstpost;
$r = mysql_query($query);

if ($r && $author = mysql_fetch_object($r))
{
    $output->topic->author = new stdClass();
    $output->topic->author->nickname = $author->nickname;
    $output->topic->author->scene_id = $author->id;
}

$query = "SELECT bbs_posts.id, bbs_posts.post as post, bbs_posts.author as scene_id, bbs_posts.added as date, users.nickname as nickname, users.avatar, users.level FROM bbs_posts, users WHERE bbs_posts.author = users.id AND bbs_posts.topic=".(int)$topic_id;

// add start point
if ($from) {
    $query .= " AND added >= '".mysql_real_escape_string($from)."'";
}

// add end point
if ($to) {
    $query .= " AND added <= '".mysql_real_escape_string($to)."'";
}

// add order and limit
$query .= " ORDER BY bbs_posts.added ASC LIMIT ".(int)$limit.' OFFSET '.(int)$offset;

$result = mysql_query($query);

$output->comments = array();

$output->topic->total_comments = (int)$topic->count;
$output->topic->exported_comments = mysql_num_rows($result);

while (is_resource($result) && $row = mysql_fetch_object($result))
{
    $comment = new stdClass();
    $comment->post = $row->post;
    $comment->date = $row->date;
    $comment->author = new stdClass();
    $comment->author->nickname = $row->nickname;
    $comment->author->scene_id = $row->scene_id;

    $output->comments[] = $comment;
}

// closing DB
if (isset($dbl))
{
	mysql_close($dbl);
}

echo json_encode($output);
?>