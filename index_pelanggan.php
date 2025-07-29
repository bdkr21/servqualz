<?php
// Ensure the database connection is available
require __DIR__ . '/include/conn.php';

// Check if the kode_transaksi is passed in the URL (GET)
if (isset($_GET['kode_transaksi'])) {
    $kode_transaksi = $_GET['kode_transaksi'];

    // Query to check if the transaction exists and its status is 'lunas'
    $query = "SELECT t.kode_transaksi, t.pembayaran, p.nama 
              FROM transaksi t 
              INNER JOIN pelanggan p ON t.id_pelanggan = p.id_pelanggan 
              WHERE t.kode_transaksi = ?";

    if ($stmt = $db->prepare($query)) {
        $stmt->bind_param("s", $kode_transaksi);  // Use "s" for string binding (kode_transaksi is a string)
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
                header("Location: kuesioner_pelanggan.php?kode_transaksi=$kode_transaksi");
                exit();
            } else {
                // Show error message if payment is not 'lunas'
                $error = "Transaction not paid yet. You cannot fill the questionnaire.";
            }
        } else {
            // Show error message if transaction does not exist
            $error = "Invalid transaction code. Please check and try again.";
        }

        // Close the statement
        $stmt->close();
    }
} else {
    // Show error message if kode_transaksi is not provided
    $error = "Transaction code is required.";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Validasi Transaksi</title>
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@300;400;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/bootstrap.css">
    <link rel="stylesheet" href="assets/vendors/bootstrap-icons/bootstrap-icons.css">
    <link rel="stylesheet" href="assets/css/app.css">
    <link rel="stylesheet" href="assets/css/pages/auth.css">
    <link rel="shortcut icon" href="assets/images/favicon.png" type="image/x-icon">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>
    <div id="auth" class="d-flex justify-content-center align-items-center vh-100" style="background: #f7f9fc;">
        <div class="col-lg-5 col-12">
            <div class="container w-100">
                <div id="auth-left" class="text-center">
                    <h1 class="mb-4">Validasi Transaksi</h1>

                    <form action="index_pelanggan.php" method="GET">
                        <!-- Display error message if any -->
                        <?php if (isset($error)): ?>
                            <div class="alert alert-danger"><?php echo $error; ?></div>
                        <?php endif; ?>

                        <!-- Input for transaction code (kode_transaksi) -->
                        <div class="form-group position-relative has-icon-left mb-4">
                            <input type="text" class="form-control form-control-xl" placeholder="Enter Transaction Code" name="kode_transaksi" required style="border-radius: 30px;">
                            <div class="form-control-icon">
                                <i class="bi bi-file-earmark-text" style="color: #007bff;"></i>
                            </div>
                        </div>

                        <!-- Submit button for validation -->
                        <button type="submit" class="btn btn-primary btn-block btn-lg shadow-lg mt-4" style="border-radius: 30px;">Validasi</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        <?php if (isset($error) && strpos($error, 'not paid yet') !== false): ?>
            Swal.fire({
                title: 'Error!',
                text: 'Transaction not paid yet. You cannot fill the questionnaire.',
                icon: 'error',
                confirmButtonText: 'OK'
            });
        <?php elseif (isset($error) && strpos($error, 'Invalid transaction') !== false): ?>
            Swal.fire({
                title: 'Error!',
                text: 'Invalid transaction code. Please check and try again.',
                icon: 'error',
                confirmButtonText: 'OK'
            });
        <?php endif; ?>
    </script>

</body>
</html>
