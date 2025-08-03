<?php
// Ensure the database connection is available
require __DIR__ . '/include/conn.php';
require __DIR__ . '/include/session_check.php';

// Handle AJAX request FIRST before any output
if (isset($_GET['ajax']) && $_GET['ajax'] == 'get_pernyataan' && isset($_GET['jenis_layanan'])) {
    try {
        // Ensure no output has been sent
        if (headers_sent()) {
            throw new Exception('Headers already sent');
        }

        $jenis_layanan = $_GET['jenis_layanan'];

        // Query to get data pernyataan based on jenis_layanan
        $query_pernyataan = "SELECT * FROM data_pernyataan WHERE FIND_IN_SET(?, jenis_layanan)";
        $stmt_pernyataan = $db->prepare($query_pernyataan);
        $stmt_pernyataan->bind_param("s", $jenis_layanan);
        if (!$stmt_pernyataan->execute()) {
            throw new Exception('Execute failed: ' . $stmt_pernyataan->error);
        }

        $result_pernyataan = $stmt_pernyataan->get_result();
        $pernyataan_data = [];

        while ($row = $result_pernyataan->fetch_assoc()) {
            $pernyataan_data[] = $row;
        }

        // Send JSON response
        header('Content-Type: application/json');
        echo json_encode([
            'success' => true,
            'data' => $pernyataan_data
        ]);
        exit(); // Stop further script execution to prevent any additional output
    } catch (Exception $e) {
        // If an error occurs, return the error message in JSON format
        header('Content-Type: application/json');
        http_response_code(500); // Internal Server Error
        echo json_encode([
            'success' => false,
            'message' => 'Error: ' . $e->getMessage()
        ]);
        exit();
    }
}

// Regular page load, not an AJAX request - Include the head.php
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

        // Get the jenis_layanan from the selected kuesioner
        $jenis_layanan = $kuesioner['jenis_layanan'];
    }
}

// Query to get initial data pernyataan
$query_pernyataan = "SELECT * FROM data_pernyataan WHERE FIND_IN_SET(?, jenis_layanan)";
$stmt_pernyataan = $db->prepare($query_pernyataan);
$stmt_pernyataan->bind_param("s", $jenis_layanan);
$stmt_pernyataan->execute();
$result_pernyataan = $stmt_pernyataan->get_result();

// Get the ENUM values for the status field in data_kuesioner
$query_enum_status = "DESCRIBE data_kuesioner status";
$result_enum_status = $db->query($query_enum_status);
$row_enum_status = $result_enum_status->fetch_assoc();
$enum_values = $row_enum_status['Type'];

// Extract the ENUM values from the string
preg_match_all("/'([^']+)'/", $enum_values, $matches);
$status_values = $matches[1];

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nama_kuesioner = $_POST['nama_kuesioner'];
    $dimensi_layanan = $_POST['dimensi_layanan'];
    $status = $_POST['status'];
    $jenis_layanan = $_POST['jenis_layanan'];

    if (empty($dimensi_layanan) || empty($nama_kuesioner) || empty($status) || empty($jenis_layanan)) {
        $error = "All fields are required!";
    } else {
        $query_update = "UPDATE data_kuesioner SET nama_kuesioner = ?, dimensi_layanan = ?, status = ?, jenis_layanan = ? WHERE id_data_kuesioner = ?";
        
        if ($stmt_update = $db->prepare($query_update)) {
            $stmt_update->bind_param("ssssi", $nama_kuesioner, $dimensi_layanan, $status, $jenis_layanan, $id_data_kuesioner);
            
            if ($stmt_update->execute()) {
                header("Location: data_kuesioner.php");
                exit();
            } else {
                $error = "Error: Could not update the record. " . $stmt_update->error;
            }

            $stmt_update->close();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Kuesioner</title>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
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
                                        $query_servqual = "SELECT dimensi_layanan FROM servqual";
                                        $result_servqual = $db->query($query_servqual);
                                        while ($row_servqual = $result_servqual->fetch_assoc()) {
                                            echo "<option value='{$row_servqual['dimensi_layanan']}' " . ($kuesioner['dimensi_layanan'] == $row_servqual['dimensi_layanan'] ? 'selected' : '') . ">{$row_servqual['dimensi_layanan']}</option>";
                                        }
                                        ?>
                                    </select>
                                </div>

                                <div class="form-group">
                                    <label for="status">Status:</label>
                                    <select name="status" id="status" class="form-control" required>
                                        <?php
                                        foreach ($status_values as $status_value) {
                                            echo "<option value='{$status_value}' " . ($kuesioner['status'] == $status_value ? 'selected' : '') . ">{$status_value}</option>";
                                        }
                                        ?>
                                    </select>
                                </div>

                                <div class="form-group">
                                    <label for="jenis_layanan">Jenis Layanan:</label>
                                    <select name="jenis_layanan" id="jenis_layanan" class="form-control" required>
                                        <option value="">Select Jenis Layanan</option>
                                        <?php
                                        $query_layanan = "SELECT jenis_layanan FROM layanan";
                                        $result_layanan = $db->query($query_layanan);
                                        while ($row_layanan = $result_layanan->fetch_assoc()) {
                                            $selected = ($kuesioner['jenis_layanan'] == $row_layanan['jenis_layanan']) ? 'selected' : '';
                                            echo "<option value='{$row_layanan['jenis_layanan']}' {$selected}>{$row_layanan['jenis_layanan']}</option>";
                                        }
                                        ?>
                                    </select>
                                </div>

                                <button type="submit" class="btn btn-primary">Update Kuesioner</button>
                                <button type="button" class="btn btn-secondary" onclick="window.location.href='data_kuesioner.php'">Batal</button>
                            </form>

                            <h5 class="mt-4">Data Pernyataan Sesuai dengan Jenis Layanan:</h5>
                            <table id="tablePernyataan" class="table">
                                <thead>
                                    <tr>
                                        <th>Dimensi Layanan</th>
                                        <th>Pernyataan</th>
                                        <th>Rekomendasi Perbaikan</th>
                                        <th>Status</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if ($result_pernyataan->num_rows === 0): ?>
                                        <tr>
                                            <td colspan="4" class="text-center">Pilih jenis layanan untuk melihat pernyataan</td>
                                        </tr>
                                    <?php else: ?>
                                        <?php while ($row_pernyataan = $result_pernyataan->fetch_assoc()): ?>
                                            <tr>
                                                <td><?= $row_pernyataan['dimensi_layanan'] ?></td>
                                                <td><?= $row_pernyataan['pernyataan'] ?></td>
                                                <td><?= $row_pernyataan['rekomendasi_perbaikan'] ?></td>
                                                <td><?= $row_pernyataan['status'] ?></td>
                                                <td>
                                                    <button class="btn btn-primary btn-sm edit-btn" data-id="<?= $row_pernyataan['id_data_pernyataan'] ?>">Edit</button>
                                                    <button class="btn btn-danger btn-sm delete-btn" data-id="<?= $row_pernyataan['id_data_pernyataan'] ?>">Delete</button>
                                                </td>
                                            </tr>
                                        <?php endwhile; ?>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </section>
        </div>
    </div>
</div>
<script>
$(document).ready(function() {
    $('#jenis_layanan').change(function() {
        var jenisLayanan = $(this).val();
        var tbody = $('#tablePernyataan tbody');
        
        if (!jenisLayanan) {
            tbody.html('<tr><td colspan="4" class="text-center">Pilih jenis layanan untuk melihat pernyataan</td></tr>');
            return;
        }

        // Show loading indicator
        tbody.html('<tr><td colspan="4" class="text-center"><i class="fas fa-spinner fa-spin"></i> Memuat data...</td></tr>');

        $.ajax({
            url: 'edit_kuesioner.php', // Use relative path
            type: 'GET',
            data: {
                ajax: 'get_pernyataan',
                jenis_layanan: jenisLayanan
            },
            dataType: 'json',
            success: function(response) {
                if (response && response.success) {
                    if (response.data && response.data.length > 0) {
                        var rows = '';
                        $.each(response.data, function(index, item) {
                            rows += '<tr>' +
                                   '<td>' + (item.dimensi_layanan || '') + '</td>' +
                                   '<td>' + (item.pernyataan || '') + '</td>' +
                                   '<td>' + (item.rekomendasi_perbaikan || '-') + '</td>' +
                                   '<td>' + (item.status || '-') + '</td>' +
                                   '</tr>';
                        });
                        tbody.html(rows);
                    } else {
                        tbody.html('<tr><td colspan="4" class="text-center">Tidak ada pernyataan untuk jenis layanan ini</td></tr>');
                    }
                } else {
                    var errorMsg = response && response.message ? response.message : 'Invalid server response';
                    tbody.html('<tr><td colspan="4" class="text-center">Error: ' + errorMsg + '</td></tr>');
                }
            },
            error: function(xhr, status, error) {
                console.error('AJAX Error:', status, error);
                console.log('Raw Response:', xhr.responseText); // Debug the raw response
                var errorMsg = 'Gagal memuat data. ';
                
                // Try to parse the response if possible
                try {
                    var response = JSON.parse(xhr.responseText);
                    if (response && response.message) {
                        errorMsg += response.message;
                    } else {
                        errorMsg += xhr.responseText.substring(0, 100);
                    }
                } catch (e) {
                    errorMsg += error;
                }
                
                tbody.html('<tr><td colspan="4" class="text-center">' + errorMsg + '</td></tr>');
            }
        });
    });
});

$(document).ready(function() {
    // Handle the Edit button click event
    $(".edit-btn").click(function() {
        var id = $(this).data('id');
        window.location.href = 'edit_pernyataan.php?id=' + id;  // Redirect to the edit page
    });
    // Handle the Delete button click event
    $(".delete-btn").click(function() {
        var id = $(this).data('id');
        
        // Confirm the delete action
        if (confirm("Are you sure you want to delete this record?")) {
            $.ajax({
                url: 'delete_pernyataan.php',  // URL of the delete script
                type: 'GET',
                data: { id: id },  // Send the id to be deleted
                dataType: 'json',  // Expect JSON response
                success: function(response) {
                    if (response.success) {
                        alert("Record deleted successfully!");  // Show success message
                        location.reload();  // Reload the page to reflect changes
                    } else {
                        alert("Error deleting record: " + response.message);  // Show error message
                    }
                },
                error: function(xhr, status, error) {
                    alert("An error occurred: " + error);  // Show AJAX error message
                }
            });
        }
    });
});

</script>
<?php require "layout/js.php"; ?>
</body>
</html>
