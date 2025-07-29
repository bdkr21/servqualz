<?php
// Ensure the database connection is available
require __DIR__ . '/include/conn.php';
require __DIR__ . '/include/session_check.php';
require "layout/head.php";
// Get the ID of the data_kuesioner to edit
if (isset($_GET['id'])) {
    $id_data_kuesioner = $_GET['id'];

    // Query to fetch the data from 'data_kuesioner' table
    $query = "SELECT * FROM data_kuesioner WHERE id_data_kuesioner = ?";
    if ($stmt = $db->prepare($query)) {
        $stmt->bind_param("i", $id_data_kuesioner);
        $stmt->execute();
        $result = $stmt->get_result();
        $kuesioner = $result->fetch_assoc();
    }
}

// Handle the form submission for updating the record
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nama_kuesioner = $_POST['nama_kuesioner'];
    $dimensi_layanan = $_POST['dimensi_layanan'];
    $status = $_POST['status'];

    // Update the data in the database
    $query_update = "UPDATE data_kuesioner SET nama_kuesioner = ?, dimensi_layanan = ?, status = ? WHERE id_data_kuesioner = ?";

    if ($stmt_update = $db->prepare($query_update)) {
        $stmt_update->bind_param("sssi", $nama_kuesioner, $dimensi_layanan, $status, $id_data_kuesioner);

        if ($stmt_update->execute()) {
            header("Location: data_kuesioner.php"); // Redirect to the data_kuesioner page
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
    <title>Edit Kuesioner</title>
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
            <h3>Edit Kuesioner</h3>
        </div>

        <div class="page-content">
            <section class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h4>Formulir Edit Kuesioner</h4>
                        </div>
                        <div class="card-body">
                            <?php if (isset($error)): ?>
                                <div class="alert alert-danger"><?php echo $error; ?></div>
                            <?php endif; ?>

                            <form action="edit_kuesioner.php?id=<?php echo $id_data_kuesioner; ?>" method="POST">
                                <div class="form-group">
                                    <label for="nama_kuesioner">Nama Kuesioner:</label>
                                    <input type="text" name="nama_kuesioner" id="nama_kuesioner" class="form-control" value="<?php echo $kuesioner['nama_kuesioner']; ?>" required>
                                </div>

                                <div class="form-group">
                                    <label for="dimensi_layanan">Dimensi Layanan:</label>
                                    <select name="dimensi_layanan" id="dimensi_layanan" class="form-control" required>
                                        <option value="">Select Dimensi Layanan</option>
                                        <?php
                                        // Query to fetch dimensi_layanan from servqual table
                                        $query_servqual = "SELECT dimensi_layanan FROM servqual";
                                        $result_servqual = $db->query($query_servqual);

                                        // Loop through and generate options
                                        while ($row_servqual = $result_servqual->fetch_assoc()) {
                                            echo "<option value='{$row_servqual['dimensi_layanan']}' " . ($kuesioner['dimensi_layanan'] == $row_servqual['dimensi_layanan'] ? 'selected' : '') . ">{$row_servqual['dimensi_layanan']}</option>";
                                        }
                                        ?>
                                    </select>
                                </div>

                                <div class="form-group">
                                    <label for="status">Status:</label>
                                    <select name="status" id="status" class="form-control" required>
                                        <option value="aktif" <?php echo ($kuesioner['status'] == 'aktif') ? 'selected' : ''; ?>>Aktif</option>
                                        <option value="tidak aktif" <?php echo ($kuesioner['status'] == 'tidak aktif') ? 'selected' : ''; ?>>Tidak Aktif</option>
                                    </select>
                                </div>

                                <button type="submit" class="btn btn-primary">Update Kuesioner</button>
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
