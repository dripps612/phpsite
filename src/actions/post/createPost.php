<?php
require '../../database.php';
session_start();

// requires a session username, title, body, and optional link.
//
//
//

if(!isset($_SESSION['username'])){
    echo "You must be logged in to create a post.";
    exit;
}

// Delete whitespace
$title = trim($_POST['title'] ?? '');
$body  = trim($_POST['body'] ?? '');
$link  = trim($_POST['link'] ?? null);

if($title === ''){
    echo "Title cannot be empty.";
    exit;
}

// get user id from session username
$stmt = $mysqli->prepare("SELECT UserID FROM Users WHERE username=?");
if(!$stmt){
    printf("Query Prep Failed: %s\n", $mysqli->error);
    exit;
}
$stmt->bind_param('s', $_SESSION['username']);
$stmt->execute();
$stmt->bind_result($user_id);
$stmt->fetch();
$stmt->close();

if(!$user_id){
    echo "Error: could not find user.";
    exit;
}

// insert post
$stmt = $mysqli->prepare("INSERT INTO Posts (UserID, date, title, body, link) VALUES (?, NOW(), ?, ?, ?)");
if(!$stmt){
    printf("Query Prep Failed: %s\n", $mysqli->error);
    exit;
}
$stmt->bind_param('isss', $user_id, $title, $body, $link);
$stmt->execute();

$post_id = $mysqli->insert_id;
$stmt->close();

// redirect to singular post view
header("Location: ../../pages/post/viewSingularPost.php?id=".$post_id);
exit;
?>
