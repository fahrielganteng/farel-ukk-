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

// Query untuk mendapatkan statistik dashboard
$statistics = [];

// 1. Total User
$qTotalUser = "SELECT COUNT(*) as total FROM users";
$resultTotalUser = mysqli_query($connect, $qTotalUser);
$statistics['total_users'] = mysqli_fetch_assoc($resultTotalUser)['total'];

// 2. Total Barang
$qTotalBarang = "SELECT COUNT(*) as total FROM barang";
$resultTotalBarang = mysqli_query($connect, $qTotalBarang);
$statistics['total_barang'] = mysqli_fetch_assoc($resultTotalBarang)['total'];

// 3. Total Peminjaman
$qTotalPeminjaman = "SELECT COUNT(*) as total FROM peminjaman";
$resultTotalPeminjaman = mysqli_query($connect, $qTotalPeminjaman);
$statistics['total_peminjaman'] = mysqli_fetch_assoc($resultTotalPeminjaman)['total'];

// 4. Total Pengembalian
$qTotalPengembalian = "SELECT COUNT(*) as total FROM pengembalian";
$resultTotalPengembalian = mysqli_query($connect, $qTotalPengembalian);
$statistics['total_pengembalian'] = mysqli_fetch_assoc($resultTotalPengembalian)['total'];

// 5. Total Peminjaman Aktif (dipinjam)
$qPeminjamanAktif = "SELECT COUNT(*) as total FROM peminjaman WHERE status = 'dipinjam'";
$resultPeminjamanAktif = mysqli_query($connect, $qPeminjamanAktif);
$statistics['peminjaman_aktif'] = mysqli_fetch_assoc($resultPeminjamanAktif)['total'];

// 6. Total Peminjaman Pending
$qPeminjamanPending = "SELECT COUNT(*) as total FROM peminjaman WHERE status = 'pending'";
$resultPeminjamanPending = mysqli_query($connect, $qPeminjamanPending);
$statistics['peminjaman_pending'] = mysqli_fetch_assoc($resultPeminjamanPending)['total'];

// 7. Total Barang Tersedia
$qBarangTersedia = "SELECT COUNT(*) as total FROM barang WHERE status = 'tersedia'";
$resultBarangTersedia = mysqli_query($connect, $qBarangTersedia);
$statistics['barang_tersedia'] = mysqli_fetch_assoc($resultBarangTersedia)['total'];

// 8. Total Barang Dipinjam
$qBarangDipinjam = "SELECT COUNT(*) as total FROM barang WHERE status = 'dipinjam'";
$resultBarangDipinjam = mysqli_query($connect, $qBarangDipinjam);
$statistics['barang_dipinjam'] = mysqli_fetch_assoc($resultBarangDipinjam)['total'];

// 9. Total Pendapatan (dari peminjaman selesai)
$qTotalPendapatan = "SELECT SUM(total_harga) as total FROM peminjaman WHERE status = 'selesai'";
$resultTotalPendapatan = mysqli_query($connect, $qTotalPendapatan);
$statistics['total_pendapatan'] = mysqli_fetch_assoc($resultTotalPendapatan)['total'] ?? 0;

// 10. Total Denda
$qTotalDenda = "SELECT SUM(denda) as total FROM pengembalian";
$resultTotalDenda = mysqli_query($connect, $qTotalDenda);
$statistics['total_denda'] = mysqli_fetch_assoc($resultTotalDenda)['total'] ?? 0;

// Query untuk data terbaru
$qPeminjamanTerbaru = "SELECT p.*, u.username, b.nama_barang 
                       FROM peminjaman p 
                       LEFT JOIN users u ON p.user_id = u.id 
                       LEFT JOIN barang b ON p.barang_id = b.id 
                       ORDER BY p.created_at DESC LIMIT 5";
$resultPeminjamanTerbaru = mysqli_query($connect, $qPeminjamanTerbaru);

$qBarangTerbaru = "SELECT * FROM barang ORDER BY created_at DESC LIMIT 5";
$resultBarangTerbaru = mysqli_query($connect, $qBarangTerbaru);

$qUserTerbaru = "SELECT * FROM users ORDER BY created_at DESC LIMIT 5";
$resultUserTerbaru = mysqli_query($connect, $qUserTerbaru);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Sistem Peminjaman Alat Berat</title>
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- ApexCharts CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/apexcharts@3.35.0/dist/apexcharts.css">
    
</head>
<body>

<?php 
include '../../partials/header.php'; 
$page = 'dashboard'; 
include '../../partials/sidebar.php'; 
?>

<div class="content-wrapper">
    <div class="page-title">
        <i class="fas fa-chart-pie"></i>
        <span>Dashboard Petugas</span>
    </div>
        <!-- Welcome Header -->
        <div class="welcome-header">
            <h1>Selamat Datang, Administrator!</h1>
            <p>Sistem Manajemen Peminjaman Alat Berat - Dashboard Utama</p>
        </div>

        <!-- Statistics Cards -->
        <div class="row g-4 mb-5">
            <div class="col-xl-3 col-md-6">
                <div class="card-admin h-100 p-4">
                    <div class="d-flex align-items-center mb-3">
                        <div class="rounded-3 bg-indigo-50 text-indigo-600 d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                            <i class="fas fa-users" style="font-size: 1.5rem;"></i>
                        </div>
                        <div class="ms-3">
                            <div class="text-slate-500 fw-600 small">Total Pengguna</div>
                            <h3 class="mb-0 fw-800 text-slate-800"><?php echo number_format($statistics['total_users']); ?></h3>
                        </div>
                    </div>
                    <div class="small fw-600 text-emerald-500 mt-2">
                        <i class="fas fa-arrow-up me-1"></i> Terdaftar di sistem
                    </div>
                </div>
            </div>
            
            <div class="col-xl-3 col-md-6">
                <div class="card-admin h-100 p-4">
                    <div class="d-flex align-items-center mb-3">
                        <div class="rounded-3 bg-emerald-50 text-emerald-600 d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                            <i class="fas fa-tools" style="font-size: 1.5rem;"></i>
                        </div>
                        <div class="ms-3">
                            <div class="text-slate-500 fw-600 small">Total Alat Berat</div>
                            <h3 class="mb-0 fw-800 text-slate-800"><?php echo number_format($statistics['total_barang']); ?></h3>
                        </div>
                    </div>
                    <div class="d-flex justify-content-between mt-2">
                        <span class="small fw-600 text-emerald-500">
                            <i class="fas fa-check-circle me-1"></i> <?= $statistics['barang_tersedia'] ?> Ready
                        </span>
                        <span class="small fw-600 text-amber-500">
                            <i class="fas fa-sync-alt me-1"></i> <?= $statistics['barang_dipinjam'] ?> Out
                        </span>
                    </div>
                </div>
            </div>
            
            <div class="col-xl-3 col-md-6">
                <div class="card-admin h-100 p-4">
                    <div class="d-flex align-items-center mb-3">
                        <div class="rounded-3 bg-amber-50 text-amber-600 d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                            <i class="fas fa-handshake" style="font-size: 1.5rem;"></i>
                        </div>
                        <div class="ms-3">
                            <div class="text-slate-500 fw-600 small">Total Peminjaman</div>
                            <h3 class="mb-0 fw-800 text-slate-800"><?php echo number_format($statistics['total_peminjaman']); ?></h3>
                        </div>
                    </div>
                    <div class="d-flex justify-content-between mt-2">
                        <span class="small fw-600 text-amber-500">
                            <i class="fas fa-clock me-1"></i> <?= $statistics['peminjaman_pending'] ?> Pending
                        </span>
                        <span class="small fw-600 text-indigo-500">
                            <i class="fas fa-truck-loading me-1"></i> <?= $statistics['peminjaman_aktif'] ?> Aktif
                        </span>
                    </div>
                </div>
            </div>
            
            <div class="col-xl-3 col-md-6">
                <div class="card-admin h-100 p-4 border-indigo-100 bg-indigo-50 bg-opacity-10">
                    <div class="d-flex align-items-center mb-3">
                        <div class="rounded-3 bg-indigo-100 text-indigo-700 d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                            <i class="fas fa-money-bill-wave" style="font-size: 1.5rem;"></i>
                        </div>
                        <div class="ms-3">
                            <div class="text-indigo-600 fw-700 small">Total Pendapatan</div>
                            <h3 class="mb-0 fw-800 text-slate-800">Rp <?php echo number_format($statistics['total_pendapatan'], 0, ',', '.'); ?></h3>
                        </div>
                    </div>
                    <div class="small fw-600 text-slate-500 mt-2">
                        Denda: <span class="text-danger">Rp <?php echo number_format($statistics['total_denda'], 0, ',', '.'); ?></span>
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-4 mb-4">
            <!-- Recent Peminjaman -->
            <div class="col-xl-8">
                <div class="card-admin">
                    <div class="card-header-admin">
                        <span><i class="fas fa-history me-2 text-indigo-500"></i> Peminjaman Terbaru</span>
                        <a href="../peminjaman/index.php" class="btn btn-sm btn-light border fw-700 text-slate-500">Lihat Semua</a>
                    </div>
                    <div class="card-body-admin p-0">
                        <div class="table-responsive">
                            <table class="table-admin mb-0">
                                <thead>
                                    <tr>
                                        <th>Barang</th>
                                        <th>Peminjam</th>
                                        <th>Status</th>
                                        <th class="text-end">Tanggal</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (mysqli_num_rows($resultPeminjamanTerbaru) > 0): ?>
                                        <?php while ($peminjaman = mysqli_fetch_assoc($resultPeminjamanTerbaru)): 
                                            $st = strtolower($peminjaman['status']);
                                        ?>
                                        <tr>
                                            <td>
                                                <div class="fw-700 text-dark"><?= htmlspecialchars($peminjaman['nama_barang']) ?></div>
                                                <small class="text-slate-400">Kode: #<?= $peminjaman['id'] ?></small>
                                            </td>
                                            <td>
                                                <div class="fw-600"><?= htmlspecialchars($peminjaman['username']) ?></div>
                                            </td>
                                            <td>
                                                <span class="status-badge status-<?= $st ?>">
                                                    <i class="fas fa-<?= ($st == 'selesai' || $st == 'disetujui' ? 'check' : ($st == 'ditolak' ? 'times' : 'clock')) ?>"></i>
                                                    <?= ucfirst($st) ?>
                                                </span>
                                            </td>
                                            <td class="text-end fw-600 text-slate-500">
                                                <?= date('d M Y', strtotime($peminjaman['created_at'])) ?>
                                            </td>
                                        </tr>
                                        <?php endwhile; ?>
                                    <?php else: ?>
                                        <tr>
                                            <td colspan="4" class="text-center py-5 text-slate-400">Belum ada data peminjaman</td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Recent Users -->
            <div class="col-xl-4">
                <div class="card-admin">
                    <div class="card-header-admin">
                        <span><i class="fas fa-user-plus me-2 text-indigo-500"></i> User Terbaru</span>
                    </div>
                    <div class="card-body-admin">
                        <div class="d-flex flex-column gap-4">
                            <?php if (mysqli_num_rows($resultUserTerbaru) > 0): ?>
                                <?php while ($user = mysqli_fetch_assoc($resultUserTerbaru)): ?>
                                <div class="d-flex align-items-center">
                                    <div class="rounded-circle d-flex align-items-center justify-content-center fw-800 text-indigo-600 bg-indigo-50" style="width: 44px; height: 44px; font-size: 0.9rem;">
                                        <?= strtoupper(substr($user['username'], 0, 2)) ?>
                                    </div>
                                    <div class="ms-3 flex-grow-1">
                                        <div class="fw-700 text-slate-800 mb-0" style="line-height: 1;"><?= htmlspecialchars($user['username']) ?></div>
                                        <small class="text-slate-400"><?= ucfirst($user['role']) ?></small>
                                    </div>
                                    <div class="text-end">
                                        <div class="fw-600 text-slate-500 small"><?= date('d M', strtotime($user['created_at'])) ?></div>
                                    </div>
                                </div>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <div class="text-center py-4 text-slate-400 italic">Belum ada user baru</div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php 
include '../../partials/script.php'; 
?>

<!-- jQuery -->
<script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<!-- ApexCharts -->
<script src="https://cdn.jsdelivr.net/npm/apexcharts@3.35.0/dist/apexcharts.min.js"></script>

<script>
$(document).ready(function() {
    // Initialize Chart
    var options = {
        series: [{
            name: 'Total Peminjaman',
            data: [
                <?php
                // Sample data for chart (you should replace with actual data from database)
                $monthlyData = [4000, 3000, 5000, 4000, 6000, 5000, 7000, 6000, 8000, 7000, 9000, 8000];
                foreach ($monthlyData as $value) {
                    echo $value . ',';
                }
                ?>
            ]
        }],
        chart: {
            type: 'area',
            height: 350,
            toolbar: {
                show: true
            }
        },
        colors: ['#667eea'],
        dataLabels: {
            enabled: false
        },
        stroke: {
            curve: 'smooth',
            width: 3
        },
        fill: {
            type: 'gradient',
            gradient: {
                shadeIntensity: 1,
                opacityFrom: 0.7,
                opacityTo: 0.3,
                stops: [0, 90, 100]
            }
        },
        xaxis: {
            categories: ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des']
        },
        yaxis: {
            title: {
                text: 'Jumlah Peminjaman'
            }
        },
        grid: {
            borderColor: '#f1f1f1',
        }
    };

    var chart = new ApexCharts(document.querySelector("#chart"), options);
    chart.render();

    // Animate stats cards on scroll
    function animateOnScroll() {
        $('.stats-card').each(function() {
            var elementTop = $(this).offset().top;
            var elementBottom = elementTop + $(this).outerHeight();
            var viewportTop = $(window).scrollTop();
            var viewportBottom = viewportTop + $(window).height();
            
            if (elementBottom > viewportTop && elementTop < viewportBottom) {
                $(this).addClass('animate__animated animate__fadeInUp');
            }
        });
    }

    // Initial check
    animateOnScroll();

    // Check on scroll
    $(window).on('scroll', function() {
        animateOnScroll();
    });

    // Auto-refresh page every 5 minutes (300000 ms)
    setTimeout(function() {
        location.reload();
    }, 300000);
});
</script>

</body>
</html>