<?php
// Ensure the database connection is available
require __DIR__ . '/include/conn.php';

// Handle AJAX request for pernyataan data
if (isset($_GET['ajax']) && $_GET['ajax'] == 'get_pernyataan' && isset($_GET['jenis_layanan'])) {
    $jenis_layanan = $_GET['jenis_layanan'];
    
    $query_pernyataan = "SELECT * FROM data_pernyataan WHERE FIND_IN_SET(?, jenis_layanan)";
    $stmt_pernyataan = $db->prepare($query_pernyataan);
    $stmt_pernyataan->bind_param("s", $jenis_layanan);
    $stmt_pernyataan->execute();
    $result_pernyataan = $stmt_pernyataan->get_result();
    
    $pernyataan_data = [];
    while ($row = $result_pernyataan->fetch_assoc()) {
        $pernyataan_data[] = $row;
    }
    
    header('Content-Type: application/json');
    echo json_encode($pernyataan_data);
    exit();
}
?>
