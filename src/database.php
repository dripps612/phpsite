
<?php
$mysqli = new mysqli('localhost', 'YOUR_USERNAME', 'YOUR_PASSWORD', 'DATABASE_NAME');

if($mysqli->connect_errno) {
    echo "Connection Failed.";
	exit;
}
?>
