<?php
// Ensure the database connection is available
require __DIR__ . '/../include/conn.php';

// Query to fetch all data from 'data_kuesioner' table and join with 'servqual' to get 'dimensi_layanan' and 'layanan' to get 'jenis_layanan'
$query = "
    SELECT dk.id_data_kuesioner, dk.nama_kuesioner, dk.status, dk.jenis_layanan, s.dimensi_layanan, l.jenis_layanan AS jenis_layanan_name
    FROM data_kuesioner dk
    LEFT JOIN servqual s ON dk.dimensi_layanan = s.id_servqual
    LEFT JOIN layanan l ON FIND_IN_SET(l.id_jenis_layanan, dk.jenis_layanan)
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
                <h4>Data Kuesioner</h4>
            </div>
            <div class="card-body">
                <table id="dataTable" class="table">
                    <thead>
                        <tr>
                            <th scope="col">No</th>
                            <th scope="col">Nama Kuesioner</th>
                            <th scope="col">Dimensi Layanan</th>
                            <th scope="col">Jenis Layanan</th>
                            <th scope="col">Status</th>
                            <th scope="col">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $row_number = 1;
                        // Loop through the results and display each row of data
                        while ($row = $result->fetch_assoc()) {
                            // Fetching the jenis_layanan names and displaying them
                            $jenis_layanan_names = explode(',', $row['jenis_layanan']);
                            $jenis_layanan_display = [];
                            foreach ($jenis_layanan_names as $jenis_id) {
                                $jenis_layanan_display[] = $row['jenis_layanan_name']; // Replace with actual 'jenis_layanan' name
                            }
                            $jenis_layanan_display = implode(', ', $jenis_layanan_display);

                            echo "<tr>
                                    <th scope='row'>{$row_number}</th>
                                    <td>{$row['nama_kuesioner']}</td>
                                    <td>{$row['dimensi_layanan']}</td>
                                    <td>{$jenis_layanan_display}</td>
                                    <td>{$row['status']}</td>
                                    <td>
                                        <a href='edit_kuesioner.php?id={$row['id_data_kuesioner']}'>Edit</a> | 
                                        <a href='delete_kuesioner.php?id={$row['id_data_kuesioner']}'>Delete</a>
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
