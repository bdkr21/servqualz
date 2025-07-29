<?php
// Ensure the database connection is available
require __DIR__ . '/../include/conn.php';

// Query to fetch all transactions along with the customer name from 'transaksi' and 'pelanggan' tables
$query = "SELECT t.id_transaksi, t.tanggal_transaksi, t.jenis_layanan, t.pembayaran, p.nama 
          FROM transaksi t
          LEFT JOIN pelanggan p ON t.id_pelanggan = p.id_pelanggan"; 

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
                <h4>Data Transaksi</h4>
            </div>
            <div class="card-body">
                <table class="table">
                    <thead>
                        <tr>
                            <th scope="col">No</th>
                            <th scope="col">ID Transaksi</th>
                            <th scope="col">Nama Pelanggan</th>
                            <th scope="col">Tanggal</th>
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
                                    <td>{$row['id_transaksi']}</td>
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
