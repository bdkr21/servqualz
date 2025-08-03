<?php
require __DIR__ . '/include/conn.php';

// Get the kode_transaksi from the URL
if (isset($_GET['kode_transaksi'])) {
    $kode_transaksi = $_GET['kode_transaksi'];

    // Query to fetch the kode_transaksi and related jenis_layanan
    $query_transaksi = "
      SELECT t.kode_transaksi, t.pembayaran, p.nama, t.jenis_layanan
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
            SELECT nama_kuesioner
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
        } else {
            $error = "No matching kuesioner found.";
        }

        // Pagination setup
        $itemsPerPage = 5;
        $page  = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $start = ($page - 1) * $itemsPerPage;

        // Fetch the pernyataan
        $query_pernyataan = "
          SELECT p.pernyataan
          FROM data_pernyataan p
          JOIN data_kuesioner k ON FIND_IN_SET(p.jenis_layanan, k.jenis_layanan)
          WHERE p.status='aktif'
            AND k.status='publish'
            AND FIND_IN_SET(?, p.jenis_layanan)
          LIMIT ?, ?";
        
        if ($stmt_pernyataan = $db->prepare($query_pernyataan)) {
            $stmt_pernyataan->bind_param("sii", $selected_service, $start, $itemsPerPage);
            $stmt_pernyataan->execute();
            $result_pernyataan = $stmt_pernyataan->get_result();

            // Fetching active kuesioner to display inside the card
            if ($result_pernyataan->num_rows) {
                $active_kuesioner = [];
                while ($r = $result_pernyataan->fetch_assoc()) {
                    $active_kuesioner[] = $r['pernyataan'];
                }
            } else {
                $error = "No active pernyataan found for this transaction.";
            }
            $stmt_pernyataan->close();

            // Total count for pagination
            $query_total = "
              SELECT COUNT(*) as total
              FROM data_pernyataan p
              JOIN data_kuesioner k ON FIND_IN_SET(p.jenis_layanan, k.jenis_layanan)
              WHERE p.status='aktif'
                AND k.status='publish'
                AND FIND_IN_SET(?, p.jenis_layanan)";
            if ($stmt_total = $db->prepare($query_total)) {
                $stmt_total->bind_param("s", $selected_service);
                $stmt_total->execute();
                $totalRows = $stmt_total->get_result()->fetch_assoc()['total'];
                $totalPages = ceil($totalRows / $itemsPerPage);
                $stmt_total->close();
            }
        }
    }
} else {
    header("Location: index_pelanggan.php");
    exit();
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
    .container { max-width: 900px; margin-top: 50px; }
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

        <!-- Display the services buttons -->
        <div class="btn-group mb-4">
          <?php foreach ($services as $service): ?>
            <a href="?kode_transaksi=<?= urlencode($kode_transaksi) ?>&service=<?= urlencode($service) ?>" class="btn btn-secondary">
              <?= $service ?>
            </a>
          <?php endforeach; ?>
        </div>

        <!-- Display the active kuesioner if available -->
        <?php if (isset($active_kuesioner) && count($active_kuesioner) > 0): ?>
          <form method="POST">
            <div class="active-kuesioner">
              <?php foreach ($active_kuesioner as $i => $stmtText): ?>
                <div class="mb-4">
                  <label class="d-block font-weight-bold"><?= htmlspecialchars($stmtText) ?></label>
                  <?php foreach ([1=>'Tidak Puas',2=>'Kurang Puas',3=>'Cukup Puas',4=>'Puas',5=>'Sangat Puas'] as $val => $lab): ?>
                    <div class="radio-inline">
                      <input type="radio" name="jawab[<?= $i ?>]" value="<?= $val ?>" required>
                      <label><?= $lab ?></label>
                    </div>
                  <?php endforeach ?>
                </div>
              <?php endforeach ?>
            </div>
            <button type="submit" class="btn btn-primary">Kirim Kuesioner</button>
          </form>

          <!-- Pagination controls -->
          <nav class="pagination">
            <?php for ($p = 1; $p <= $totalPages; $p++): ?>
              <a href="?kode_transaksi=<?= urlencode($kode_transaksi) ?>&service=<?= urlencode($selected_service) ?>&page=<?= $p ?>"><?= $p ?></a>
            <?php endfor ?>
          </nav>
        <?php else: ?>
          <p>No active kuesioner found for this service.</p>
        <?php endif ?>
      </div>
    </div>
  </div>
</body>
</html>
