<?php
// Ensure the database connection is available
require __DIR__ . '/include/conn.php';
require __DIR__ . '/include/session_check.php';
require "layout/head.php";
// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get the data from the form
    $id_transaksi = $_POST['id_transaksi'];
    $id_pelanggan = $_POST['id_pelanggan'];
    $tanggal_transaksi = $_POST['tanggal_transaksi'];
    $pembayaran = $_POST['pembayaran'];
    
    // Get the selected services from the checkboxes
    $selected_services = isset($_POST['jenis_layanan']) ? implode(',', $_POST['jenis_layanan']) : '';

    // Check if any of the fields are empty
    if (empty($id_pelanggan) || empty($tanggal_transaksi) || empty($pembayaran) || empty($selected_services)) {
        $error = "All fields are required!";
    } else {
        // Prepare the UPDATE query
        $query = "UPDATE transaksi 
                  SET id_pelanggan = ?, tanggal_transaksi = ?, pembayaran = ?, jenis_layanan = ? 
                  WHERE id_transaksi = ?";

        // Prepare the statement
        if ($stmt = $db->prepare($query)) {
            // Bind parameters
            $stmt->bind_param("isssi", $id_pelanggan, $tanggal_transaksi, $pembayaran, $selected_services, $id_transaksi);

            // Execute the query
            if ($stmt->execute()) {
                // Success: Redirect to the transaksi data page
                header("Location: data_transaksi.php");
                exit();
            } else {
                // Error: Query failed
                $error = "Error: Could not execute the query.";
            }

            // Close the statement
            $stmt->close();
        } else {
            // Error: Query preparation failed
            $error = "Error: Could not prepare the query.";
        }
    }
}

// Fetch the current transaction data based on id_transaksi
if (isset($_GET['id'])) {
    $id_transaksi = $_GET['id'];
    $query = "SELECT * FROM transaksi WHERE id_transaksi = ?";
    if ($stmt = $db->prepare($query)) {
        $stmt->bind_param("i", $id_transaksi);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows > 0) {
            $transaction = $result->fetch_assoc();
        } else {
            // Transaction not found
            echo "Transaction not found.";
            exit();
        }
    } else {
        // Error preparing the query
        echo "Error: Could not prepare the query.";
        exit();
    }
} else {
    // If no ID is passed
    echo "Transaction ID is missing.";
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Transaksi</title>
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
            <h3>Edit Transaksi</h3>
        </div>

        <div class="page-content">
            <section class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h4>Formulir Edit Transaksi</h4>
                        </div>
                        <div class="card-body">
                            <?php if (isset($error)): ?>
                                <div class="alert alert-danger"><?php echo $error; ?></div>
                            <?php endif; ?>

                            <form action="edit_transaksi.php" method="POST">
                                <input type="hidden" name="id_transaksi" value="<?php echo $transaction['id_transaksi']; ?>">

                                <div class="form-group">
                                    <label for="id_pelanggan">Pelanggan:</label>
                                    <select name="id_pelanggan" id="id_pelanggan" class="form-control" required>
                                        <option value="">Select Pelanggan</option>
                                        <?php
                                        // Query to fetch all pelanggan
                                        $query_pelanggan = "SELECT id_pelanggan, nama FROM pelanggan";
                                        $result_pelanggan = $db->query($query_pelanggan);

                                        // Loop through and generate options
                                        while ($row_pelanggan = $result_pelanggan->fetch_assoc()) {
                                            $selected = ($row_pelanggan['id_pelanggan'] == $transaction['id_pelanggan']) ? 'selected' : '';
                                            echo "<option value='{$row_pelanggan['id_pelanggan']}' {$selected}>{$row_pelanggan['nama']}</option>";
                                        }
                                        ?>
                                    </select>
                                </div>

                                <div class="form-group">
                                    <label for="tanggal_transaksi">Tanggal Transaksi:</label>
                                    <input type="date" name="tanggal_transaksi" id="tanggal_transaksi" class="form-control" value="<?php echo $transaction['tanggal_transaksi']; ?>" required>
                                </div>

                                <div class="form-group">
                                    <label for="pembayaran">Pembayaran:</label>
                                    <select name="pembayaran" id="pembayaran" class="form-control" required>
                                        <option value="lunas" <?php echo ($transaction['pembayaran'] == 'lunas') ? 'selected' : ''; ?>>Lunas</option>
                                        <option value="belum lunas" <?php echo ($transaction['pembayaran'] == 'belum lunas') ? 'selected' : ''; ?>>Belum Lunas</option>
                                    </select>
                                </div>

                                <div class="form-group">
                                    <label for="jenis_layanan">Jenis Layanan:</label><br>
                                    <?php
                                    $services = ['Service A', 'Service B', 'Service C'];  // Add the available services here
                                    $selected_services = explode(',', $transaction['jenis_layanan']);  // Split the comma-separated string into an array
                                    foreach ($services as $service) {
                                        $checked = in_array($service, $selected_services) ? 'checked' : '';
                                        echo "<label><input type='checkbox' name='jenis_layanan[]' value='$service' $checked> $service</label><br>";
                                    }
                                    ?>
                                </div>

                                <button type="submit" class="btn btn-primary">Update Transaksi</button>
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
