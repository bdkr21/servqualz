<?php
require __DIR__ . '/include/conn.php';

// Get the kode_transaksi from the URL
if (isset($_GET['kode_transaksi'])) {
    $kode_transaksi = $_GET['kode_transaksi'];

    // Query to fetch the kode_transaksi and related jenis_layanan
    $query_transaksi = "
      SELECT t.kode_transaksi, t.pembayaran, p.nama, t.jenis_layanan, t.id_pelanggan
      FROM transaksi t
      JOIN pelanggan p ON t.id_pelanggan = p.id_pelanggan
      WHERE t.kode_transaksi = ?";
    $stmt = $db->prepare($query_transaksi);
    $stmt->bind_param("s", $kode_transaksi);
    $stmt->execute();
    $res = $stmt->get_result();

    if (!$res->num_rows) {
        die("Invalid transaction code.");
    }

    $row = $res->fetch_assoc();
    $stmt->close();

    $id_pelanggan = $row['id_pelanggan']; // Get the id_pelanggan for validation

    if ($row['pembayaran'] !== 'lunas') {
        $error = "Transaction not paid yet. You cannot fill the questionnaire.";
    }
    else {
        // Split services into array
        $services = array_map('trim', explode(',', $row['jenis_layanan']));
        $svcCount = count($services);

        // Fetch the nama_kuesioner
        $selected_service = isset($_GET['service']) ? $_GET['service'] : $services[0];

        $query_nama_kuesioner = "
            SELECT nama_kuesioner, id_data_kuesioner
            FROM data_kuesioner
            WHERE FIND_IN_SET(?, jenis_layanan) > 0
            AND status = 'publish'
        ";
        $stmt_kuesioner = $db->prepare($query_nama_kuesioner);
        $stmt_kuesioner->bind_param("s", $selected_service);
        $stmt_kuesioner->execute();
        $result_kuesioner = $stmt_kuesioner->get_result();

        if ($result_kuesioner->num_rows > 0) {
            $kuesioner = $result_kuesioner->fetch_assoc();
            $nama_kuesioner = $kuesioner['nama_kuesioner'];
            $id_data_kuesioner = $kuesioner['id_data_kuesioner']; // Get the ID of the selected kuesioner
        } else {
            $error = "No matching kuesioner found.";
        }

        // Pagination setup
        $itemsPerPage = 5;
        $page  = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $start = ($page - 1) * $itemsPerPage;

        // Fetch the pernyataan for "Kenyataan" and "Harapan"
        $query_pernyataan_kenyataan = "
          SELECT p.pernyataan
          FROM data_pernyataan p
          JOIN data_kuesioner k ON FIND_IN_SET(p.jenis_layanan, k.jenis_layanan)
          WHERE p.status='aktif'
            AND k.status='publish'
            AND FIND_IN_SET(?, p.jenis_layanan)
          LIMIT ?, ?
        ";

        $query_pernyataan_harapan = "
          SELECT p.pernyataan
          FROM data_pernyataan p
          JOIN data_kuesioner k ON FIND_IN_SET(p.jenis_layanan, k.jenis_layanan)
          WHERE p.status='aktif'
            AND k.status='publish'
            AND FIND_IN_SET(?, p.jenis_layanan)
          LIMIT ?, ?
        ";

        // Execute both queries
        if ($stmt_pernyataan_kenyataan = $db->prepare($query_pernyataan_kenyataan)) {
            $stmt_pernyataan_kenyataan->bind_param("sii", $selected_service, $start, $itemsPerPage);
            $stmt_pernyataan_kenyataan->execute();
            $result_pernyataan_kenyataan = $stmt_pernyataan_kenyataan->get_result();

            if ($result_pernyataan_kenyataan->num_rows) {
                $active_kuesioner_kenyataan = [];
                while ($r = $result_pernyataan_kenyataan->fetch_assoc()) {
                    $active_kuesioner_kenyataan[] = $r['pernyataan'];
                }
            } else {
                $error = "No active pernyataan found for 'Kenyataan'.";
            }
            $stmt_pernyataan_kenyataan->close();
        }

        if ($stmt_pernyataan_harapan = $db->prepare($query_pernyataan_harapan)) {
            $stmt_pernyataan_harapan->bind_param("sii", $selected_service, $start, $itemsPerPage);
            $stmt_pernyataan_harapan->execute();
            $result_pernyataan_harapan = $stmt_pernyataan_harapan->get_result();

            if ($result_pernyataan_harapan->num_rows) {
                $active_kuesioner_harapan = [];
                while ($r = $result_pernyataan_harapan->fetch_assoc()) {
                    $active_kuesioner_harapan[] = $r['pernyataan'];
                }
            } else {
                $error = "No active pernyataan found for 'Harapan'.";
            }
            $stmt_pernyataan_harapan->close();
        }
    }
} else {
    header("Location: index_pelanggan.php");
    exit();
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['jawab_kenyataan']) && isset($_POST['jawab_harapan'])) {
    // Store the current date in a variable
    $tgl_pengisian = date('Y-m-d');

    // Loop through each pernyataan
    foreach ($_POST['jawab_kenyataan'] as $index => $jawaban_kenyataan) {
        $jawaban_harapan = $_POST['jawab_harapan'][$index];

        // Calculate the gap (difference between Kenyataan and Harapan)
        $gap = $jawaban_kenyataan - $jawaban_harapan;

        // Check if id_data_kuesioner is valid before inserting
        $query_check_service = "
            SELECT id_data_kuesioner 
            FROM data_kuesioner 
            WHERE id_data_kuesioner = ?
        ";
        $stmt_check_service = $db->prepare($query_check_service);
        $stmt_check_service->bind_param("i", $id_data_kuesioner);
        $stmt_check_service->execute();
        $result_check_service = $stmt_check_service->get_result();

        if ($result_check_service->num_rows === 0) {
            $error = "Invalid service ID: No matching kuesioner found.";
            break;
        }

        $stmt_check_service->close();

        // Insert the answers into the kuesioner table
        $query_insert = "
            INSERT INTO kuesioner (id_data_kuesioner, id_pelanggan, tgl_pengisian)
            VALUES (?, ?, ?)
        ";
        $stmt_insert = $db->prepare($query_insert);
        $stmt_insert->bind_param("iis", $id_data_kuesioner, $id_pelanggan, $tgl_pengisian);
        $stmt_insert->execute();

        if ($stmt_insert->affected_rows > 0) {
            $success_message = "Data successfully saved!";
        } else {
            $error = "Failed to save data.";
        }

        $stmt_insert->close();
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Kuesioner Pelanggan</title>
  <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@300;400;600;700;800&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="assets/css/bootstrap.css">
  <style>
    .container { max-width: 1250px; margin-top: 50px; }
    .card { margin-bottom:20px; border-radius:8px; box-shadow:0 2px 10px rgba(0,0,0,0.1); }
    .card-header { background:#f7f7f7; padding:20px; text-align:center; font-size:24px; }
    .card-body { padding:25px; }
    .active-kuesioner { max-height:400px; overflow-y:auto; padding-right:10px; }
    .radio-inline { display:inline-block; margin:0 20px 15px 0; text-align:center; }
    .radio-inline input { margin-bottom:8px; }
    .radio-inline label { display:block; margin-top:5px; font-size:14px; }
    .pagination { text-align:center; margin-top:20px; }
    .pagination a { padding:8px 12px; margin:0 5px; border:1px solid #ddd; border-radius:5px; text-decoration:none; color:#007bff;}
    .pagination a:hover { background:#007bff; color:#fff; }
    .columns { display: flex; justify-content: space-between; }
    .column { width: 48%; }
  </style>
</head>
<body>
  <div class="container">
    <h3 class="mb-4">Kuesioner Pelanggan</h3>
    <div class="card">
      <div class="card-header">
        <h4>Formulir Kuesioner - <?= htmlspecialchars($nama_kuesioner) ?></h4>
      </div>
      <div class="card-body">
        <?php if (isset($error)): ?>
          <div class="alert alert-danger"><?= $error ?></div>
        <?php endif; ?>
        <?php if (isset($success_message)): ?>
          <div class="alert alert-success"><?= $success_message ?></div>
        <?php endif; ?>

        <!-- Display the services buttons -->
        <div class="btn-group mb-4">
          <?php foreach ($services as $service): ?>
            <a href="?kode_transaksi=<?= urlencode($kode_transaksi) ?>&service=<?= urlencode($service) ?>" class="btn btn-secondary">
              <?= $service ?>
            </a>
          <?php endforeach; ?>
        </div>

        <!-- Display the active kuesioner for Kenyataan and Harapan -->
        <?php if (isset($active_kuesioner_kenyataan) && count($active_kuesioner_kenyataan) > 0 && isset($active_kuesioner_harapan) && count($active_kuesioner_harapan) > 0): ?>
          <form method="POST">
            <div class="columns">
              <!-- Kenyataan section -->
              <div class="column">
                <h5>Kuesioner Kenyataan</h5>
                <?php foreach ($active_kuesioner_kenyataan as $i => $stmtText): ?>
                  <div class="mb-4">
                    <label class="d-block font-weight-bold"><?= htmlspecialchars($stmtText) ?></label>
                    <?php foreach ([1=>'Tidak Puas',2=>'Kurang Puas',3=>'Cukup Puas',4=>'Puas',5=>'Sangat Puas'] as $val => $lab): ?>
                      <div class="radio-inline">
                        <input type="radio" name="jawab_kenyataan[<?= $i ?>]" value="<?= $val ?>" required>
                        <label><?= $lab ?></label>
                      </div>
                    <?php endforeach ?>
                  </div>
                <?php endforeach ?>
              </div>

              <!-- Harapan section -->
              <div class="column">
                <h5>Kuesioner Harapan</h5>
                <?php foreach ($active_kuesioner_harapan as $i => $stmtText): ?>
                  <div class="mb-4">
                    <label class="d-block font-weight-bold"><?= htmlspecialchars($stmtText) ?></label>
                    <?php foreach ([1=>'Tidak Puas',2=>'Kurang Puas',3=>'Cukup Puas',4=>'Puas',5=>'Sangat Puas'] as $val => $lab): ?>
                      <div class="radio-inline">
                        <input type="radio" name="jawab_harapan[<?= $i ?>]" value="<?= $val ?>" required>
                        <label><?= $lab ?></label>
                      </div>
                    <?php endforeach ?>
                  </div>
                <?php endforeach ?>
              </div>
            </div>
            <button type="submit" class="btn btn-primary">Kirim Kuesioner</button>
          </form>
        <?php else: ?>
          <p>No active kuesioner found for this service.</p>
        <?php endif ?>
      </div>
    </div>
  </div>
</body>
</html>
