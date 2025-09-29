<?php
session_start();
require_once("../../database.php");

$postID = $_GET['id'] ?? null;
if (!$postID) {
    die("No post ID specified.");
}

// Fetch post data
$stmt = $mysqli->prepare("
    SELECT Posts.PostID, Posts.title, Posts.body, Posts.link, Posts.date, Users.UserID, Users.username
    FROM Posts
    JOIN Users ON Posts.UserID = Users.UserID
    WHERE Posts.PostID = ?
");
$stmt->bind_param("i", $postID);
$stmt->execute();
$result = $stmt->get_result();
$post = $result->fetch_assoc();
$stmt->close();

if (!$post) {
    die("Post not found.");
}

// Get session userID
$stmt = $mysqli->prepare("SELECT UserID FROM Users WHERE username = ?");
$stmt->bind_param("s", $_SESSION['username']);
$stmt->execute();
$stmt->bind_result($sessionUserID);
$stmt->fetch();
$stmt->close();

// Fetch comments
$stmt = $mysqli->prepare("
    SELECT Comments.CommentID, Comments.body, Comments.date, Users.UserID, Users.username
    FROM Comments
    JOIN Users ON Comments.UserID = Users.UserID
    WHERE Comments.PostID = ?
    ORDER BY Comments.date ASC
");
$stmt->bind_param("i", $postID);
$stmt->execute();
$comments = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../../style.css">
    <title>Story</title>
</head>
<body>
    <!-- Nav bar -->
    <div class="topbar">
        <div class="left-buttons">
            <a href="viewPosts.php" class="submit">Stories</a>
            <a href="../profile/profilePage.php" class="submit">My Profile</a>
        </div>
        <div class="right-buttons">
            <a href="../../actions/auth/logout.php" class="submit">Log out</a>
        </div>
    </div>

    <!-- Post section -->
    <div class="post-container">
        <div class="post-header">
            <h1><?= htmlspecialchars($post['title']) ?></h1>
            <h5>
                by <a href="../profile/profilePage.php?username=<?= urlencode($post['username']) ?>">
                    <?= htmlspecialchars($post['username']) ?>
                </a> on <?= htmlspecialchars($post['date']) ?>
            </h5>
        </div>
        <div class="post-body">
            <p><?= nl2br(htmlspecialchars($post['body'])) ?></p>
            <?php if ($post['link']): ?>
                <p><a href="<?= htmlspecialchars($post['link']) ?>" target="_blank">Link!</a></p>
            <?php endif; ?>
        </div>
        <?php if ($sessionUserID == $post['UserID']): ?>
            <div class="post-actions">
                <a href="editPost.php?postID=<?= $post['PostID'] ?>" class="submit">Edit Post</a>
                <form method="POST" action="../../actions/post/deletePost.php" style="display:inline;">
                    <input type="hidden" name="postID" value="<?= $post['PostID'] ?>">
                    <button type="submit" class="submit"
                        onclick="return confirm('Are you sure you want to delete this post?');">
                        Delete Post
                    </button>
                </form>
            </div>
        <?php endif; ?>
    </div>

<!-- Comment section -->
<h3 class="commentTitle">Comments</h3>

    <div class="comments-container">
        <br><br>
        <?php if (count($comments) > 0): ?>
            <?php foreach ($comments as $comment): ?>
                <div class="comment-item">
                    <div class="comment-meta">
                        <a href="../profile/profilePage.php?username=<?= urlencode($comment['username']) ?>">
                            <?= htmlspecialchars($comment['username']) ?>
                        </a> - <?= htmlspecialchars($comment['date']) ?>
                    </div>
                    <div class="comment-body">
                        <?php if ($comment['UserID'] == $sessionUserID): ?>
                            <form method="POST" action="../../actions/comment/editComment.php">
                                <input type="hidden" name="commentID" value="<?= $comment['CommentID'] ?>">
                                <input type="hidden" name="postID" value="<?= $post['PostID'] ?>">
                                <textarea name="body" rows="2" cols="50"><?= htmlspecialchars($comment['body']) ?></textarea>
                                <br>
                                <button type="submit" class="submit">Save Edit</button>
                            </form>
                            <form method="POST" action="../../actions/comment/deleteComment.php" style="display:inline;">
                                <input type="hidden" name="commentID" value="<?= $comment['CommentID'] ?>">
                                <input type="hidden" name="postID" value="<?= $post['PostID'] ?>">
                                <button type="submit" class="submit"
                                    onclick="return confirm('Delete this comment?');">Delete</button>
                            </form>
                        <?php else: ?>
                            <p><?= nl2br(htmlspecialchars($comment['body'])) ?></p>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p>No Comments yet...</p>
        <?php endif; ?>
    </div>
        <!--Comment buttn-->
    <?php if (isset($_SESSION['username'])): ?>
        <div class="comment-form">
            <h4>Leave a Comment:</h4>
            <form method="POST" action="../../actions/comment/createComment.php">
                <input type="hidden" name="postID" value="<?= $post['PostID'] ?>">
                <textarea name="body" rows="3" cols="60" required></textarea>
                <br>
                <button type="submit" class="submit">Comment</button>
            </form>
        </div>
    <?php endif; ?>
</body>
</html>
