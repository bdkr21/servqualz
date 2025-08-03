<?php
// Ensure the database connection is available
require __DIR__ . '/include/conn.php';
require __DIR__ . '/include/session_check.php';
require "layout/head.php";

// Get the ID of the statement to edit
if (isset($_GET['id'])) {
    $id_data_pernyataan = $_GET['id'];

    // Query to fetch the data from 'data_pernyataan' table
    $query = "SELECT * FROM data_pernyataan WHERE id_data_pernyataan = ?";
    if ($stmt = $db->prepare($query)) {
        $stmt->bind_param("i", $id_data_pernyataan);
        $stmt->execute();
        $result = $stmt->get_result();
        $pernyataan = $result->fetch_assoc();
    }
}

// Handle the form submission for updating the record
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $dimensi_layanan = $_POST['dimensi_layanan'];
    $pernyataan_text = $_POST['pernyataan'];
    $rekomendasi_perbaikan = $_POST['rekomendasi_perbaikan'];
    $status = $_POST['status'];
    $jenis_layanan = implode(',', $_POST['jenis_layanan']); // Combine selected checkboxes into a string

    // Update the data in the database
    $query_update = "UPDATE data_pernyataan SET dimensi_layanan = ?, pernyataan = ?, rekomendasi_perbaikan = ?, status = ?, jenis_layanan = ? WHERE id_data_pernyataan = ?";

    if ($stmt_update = $db->prepare($query_update)) {
        $stmt_update->bind_param("sssssi", $dimensi_layanan, $pernyataan_text, $rekomendasi_perbaikan, $status, $jenis_layanan, $id_data_pernyataan);

        if ($stmt_update->execute()) {
            // Redirect to the data_pernyataan page
            header("Location: data_pernyataan.php");
            exit();
        } else {
            $error = "Error: Could not update the record.";
        }

        $stmt_update->close();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Pernyataan</title>
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
            <h3>Edit Pernyataan</h3>
        </div>

        <div class="page-content">
            <section class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h4>Formulir Edit Pernyataan</h4>
                        </div>
                        <div class="card-body">
                            <?php if (isset($error)): ?>
                                <div class="alert alert-danger"><?php echo $error; ?></div>
                            <?php endif; ?>

                            <form action="edit_pernyataan.php?id=<?php echo $id_data_pernyataan; ?>" method="POST">
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
                                            // Check if the option is the current value from the database
                                            $selected = ($row_servqual['dimensi_layanan'] == $pernyataan['dimensi_layanan']) ? 'selected' : '';
                                            echo "<option value='{$row_servqual['dimensi_layanan']}' $selected>{$row_servqual['dimensi_layanan']}</option>";
                                        }
                                        ?>
                                    </select>
                                </div>

                                <div class="form-group">
                                    <label for="pernyataan">Pernyataan:</label>
                                    <textarea name="pernyataan" id="pernyataan" class="form-control" required><?php echo $pernyataan['pernyataan']; ?></textarea>
                                </div>

                                <div class="form-group">
                                    <label for="rekomendasi_perbaikan">Rekomendasi Perbaikan:</label>
                                    <textarea name="rekomendasi_perbaikan" id="rekomendasi_perbaikan" class="form-control" required><?php echo $pernyataan['rekomendasi_perbaikan']; ?></textarea>
                                </div>

                                <div class="form-group">
                                    <label for="status">Status:</label>
                                    <select name="status" id="status" class="form-control" required>
                                        <option value="aktif" <?php echo ($pernyataan['status'] == 'aktif') ? 'selected' : ''; ?>>Aktif</option>
                                        <option value="tidak aktif" <?php echo ($pernyataan['status'] == 'tidak aktif') ? 'selected' : ''; ?>>Tidak Aktif</option>
                                    </select>
                                </div>

                                <div class="form-group">
                                    <label for="jenis_layanan">Jenis Layanan:</label><br>
                                    <?php
                                    // Query to fetch available jenis_layanan from layanan table
                                    $query_layanan = "SELECT jenis_layanan FROM layanan";
                                    $result_layanan = $db->query($query_layanan);

                                    // Loop through and generate checkboxes with pre-selected ones
                                    $selected_layanan = explode(',', $pernyataan['jenis_layanan']); // Get selected layanan values as an array
                                    while ($row_layanan = $result_layanan->fetch_assoc()) {
                                        $checked = in_array($row_layanan['jenis_layanan'], $selected_layanan) ? 'checked' : '';
                                        echo "<label><input type='checkbox' name='jenis_layanan[]' value='{$row_layanan['jenis_layanan']}' $checked> {$row_layanan['jenis_layanan']}</label><br>";
                                    }
                                    ?>
                                </div>

                                <button type="submit" class="btn btn-primary">Update Pernyataan</button>
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
