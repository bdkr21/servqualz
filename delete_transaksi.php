<?php
// Ensure the database connection is available
require __DIR__ . '/include/conn.php';
require __DIR__ . '/include/session_check.php';

// Check if the 'id_transaksi' is provided via GET
if (isset($_GET['id'])) {
    $id_transaksi = $_GET['id'];

    // Prepare the DELETE query
    $query = "DELETE FROM transaksi WHERE id_transaksi = ?";

    // Prepare the statement
    if ($stmt = $db->prepare($query)) {
        // Bind the parameter
        $stmt->bind_param("i", $id_transaksi);

        // Execute the query
        if ($stmt->execute()) {
            // Success: Redirect to the transaksi data page
            header("Location: data_transaksi.php");
            exit();
        } else {
            // Error: Query failed
            echo "Error: Could not delete the transaction.";
        }

        // Close the statement
        $stmt->close();
    } else {
        // Error: Query preparation failed
        echo "Error: Could not prepare the query.";
    }
} else {
    // If 'id_transaksi' is not provided, redirect back to the data_transaksi page
    header("Location: data_transaksi.php");
    exit();
}
?>
