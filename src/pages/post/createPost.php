<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../../style.css">
    <title>Create</title>
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

    <h1>Create Story</h1>
    <h5>Share something new with the community</h5>
	<br>

    <form method="POST" action="../../actions/post/createPost.php">
        <label for="title">Title:</label><br>
        <input type="text" id="title" name="title" maxlength="100" class="inputbox" required><br>

        <label for="body">Body:</label><br>
        <textarea id="body" name="body" rows="6" cols="60" class="inputbox" required></textarea><br>

        <label for="link">Link (optional):</label><br>
        <input type="url" id="link" name="link" maxlength="255" class="inputbox"><br>

        <button type="submit" class="submit">Post</button>
    </form>
</body>
</html>
