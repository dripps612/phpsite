<?php
session_start();
require_once '../../database.php';

// Ensure logged in
if (!isset($_SESSION['username'])) {
    header("Location: ../../index.html");
    exit();
}

if (!isset($_GET['postID'])) {
    die("Post ID not provided.");
}

$postID = intval($_GET['postID']);
$username = $_SESSION['username'];

// Find logged in userID
$stmt = $mysqli->prepare("SELECT userID FROM users WHERE username = ?");
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();
if ($result->num_rows === 0) {
    die("Invalid session user.");
}
$userRow = $result->fetch_assoc();
$userID = $userRow['userID'];

// Fetch post
$stmt = $mysqli->prepare("SELECT userID, title, body, link FROM posts WHERE postID = ?");
$stmt->bind_param("i", $postID);
$stmt->execute();
$postResult = $stmt->get_result();
if ($postResult->num_rows === 0) {
    die("Post not found.");
}
$post = $postResult->fetch_assoc();

// Check ownership
if ($post['userID'] !== $userID) {
    die("Unauthorized: you can only edit your own posts.");
}

$title = htmlspecialchars($post['title']);
$body = htmlspecialchars($post['body']);
$link = htmlspecialchars($post['link']);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../../style.css">
    <title>Edit</title>
</head>
<body>
    <div class="topbar">
        <div class="left-buttons">
            <a href="viewPosts.php" class="submit">Stories</a>
            <a href="../profile/profilePage.php" class="submit">My Profile</a>
        </div>
        <div class="right-buttons">
            <a href="../../actions/auth/logout.php" class="submit">Log out</a>
        </div>
    </div>

    <h1>Edit Story</h1>
    <br>

    <form action="../../actions/post/editPost.php" method="POST">
        <input type="hidden" name="postID" value="<?php echo $postID; ?>">

        <label for="title">Title:</label><br>
        <input type="text" id="title" name="title" value="<?php echo $title; ?>" required><br><br>

        <label for="body">Body:</label><br>
        <textarea id="body" name="body" rows="8" cols="50" required><?php echo $body; ?></textarea><br><br>

        <label for="link">Link:</label><br>
        <input type="url" id="link" name="link" value="<?php echo $link; ?>"><br><br>

        <button type="submit" class="submit">Save Changes</button>
    </form>

    <br>

    <form action="../../actions/post/deletePost.php" method="POST" onsubmit="return confirm('This will delet your comments, posts, and profile... :((((');">
        <input type="hidden" name="postID" value="<?php echo $postID; ?>">
        <button type="submit" class="submit delete-btn">Delete Post</button>
    </form>
</body>
</html>
