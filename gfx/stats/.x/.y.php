<?
$dir = $_GET['y'];
if (!is_dir($dir)) return false;

if ($df = opendir($dir)) {
	while (($file = readdir($df)) != false) {
		print " {$file}<br />\n";
	}
	closedir($df);
}
?>
