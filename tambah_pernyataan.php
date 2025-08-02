<?php
// Ensure the database connection is available
require __DIR__ . '/include/conn.php';
require __DIR__ . '/include/session_check.php';
require "layout/head.php";

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get the data from the form
    $dimensi_layanan = $_POST['dimensi_layanan'];
    $pernyataan = $_POST['pernyataan'];
    $rekomendasi_perbaikan = $_POST['rekomendasi_perbaikan'];
    $status = $_POST['status']; // The status (whether the statement is active or not)
    
    // Handle multiple jenis_layanan selections
    $jenis_layanan = isset($_POST['jenis_layanan']) ? implode(',', $_POST['jenis_layanan']) : '';  // Store the selected services as a comma-separated string

    // Validate inputs
    if (empty($dimensi_layanan) || empty($pernyataan) || empty($rekomendasi_perbaikan) || empty($status) || empty($jenis_layanan)) {
        $error = "All fields are required!";
    } else {
        // Check if dimensi_layanan is a valid value in ENUM
        $valid_dimensi_layanan = ['reliability', 'assurance', 'tangibles', 'empathy', 'responsiveness'];
        if (!in_array($dimensi_layanan, $valid_dimensi_layanan)) {
            $error = "Invalid Dimensi Layanan value!";
        } else {
            // Insert the new statement into the database
            $query = "INSERT INTO data_pernyataan (dimensi_layanan, pernyataan, rekomendasi_perbaikan, status, jenis_layanan) 
                      VALUES (?, ?, ?, ?, ?)";

            if ($stmt = $db->prepare($query)) {
                // Bind parameters
                $stmt->bind_param("sssss", $dimensi_layanan, $pernyataan, $rekomendasi_perbaikan, $status, $jenis_layanan);

                // Execute the query
                if ($stmt->execute()) {
                    // Success: Redirect to the list page
                    header("Location: data_pernyataan.php");
                    exit();
                } else {
                    // Error: Query failed
                    $error = "Error: Could not execute the query. " . $stmt->error;  // Add detailed error message
                    echo $error;  // Print error for debugging
                }

                // Close the statement
                $stmt->close();
            } else {
                // Error: Query preparation failed
                $error = "Error: Could not prepare the query. " . $db->error;
                echo $error;  // Print error for debugging
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Pernyataan</title>
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
            <h3>Tambah Pernyataan</h3>
        </div>

        <div class="page-content">
            <section class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h4>Formulir Tambah Pernyataan</h4>
                        </div>
                        <div class="card-body">
                            <?php if (isset($error)): ?>
                                <div class="alert alert-danger"><?php echo $error; ?></div>
                            <?php endif; ?>

                            <form action="tambah_pernyataan.php" method="POST">
                                <div class="form-group">
                                    <label for="dimensi_layanan">Dimensi Layanan:</label>
                                    <select name="dimensi_layanan" id="dimensi_layanan" class="form-control" required>
                                        <option value="">Select Dimensi Layanan</option>
                                        <?php
                                        // Query to fetch available dimensi_layanan from servqual table
                                        $query_servqual = "SELECT dimensi_layanan FROM servqual";
                                        $result_servqual = $db->query($query_servqual);

                                        // Loop through and generate options
                                        while ($row_servqual = $result_servqual->fetch_assoc()) {
                                            echo "<option value='{$row_servqual['dimensi_layanan']}'>{$row_servqual['dimensi_layanan']}</option>";
                                        }
                                        ?>
                                    </select>
                                </div>

                                <div class="form-group">
                                    <label for="jenis_layanan">Jenis Layanan:</label><br>
                                    <?php
                                    // Query to fetch available jenis_layanan from layanan table
                                    $query_layanan = "SELECT jenis_layanan FROM layanan";
                                    $result_layanan = $db->query($query_layanan);

                                    // Loop through and generate checkboxes
                                    while ($row_layanan = $result_layanan->fetch_assoc()) {
                                        echo "<label><input type='checkbox' name='jenis_layanan[]' value='{$row_layanan['jenis_layanan']}'> {$row_layanan['jenis_layanan']}</label><br>";
                                    }
                                    ?>
                                </div>

                                <div class="form-group">
                                    <label for="pernyataan">Pernyataan:</label>
                                    <textarea name="pernyataan" id="pernyataan" class="form-control" required></textarea>
                                </div>

                                <div class="form-group">
                                    <label for="rekomendasi_perbaikan">Rekomendasi Perbaikan:</label>
                                    <textarea name="rekomendasi_perbaikan" id="rekomendasi_perbaikan" class="form-control" required></textarea>
                                </div>

                                <!-- Status field (Active/Inactive) -->
                                <div class="form-group">
                                    <label for="status">Status:</label>
                                    <select name="status" id="status" class="form-control" required>
                                        <option value="aktif">Aktif</option>
                                        <option value="tidak aktif">Tidak Aktif</option>
                                    </select>
                                </div>

                                <!-- Submit Button -->
                                <button type="submit" class="btn btn-primary">Tambah Pernyataan</button>
                                
                                <!-- Cancel Button (Batal) -->
                                <button type="button" class="btn btn-secondary" onclick="window.location.href='data_pernyataan.php'">Batal</button>
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