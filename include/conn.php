<?php
// conn.php
$servername = "localhost";
$username = "root"; // Your database username
$password = "";     // Your database password
$dbname = "servqual"; // Your database name

// Create connection
$db = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($db->connect_error) {
    die("Connection failed: " . $db->connect_error);
}
?>
