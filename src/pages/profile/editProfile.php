<?php
session_start();
require_once("../../database.php");

// Ensure we have a username in the query
$profile_username = $_GET['username'] ?? null;

if (!$profile_username) {
    die("No username provided.");
}

// Make sure the session user matches the profile being edited
if (!isset($_SESSION['username']) || $_SESSION['username'] !== $profile_username) {
    die("You are not allowed to edit this profile.");
}

// Fetch current profile details for pre-filling
$stmt = $mysqli->prepare("SELECT university, major, about_me, avatar FROM Users WHERE username = ?");
$stmt->bind_param("s", $profile_username);
$stmt->execute();
$stmt->bind_result($university, $major, $about_me, $avatar);
$stmt->fetch();
$stmt->close();

// Fallbacks
$university = $university ?? "";
$major = $major ?? "";
$about_me = $about_me ?? "";
$avatar = $avatar ?? "default";

// Avatar options
$avatars = ["default", "card", "galatasary", "horse", "lion","mudkip", "redpanda", "snake", "snoopy"];

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Profile - <?= htmlspecialchars($profile_username) ?></title>
    <link rel="stylesheet" href="../../style.css">
    <link rel="stylesheet" href="profile.css">
</head>
<body>
    <div class="topbar">
        <div class="left-buttons">
            <a href="../post/viewPosts.php" class="submit">Stories</a>
            <a href="profilePage.php?username=<?= urlencode($_SESSION['username']) ?>" class="submit">My Profile</a>
        </div>
        <div class="right-buttons">
            <a href="../../actions/auth/logout.php" class="submit">Log out</a>
        </div>
    </div>

    <div class="profile-box">
        <h1>Edit Profile: <?= htmlspecialchars($profile_username) ?></h1>
        <form method="POST" action="../../actions/profile/updateProfile.php">
            <input type="hidden" name="username" value="<?= htmlspecialchars($profile_username) ?>">

            <label for="university"><strong>University:</strong></label><br>
            <input type="text" id="university" name="university" 
                   value="<?= htmlspecialchars($university) ?>" required><br><br>

            <label for="major"><strong>Major:</strong></label><br>
            <input type="text" id="major" name="major" 
                   value="<?= htmlspecialchars($major) ?>" required><br><br>

            <label for="about_me"><strong>About Me:</strong></label><br>
            <textarea id="about_me" name="about_me" rows="4" cols="50" required><?= htmlspecialchars($about_me) ?></textarea><br><br>

            <h3>Select Avatar:</h3>
            <div class="avatar-grid">
                <?php foreach ($avatars as $av): ?>
                    <label class="avatar-option">
                        <input type="radio" name="avatar" value="<?= $av ?>" 
                            <?= ($avatar === $av ? "checked" : "") ?>>
                        <img src="../../avatars/<?= $av ?>.png" alt="<?= $av ?>" width="128" height="128">
                        <div><?= ucfirst($av) ?></div>
                    </label>
                <?php endforeach; ?>
            </div>

            <br>
            <button type="submit" class="submit">Save Changes</button>
        </form>

        <form method="POST" action="../../actions/profile/deleteProfile.php" 
              onsubmit="return confirm('are you SURE you want to delete your account :(( ??');">
            <input type="hidden" name="username" value="<?= htmlspecialchars($profile_username) ?>">
            <button type="submit" class="submit delete-btn">Delete Account</button>
        </form>
    </div>
</body>
</html>