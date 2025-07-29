<?php
// Ensure the database connection is available
require __DIR__ . '/include/conn.php';
require __DIR__ . '/include/session_check.php';
require "layout/head.php";
// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get the data from the form
    $nama = $_POST['nama'];  // Added field for customer name
    $no_telp = $_POST['no_telp'];
    $tanggal_lahir = $_POST['tanggal_lahir'];
    $jenis_layanan_customer = isset($_POST['jenis_layanan_customer']) ? implode(',', $_POST['jenis_layanan_customer']) : '';
    
    $tanggal_transaksi = $_POST['tanggal_transaksi'];
    $pembayaran = $_POST['pembayaran'];
    
    // Get the selected services from the checkboxes
    $selected_services = isset($_POST['jenis_layanan']) ? implode(',', $_POST['jenis_layanan']) : '';

    // Check if any of the fields are empty
    if (empty($nama) || empty($no_telp) || empty($tanggal_lahir) || empty($tanggal_transaksi) || empty($pembayaran) || empty($selected_services)) {
        $error = "All fields are required!";
    } else {
        // Step 1: Insert the customer (pelanggan) data
        $query_pelanggan = "INSERT INTO pelanggan (nama, no_telp, tanggal_lahir, jenis_layanan) VALUES (?, ?, ?, ?)";
        
        if ($stmt_pelanggan = $db->prepare($query_pelanggan)) {
            $stmt_pelanggan->bind_param("ssss", $nama, $no_telp, $tanggal_lahir, $jenis_layanan_customer);

            // Execute the customer insertion
            if ($stmt_pelanggan->execute()) {
                // Get the customer ID of the newly inserted record
                $id_pelanggan = $stmt_pelanggan->insert_id;

                // Step 2: Insert the transaction data (transaksi)
                $query_transaksi = "INSERT INTO transaksi (id_pelanggan, tanggal_transaksi, pembayaran, jenis_layanan) VALUES (?, ?, ?, ?)";
                
                if ($stmt_transaksi = $db->prepare($query_transaksi)) {
                    $stmt_transaksi->bind_param("isss", $id_pelanggan, $tanggal_transaksi, $pembayaran, $selected_services);

                    // Execute the transaction insertion
                    if ($stmt_transaksi->execute()) {
                        // Success: Redirect to the transaksi data page
                        header("Location: data_transaksi.php");
                        exit();
                    } else {
                        // Error: Could not insert transaction data
                        $error = "Error: Could not insert transaction data.";
                    }

                    // Close the transaction statement
                    $stmt_transaksi->close();
                } else {
                    $error = "Error: Could not prepare the transaction insert query.";
                }
            } else {
                // Error: Could not insert customer data
                $error = "Error: Could not insert customer data.";
            }

            // Close the customer statement
            $stmt_pelanggan->close();
        } else {
            $error = "Error: Could not prepare the customer insert query.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Transaksi</title>
</head>
<body>

<div id="app">
    <?php require "layout/sidebar.php"; ?>

    <div id="main">
        <header class="mb-3">
            <a href="#" class="burger-btn d-block d-xl-none">
                <i class="bi bi-justify fs-3"></i>
            </a>
        </header>

        <div class="page-heading">
            <h3>Tambah Transaksi dan Pelanggan</h3>
        </div>

        <div class="page-content">
            <section class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h4>Formulir Tambah Transaksi dan Pelanggan</h4>
                        </div>
                        <div class="card-body">
                            <?php if (isset($error)): ?>
                                <div class="alert alert-danger"><?php echo $error; ?></div>
                            <?php endif; ?>

                            <form action="tambah_transaksi.php" method="POST">
                                <!-- Customer Information -->
                                <div class="form-group">
                                    <label for="nama">Nama Pelanggan:</label>
                                    <input type="text" name="nama" id="nama" class="form-control" required>
                                </div>

                                <div class="form-group">
                                    <label for="no_telp">No Telepon:</label>
                                    <input type="text" name="no_telp" id="no_telp" class="form-control" required>
                                </div>

                                <div class="form-group">
                                    <label for="tanggal_lahir">Tanggal Lahir:</label>
                                    <input type="date" name="tanggal_lahir" id="tanggal_lahir" class="form-control" required>
                                </div>
                                <!-- Transaction Information -->
                                <div class="form-group">
                                    <label for="tanggal_transaksi">Tanggal Transaksi:</label>
                                    <input type="date" name="tanggal_transaksi" id="tanggal_transaksi" class="form-control" required>
                                </div>

                                <div class="form-group">
                                    <label for="pembayaran">Pembayaran:</label>
                                    <select name="pembayaran" id="pembayaran" class="form-control" required>
                                        <option value="lunas">Lunas</option>
                                        <option value="belum lunas">Belum Lunas</option>
                                    </select>
                                </div>

                                <div class="form-group">
                                    <label for="jenis_layanan">Jenis Layanan:</label><br>
                                    <?php
                                    // Query to fetch available layanan (services)
                                    $query_layanan = "SELECT id_jenis_layanan, jenis_layanan FROM layanan";
                                    $result_layanan = $db->query($query_layanan);

                                    // Loop through and generate checkboxes
                                    while ($row_layanan = $result_layanan->fetch_assoc()) {
                                        echo "<label><input type='checkbox' name='jenis_layanan[]' value='{$row_layanan['jenis_layanan']}'> {$row_layanan['jenis_layanan']}</label><br>";
                                    }
                                    ?>
                                </div>

                                <button type="submit" class="btn btn-primary">Tambah Transaksi</button>
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
