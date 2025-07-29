<?php
// Ensure the database connection is available
require __DIR__ . '/include/conn.php';
require "layout/head.php";
// Get the ID of the customer to edit
if (isset($_GET['id'])) {
    $id_pelanggan = $_GET['id'];

    // Query to fetch the customer data
    $query = "SELECT * FROM pelanggan WHERE id_pelanggan = ?";
    if ($stmt = $db->prepare($query)) {
        $stmt->bind_param("i", $id_pelanggan);
        $stmt->execute();
        $result = $stmt->get_result();
        $customer = $result->fetch_assoc();
    }
}

// Handle the form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['name'];
    $no_telp = $_POST['no_telp'];
    $tanggal_lahir = $_POST['tanggal_lahir'];

    // Check if any of the fields are empty
    if (empty($name) || empty($no_telp) || empty($tanggal_lahir)) {
        $error = "All fields are required!";
    } else {
        // Update the customer data
        $query_update = "UPDATE pelanggan SET nama = ?, no_telp = ?, tanggal_lahir = ? WHERE id_pelanggan = ?";
        
        if ($stmt_update = $db->prepare($query_update)) {
            $stmt_update->bind_param("sssi", $name, $no_telp, $tanggal_lahir, $id_pelanggan);

            if ($stmt_update->execute()) {
                header("Location: data_pelanggan.php"); // Redirect to the customer data page
                exit();
            } else {
                $error = "Error: Could not update customer data.";
            }

            $stmt_update->close();
        } else {
            $error = "Error: Could not prepare the update query.";
        }
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Pelanggan</title>
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
            <h3>Edit Pelanggan</h3>
        </div>

        <div class="page-content">
            <section class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h4>Formulir Edit Pelanggan</h4>
                        </div>
                        <div class="card-body">
                            <?php if (isset($error)): ?>
                                <div class="alert alert-danger"><?php echo $error; ?></div>
                            <?php endif; ?>

                            <form action="edit_pelanggan.php?id=<?php echo $id_pelanggan; ?>" method="POST">
                                <div class="form-group">
                                    <label for="name">name Pelanggan:</label>
                                    <input type="text" name="name" id="name" class="form-control" value="<?php echo $customer['nama']; ?>" required>
                                </div>

                                <div class="form-group">
                                    <label for="no_telp">No Telepon:</label>
                                    <input type="text" name="no_telp" id="no_telp" class="form-control" value="<?php echo $customer['no_telp']; ?>" required>
                                </div>

                                <div class="form-group">
                                    <label for="tanggal_lahir">Tanggal Lahir:</label>
                                    <input type="date" name="tanggal_lahir" id="tanggal_lahir" class="form-control" value="<?php echo $customer['tanggal_lahir']; ?>" required>
                                </div>

                                <button type="submit" class="btn btn-primary">Update Pelanggan</button>
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
