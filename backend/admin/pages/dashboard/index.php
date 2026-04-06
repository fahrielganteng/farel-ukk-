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

// --- CHART DATA: Peminjaman 6 Bulan Terakhir ---
$qChartPeminjaman = "
    SELECT DATE_FORMAT(created_at, '%b %Y') as bulan, COUNT(*) as total,
           YEAR(created_at) as thn, MONTH(created_at) as bln
    FROM peminjaman
    GROUP BY thn, bln, bulan
    ORDER BY thn DESC, bln DESC
    LIMIT 6
";
$resultChartPeminjaman = mysqli_query($connect, $qChartPeminjaman);
$chartPeminjamanLables = [];
$chartPeminjamanData = [];
if($resultChartPeminjaman) {
    while($row = mysqli_fetch_assoc($resultChartPeminjaman)) {
        array_unshift($chartPeminjamanLables, $row['bulan']);
        array_unshift($chartPeminjamanData, $row['total']);
    }
}

// --- CHART DATA: Status Barang ---
$qChartBarang = "SELECT status, COUNT(*) as total FROM barang GROUP BY status";
$resultChartBarang = mysqli_query($connect, $qChartBarang);
$chartBarangLabels = [];
$chartBarangData = [];
if($resultChartBarang) {
    while($row = mysqli_fetch_assoc($resultChartBarang)) {
        $chartBarangLabels[] = ucfirst($row['status']);
        $chartBarangData[] = $row['total'];
    }
}
?>

<?php 
include '../../partials/header.php'; 
$page = 'dashboard'; 
include '../../partials/sidebar.php'; 
?>

<!-- Content Wrapper -->
<div class="content-wrapper">
    <div class="page-title">
        <i class="fas fa-home"></i> Sekilas Info Dashboard
    </div>

    <!-- Stats Row 1 -->
    <div class="row g-4 mb-5">
        <!-- Pengguna -->
        <div class="col-xl-3 col-md-6">
            <div class="card-admin p-4 h-100 d-flex flex-row align-items-center">
                <div class="rounded-4 d-flex align-items-center justify-content-center me-4" style="width: 64px; height: 64px; background-color: #eff6ff; color: #3b82f6;">
                    <i class="fas fa-users" style="font-size: 1.75rem;"></i>
                </div>
                <div>
                    <h3 class="mb-1 fw-800 text-dark fs-3"><?php echo number_format($statistics['total_users']); ?></h3>
                    <div class="text-secondary fw-500" style="font-size: 0.95rem;">Total Pengguna</div>
                </div>
            </div>
        </div>

        <!-- Alat Berat -->
        <div class="col-xl-3 col-md-6">
            <div class="card-admin p-4 h-100 d-flex flex-row align-items-center">
                <div class="rounded-4 d-flex align-items-center justify-content-center me-4" style="width: 64px; height: 64px; background-color: #ecfdf5; color: #10b981;">
                    <i class="fas fa-truck-monster" style="font-size: 1.75rem;"></i>
                </div>
                <div>
                    <h3 class="mb-1 fw-800 text-dark fs-3"><?php echo number_format($statistics['total_barang']); ?></h3>
                    <div class="text-secondary fw-500" style="font-size: 0.95rem;">Unit Alat Berat</div>
                </div>
            </div>
        </div>

        <!-- Peminjaman -->
        <div class="col-xl-3 col-md-6">
            <div class="card-admin p-4 h-100 d-flex flex-row align-items-center">
                <div class="rounded-4 d-flex align-items-center justify-content-center me-4" style="width: 64px; height: 64px; background-color: #fff7ed; color: #f59e0b;">
                    <i class="fas fa-shopping-cart" style="font-size: 1.75rem;"></i>
                </div>
                <div>
                    <h3 class="mb-1 fw-800 text-dark fs-3"><?php echo number_format($statistics['total_peminjaman']); ?></h3>
                    <div class="text-secondary fw-500" style="font-size: 0.95rem;">Transaksi Sewa</div>
                </div>
            </div>
        </div>

        <!-- Pendapatan -->
        <div class="col-xl-3 col-md-6">
            <div class="card-admin p-4 h-100 d-flex flex-row align-items-center">
                <div class="rounded-4 d-flex align-items-center justify-content-center me-4" style="width: 64px; height: 64px; background-color: #eef2ff; color: #6366f1;">
                    <i class="fas fa-wallet" style="font-size: 1.75rem;"></i>
                </div>
                <div>
                    <h3 class="mb-1 fw-800 text-dark fs-3">Rp<?php echo number_format($statistics['total_pendapatan'], 0, ',', '.'); ?></h3>
                    <div class="text-secondary fw-500" style="font-size: 0.95rem;">Pendapatan</div>
                </div>
            </div>
        </div>
    </div> <!-- end rows stats -->

    <!-- Charts Row -->
    <div class="row g-4 mb-5">
        <!-- Chart Peminjaman -->
        <div class="col-xl-8">
            <div class="card-admin h-100">
                <div class="card-header-admin">
                    <span><i class="fas fa-chart-line me-2 text-indigo-500"></i> Tren Peminjaman</span>
                    <span class="badge bg-slate-100 text-slate-600 fw-medium">6 Bulan Terakhir</span>
                </div>
                <div class="card-body-admin">
                    <div style="height: 320px; position: relative;">
                        <canvas id="peminjamanChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Chart Barang -->
        <div class="col-xl-4">
            <div class="card-admin h-100">
                <div class="card-header-admin">
                    <span><i class="fas fa-chart-pie me-2 text-emerald-500"></i> Status Unit</span>
                </div>
                <div class="card-body-admin">
                    <div style="height: 320px; position: relative; display: flex; justify-content: center;">
                        <canvas id="barangChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Details Row 2 -->
    <div class="row g-4">
        
        <!-- Peminjaman Terbaru -->
        <div class="col-lg-8">
            <div class="card-admin h-100">
                <div class="card-header-admin">
                    <span><i class="fas fa-history me-2 text-blue-500"></i> Aktivitas Terbaru</span>
                    <a href="../peminjaman/index.php" class="btn btn-sm btn-light border" style="font-size: 0.8rem;">Lihat Semua</a>
                </div>
                <div class="card-body-admin p-0">
                    <div class="table-responsive">
                        <table class="table-admin table-hover align-middle mb-0">
                            <thead>
                                <tr>
                                    <th class="ps-4">Pelanggan</th>
                                    <th>Unit Alat</th>
                                    <th>Tanggal</th>
                                    <th class="text-center">Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (mysqli_num_rows($resultPeminjamanTerbaru) > 0): ?>
                                    <?php while ($peminjaman = mysqli_fetch_assoc($resultPeminjamanTerbaru)): ?>
                                    <tr>
                                        <td class="ps-4">
                                            <div class="d-flex align-items-center">
                                                <div class="rounded-circle bg-slate-100 text-slate-500 d-flex align-items-center justify-content-center me-3" style="width: 32px; height: 32px; font-size: 0.75rem; font-weight: 700;">
                                                    <i class="fas fa-user"></i>
                                                </div>
                                                <span class="fw-700 text-dark"><?php echo htmlspecialchars($peminjaman['username']); ?></span>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="fw-600 text-slate-700"><?php echo htmlspecialchars($peminjaman['nama_barang']); ?></div>
                                        </td>
                                        <td>
                                            <div class="text-slate-500 small"><?php echo date('d M Y', strtotime($peminjaman['created_at'])); ?></div>
                                        </td>
                                        <td class="text-center">
                                            <?php 
                                            $st = strtolower($peminjaman['status']);
                                            $badgeClass = 'status-available'; // Default
                                            
                                            if($st == 'selesai') $badgeClass = 'status-tersedia';
                                            else if($st == 'dipinjam') $badgeClass = 'status-disetujui';
                                            else if($st == 'pending') $badgeClass = 'status-warning';
                                            else if($st == 'ditolak' || $st == 'dibatalkan') $badgeClass = 'status-danger';
                                            ?>
                                            <span class="status-badge <?= $badgeClass ?>" style="font-size: 0.7rem;">
                                                <i class="fas fa-<?= 
                                                    ($st == 'selesai') ? 'check-circle' : 
                                                    (($st == 'dipinjam') ? 'truck' : 
                                                    (($st == 'pending') ? 'clock' : 'times-circle')) 
                                                ?>"></i>
                                                <?php echo ucfirst($st); ?>
                                            </span>
                                        </td>
                                    </tr>
                                    <?php endwhile; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="4" class="text-center py-5">
                                            <i class="fas fa-history fa-3x text-slate-200 mb-3"></i>
                                            <p class="text-slate-400">Belum ada transaksi peminjaman terbaru.</p>
                                        </td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- User Terbaru -->
        <div class="col-lg-4">
            <div class="card-admin h-100">
                <div class="card-header-admin">
                    <span><i class="fas fa-user-plus me-2 text-indigo-500"></i> Pengguna Baru</span>
                </div>
                <div class="card-body-admin p-0">
                    <div class="list-group list-group-flush">
                        <?php if (mysqli_num_rows($resultUserTerbaru) > 0): ?>
                            <?php while ($user = mysqli_fetch_assoc($resultUserTerbaru)): ?>
                            <div class="list-group-item d-flex align-items-center p-4 border-0 border-bottom">
                                <div class="rounded-circle bg-indigo-50 text-indigo-600 d-flex align-items-center justify-content-center me-3" style="width: 44px; height: 44px; font-weight: 700;">
                                    <?php echo strtoupper(substr($user['username'], 0, 1)); ?>
                                </div>
                                <div class="flex-grow-1">
                                    <h6 class="mb-0 fw-700 text-dark"><?php echo htmlspecialchars($user['username']); ?></h6>
                                    <small class="text-secondary text-uppercase fw-600" style="font-size: 0.7rem;"><?php echo $user['role']; ?></small>
                                </div>
                                <div class="text-end">
                                    <small class="text-secondary"><?php echo date('d M', strtotime($user['created_at'])); ?></small>
                                </div>
                            </div>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <div class="p-5 text-center text-secondary">Belum ada pengguna terdaftar.</div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
        
    </div> <!-- end row -->

</div> <!-- end content-wrapper -->

<!-- Load Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Global Config
        Chart.defaults.font.family = "'Inter', sans-serif";
        Chart.defaults.color = '#64748b';

        // Data Peminjaman
        const pemCtx = document.getElementById('peminjamanChart');
        if (pemCtx) {
            const peminjamanCtx = pemCtx.getContext('2d');
            const gradientFill = peminjamanCtx.createLinearGradient(0, 0, 0, 300);
            gradientFill.addColorStop(0, 'rgba(79, 70, 229, 0.15)');
            gradientFill.addColorStop(1, 'rgba(79, 70, 229, 0)');

            new Chart(peminjamanCtx, {
                type: 'line',
                data: {
                    labels: <?php echo json_encode($chartPeminjamanLables); ?>,
                    datasets: [{
                        label: 'Jumlah Sewa',
                        data: <?php echo json_encode($chartPeminjamanData); ?>,
                        borderColor: '#4f46e5',
                        backgroundColor: gradientFill,
                        borderWidth: 3,
                        pointBackgroundColor: '#ffffff',
                        pointBorderColor: '#4f46e5',
                        pointBorderWidth: 2,
                        pointRadius: 5,
                        pointHoverRadius: 7,
                        fill: true,
                        tension: 0.4
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            backgroundColor: '#1e293b',
                            padding: 12,
                            titleFont: { size: 14, weight: 'bold' },
                            bodyFont: { size: 14 },
                            displayColors: false,
                            callbacks: {
                                label: function(context) { return ' ' + context.parsed.y + ' Transaksi'; }
                            }
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            grid: { color: '#f1f5f9', drawBorder: false },
                            ticks: { padding: 10, stepSize: 1 }
                        },
                        x: {
                            grid: { display: false },
                            ticks: { padding: 10 }
                        }
                    }
                }
            });
        }

        // Data Barang
        const brgCtx = document.getElementById('barangChart');
        if (brgCtx) {
            const barangCtx = brgCtx.getContext('2d');
            new Chart(barangCtx, {
                type: 'doughnut',
                data: {
                    labels: <?php echo json_encode($chartBarangLabels); ?>,
                    datasets: [{
                        data: <?php echo json_encode($chartBarangData); ?>,
                        backgroundColor: ['#10b981', '#f59e0b', '#ef4444', '#64748b', '#4f46e5'],
                        borderWidth: 0,
                        hoverOffset: 10
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    cutout: '78%',
                    plugins: {
                        legend: {
                            position: 'bottom',
                            labels: {
                                usePointStyle: true,
                                padding: 25,
                                font: { size: 13, weight: '500' },
                                color: '#334155'
                            }
                        },
                        tooltip: {
                            backgroundColor: '#1e293b',
                            padding: 12,
                            titleFont: { size: 14, weight: 'bold' },
                            bodyFont: { size: 14 },
                            callbacks: {
                                label: function(context) { return ' ' + context.label + ': ' + context.parsed + ' Unit'; }
                            }
                        }
                    }
                }
            });
        }
    });
</script>

<?php include '../../partials/footer.php'; ?>
