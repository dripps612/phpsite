<?php
session_start();
require '../../database.php';

//
// creates a comment on a given post id. must bre logged in.
//

// Make sure user is logged in
if (!isset($_SESSION['username'])) {
    header("Location: ../../pages/auth/login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $storyID = $_POST['postID'];
    $body = $_POST['body'];
    $username = $_SESSION['username'];

    // Get the UserID for the logged in user
    $stmt = $mysqli->prepare("SELECT UserID FROM Users WHERE username = ?");
    if(!$stmt){
        printf("Query Prep Failed: %s\n", $mysqli->error);
        exit;
    }
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $stmt->bind_result($userID);
    $stmt->fetch();
    $stmt->close();

    // Insert the comment
    $stmt = $mysqli->prepare("INSERT INTO Comments (PostID, UserID, date, body) VALUES (?, ?, NOW(), ?)");
    if(!$stmt){
        printf("Query Prep Failed: %s\n", $mysqli->error);
        exit;
    }
    $stmt->bind_param("iis", $storyID, $userID, $body);
    $stmt->execute();
    $stmt->close();

    // Redirect back to the post
    header("Location: ../../pages/post/viewSingularPost.php?id=" . urlencode($storyID));
    exit();
}
?>
