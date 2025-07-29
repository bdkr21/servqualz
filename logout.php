<?php
// Check if session has already started
if (session_status() == PHP_SESSION_NONE) {
    session_start(); // Start the session if not already started
}

// Destroy the session and redirect to login page
session_destroy();
header("location: login.php");
exit();
?>
