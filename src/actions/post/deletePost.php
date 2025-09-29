<?php
require '../../database.php';
session_start();

// Requires session username and post id to match

if(!isset($_SESSION['username'])){
    echo "You must be logged in to delete a post.";
    exit;
}

$post_id = intval($_POST['postID'] ?? 0);
if($post_id <= 0){
    echo "Invalid post ID.";
    exit;
}



// get userID of current user
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

// check if this post belongs to that user
$stmt = $mysqli->prepare("SELECT UserID FROM Posts WHERE PostID=?");
if(!$stmt){
    printf("Query Prep Failed: %s\n", $mysqli->error);
    exit;
}
$stmt->bind_param('i', $post_id);
$stmt->execute();
$stmt->bind_result($post_owner);
$stmt->fetch();
$stmt->close();

if($post_owner !== $user_id){
    echo "You are not allowed to delete this post.";
    exit;
}

// IMPORTANT: delete comments first.
$stmt = $mysqli->prepare("DELETE FROM Comments WHERE PostID=?");
if(!$stmt){
    printf("Query Prep Failed: %s\n", $mysqli->error);
    exit;
}
$stmt->bind_param('i', $post_id);
$stmt->execute();
$stmt->close();

// delete post
$stmt = $mysqli->prepare("DELETE FROM Posts WHERE PostID=?");
if(!$stmt){
    printf("Query Prep Failed: %s\n", $mysqli->error);
    exit;
}
$stmt->bind_param('i', $post_id);
$stmt->execute();
$stmt->close();

// redirect
header("Location: ../../pages/post/viewPosts.php");
exit;
?>
