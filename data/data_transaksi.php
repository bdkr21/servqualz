<?php
// Ensure the database connection is available
require __DIR__ . '/../include/conn.php';

// Get search term from the URL or form input
$search_term = isset($_GET['search']) ? $_GET['search'] : '';

// Query to fetch all transactions along with the customer name from 'transaksi' and 'pelanggan' tables
$query = "SELECT t.kode_transaksi, t.id_transaksi, t.tanggal_transaksi, t.jenis_layanan, t.pembayaran, p.nama 
          FROM transaksi t
          LEFT JOIN pelanggan p ON t.id_pelanggan = p.id_pelanggan
          WHERE t.kode_transaksi LIKE ? OR p.nama LIKE ? OR t.tanggal_transaksi LIKE ?";

// Prepare the query
$stmt = $db->prepare($query);
$search_param = '%' . $search_term . '%';
$stmt->bind_param("sss", $search_param, $search_param, $search_param);

// Execute the query
$stmt->execute();
$result = $stmt->get_result();

// Check for query errors
if (!$result) {
    die('Query failed: ' . $db->error);
}
?>

<section class="row"> 
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h4>Data Transaksi</h4>
            </div>
            <div class="card-body">
                <!-- Table -->
                <table id="transactionsTable" class="table">
                    <thead>
                        <tr>
                            <th scope="col">No</th>
                            <th scope="col">Kode Transaksi</th>
                            <th scope="col">Nama Pelanggan</th>
                            <th scope="col">Tanggal Transaksi</th>
                            <th scope="col">Jenis Layanan</th>
                            <th scope="col">Pembayaran</th>
                            <th scope="col">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $row_number = 1;
                        // Loop through the results and display each transaction along with the customer's name
                        while ($row = $result->fetch_assoc()) {
                            echo "<tr>
                                    <th scope='row'>{$row_number}</th>
                                    <td>{$row['kode_transaksi']}</td>
                                    <td>{$row['nama']}</td>
                                    <td>{$row['tanggal_transaksi']}</td>
                                    <td>{$row['jenis_layanan']}</td>
                                    <td>{$row['pembayaran']}</td>
                                    <td><a href='edit_transaksi.php?id={$row['id_transaksi']}'>Edit</a> | <a href='delete_transaksi.php?id={$row['id_transaksi']}'>Delete</a></td>
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

<!-- Initialize DataTables -->
<script>
    $(document).ready(function() {
        // Initialize DataTables
        $('#transactionsTable').DataTable({
            "paging": true,            // Enable pagination
            "searching": true,         // Enable search functionality
            "order": [[0, 'asc']],      // Default sorting by first column
            "info": true               // Display info text (e.g., "Showing 1 to 10 of 50 entries")
        });
    });
</script>
