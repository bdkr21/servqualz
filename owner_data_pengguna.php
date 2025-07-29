<?php
require __DIR__ . '/include/conn.php';
require __DIR__ . '/include/session_check.php';
require "layout/head.php";

// Redirect if not logged in
if (!isset($_SESSION['username']) && !isset($_SESSION['name'])) {
    header("Location: login.php");
    exit();
}

// Get search term from the URL or form input
$search_term = isset($_GET['search']) ? $_GET['search'] : '';

// Query to fetch all users from 'pengguna' table with search functionality
$query = "SELECT id_pengguna, name, username FROM pengguna WHERE name LIKE ? OR username LIKE ?";

// Prepare the query
$stmt = $db->prepare($query);
$search_param = '%' . $search_term . '%';
$stmt->bind_param("ss", $search_param, $search_param);

// Execute the query
$stmt->execute();
$result = $stmt->get_result();

if (!$result) {
    die('Query failed: ' . $db->error);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <!-- Head content can go here -->
</head>

<body>
<div id="app">
    <?php require "layout/sidebar.php"; ?>
    <div id="main">
        <div class="page-heading">
            <h3>Beranda</h3>
            <div class="page-content">
                <section class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <h4>SELAMAT DATANG <?php echo strtoupper($_SESSION['jabatan']); ?></h4>
                                <button><a href="tambah_pengguna.php">TAMBAH PENGGUNA</a></button>
                            </div>
                        </div>
                    </div>
                </section>
                <!-- Table to display users -->
                <section class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-body">
                                <table id="usersTable" class="table">
                                    <thead>
                                        <tr>
                                            <th scope="col">No</th>
                                            <th scope="col">Nama</th>
                                            <th scope="col">Username</th>
                                            <th scope="col">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        $row_number = 1;
                                        // Loop through the results and display each user
                                        while ($row = $result->fetch_assoc()) {
                                            echo "<tr>
                                                    <th scope='row'>{$row_number}</th>
                                                    <td>{$row['name']}</td>
                                                    <td>{$row['username']}</td>
                                                    <td>
                                                        <a href='edit_pengguna.php?id={$row['id_pengguna']}'>Edit</a> | 
                                                        <a href='delete_pengguna.php?id={$row['id_pengguna']}'>Delete</a>
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
             </div> <!-- End page-content -->

        <?php require "layout/footer.php"; ?>
    </div> <!-- End main -->
</div> <!-- End app -->

<!-- Initialize DataTables -->
<script>
    $(document).ready(function() {
        // Initialize DataTables
        $('#usersTable').DataTable({
            "paging": true,            // Enable pagination
            "searching": true,         // Enable search functionality
            "order": [[0, 'asc']],      // Default sorting by first column
            "info": true               // Display info text (e.g., "Showing 1 to 10 of 50 entries")
        });
    });
</script>

<?php require "layout/js.php"; ?>
</body>
</html>
