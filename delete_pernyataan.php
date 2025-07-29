<?php
// Ensure the database connection is available
require __DIR__ . '/include/conn.php';

// Get the ID of the statement to delete
if (isset($_GET['id'])) {
    $id_data_pernyataan = $_GET['id'];

    // Delete the statement
    $query_delete = "DELETE FROM data_pernyataan WHERE id_data_pernyataan = ?";
    
    if ($stmt_delete = $db->prepare($query_delete)) {
        $stmt_delete->bind_param("i", $id_data_pernyataan);
        if ($stmt_delete->execute()) {
            // Redirect to the data_pernyataan page
            header("Location: data_pernyataan.php");
            exit();
        } else {
            echo "Error deleting statement.";
        }

        $stmt_delete->close();
    }
}
?>
