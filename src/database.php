
<?php
$mysqli = new mysqli('localhost', 'wustl_inst', 'wustl_pass', 'StoryDB');

if($mysqli->connect_errno) {
    echo "Connection Failed.";
	exit;
}
?>