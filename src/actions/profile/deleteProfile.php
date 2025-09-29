<?php
session_start();
require_once("../../database.php");

if (!isset($_SESSION['username'])) {
    die("You must be logged in to delete your account.");
}

$username = $_SESSION['username'];

// Find the user's ID
$stmt = $mysqli->prepare("SELECT UserID FROM Users WHERE username = ?");
$stmt->bind_param("s", $username);
$stmt->execute();
$stmt->bind_result($userID);
$stmt->fetch();
$stmt->close();

if (!$userID) {
    die("User not found.");
}

//Delete comments on the users posts
$stmt = $mysqli->prepare("
    DELETE c
    FROM Comments c
    JOIN Posts p ON c.PostID = p.PostID
    WHERE p.UserID = ?
");
$stmt->bind_param("i", $userID);
$stmt->execute();
$stmt->close();

//Delete the users own comments
$stmt = $mysqli->prepare("DELETE FROM Comments WHERE UserID = ?");
$stmt->bind_param("i", $userID);
$stmt->execute();
$stmt->close();

//Delete the users posts
$stmt = $mysqli->prepare("DELETE FROM Posts WHERE UserID = ?");
$stmt->bind_param("i", $userID);
$stmt->execute();
$stmt->close();

// Delete the user
$stmt = $mysqli->prepare("DELETE FROM Users WHERE UserID = ?");
$stmt->bind_param("i", $userID);
$stmt->execute();
$stmt->close();

// Back to starting page
session_unset();
session_destroy();
header("Location: ../../index.html");
exit;
?>
