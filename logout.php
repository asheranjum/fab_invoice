<?php
// Start the session only if it's not already started
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Destroy the session
session_unset(); // Clear session variables
session_destroy(); // Destroy the session

// Redirect to the login page after destroying the session
header("Location: login.php");
exit();
?>
