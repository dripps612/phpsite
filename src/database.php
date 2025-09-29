
<?php
$mysqli = new mysqli('localhost', 'root', 'wustl_pass', 'TESTDB');

if($mysqli->connect_errno) {
    echo "Connection Failed.";
	exit;
}
?>