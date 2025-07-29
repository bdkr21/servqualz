<?php
// Ensure the database connection is available
require __DIR__ . '/../include/conn.php';

// Get the ID of the customer to delete
if (isset($_GET['id'])) {
    $id_pelanggan = $_GET['id'];

    // Delete related transactions first
    $query_delete_transaksi = "DELETE FROM transaksi WHERE id_pelanggan = ?";
    
    if ($stmt_transaksi = $db->prepare($query_delete_transaksi)) {
        $stmt_transaksi->bind_param("i", $id_pelanggan);
        $stmt_transaksi->execute();
        $stmt_transaksi->close();
    }

    // Delete the customer
    $query_delete_pelanggan = "DELETE FROM pelanggan WHERE id_pelanggan = ?";
    
    if ($stmt_pelanggan = $db->prepare($query_delete_pelanggan)) {
        $stmt_pelanggan->bind_param("i", $id_pelanggan);
        if ($stmt_pelanggan->execute()) {
            // Success: Redirect to the data pelanggan page
            header("Location: data_pelanggan.php");
            exit();
        } else {
            echo "Error deleting customer.";
        }

        $stmt_pelanggan->close();
    }
}
?>
