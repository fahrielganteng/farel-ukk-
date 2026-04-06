<?php $page = isset($page) ? $page : ''; ?>
<!-- Sidebar -->
<nav class="sidebar">
    <a href="../../pages/dashboard/index.php" class="logo">
        <i class="fas fa-user-shield"></i> 
        <span>Petugas Panel</span>
    </a>
    <div class="sidebar-wrapper">
        <ul class="nav">
            <li class="nav-item <?php echo ($page == 'dashboard') ? 'active' : ''; ?>">
                <a class="nav-link" href="../../pages/dashboard/index.php">
                    <i class="fas fa-chart-pie"></i>
                    <span>Dashboard</span>
                </a>
            </li>
            
            <div class="nav-heading px-4 mt-4 mb-2 text-uppercase fw-800 text-slate-500" style="font-size: 0.7rem; letter-spacing: 0.05em;">Layanan Utama</div>

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
            
            <div class="nav-heading px-4 mt-4 mb-2 text-uppercase fw-800 text-slate-500" style="font-size: 0.7rem; letter-spacing: 0.05em;">Laporan</div>

            <li class="nav-item <?php echo ($page == 'laporan') ? 'active' : ''; ?>">
                <a class="nav-link" href="../../pages/laporan/index.php">
                    <i class="fas fa-file-alt"></i>
                    <span>Cetak Laporan</span>
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
            <h5 class="mb-0 fw-700 text-slate-800 d-none d-sm-block">Selamat Datang, <?php echo isset($_SESSION['username']) ? $_SESSION['username'] : 'Petugas'; ?>!</h5>
        </div>
        <div class="user-profile d-flex align-items-center">
            <div class="text-end me-3 d-none d-md-block">
                <div class="fw-800 text-slate-800" style="font-size: 0.9rem;">Petugas Area</div>
                <div class="text-emerald-500 fw-600" style="font-size: 0.75rem;"><i class="fas fa-circle me-1" style="font-size: 0.5rem;"></i> Active Now</div>
            </div>
            <div class="rounded-3 d-flex align-items-center justify-content-center" style="width: 44px; height: 44px; background: var(--primary-light); color: var(--primary);">
                <i class="fas fa-user-check" style="font-size: 1.25rem;"></i>
            </div>
        </div>
    </header>