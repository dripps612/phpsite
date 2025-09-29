<?php
session_start();
require '../../database.php';

// Edits comment.

if (!isset($_SESSION['username'])) {
    header("Location: ../../pages/login.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $commentID = $_POST['commentID'] ?? null;
    $newBody   = trim($_POST['body'] ?? "");
    $username  = $_SESSION['username'];

    if (!$commentID || $newBody === "") {
        die("Missing commentID or body.");
    }

    // Lookup UserID
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

    // Update comment body and date
    $now = date("Y-m-d H:i:s");
    $stmt = $mysqli->prepare("UPDATE Comments SET body = ?, date = ? WHERE CommentID = ?");
    $stmt->bind_param("ssi", $newBody, $now, $commentID);

    if ($stmt->execute()) {
        $stmt->close();
        header("Location: ../../pages/post/viewSingularPost.php?id=" . $postID);
        exit();
    } else {
        die("Error updating comment: " . $stmt->error);
    }
}
?>
