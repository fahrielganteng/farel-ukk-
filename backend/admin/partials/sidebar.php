<?php $page = isset($page) ? $page : ''; ?>
<!-- Sidebar -->
<nav class="sidebar">
    <a href="../../pages/dashboard/index.php" class="logo">
        <i class="fas fa-truck-monster"></i> 
        <span>HeavyHire</span>
    </a>
    <div class="sidebar-wrapper">
        <ul class="nav">
            <li class="nav-item <?php echo ($page == 'dashboard') ? 'active' : ''; ?>">
                <a class="nav-link" href="../../pages/dashboard/index.php">
                    <i class="fas fa-grid-2"></i>
                    <i class="fas fa-chart-pie"></i>
                    <span>Dashboard</span>
                </a>
            </li>
            
            <div class="nav-heading px-4 mt-4 mb-2 text-uppercase fw-800 text-slate-500" style="font-size: 0.7rem; letter-spacing: 0.05em;">Manajemen Data</div>

            <li class="nav-item <?php echo ($page == 'alat') ? 'active' : ''; ?>">
                <a class="nav-link" href="../../pages/alat/index.php">
                    <i class="fas fa-box-open"></i>
                    <span>Unit Alat Berat</span>
                </a>
            </li>
            <li class="nav-item <?php echo ($page == 'kategori') ? 'active' : ''; ?>">
                <a class="nav-link" href="../../pages/kategori/index.php">
                    <i class="fas fa-tags"></i>
                    <span>Kategori Alat</span>
                </a>
            </li>
            
            <div class="nav-heading px-4 mt-4 mb-2 text-uppercase fw-800 text-slate-500" style="font-size: 0.7rem; letter-spacing: 0.05em;">Transaksi</div>

            <li class="nav-item <?php echo ($page == 'peminjaman') ? 'active' : ''; ?>">
                <a class="nav-link" href="../../pages/peminjaman/index.php">
                    <i class="fas fa-shopping-cart"></i>
                    <span>Peminjaman</span>
                </a>
            </li>
            <li class="nav-item <?php echo ($page == 'pengembalian') ? 'active' : ''; ?>">
                <a class="nav-link" href="../../pages/pengembalian/index.php">
                    <i class="fas fa-undo-alt"></i>
                    <span>Pengembalian</span>
                </a>
            </li>
            
            <div class="nav-heading px-4 mt-4 mb-2 text-uppercase fw-800 text-slate-500" style="font-size: 0.7rem; letter-spacing: 0.05em;">Sistem</div>

            <li class="nav-item <?php echo ($page == 'user') ? 'active' : ''; ?>">
                <a class="nav-link" href="../../pages/user/index.php">
                    <i class="fas fa-users-cog"></i>
                    <span>Kelola User</span>
                </a>
            </li>
            <li class="nav-item <?php echo ($page == 'log_activity') ? 'active' : ''; ?>">
                <a class="nav-link" href="../../pages/log_activity/index.php">
                    <i class="fas fa-stream"></i>
                    <span>Log Aktivitas</span>
                </a>
            </li>
            <li class="nav-item <?php echo ($page == 'laporan') ? 'active' : ''; ?>">
                <a class="nav-link" href="../../pages/laporan/index.php">
                    <i class="fas fa-file-alt"></i>
                    <span>Laporan</span>
                </a>
            </li>

            <li class="nav-item mt-5">
                <a class="nav-link text-danger border border-danger border-opacity-10" href="../../../action/auth/logout.php" onclick="return confirm('Apakah Anda yakin ingin keluar?')">
                    <i class="fas fa-sign-out-alt"></i>
                    <span>Log Out</span>
                </a>
            </li>
        </ul>
    </div>
</nav>

<!-- Main Panel start -->
<div class="main-panel">
    <!-- Sidebar Overlay for Mobile -->
    <div class="sidebar-overlay" id="sidebarOverlay"></div>

    <!-- Top Navbar -->
    <header class="top-navbar d-flex justify-content-between align-items-center">
        <div class="d-flex align-items-center">
            <button class="btn btn-light border-0 d-lg-none me-3" id="sidebarToggle">
                <i class="fas fa-bars"></i>
            </button>
            <h5 class="mb-0 fw-700 text-slate-800 d-none d-sm-block">Selamat Datang, <?php echo isset($_SESSION['username']) ? $_SESSION['username'] : 'Admin'; ?>!</h5>
        </div>
        <div class="user-profile d-flex align-items-center">
            <div class="text-end me-3 d-none d-md-block">
                <div class="fw-800 text-slate-800" style="font-size: 0.9rem;"><?php echo isset($_SESSION['user_id']) ? 'HeavyHire Admin' : 'Guest'; ?></div>
                <div class="text-emerald-500 fw-600" style="font-size: 0.75rem;"><i class="fas fa-circle me-1" style="font-size: 0.5rem;"></i> Active Now</div>
            </div>
            <div class="rounded-3 d-flex align-items-center justify-content-center" style="width: 44px; height: 44px; background: var(--primary-light); color: var(--primary);">
                <i class="fas fa-user-shield" style="font-size: 1.25rem;"></i>
            </div>
        </div>
    </header>