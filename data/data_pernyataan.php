<?php
// Ensure the database connection is available
require __DIR__ . '/../include/conn.php';

// Query to fetch all data from 'data_pernyataan' table and join with 'servqual' to get 'dimensi_layanan' and 'layanan' to get 'jenis_layanan'
$query = "
    SELECT dp.id_data_pernyataan, dp.pernyataan, dp.rekomendasi_perbaikan, dp.status, s.dimensi_layanan, l.jenis_layanan 
    FROM data_pernyataan dp
    LEFT JOIN servqual s ON dp.dimensi_layanan = s.id_servqual
    LEFT JOIN layanan l ON FIND_IN_SET(l.id_jenis_layanan, dp.jenis_layanan)
";

$result = $db->query($query);

// Check for query errors
if (!$result) {
    die('Query failed: ' . $db->error);
}
?>

<section class="row"> 
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h4>Data Pernyataan</h4>
            </div>
            <div class="card-body">
                <table id="dataTable" class="table">
                    <thead>
                        <tr>
                            <th scope="col">No</th>
                            <th scope="col">Dimensi Layanan</th>
                            <th scope="col">Pernyataan</th>
                            <th scope="col">Rekomendasi Perbaikan</th>
                            <th scope="col">Jenis Layanan</th>
                            <th scope="col">Status</th>
                            <th scope="col">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $row_number = 1;
                        // Loop through and display each statement
                        while ($row = $result->fetch_assoc()) {
                            // Handling multiple jenis_layanan (assuming comma-separated values)
                            $jenis_layanan_names = explode(',', $row['jenis_layanan']);
                            $jenis_layanan_display = [];
                            foreach ($jenis_layanan_names as $jenis_id) {
                                // Here we fetch the correct 'jenis_layanan' based on its ID
                                $jenis_layanan_display[] = $jenis_id; // Replace with proper 'jenis_layanan' name if necessary
                            }
                            $jenis_layanan_display = implode(', ', $jenis_layanan_display);

                            echo "<tr>
                                    <th scope='row'>{$row_number}</th>
                                    <td>{$row['dimensi_layanan']}</td>  <!-- Now displaying dimensi_layanan from servqual table -->
                                    <td>{$row['pernyataan']}</td>
                                    <td>{$row['rekomendasi_perbaikan']}</td>
                                    <td>{$jenis_layanan_display}</td>  <!-- Now displaying jenis_layanan from layanan table -->
                                    <td>{$row['status']}</td>
                                    <td>
                                        <a href='edit_pernyataan.php?id={$row['id_data_pernyataan']}'>Edit</a> | 
                                        <a href='delete_pernyataan.php?id={$row['id_data_pernyataan']}'>Delete</a> | 
                                    </td>
                                  </tr>";
                                  $row_number++;
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</section>

<script>
    $(document).ready(function() {
        // Initialize DataTables
        $('#dataTable').DataTable({
            "paging": true,            // Enable pagination
            "searching": true,         // Enable search functionality
            "order": [[0, 'asc']],      // Default sorting by first column
            "info": true               // Display info text (e.g., "Showing 1 to 10 of 50 entries")
        });
    });
</script>
