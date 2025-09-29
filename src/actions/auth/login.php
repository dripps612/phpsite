<?php
require '../../database.php';
session_start();

//
// Log in a user.
//

$username = $_POST['username'] ?? '';
$password = $_POST['password'] ?? '';

// Username must exist in DB
$stmt = $mysqli->prepare("SELECT password FROM Users WHERE username=?");
if(!$stmt){
    printf("Query Prep Failed: %s\n", $mysqli->error);
    exit;
}
$stmt->bind_param('s', $username);
$stmt->execute();
$stmt->bind_result($hashed_pw);

if($stmt->fetch()){
    // verify password
    if(password_verify($password, $hashed_pw)){
        $_SESSION['username'] = $username;
        header("Location: ../../pages/post/viewPosts.php");
        exit;
    } else {
        echo "Invalid password.";
    }
} else {
    echo "User not found.";
}
$stmt->close();
?>
