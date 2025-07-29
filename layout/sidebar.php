<div id="sidebar" class="active">
    <div class="sidebar-wrapper active">
        <div class="sidebar-header">
            <div class="d-flex justify-content-between">
                <div class="logo">
                    <a href="index.php">
                        <p>HettieSalon</p>
                        <!-- <img src="assets/images/logocv.png" alt="Logo" class="mb-4" style="width: 100%; height: auto; max-width: none;"> -->
                    </a>
                </div>
                <div class="toggler">
                    <a href="#" class="sidebar-hide d-xl-none d-block">
                        <i class="bi bi-x bi-middle"></i>
                    </a>
                </div>
            </div>
        </div>
        <div class="sidebar-menu">
            <ul class="menu">
                <li class="sidebar-title">
                    Menu 
                    <!-- <?php if (isset($_SESSION['jabatan'])): ?>
                        <span class="user-role" style="font-size: 0.9rem; color: #00796b; margin-left: 10px;">
                            (<?php echo ucfirst($_SESSION['jabatan']); ?>)
                        </span>
                    <?php endif; ?> -->
                </li>

                <!-- Common Menu Item for All Roles -->
                <li class="sidebar-item">
                    <a href="index.php" class="sidebar-link">
                        <i class="bi bi-house-door-fill"></i>
                        <span>Beranda</span>
                    </a>
                </li>

                <!-- Menu Item for 'owner' Role -->
                <?php if (isset($_SESSION['jabatan']) && $_SESSION['jabatan'] === 'Owner'): ?>
                    <li class="sidebar-item has-sub">
                        <a href="#" class="sidebar-link">
                            <i class="bi bi-folder-fill"></i>
                            <span>Data Master</span>
                        </a>
                        <ul class="submenu">
                            <li class="submenu-item">
                                <a href="data_pernyataan.php">
                                    <i class="bi bi-people-fill"></i>
                                    <span>Master Pernyataan</span>
                                </a>
                            </li>
                            <li class="submenu-item">
                                <a href="data_kuesioner.php">
                                    <i class="bi bi-people-fill"></i>
                                    <span>Master Kuesioner</span>
                                </a>
                            </li>
                            <li class="submenu-item">
                                <a href="data_kuesioner_pelanggan.php">
                                    <i class="bi bi-people-fill"></i>
                                    <span>Kuesioner</span>
                                </a>
                            </li>
                            <!-- <li class="submenu-item">
                                <a href="pengguna.php">
                                    <i class="bi bi-people-fill"></i>
                                    <span>Keluhan</span>
                                </a>
                            </li> -->
                        </ul>
                    </li>
                    <li class="sidebar-item">
                        <a href="owner_data_pengguna.php" class="sidebar-link">
                            <i class="bi bi-list-ol"></i>
                            <span>Kelola Pengguna</span>
                        </a>
                    </li>
                <?php endif; ?>

                <!-- Menu Item for 'administrasi' Role -->
                    <li class="sidebar-item">
                        <a href="data_transaksi.php" class="sidebar-link">
                            <i class="bi bi-list-ol"></i>
                            <span>Data Transaksi</span>
                        </a>
                    </li>
                    <li class="sidebar-item">
                        <a href="data_pelanggan.php" class="sidebar-link">
                            <i class="bi bi-list-ol"></i>
                            <span>Data Pelanggan</span>
                        </a>
                    </li>
                <li class="sidebar-item">
                        <a href="logout.php" class="sidebar-link">
                            <i class="bi bi-list-ol"></i>
                            <span>Logout</span>
                        </a>
                </li>
            </ul>
        </div>
        <button class="sidebar-toggler btn x">
            <i data-feather="x"></i>
        </button>
    </div>
</div>
