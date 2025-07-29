<?php
require __DIR__ . '../include/conn.php';
require __DIR__ . '../include/session_check.php';
require "layout/head.php";

// Redirect if not logged in
if (!isset($_SESSION['username']) && !isset($_SESSION['name'])) {
    header("Location: login.php");
    exit();
}

// Query to fetch all users from 'pengguna' table
$query = "SELECT id_pengguna, name, username FROM pengguna";
$result = $db->query($query);

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
        <!-- <header class="mb-3">
            <a href="#" class="burger-btn d-block d-xl-none">
                <i class="bi bi-justify fs-3"></i>
            </a>
            <div class="d-flex justify-content-end">
                <div class="dropdown">
                    <div class="d-flex align-items-center" type="button" id="dropdownMenuButton" data-bs-toggle="dropdown" aria-expanded="false" style="cursor: pointer;">
                        <img src="assets/images/pengguna.jpg" alt="Profile" class="rounded-circle" width="30" height="30" style="margin-right: 10px;">
                        <?php echo ucfirst($_SESSION['jabatan']); ?>
                        <i class="bi bi-caret-down-fill" style="margin-left: 10px;"></i>
                    </div>
                    <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                        <li><a class="dropdown-item" href="logout.php">Logout</a></li>
                    </ul>
                </div>
            </div>
        </header> -->

        <div class="page-heading">
            <h3>Beranda</h3>
            <div class="page-content">
                <section class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <h4>SELAMAT DATANG <?php echo strtoupper($_SESSION['jabatan']); ?></h4>
                                <button><a href="index_pelanggan.php">KUESIONER PELANGGAN</a></button>
                            </div>
                        </div>
                    </div>
                </section>
                    <?php if (isset($_SESSION['jabatan']) && $_SESSION['jabatan'] === 'Owner'): ?>
                        <div class="row">
                            <div class="col-md-4">
                                <div class="card" style="background-color: #e0f7fa;">
                                    <div class="card-header d-flex align-items-center" style="background-color: #e0f7fa;">
                                        <i class="bi bi-people-fill" style="font-size: 1rem; margin-right: 10px; color: #00796b;"></i>
                                        <h4 class="mb-0" style="color: #00796b;">Total Pengguna</h4>
                                    </div>
                                    <div class="card-body">
                                        <?php
                                        $result = $db->query("SELECT COUNT(*) AS total_users FROM pengguna");
                                        $row = $result->fetch_assoc();
                                        $total_users = $row['total_users'];
                                        ?>
                                        <p><?php echo $total_users; ?></p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>
                    <?php if (isset($_SESSION['jabatan']) && $_SESSION['jabatan'] === 'administrasi'): ?>
                        <!-- Include the user data table -->
                        <?php require "data/data_transaksi.php"; ?>
                    <?php endif; ?>
        <?php require "layout/footer.php"; ?>
    </div> <!-- End main -->
</div> <!-- End app -->

<?php require "layout/js.php"; ?>
</body>
</html>
