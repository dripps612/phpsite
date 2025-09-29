<?php
session_start();
require '../../database.php';

// requires session username and post username to match
//  requires new title, content, or link

if (!isset($_SESSION['username'])) {
    header("Location: ../../pages/login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Check if any field was null null
    $postID = $_POST['postID'] ?? null; 
    $newTitle = $_POST['title'] ?? null;
    $newContent = $_POST['body'] ?? null;
    $newLink = $_POST['link'] ?? null;
    $username = $_SESSION['username'];

    // required fields
    if (!$postID || !$newTitle || !$newContent) {
        echo '<p>Post ID:'.$postID.'</p>';
        echo '<p>Title:  '.$newTitle.'</p>';
        echo '<p>Content: '.$newContent.'</p>';
        die("Missing required fields.");
    }

    // First make sure this user owns the post
   // Verify ownership
    $stmt = $mysqli->prepare("
        SELECT users.username 
        FROM posts 
        JOIN users ON posts.UserID = users.UserID 
        WHERE posts.PostID = ?
    ");

    $stmt->bind_param("i", $postID);
    $stmt->execute();
    $stmt->bind_result($author);
    $stmt->fetch();
    $stmt->close();

    if ($author !== $username) {
        die("Unauthorized: you cannot edit this post.");
    }

    // Update post
    $stmt = $mysqli->prepare("UPDATE posts SET title = ?, body = ?, link = ? WHERE PostID = ?");
    $stmt->bind_param("sssi", $newTitle, $newContent, $newLink, $postID);

    if ($stmt->execute()) {
        $stmt->close();
        header("Location: ../../pages/post/viewSingularPost.php?id=".$postID);
        exit();
    } else {
        die("Error updating post: " . $stmt->error);
    }
}
?>
