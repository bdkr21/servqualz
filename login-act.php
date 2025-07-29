<?php
// Include database connection
include 'include/conn.php';

// Start the session
session_start();

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve form data
    $username = trim($_POST['identifier']);
    $password = trim($_POST['password']);

    // Check if inputs are not empty
    if (empty($username) || empty($password)) {
        header("Location: login.php?error=emptyfields");
        exit();
    }

    // SQL query to check if the user exists in the database, joining with the roles table
    $sql = "SELECT pengguna.*, servqual_roles.jabatan 
            FROM pengguna 
            LEFT JOIN servqual_roles ON pengguna.roles_id = servqual_roles.id_jabatan
            WHERE pengguna.username = ? LIMIT 1";
    
    // Prepare the SQL statement to prevent SQL injection
    if ($stmt = $db->prepare($sql)) {
        // Bind parameters
        $stmt->bind_param("s", $username);
        
        // Execute the query
        $stmt->execute();
        
        // Get the result
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            // Fetch user data from the result
            $user = $result->fetch_assoc();
            
            // Verify the password
            if (password_verify($password, $user['password'])) {
                // Password is correct, set session variables
                $_SESSION['id_roles'] = $user['roles_id']; 
                $_SESSION['user_id'] = $user['id_pengguna'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['roles_id'] = $user['roles_id'];
                $_SESSION['jabatan'] = $user['jabatan'];  // Store the role/jabatan
                
                // Redirect to the dashboard or homepage
                header("Location: index.php");
                exit();
            } else {
                // Incorrect password
                header("Location: login.php?error=invalidlogin");
                exit();
            }
        } else {
            // User not found
            header("Location: login.php?error=invalidlogin");
            exit();
        }
    } else {
        // SQL query preparation failed
        header("Location: login.php?error=invalidlogin");
        exit();
    }
}
?>
