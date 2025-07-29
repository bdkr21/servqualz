<?php
// Ensure the database connection is available
require __DIR__ . '/include/conn.php';
require __DIR__ . '/include/session_check.php';
require "layout/head.php";

// Function to generate the next transaction code (TR001, TR002, etc.)
function generateKodeTransaksi() {
    global $db;

    // Query to get the latest kode_transaksi from the table
    $query = "SELECT kode_transaksi FROM transaksi ORDER BY id_transaksi DESC LIMIT 1";
    $result = $db->query($query);

    // If there's a result, extract the number and increment it
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $last_code = $row['kode_transaksi'];

        // Extract the numeric part after 'TR'
        $last_number = (int)substr($last_code, 2); // Remove 'TR' and cast to integer
        $next_number = $last_number + 1; // Increment the number
    } else {
        // If no previous code exists, start with 1
        $next_number = 1;
    }

    // Format the number with leading zeros (e.g., TR001, TR002, etc.)
    $formatted_number = str_pad($next_number, 3, '0', STR_PAD_LEFT);

    // Return the new transaction code
    return 'TR' . $formatted_number;
}

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get the data from the form
    $nama = $_POST['nama'];  // Added field for customer name
    $no_telp = $_POST['no_telp'];
    $tanggal_lahir = $_POST['tanggal_lahir'];
    
    // Get the selected services from the checkboxes
    $selected_services = isset($_POST['jenis_layanan']) ? implode(',', $_POST['jenis_layanan']) : ''; // jenis_layanan
    
    // For `pelanggan`, pass the selected `jenis_layanan` as well
    $jenis_layanan_pelanggan = $selected_services; // Make sure `jenis_layanan_pelanggan` gets the same value
    
    $tanggal_transaksi = $_POST['tanggal_transaksi'];
    $pembayaran = $_POST['pembayaran'];

    // Check if any of the fields are empty
    if (empty($nama) || empty($no_telp) || empty($tanggal_lahir) || empty($tanggal_transaksi) || empty($pembayaran) || empty($selected_services)) {
        $error = "All fields are required!";
    } else {
        // Step 1: Insert the customer (pelanggan) data
        $query_pelanggan = "INSERT INTO pelanggan (nama, no_telp, tanggal_lahir, jenis_layanan) VALUES (?, ?, ?, ?)";

        if ($stmt_pelanggan = $db->prepare($query_pelanggan)) {
            $stmt_pelanggan->bind_param("ssss", $nama, $no_telp, $tanggal_lahir, $jenis_layanan_pelanggan);

            // Execute the customer insertion
            if ($stmt_pelanggan->execute()) {
                // Get the customer ID of the newly inserted record
                $id_pelanggan = $stmt_pelanggan->insert_id;

                // Step 2: Generate the next kode_transaksi (TR001, TR002, etc.)
                $kode_transaksi = generateKodeTransaksi();

                // Step 3: Insert the transaction data (transaksi) with the selected services
                $query_transaksi = "INSERT INTO transaksi (id_pelanggan, tanggal_transaksi, pembayaran, jenis_layanan, kode_transaksi) VALUES (?, ?, ?, ?, ?)";

                if ($stmt_transaksi = $db->prepare($query_transaksi)) {
                    $stmt_transaksi->bind_param("issss", $id_pelanggan, $tanggal_transaksi, $pembayaran, $selected_services, $kode_transaksi);

                    // Execute the transaction insertion
                    if ($stmt_transaksi->execute()) {
                        // Send WhatsApp message using Whapify API
                        $whatsapp_api_url = "https://whapify.id/api/send/whatsapp";
                        $whatsapp_data = [
                            "secret" => "daf42e9b8914337a175eb2419e560f4a3d74a131", // Replace with your API secret
                            "account" => "1753812583fa83a11a198d5a7f0bf77a1987bcd00668890e679d008", // Replace with your unique account ID
                            "recipient" => $no_telp, // Customer phone number
                            "type" => "text",
                            "message" => "Terima kasih telah melakukan transaksi di HETTIE PROFESSIONAL HAIRSTYLIST! Kode transaksi Anda adalah: *" . $kode_transaksi . "*.\n\n" .
                            "Kode transaksi ini dapat digunakan untuk mengisi **kuesioner** dan memberikan **keluhan** melalui website kami. " .
                            "Silakan gunakan kode transaksi ini pada halaman yang sesuai untuk memberikan masukan dan feedback tentang layanan kami.\n\n" .
                            "Kami menghargai partisipasi Anda dalam membantu kami meningkatkan kualitas layanan kami. https://servqualz.chandramlnh.my.id/index_pelanggan.php\n\n" .
                            "Jika ada pertanyaan, jangan ragu untuk menghubungi kami."
                        ];

                        // Initialize cURL session to send the message
                        $ch = curl_init();
                        curl_setopt($ch, CURLOPT_URL, $whatsapp_api_url);
                        curl_setopt($ch, CURLOPT_POST, true);
                        curl_setopt($ch, CURLOPT_POSTFIELDS, $whatsapp_data);
                        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

                        // Execute the cURL request
                        $response = curl_exec($ch);
                        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);

                        // Handle the response
                        if ($http_code === 200) {
                            // Success: Redirect to the transaksi data page
                            header("Location: data_transaksi.php");
                            exit();
                        } else {
                            echo "Error sending WhatsApp message: HTTP Code " . $http_code . ", Response: " . $response;
                        }

                        // Close cURL session
                        curl_close($ch);
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
    <script>
        // Function to format the phone number input as 0812-345-678
        function formatPhoneNumber(input) {
            // Remove non-numeric characters
            let value = input.value.replace(/\D/g, '');

            // Format phone number as 0812-345-678
            if (value.length > 4) {
                value = value.replace(/(\d{4})(\d{3})(\d{3})/, '$1-$2-$3');
            } else if (value.length > 3) {
                value = value.replace(/(\d{4})(\d{3})/, '$1-$2');
            }

            // Set the formatted value back to the input field
            input.value = value;
        }
    </script>
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
                                    <input type="text" name="no_telp" id="no_telp" class="form-control" oninput="formatPhoneNumber(this)" required>
                                </div>

                                <div class="form-group">
                                    <label for="tanggal_lahir">Tanggal Lahir:</label>
                                    <input type="text" name="tanggal_lahir" id="tanggal_lahir" class="form-control" required>
                                </div>

                                <div class="form-group">
                                    <label for="tanggal_transaksi">Tanggal Transaksi:</label>
                                    <input type="text" name="tanggal_transaksi" id="tanggal_transaksi" class="form-control" required>
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
                                <button type="button" class="btn btn-secondary" onclick="window.location.href='data_transaksi.php'">Batal</button>
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
