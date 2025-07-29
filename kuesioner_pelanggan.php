<?php
// Ensure the database connection is available
require __DIR__ . '/include/conn.php';

// Get the id_transaksi from the URL
if (isset($_GET['id_transaksi'])) {
    $id_transaksi = $_GET['id_transaksi'];

    // Query to fetch only active statements (pernyataan) for the given transaction
    $query_pernyataan = "SELECT * FROM data_pernyataan WHERE status = 'aktif'";
    $result_pernyataan = $db->query($query_pernyataan);

    // Check if the form is submitted
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        // First, insert the kuesioner (questionnaire) into the `kuesioner` table
        $id_data_kuesioner = 1; // Assuming you want to use a static kuesioner ID or fetch dynamically
        $tgl_pengisian = date('Y-m-d H:i:s'); // Get the current timestamp
        $id_pelanggan = $_GET['id_transaksi']; // Assuming you want to link to the current transaction

        // Insert into `kuesioner` table
        $query_insert_kuesioner = "INSERT INTO kuesioner (id_data_kuesioner, id_pelanggan, tgl_pengisian) 
                                   VALUES (?, ?, ?)";
        if ($stmt_insert_kuesioner = $db->prepare($query_insert_kuesioner)) {
            $stmt_insert_kuesioner->bind_param("iis", $id_data_kuesioner, $id_pelanggan, $tgl_pengisian);
            if ($stmt_insert_kuesioner->execute()) {
                $id_kuesioner = $stmt_insert_kuesioner->insert_id; // Get the last inserted ID (kuesioner)

                // Now, insert answers into the jawaban_kuesioner table
                $query_insert_jawaban = "INSERT INTO jawaban_kuesioner (id_kuesioner, id_data_pernyataan, jawaban) 
                                         VALUES (?, ?, ?)";
                $error = false;

                // Loop through all answers and insert them into the jawaban_kuesioner table
                foreach ($_POST['jawaban'] as $id_data_pernyataan => $jawaban) {
                    if ($stmt_insert_jawaban = $db->prepare($query_insert_jawaban)) {
                        $stmt_insert_jawaban->bind_param("iis", $id_kuesioner, $id_data_pernyataan, $jawaban);
                        if (!$stmt_insert_jawaban->execute()) {
                            $error = true;
                            break;
                        }
                        $stmt_insert_jawaban->close();
                    }
                }

                // Check if there was an error in inserting the answers
                if ($error) {
                    $message = "There was an error while submitting your answers.";
                } else {
                    $message = "Your answers have been successfully submitted!";
                }
            } else {
                $message = "Error: Could not insert kuesioner.";
            }
            $stmt_insert_kuesioner->close();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kuesioner Pelanggan</title>
</head>
<body>

<div id="app">
    <div id="main">
        <header class="mb-3">
            <a href="#" class="burger-btn d-block d-xl-none">
                <i class="bi bi-justify fs-3"></i>
            </a>
        </header>

        <div class="page-heading">
            <h3>Formulir Kuesioner Pelanggan</h3>
        </div>

        <div class="page-content">
            <section class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h4>Isilah Kuesioner Berikut</h4>
                        </div>
                        <div class="card-body">
                            <?php if (isset($message)): ?>
                                <div class="alert alert-success"><?php echo $message; ?></div>
                            <?php endif; ?>

                            <form action="kuesioner_pelanggan.php?id_transaksi=<?php echo $id_transaksi; ?>" method="POST">
                                <div class="form-group">
                                    <h5>Pernyataan</h5>

                                    <?php
                                    // Check if the query is successful
                                    if ($result_pernyataan->num_rows > 0) {
                                        $row_number = 1;
                                        while ($row = $result_pernyataan->fetch_assoc()) {
                                            // Generate the statement and Likert scale options
                                            echo "<div class='pernyataan'>
                                                    <label><b>" . $row_number++ . ". " . $row['pernyataan'] . "</b></label><br>";
                                            
                                            // Generate Likert scale options (1 to 5)
                                            echo "<input type='radio' name='jawaban[{$row['id_data_pernyataan']}]' value='1' required> 1 
                                                  <input type='radio' name='jawaban[{$row['id_data_pernyataan']}]' value='2'> 2 
                                                  <input type='radio' name='jawaban[{$row['id_data_pernyataan']}]' value='3'> 3 
                                                  <input type='radio' name='jawaban[{$row['id_data_pernyataan']}]' value='4'> 4 
                                                  <input type='radio' name='jawaban[{$row['id_data_pernyataan']}]' value='5'> 5
                                                  <br><br>";
                                        }
                                    } else {
                                        echo "Tidak ada pernyataan tersedia untuk diisi.";
                                    }
                                    ?>
                                </div>

                                <button type="submit" class="btn btn-primary">Kirim Kuesioner</button>
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
