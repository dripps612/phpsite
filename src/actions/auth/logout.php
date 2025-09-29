<?php

// Log the user out. release all session variables
session_start();
session_unset();
session_destroy();
header("Location: ../../index.html")
?>