<?php
// Ensure the database connection is available
require __DIR__ . '/include/conn.php';
require __DIR__ . '/include/session_check.php';
// Get the ID of the kuesioner to delete
if (isset($_GET['id'])) {
    $id_data_kuesioner = $_GET['id'];

    // Delete the kuesioner
    $query_delete = "DELETE FROM data_kuesioner WHERE id_data_kuesioner = ?";
    
    if ($stmt_delete = $db->prepare($query_delete)) {
        $stmt_delete->bind_param("i", $id_data_kuesioner);
        if ($stmt_delete->execute()) {
            // Redirect to the data_kuesioner page
            header("Location: data_kuesioner.php");
            exit();
        } else {
            echo "Error deleting record.";
        }

        $stmt_delete->close();
    }
}
?>
