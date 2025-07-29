<?php
require __DIR__ . '/include/conn.php';
require __DIR__ . '/include/session_check.php';
require "layout/head.php";

// Redirect if not logged in
if (!isset($_SESSION['username']) && !isset($_SESSION['name'])) {
    header("Location: login.php");
    exit();
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
                    <div class="col-16">
                        <div class="card">
                            <div class="card-header">
                                <h4>SELAMAT DATANG <?php echo strtoupper($_SESSION['jabatan']); ?></h4>
                                <button><a href="tambah_pernyataan.php">TAMBAH PERNYATAAN</a></button>
                            </div>
                        </div>
                    </div>
                </section>
             <?php require "data/data_pernyataan.php"; ?>
        <?php require "layout/footer.php"; ?>
    </div> <!-- End main -->
</div> <!-- End app -->

<?php require "layout/js.php"; ?>
</body>
</html>
