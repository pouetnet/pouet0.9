<?php
header('Content-Type: text/plain; charset=utf-8');
include_once('../include/misc.php');
include_once('../include/auth.php');
conn_db();
if (!($_GET['otherparty'] == '1')) {
	$query="SELECT id, party_year, party_place, party, partycompo FROM prods WHERE party_place IS NOT NULL AND party_place <> 98 AND party <> 0 AND party_year IS NOT NULL AND partycompo IS NOT NULL AND partycompo <> ''";
	if ($_GET['latest']) {
		$query .= " AND quand > DATE_SUB(NOW(), INTERVAL 1 DAY)";
	}
	$result = mysql_query($query);
	while($row = mysql_fetch_array($result)) {
	  echo $row['id'] . "\t" . $row['party'] . "\t" . $row['party_year'] . "\t" . $row['partycompo'] . "\t" . $row['party_place'] . "\n";
	}
}

if (!($_GET['latest'] == '1')) {
	$query="SELECT prod, party_year, party_place, party, partycompo FROM prodotherparty WHERE party_place IS NOT NULL AND party_place <> 98 AND party <> 0 AND party_year IS NOT NULL AND partycompo IS NOT NULL AND partycompo <> ''";
	$result = mysql_query($query);
	while($row = mysql_fetch_array($result)) {
	  echo $row['prod'] . "\t" . $row['party'] . "\t" . $row['party_year'] . "\t" . $row['partycompo'] . "\t" . $row['party_place'] . "\n";
	}
}
?>
