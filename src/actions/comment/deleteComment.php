<?php
session_start();
require '../../database.php';

// Deletes a comment if the user is the owner

if (!isset($_SESSION['username'])) {
    header("Location: ../../pages/login.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $commentID = $_POST['commentID'] ?? null;
    $username  = $_SESSION['username'];

    if (!$commentID) {
        die("Missing commentID.");
    }

    // Lookup comment owner and PostID
    $stmt = $mysqli->prepare("SELECT UserID, PostID FROM Comments WHERE CommentID = ?");
    $stmt->bind_param("i", $commentID);
    $stmt->execute();
    $stmt->bind_result($commentUserID, $postID);
    $stmt->fetch();
    $stmt->close();

    if (!$commentUserID) {
        die("Comment not found.");
    }

    // Get session user ID
    $stmt = $mysqli->prepare("SELECT UserID FROM Users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $stmt->bind_result($userID);
    $stmt->fetch();
    $stmt->close();

    if ($userID !== $commentUserID) {
        die("Unauthorized.");
    }

    // Delete comment
    $stmt = $mysqli->prepare("DELETE FROM Comments WHERE CommentID = ?");
    $stmt->bind_param("i", $commentID);

    if ($stmt->execute()) {
        $stmt->close();
        header("Location: ../../pages/post/viewSingularPost.php?id=" . $postID);
        exit();
    } else {
        die("Error deleting comment: " . $stmt->error);
    }
}
?>
