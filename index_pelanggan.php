<?php
// Ensure the database connection is available
require __DIR__ . '/include/conn.php';

// Check if the id_transaksi is passed in the URL (GET) or form (POST)
if (isset($_GET['id_transaksi'])) {
    $id_transaksi = $_GET['id_transaksi'];

    // Query to check if the transaction exists and its status is 'lunas'
    $query = "SELECT t.id_transaksi, t.pembayaran, p.nama 
              FROM transaksi t 
              INNER JOIN pelanggan p ON t.id_pelanggan = p.id_pelanggan 
              WHERE t.id_transaksi = ?";

    if ($stmt = $db->prepare($query)) {
        $stmt->bind_param("i", $id_transaksi);
        $stmt->execute();
        $result = $stmt->get_result();

        // Check if the transaction exists
        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $pembayaran = $row['pembayaran'];
            $nama_pelanggan = $row['nama'];

            // Check if the transaction status is 'lunas'
            if ($pembayaran == 'lunas') {
                // Redirect to the questionnaire page
                header("Location: kuesioner_pelanggan.php?id_transaksi=$id_transaksi");
                exit();
            } else {
                // Show error message if payment is not 'lunas'
                $error = "Transaction not paid yet. You cannot fill the questionnaire.";
            }
        } else {
            // Show error message if transaction does not exist
            $error = "Invalid transaction ID. Please check and try again.";
        }

        // Close the statement
        $stmt->close();
    }
} else {
    // Show error message if id_transaksi is not provided
    $error = "Transaction ID is required.";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Validasi Pelanggan</title>
</head>
<body>

<div id="app">
    <div id="main">
        <header class="mb-3">
            <a href="#" class="burger-btn d-block d-xl-none">
                <i class="bi bi-justify fs-3"></i>
            </a>
        </header>

        <div class="page-heading">
            <h3>Validasi Pelanggan</h3>
        </div>

        <div class="page-content">
            <section class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h4>Formulir Validasi</h4>
                        </div>
                        <div class="card-body">
                            <?php if (isset($error)): ?>
                                <div class="alert alert-danger"><?php echo $error; ?></div>
                            <?php endif; ?>

                            <form action="index_pelanggan.php" method="GET">
                                <div class="form-group">
                                    <label for="id_transaksi">ID Transaksi:</label>
                                    <input type="text" name="id_transaksi" id="id_transaksi" class="form-control" required>
                                </div>

                                <button type="submit" class="btn btn-primary">Validasi</button>
                            </form>
                        </div>
                    </div>
                </div>
            </section>
        </div> <!-- End page-content -->

    </div> <!-- End main -->
</div> <!-- End app -->

<?php require "layout/js.php"; ?>
</body>
</html>
