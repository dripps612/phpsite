<?php
require '../../database.php';
require_once __DIR__ . "/passwordStrength.php"; // adjust path if needed
session_start();

// Register User: Passwords must match and be strong, username cannot be in use.

$username = strtolower($_POST['username']);
$pw1 = $_POST['pw1'];
$pw2 = $_POST['pw2'];


if ($_SERVER["REQUEST_METHOD"] === "POST") {

	// check matching passwords
	if($pw1 !== $pw2){
	echo "Passwords do not match.";
	exit;
	}

	// $strength is an array of error messages for easy printing
	$strength = checkPasswordStrength($pw1);
	if ($strength !== true) {
		foreach ($strength as $msg) {
			echo "<p>$msg</p>";
		}
		exit;
	}

	// check if username already exists
	$stmt = $mysqli->prepare("SELECT username FROM Users WHERE username=?");
	if(!$stmt){
	printf("Query Prep Failed: %s\n", $mysqli->error);
	exit;
	}
	$stmt->bind_param('s', $username);
	$stmt->execute();
	$stmt->store_result();

	if($stmt->num_rows > 0){
	echo "Username already taken.";
	$stmt->close();
	exit;
	}
	$stmt->close();

	// hash password with correct function
	$hashed_pw = password_hash($pw1, PASSWORD_DEFAULT);

	// insert new user
	$stmt = $mysqli->prepare("INSERT INTO Users (username, password) VALUES (?, ?)");
	if(!$stmt){
	printf("Query Prep Failed: %s\n", $mysqli->error);
	exit;
	}
	$stmt->bind_param('ss', $username, $hashed_pw);
	$stmt->execute();
	$stmt->close();

	// start session
	$_SESSION['username'] = $username;
	header("Location: ../../pages/post/viewPosts.php");
	exit;
}
?>
