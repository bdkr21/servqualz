<?php
// Include database connection
require __DIR__ . '/include/conn.php';
require __DIR__ . '/include/session_check.php';  // Ensure the user is logged in

// Check if the user ID is passed in the URL
if (isset($_GET['id'])) {
    // Get the user ID from the query string
    $id_pengguna = $_GET['id'];

    // Get the current logged-in user's ID
    $current_user_id = $_SESSION['user_id'];

    // Check if the logged-in user is trying to delete their own account
    if ($current_user_id == $id_pengguna) {
        // Query to delete the user from the database
        $query = "DELETE FROM pengguna WHERE id_pengguna = ?";

        if ($stmt = $db->prepare($query)) {
            // Bind parameters
            $stmt->bind_param("i", $id_pengguna);

            // Execute the query
            if ($stmt->execute()) {
                // Destroy the session and log the user out
                session_start();  // Start the session to destroy it
                session_unset();  // Unset all session variables
                session_destroy();  // Destroy the session

                // Redirect to login page after logout
                header("Location: login.php?message=account_deleted");
                exit();
            } else {
                // Error: Query execution failed
                header("Location: owner_data_pengguna.php?error=deletion_failed");
                exit();
            }

            // Close the statement
            $stmt->close();
        } else {
            // Error: Query preparation failed
            header("Location: owner_data_pengguna.php?error=deletion_failed");
            exit();
        }
    } else {
        // If it's not the logged-in user's account, proceed with deletion as usual
        $query = "DELETE FROM pengguna WHERE id_pengguna = ?";

        if ($stmt = $db->prepare($query)) {
            // Bind parameters
            $stmt->bind_param("i", $id_pengguna);

            // Execute the query
            if ($stmt->execute()) {
                // Redirect to user data page after deletion
                header("Location: owner_data_pengguna.php?status=deleted");
                exit();
            } else {
                // Error: Query execution failed
                header("Location: owner_data_pengguna.php?error=deletion_failed");
                exit();
            }

            // Close the statement
            $stmt->close();
        } else {
            // Error: Query preparation failed
            header("Location: owner_data_pengguna.php?error=deletion_failed");
            exit();
        }
    }
} else {
    // If no user ID is provided, redirect to the owner data page with an error
    header("Location: owner_data_pengguna.php?error=invalid_request");
    exit();
}
