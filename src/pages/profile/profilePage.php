<?php
session_start();
require_once("../../database.php");

if (!isset($_SESSION['username'])) {
    header("Location: ../../index.html");
    exit();
}

// INPUT: 'username'
$profile_username = $_GET['username'] ?? $_SESSION['username'];

$stmt = $mysqli->prepare("SELECT UserID, university, major, about_me, avatar FROM Users WHERE username = ?");
$stmt->bind_param("s", $profile_username);
$stmt->execute();
$stmt->bind_result($profileUserID, $university, $major, $aboutMe, $avatar);
$stmt->fetch();
$stmt->close();

// ENSURE they are not null values, most are at the start.
$university = $university ?? "Not provided";
$major = $major ?? "Not provided";
$aboutMe = $aboutMe ?? "No bio yet.";
$avatar = $avatar ?? "";

// Get session UserID
$stmt2 = $mysqli->prepare("SELECT UserID FROM Users WHERE username = ?");
$stmt2->bind_param("s", $_SESSION['username']);
$stmt2->execute();
$stmt2->bind_result($sessionUserID);
$stmt2->fetch();
$stmt2->close();

// Compare for later
$isOwner = ($sessionUserID === $profileUserID);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../../style.css">
    <link rel="stylesheet" href="../../profile.css">
    <title><?= htmlspecialchars($profile['username'] ?? '') ?>'s Profile</title>
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

    <div class="profile-container">
            <div class="avatar-container">
                <img src="../../avatars/<?= htmlspecialchars($avatar) ?>.png" 
                     alt="Avatar of <?= htmlspecialchars($profile_username) ?>" 
                     class="avatar-img">
            </div>

        <h1>
            <?= htmlspecialchars($profile_username) ?>
            <?php if ($sessionUserID ===$profileUserID): ?>
                    <a href="editProfile.php?username=<?= urlencode($profile_username) ?>" class="submit-edit">Edit</a>
            <?php endif; ?>
        </h1>

        <div class="profile-box">
            <h3>University</h3>
            <p><?= htmlspecialchars($university) ?></p>
        </div>

        <div class="profile-box">
            <h3>Major</h3>
            <p><?= htmlspecialchars($major) ?></p>
        </div>

        <div class="profile-box">
            <h3>About Me</h3>
            <p><?= nl2br(htmlspecialchars($aboutMe)) ?></p>
        </div>
    </div>
</body>
</html>