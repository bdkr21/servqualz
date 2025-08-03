<?php
// Ensure the database connection is available
require __DIR__ . '/include/conn.php';

// Check if the id is provided in the request
if (isset($_GET['id'])) {
    $id = $_GET['id'];

    // Prepare the DELETE query to remove the statement
    $query_delete = "DELETE FROM data_pernyataan WHERE id_data_pernyataan = ?";

    if ($stmt_delete = $db->prepare($query_delete)) {
        $stmt_delete->bind_param("i", $id);
        
        // Execute the query
        if ($stmt_delete->execute()) {
            // Check where the delete request came from and redirect accordingly
            $referer = $_SERVER['HTTP_REFERER'];

            if (strpos($referer, 'data_pernyataan.php') !== false) {
                // Redirect back to data_pernyataan.php
                header("Location: data_pernyataan.php");
            } elseif (strpos($referer, 'edit_kuesioner.php') !== false) {
                // Redirect back to edit_kuesioner.php
                echo json_encode(['success' => true]);
            } else {
                // If no referrer, just redirect to the data_pernyataan.php
                header("Location: data_pernyataan.php");
            }
            exit();
        } else {
            // Respond with an error message if the deletion fails
            echo json_encode(['success' => false, 'message' => 'Failed to delete the record.']);
        }

        $stmt_delete->close();
    } else {
        // Respond with an error message if there’s an issue preparing the query
        echo json_encode(['success' => false, 'message' => 'Failed to prepare the query.']);
    }
} else {
    // Respond with an error if no id is provided
    echo json_encode(['success' => false, 'message' => 'No id provided.']);
}
?>
