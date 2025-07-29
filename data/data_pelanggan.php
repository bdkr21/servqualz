<?php
// Ensure the database connection is available
require __DIR__ . '/../include/conn.php';

// Query to fetch customer data along with the jenis_layanan from transaksi
$query = "SELECT pelanggan.id_pelanggan, pelanggan.nama, GROUP_CONCAT(transaksi.jenis_layanan SEPARATOR ', ') AS layanan 
          FROM pelanggan
          LEFT JOIN transaksi ON pelanggan.id_pelanggan = transaksi.id_pelanggan
          GROUP BY pelanggan.id_pelanggan";

// Execute the query
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
                <h4>Data Pelanggan</h4>
            </div>
            <div class="card-body">
                <table class="table">
                    <thead>
                        <tr>
                            <th scope="col">No</th>
                            <th scope="col">ID Pelanggan</th>
                            <th scope="col">Nama</th>
                            <th scope="col">Jenis Layanan</th>
                            <th scope="col">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $row_number = 1;
                        // Loop through the results and display each customer
                        while ($row = $result->fetch_assoc()) {
                            echo "<tr>
                                    <th scope='row'>{$row_number}</th>
                                    <td>{$row['id_pelanggan']}</td>
                                    <td>{$row['nama']}</td>
                                    <td>{$row['layanan']}</td>
                                    <td>
                                        <a href='edit_pelanggan.php?id={$row['id_pelanggan']}'>Edit</a> | 
                                        <a href='delete_pelanggan.php?id={$row['id_pelanggan']}'>Delete</a>
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
