<aside class="sidebar">
    <div class="sidebar-header">
        <a href="#" class="sidebar-brand">
            <i class="fas fa-tractor text-amber-400"></i>
            Heavy<span>Hire</span>
        </a>
    </div>

    <div class="sidebar-nav">
        <div class="nav-heading">Menu Utama</div>
        <a href="../../pages/dashboard/index.php" class="nav-link <?= $page == 'dashboard' ? 'active' : '' ?>">
            <i class="fas fa-chart-pie"></i>
            <span>Dashboard</span>
        </a>
        
        <div class="nav-heading">Layanan</div>
        <a href="../../pages/alat/index.php" class="nav-link <?= $page == 'alat' ? 'active' : '' ?>">
            <i class="fas fa-hard-hat"></i>
            <span>Katalog Alat</span>
        </a>
        <a href="../../pages/peminjaman/index.php" class="nav-link <?= $page == 'peminjaman' ? 'active' : '' ?>">
            <i class="fas fa-calendar-plus"></i>
            <span>Pinjam Alat</span>
        </a>
        <a href="../../pages/pengembalian/index.php" class="nav-link <?= $page == 'pengembalian' ? 'active' : '' ?>">
            <i class="fas fa-undo"></i>
            <span>Pengembalian</span>
        </a>

        <div class="nav-heading">Akun & Sistem</div>
        <a href="../../pages/user/index.php" class="nav-link <?= $page == 'user' ? 'active' : '' ?>">
            <i class="fas fa-user-circle"></i>
            <span>Profil Saya</span>
        </a>
        <a href="../../../action/auth/logout.php" class="nav-link text-danger mt-4" onclick="return confirm('Apakah Anda yakin ingin keluar?')">
            <i class="fas fa-sign-out-alt"></i>
            <span>Keluar</span>
        </a>
    </div>
</aside>