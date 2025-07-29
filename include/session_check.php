<?php
// Start the session
session_start();

// Check if the session variable 'jabatan' exists
if (!isset($_SESSION['jabatan'])) {
    echo 'No user role found. Please log in first.';
    header('Location: login.php'); // Redirect to login page if no session
    exit();
}
