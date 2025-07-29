<?php
// Ensure the database connection is available
require __DIR__ . '/../include/conn.php';
// Query to fetch data from the kuesioner table
$query = "SELECT k.id_kuesioner, k.id_data_kuesioner, k.id_pelanggan, k.tgl_pengisian, 
           dk.nama_kuesioner, p.nama AS nama_pelanggan
            FROM kuesioner k
            JOIN data_kuesioner dk ON k.id_data_kuesioner = dk.id_data_kuesioner
            JOIN pelanggan p ON k.id_pelanggan = p.id_pelanggan
            ORDER BY k.tgl_pengisian DESC"; // You can modify the ORDER BY if needed

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
                            <h4>Data Kuesioner Pelanggan</h4>
                        </div>
                        <div class="card-body">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th scope="col">No</th>
                                        <th scope="col">Nama Kuesioner</th>
                                        <th scope="col">Nama Pelanggan</th>
                                        <th scope="col">Tanggal Pengisian</th>
                                        <th scope="col">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $row_number = 1;
                                    while ($row = $result->fetch_assoc()) {
                                        echo "<tr>
                                                <th scope='row'>{$row_number}</th>
                                                <td>{$row['nama_kuesioner']}</td>
                                                <td>{$row['nama_pelanggan']}</td>
                                                <td>{$row['tgl_pengisian']}</td>
                                                <td><a href='view_kuesioner.php?id_kuesioner={$row['id_kuesioner']}'>View</a></td>
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

<?php require "layout/js.php"; ?>
</body>
</html>
