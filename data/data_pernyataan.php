<?php
// Ensure the database connection is available
require __DIR__ . '/../include/conn.php';

// Fetch all statements from data_pernyataan
$query = "SELECT * FROM data_pernyataan";
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
                            echo "<tr>
                                    <th scope='row'>{$row_number}</th>
                                    <td>{$row['dimensi_layanan']}</td>
                                    <td>{$row['pernyataan']}</td>
                                    <td>{$row['rekomendasi_perbaikan']}</td>
                                    <td>{$row['jenis_layanan']}</td>
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