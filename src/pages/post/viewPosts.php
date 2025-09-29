<?php
session_start();
require_once("../../database.php");

// Get all posts with their authors
$stmt = $mysqli->prepare("
    SELECT Posts.PostID, Posts.title, Posts.date, Users.username
    FROM Posts
    JOIN Users ON Posts.UserID = Users.UserID
    ORDER BY Posts.date DESC
");

// execute
$stmt->execute();
$result = $stmt->get_result();
$posts = $result->fetch_all(MYSQLI_ASSOC);
$stmt->close();

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../../style.css">
    <title>View</title>
</head>
<body>
    <!-- Nav bar -->
    <div class="topbar">
        <div class="left-buttons">
            <a href="viewPosts.php" class="submit">Stories</a>
            <?php if (isset($_SESSION['username'])): ?>
                <a href="../profile/profilePage.php" class="submit">My Profile</a>
                <a href="createPost.php" class="submit">New Post</a>
            <?php endif; ?>
        </div>
        <div class="right-buttons">
            <?php if (isset($_SESSION['username'])): ?>
                <a href="../../actions/auth/logout.php" class="submit">Log out</a>
            <?php else: ?>
                <a href="../auth/loginPage.html" class="submit">Log in</a>
            <?php endif; ?>
        </div>
    </div>

    <h1>Stories</h1>
    <br><br>
    <!-- Loop through our stories/posts. -->
    <?php if (count($posts) > 0): ?>
        <ul class="post-list">
            <?php foreach ($posts as $post): ?>
                <li class="post-item">
                    <a href="viewSingularPost.php?id=<?= htmlspecialchars($post['PostID']) ?>">
                        <?= htmlspecialchars($post['title']) ?>
                    </a>
                    <br>
                    by 
                    <a href="../profile/profilePage.php?username=<?= urlencode($post['username']) ?>">
                        <?= htmlspecialchars($post['username']) ?>
                    </a>
                    <span class="date">(<?= htmlspecialchars($post['date']) ?>)</span>
                </li>
                <hr>
            <?php endforeach; ?>
        </ul>
    <?php else: ?>
        <p>No posts yet</p>
    <?php endif; ?>
</body>
</html>
