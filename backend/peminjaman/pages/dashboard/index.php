<?php
// AKTIFKAN ERROR REPORTING
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Pastikan session_start() di awal
session_start();

if (!isset($_SESSION['user_id']) || !isset($_SESSION['status']) || $_SESSION['status'] != 'login') {
    // Redirect ke login dengan pesan
    header("Location: ../../../pages/auth/login.php?pesan=belum_login");
    exit();
}

// Include koneksi database
include '../../app.php';

// Include koneksi database
include '../../app.php';

$user_id = $_SESSION['user_id'];

// Query untuk mendapatkan statistik dashboard
$statistics = [];

// 1. Katalog Alat (Bisa dilihat semua)
$qTotalBarang = "SELECT COUNT(*) as total FROM barang";
$resultTotalBarang = mysqli_query($connect, $qTotalBarang);
$statistics['total_barang'] = mysqli_fetch_assoc($resultTotalBarang)['total'];

// 2. Barang Tersedia
$qBarangTersedia = "SELECT COUNT(*) as total FROM barang WHERE status = 'tersedia'";
$resultBarangTersedia = mysqli_query($connect, $qBarangTersedia);
$statistics['barang_tersedia'] = mysqli_fetch_assoc($resultBarangTersedia)['total'];

// 3. Total Peminjaman (Milik User)
$qTotalPeminjaman = "SELECT COUNT(*) as total FROM peminjaman WHERE user_id = '$user_id'";
$resultTotalPeminjaman = mysqli_query($connect, $qTotalPeminjaman);
$statistics['total_peminjaman'] = mysqli_fetch_assoc($resultTotalPeminjaman)['total'];

// 4. Peminjaman Aktif (Milik User)
$qPeminjamanAktif = "SELECT COUNT(*) as total FROM peminjaman WHERE user_id = '$user_id' AND status = 'dipinjam'";
$resultPeminjamanAktif = mysqli_query($connect, $qPeminjamanAktif);
$statistics['peminjaman_aktif'] = mysqli_fetch_assoc($resultPeminjamanAktif)['total'];

// 5. Total Pengeluaran (Milik User)
$qTotalPendapatan = "SELECT SUM(total_harga) as total FROM peminjaman WHERE user_id = '$user_id' AND status = 'selesai'";
$resultTotalPendapatan = mysqli_query($connect, $qTotalPendapatan);
$statistics['total_pendapatan'] = mysqli_fetch_assoc($resultTotalPendapatan)['total'] ?? 0;

// 6. Total Denda (Milik User)
$qTotalDenda = "SELECT SUM(pb.denda) as total 
                FROM pengembalian pb 
                JOIN peminjaman p ON pb.peminjaman_id = p.id 
                WHERE p.user_id = '$user_id'";
$resultTotalDenda = mysqli_query($connect, $qTotalDenda);
$statistics['total_denda'] = mysqli_fetch_assoc($resultTotalDenda)['total'] ?? 0;

// Query untuk data terbaru (Milik User)
$qPeminjamanTerbaru = "SELECT p.*, u.username, b.nama_barang 
                       FROM peminjaman p 
                       LEFT JOIN users u ON p.user_id = u.id 
                       LEFT JOIN barang b ON p.barang_id = b.id 
                       WHERE p.user_id = '$user_id'
                       ORDER BY p.created_at DESC LIMIT 5";
$resultPeminjamanTerbaru = mysqli_query($connect, $qPeminjamanTerbaru);
?>

<?php 
include '../../partials/header.php'; 
$page = 'dashboard'; 
include '../../partials/sidebar.php'; 
?>

<div class="content-wrapper">
    <div class="page-title">
        <i class="fas fa-chart-pie"></i>
        <span>Dashboard Peminjam</span>
    </div>

    <!-- Welcome Card -->
    <div class="card-admin mb-4 bg-indigo-600 text-white border-0 shadow-lg" style="background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%) !important;">
        <div class="card-body p-5">
            <h1 class="fw-900 mb-2">Halo, <?= htmlspecialchars($_SESSION['username']) ?>!</h1>
            <p class="lead opacity-75 mb-0">Selamat datang kembali di HeavyHire. Kelola penyewaan alat berat Anda dengan mudah.</p>
        </div>
    </div>

    <!-- Stats Row -->
    <div class="row g-4 mb-4">
        <!-- Total Barang -->
        <div class="col-xl-3 col-md-6">
            <div class="card-admin p-4 h-100 transition-all hover-translate-y">
                <div class="d-flex align-items-center mb-3">
                    <div class="bg-indigo-50 text-indigo-600 p-3 rounded-4 me-3">
                        <i class="fas fa-tools fa-lg"></i>
                    </div>
                    <div class="fw-800 text-slate-500 small uppercase tracking-wider">Katalog Alat</div>
                </div>
                <div class="h2 fw-900 text-slate-900 mb-1"><?= safeNumberFormat($statistics['total_barang']) ?></div>
                <div class="small fw-700 text-emerald-500">
                    <i class="fas fa-check-circle me-1"></i> <?= safeNumberFormat($statistics['barang_tersedia']) ?> Tersedia
                </div>
            </div>
        </div>

        <!-- Peminjaman Aktif -->
        <div class="col-xl-3 col-md-6">
            <div class="card-admin p-4 h-100 transition-all hover-translate-y">
                <div class="d-flex align-items-center mb-3">
                    <div class="bg-amber-50 text-amber-600 p-3 rounded-4 me-3">
                        <i class="fas fa-clock fa-lg"></i>
                    </div>
                    <div class="fw-800 text-slate-500 small uppercase tracking-wider">Pinjaman Aktif</div>
                </div>
                <div class="h2 fw-900 text-slate-900 mb-1"><?= safeNumberFormat($statistics['peminjaman_aktif']) ?></div>
                <div class="small fw-700 text-slate-400">Sedang Anda gunakan</div>
            </div>
        </div>

        <!-- Total Transaksi -->
        <div class="col-xl-3 col-md-6">
            <div class="card-admin p-4 h-100 transition-all hover-translate-y">
                <div class="d-flex align-items-center mb-3">
                    <div class="bg-indigo-50 text-indigo-600 p-3 rounded-4 me-3">
                        <i class="fas fa-history fa-lg"></i>
                    </div>
                    <div class="fw-800 text-slate-500 small uppercase tracking-wider">Total Sewa</div>
                </div>
                <div class="h2 fw-900 text-slate-900 mb-1"><?= safeNumberFormat($statistics['total_peminjaman']) ?></div>
                <div class="small fw-700 text-slate-400">Riwayat transaksi</div>
            </div>
        </div>

        <!-- Total Biaya -->
        <div class="col-xl-3 col-md-6">
            <div class="card-admin p-4 h-100 transition-all hover-translate-y">
                <div class="d-flex align-items-center mb-3">
                    <div class="bg-rose-50 text-rose-600 p-3 rounded-4 me-3">
                        <i class="fas fa-wallet fa-lg"></i>
                    </div>
                    <div class="fw-800 text-slate-500 small uppercase tracking-wider">Pengeluaran</div>
                </div>
                <div class="h2 fw-900 text-slate-900 mb-1">Rp <?= safeNumberFormat($statistics['total_pendapatan'], 0, ',', '.') ?></div>
                <div class="small fw-700 text-rose-500">
                    <i class="fas fa-exclamation-circle me-1"></i> Denda: Rp <?= safeNumberFormat($statistics['total_denda'], 0, ',', '.') ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Content Row -->
    <div class="row g-4">
        <!-- Recent Activity -->
        <div class="col-xl-8">
            <div class="card-admin shadow-sm">
                <div class="card-header-admin bg-white d-flex justify-content-between align-items-center">
                    <span><i class="fas fa-list-ul me-2 text-indigo-600"></i> Riwayat Peminjaman Terakhir</span>
                    <a href="../peminjaman/index.php" class="btn btn-sm btn-indigo">Lihat Semua</a>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table-admin mb-0">
                            <thead>
                                <tr>
                                    <th>Alat Berat</th>
                                    <th>Tgl Pinjam</th>
                                    <th class="text-center">Status</th>
                                    <th class="text-end">Total Harga</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (mysqli_num_rows($resultPeminjamanTerbaru) > 0): ?>
                                    <?php while ($peminjaman = mysqli_fetch_assoc($resultPeminjamanTerbaru)): ?>
                                    <tr>
                                        <td>
                                            <div class="fw-800 text-slate-800"><?= htmlspecialchars($peminjaman['nama_barang']) ?></div>
                                            <div class="small text-slate-400">Kode: <?= htmlspecialchars($peminjaman['kode_peminjaman']) ?></div>
                                        </td>
                                        <td class="fw-600 text-slate-600"><?= date('d M Y', strtotime($peminjaman['tgl_pinjam'])) ?></td>
                                        <td class="text-center">
                                            <span class="status-badge status-<?= strtolower($peminjaman['status']) ?>">
                                                <?= ucfirst($peminjaman['status']) ?>
                                            </span>
                                        </td>
                                        <td class="text-end fw-800 text-indigo-600">Rp <?= safeNumberFormat($peminjaman['total_harga'], 0, ',', '.') ?></td>
                                    </tr>
                                    <?php endwhile; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="4" class="text-center py-5">
                                            <div class="text-slate-300 mb-3"><i class="fas fa-folder-open fa-3x"></i></div>
                                            <h5 class="text-slate-400 fw-700">Belum ada riwayat peminjaman</h5>
                                        </td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Links -->
        <div class="col-xl-4">
            <div class="card-admin shadow-sm bg-slate-900 border-0 text-white h-100" style="background: #111827 !important;">
                <div class="card-body p-4 d-flex flex-column">
                    <h4 class="fw-900 mb-4">Aksi Cepat</h4>
                    
                    <a href="../peminjaman/create.php" class="btn btn-indigo w-100 mb-3 py-3 d-flex align-items-center justify-content-center gap-2 shadow">
                        <i class="fas fa-plus"></i> Pinjam Alat Sekarang
                    </a>
                    
                    <a href="../alat/index.php" class="btn btn-outline-light w-100 mb-3 py-3 d-flex align-items-center justify-content-center gap-2" style="border-width: 2px;">
                        <i class="fas fa-search"></i> Telusuri Katalog
                    </a>

                    <div class="mt-auto p-4 rounded-4 bg-white bg-opacity-10 border border-white border-opacity-10">
                        <div class="d-flex align-items-center gap-3">
                            <div class="bg-amber-400 text-slate-900 p-3 rounded-circle">
                                <i class="fas fa-headset fa-lg"></i>
                            </div>
                            <div>
                                <div class="fw-800 small">Butuh Bantuan?</div>
                                <div class="small opacity-50">Hubungi petugas kami</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="p-4 bg-white rounded-4 border border-slate-200 mt-4 text-center">
        <small class="text-slate-400 fw-600 italic">&copy; <?= date('Y') ?> HeavyHire - Premium Heavy Equipment Rental Solution</small>
    </div>
</div>

<?php include '../../partials/script.php'; ?>

<script>
$(document).ready(function() {
    // Auto-refresh page every 5 minutes (300000 ms)
    setTimeout(function() {
        location.reload();
    }, 300000);
});
</script>

<?php include '../../partials/footer.php'; ?>