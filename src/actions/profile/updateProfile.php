<?php
session_start();
require_once("../../database.php");

if (!isset($_SESSION['username'])) {
    die("You must be logged in to update your profile.");
}

// Get POST values safely. they can be empty.
$username   = $_POST['username'] ?? null;
$university = trim($_POST['university'] ?? "");
$major      = trim($_POST['major'] ?? "");
$about_me   = trim($_POST['about_me'] ?? "");
$avatar     = $_POST['avatar'] ?? "default";

if ($username !== $_SESSION['username']) {
    die("You cannot edit another user's profile.");
}

// Prepare update query
$stmt = $mysqli->prepare("
    UPDATE Users
    SET university = ?, major = ?, about_me = ?, avatar = ?
    WHERE username = ?
");
if (!$stmt) {
    printf("Query Prep Failed: %s\n", $mysqli->error);
    exit;
}

$stmt->bind_param("sssss", $university, $major, $about_me, $avatar, $username);

if (!$stmt->execute()) {
    printf("Update failed: %s\n", $stmt->error);
    exit;
}

$stmt->close();

// Redirect back to profile page
header("Location: ../../pages/profile/profilePage.php?username=" . urlencode($username));
exit;
?>
