<?php
// Ensure the database connection is available
require __DIR__ . '/include/conn.php';
require __DIR__ . '/include/session_check.php';
require "layout/head.php";

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get the data from the form
    $nama_kuesioner = $_POST['nama_kuesioner'];
    $status = $_POST['status']; // The status (whether the kuesioner is active or not)
    
    // Handle the selected jenis_layanan
    $jenis_layanan = isset($_POST['jenis_layanan']) ? implode(',', $_POST['jenis_layanan']) : '';  // Store the selected services as a comma-separated string

    // Check if the dimensi_layanan is selected
    $dimensi_layanan = isset($_POST['dimensi_layanan']) ? $_POST['dimensi_layanan'] : ''; // Assume dimensi_layanan is passed in the form

    // Validate inputs
    if (empty($nama_kuesioner) || empty($status) || empty($jenis_layanan) || empty($dimensi_layanan)) {
        $error = "All fields are required!";
    } else {
        // Insert the new kuesioner into the database
        $query = "INSERT INTO data_kuesioner (nama_kuesioner, status, jenis_layanan, dimensi_layanan) 
                  VALUES (?, ?, ?, ?)";

        if ($stmt = $db->prepare($query)) {
            // Bind parameters
            $stmt->bind_param("ssss", $nama_kuesioner, $status, $jenis_layanan, $dimensi_layanan);

            // Execute the query
            if ($stmt->execute()) {
                // Success: Redirect to the kuesioner data page
                header("Location: data_kuesioner.php");
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

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Kuesioner</title>
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
            <h3>Tambah Kuesioner</h3>
        </div>

        <div class="page-content">
            <section class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h4>Formulir Tambah Kuesioner</h4>
                        </div>
                        <div class="card-body">
                            <?php if (isset($error)): ?>
                                <div class="alert alert-danger"><?php echo $error; ?></div>
                            <?php endif; ?>

                            <form action="tambah_kuesioner.php" method="POST">
                                <div class="form-group">
                                    <label for="nama_kuesioner">Nama Kuesioner:</label>
                                    <input type="text" name="nama_kuesioner" id="nama_kuesioner" class="form-control" required>
                                </div>

                                <div class="form-group">
                                    <label for="dimensi_layanan">Dimensi Layanan:</label>
                                    <select name="dimensi_layanan" id="dimensi_layanan" class="form-control" required>
                                        <option value="">Select Dimensi Layanan</option>
                                        <?php
                                        // Query to fetch available dimensi_layanan and their corresponding ids from servqual table
                                        $query_servqual = "SELECT id_servqual, dimensi_layanan FROM servqual";
                                        $result_servqual = $db->query($query_servqual);

                                        // Loop through and generate options
                                        while ($row_servqual = $result_servqual->fetch_assoc()) {
                                            echo "<option value='{$row_servqual['id_servqual']}'>{$row_servqual['dimensi_layanan']}</option>";
                                        }
                                        ?>
                                    </select>
                                </div>

                                <div class="form-group">
                                    <label for="jenis_layanan">Jenis Layanan:</label><br>
                                    <?php
                                    // Query to fetch available jenis_layanan from layanan table (use ids for the checkboxes)
                                    $query_layanan = "SELECT id_jenis_layanan, jenis_layanan FROM layanan";
                                    $result_layanan = $db->query($query_layanan);

                                    // Loop through and generate checkboxes with ids
                                    while ($row_layanan = $result_layanan->fetch_assoc()) {
                                        echo "<label><input type='checkbox' name='jenis_layanan[]' value='{$row_layanan['id_jenis_layanan']}'> {$row_layanan['jenis_layanan']}</label><br>";
                                    }
                                    ?>
                                </div>

                                <div class="form-group">
                                    <label for="status">Status:</label>
                                    <select name="status" id="status" class="form-control" required>
                                        <option value="tidak publish" selected>Tidak Publish</option>
                                        <option value="publish">Publish</option>
                                        <option value="selesai">Selesai</option>
                                    </select>
                                </div>

                                <button type="submit" class="btn btn-primary">Tambah Kuesioner</button>
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
