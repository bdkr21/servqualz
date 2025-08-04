<?php
// Ensure the database connection is available
require __DIR__ . '/include/conn.php';

// Handle AJAX request for pernyataan data
if (isset($_GET['ajax']) && $_GET['ajax'] == 'get_pernyataan' && isset($_GET['jenis_layanan'])) {
    $jenis_layanan = $_GET['jenis_layanan'];
    
    // Check if multiple jenis_layanan were selected (comma-separated)
    $jenis_layanan_array = explode(',', $jenis_layanan);
    
    // Prepare the query to match any of the selected jenis_layanan
    $placeholders = implode(',', array_fill(0, count($jenis_layanan_array), '?'));
    $query_pernyataan = "SELECT * FROM data_pernyataan WHERE FIND_IN_SET(id_jenis_layanan, ?) AND FIND_IN_SET(id_jenis_layanan, ?)";
    
    // Prepare the statement
    if ($stmt_pernyataan = $db->prepare($query_pernyataan)) {
        // Bind the parameters dynamically
        $stmt_pernyataan->bind_param(str_repeat('s', count($jenis_layanan_array)), ...$jenis_layanan_array);

        // Execute the query
        $stmt_pernyataan->execute();
        $result_pernyataan = $stmt_pernyataan->get_result();
        
        // Fetch data into an array
        $pernyataan_data = [];
        while ($row = $result_pernyataan->fetch_assoc()) {
            $pernyataan_data[] = $row;
        }
        
        // Return the result as JSON
        header('Content-Type: application/json');
        echo json_encode($pernyataan_data);
        exit();
    } else {
        // In case of an error preparing the query
        header('Content-Type: application/json');
        echo json_encode(['error' => 'Failed to prepare query']);
        exit();
    }
}
?>
