<?php
// Ensure the database connection is available
require __DIR__ . '/include/conn.php';

// Get the kode_transaksi from the URL
if (isset($_GET['kode_transaksi'])) {
    $kode_transaksi = $_GET['kode_transaksi'];

    // Query to fetch the kode_transaksi and related jenis_layanan
    $query_transaksi = "SELECT t.kode_transaksi, t.pembayaran, p.nama, t.jenis_layanan 
                        FROM transaksi t 
                        INNER JOIN pelanggan p ON t.id_pelanggan = p.id_pelanggan 
                        WHERE t.kode_transaksi = ?";

    if ($stmt = $db->prepare($query_transaksi)) {
        $stmt->bind_param("s", $kode_transaksi);  // Use "s" for string binding (kode_transaksi is a string)
        $stmt->execute();
        $result_transaksi = $stmt->get_result();

        // Check if the transaction exists
        if ($result_transaksi->num_rows > 0) {
            $row_transaksi = $result_transaksi->fetch_assoc();
            $pembayaran = $row_transaksi['pembayaran'];
            $nama_pelanggan = $row_transaksi['nama'];
            $jenis_layanan_transaksi = $row_transaksi['jenis_layanan'];  // Get jenis layanan from transaction

            // Debugging: Print the transaction details
            echo "<pre>Transaction details: ";
            print_r($row_transaksi);
            echo "</pre>";

            // Check if the transaction status is 'lunas'
            if ($pembayaran == 'lunas') {
                // Step 1: Query to fetch data_kuesioner where status is 'publish' and jenis_layanan matches the transaction's services
                $query_kuesioner = "SELECT id_data_kuesioner, nama_kuesioner, dimensi_layanan 
                                    FROM data_kuesioner 
                                    WHERE status = 'publish' 
                                    AND FIND_IN_SET(?, dimensi_layanan)";  // Using FIND_IN_SET for multi-value matching

                if ($stmt_kuesioner = $db->prepare($query_kuesioner)) {
                    $stmt_kuesioner->bind_param("s", $jenis_layanan_transaksi);  // Use "s" for string binding
                    $stmt_kuesioner->execute();
                    $result_kuesioner = $stmt_kuesioner->get_result();

                    // Debugging: Check if there are any kuesioners
                    if ($result_kuesioner->num_rows > 0) {
                        // Loop through the kuesioners and display the active pernyataan for each
                        echo "<h4>Kuesioner Results:</h4>"; // Debugging message
                        while ($row_kuesioner = $result_kuesioner->fetch_assoc()) {
                            $id_data_kuesioner = $row_kuesioner['id_data_kuesioner'];
                            $nama_kuesioner = $row_kuesioner['nama_kuesioner'];
                            $dimensi_layanan = $row_kuesioner['dimensi_layanan'];  // Get dimensi_layanan

                            // Debugging: Print dimensi_layanan from data_kuesioner
                            echo "<pre>Dimensi Layanan in Kuesioner: ";
                            print_r($dimensi_layanan); // This will show the value of dimensi_layanan
                            echo "</pre>";

                            // Step 2: Fetch active pernyataan for this kuesioner and matching dimensi_layanan
                            $query_pernyataan = "SELECT p.pernyataan 
                                                 FROM data_pernyataan p 
                                                 WHERE p.id_data_kuesioner = ? 
                                                 AND p.status = 'aktif' 
                                                 AND FIND_IN_SET(?, p.jenis_layanan)";  // Find matching jenis_layanan

                            if ($stmt_pernyataan = $db->prepare($query_pernyataan)) {
                                $stmt_pernyataan->bind_param("ss", $id_data_kuesioner, $jenis_layanan_transaksi);  // Bind parameters
                                $stmt_pernyataan->execute();
                                $result_pernyataan = $stmt_pernyataan->get_result();

                                // Debugging: Check if there are any active pernyataan
                                if ($result_pernyataan->num_rows > 0) {
                                    echo "<h5>$nama_kuesioner</h5>";
                                    echo "<ul>";

                                    while ($row_pernyataan = $result_pernyataan->fetch_assoc()) {
                                        echo "<li>{$row_pernyataan['pernyataan']}</li>";
                                    }

                                    echo "</ul>";
                                } else {
                                    // Debugging: No active pernyataan found
                                    echo "<p>No active pernyataan found for this kuesioner.</p>";
                                }
                                $stmt_pernyataan->close();
                            }
                        }
                    } else {
                        // Debugging: No kuesioner found
                        echo "<p>No kuesioner found with the status 'publish'.</p>";
                    }
                    $stmt_kuesioner->close();
                }
            } else {
                $error = "Transaction not paid yet. You cannot fill the questionnaire.";
            }
        } else {
            $error = "Invalid transaction code. Please check and try again.";
        }
        $stmt->close();
    }
} else {
    // Redirect to a different page if no kode_transaksi is provided
    header("Location: index_pelanggan.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kuesioner Pelanggan</title>
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@300;400;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/bootstrap.css">
    <link rel="stylesheet" href="assets/vendors/bootstrap-icons/bootstrap-icons.css">
    <link rel="stylesheet" href="assets/css/app.css">
    <link rel="shortcut icon" href="assets/images/favicon.png" type="image/x-icon">
</head>
<body>
    <div id="app">
        <div id="main">
            <div class="page-heading">
                <h3>Kuesioner Pelanggan</h3>
            </div>

            <div class="page-content">
                <section class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <h4>Formulir Kuesioner</h4>
                            </div>
                            <div class="card-body">
                                <?php
                                if (isset($error)) {
                                    echo "<div class='alert alert-danger'>{$error}</div>";
                                }
                                ?>
                                <!-- Loop through the active pernyataan and display them here -->
                            </div>
                        </div>
                    </div>
                </section>
            </div> <!-- End page-content -->
        </div> <!-- End main -->
    </div> <!-- End app -->

    <script src="path/to/bootstrap.bundle.js"></script> <!-- Add your bootstrap js path here -->
</body>
</html>
