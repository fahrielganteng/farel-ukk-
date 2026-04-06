<?php
// AKTIFKAN ERROR REPORTING
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Pastikan session_start() di awal
session_start();

// Include koneksi database
include '../../app.php';

// Tanggal filter default (bulan ini)
$start_date = isset($_GET['start_date']) ? $_GET['start_date'] : date('Y-m-01');
$end_date = isset($_GET['end_date']) ? $_GET['end_date'] : date('Y-m-t');
$tipe_laporan = isset($_GET['tipe_laporan']) ? $_GET['tipe_laporan'] : 'peminjaman';

// Query berdasarkan tipe laporan
$reports = [];
$total_data = 0;

switch($tipe_laporan) {
    case 'peminjaman':
        // Laporan Peminjaman
        $query = "SELECT 
                    p.*,
                    u.username as nama_peminjam,
                    b.nama_barang,
                    b.harga_sewa_perhari,
                    k.nama_kategori
                FROM peminjaman p
                LEFT JOIN users u ON p.user_id = u.id
                LEFT JOIN barang b ON p.barang_id = b.id
                LEFT JOIN kategori k ON b.kategori_id = k.id
                WHERE DATE(p.created_at) BETWEEN '$start_date' AND '$end_date'
                ORDER BY p.created_at DESC";
        
        $result = mysqli_query($connect, $query);
        if ($result) {
            while ($row = mysqli_fetch_assoc($result)) {
                $reports[] = $row;
            }
            $total_data = count($reports);
        }
        break;
        
    case 'pengembalian':
        // Laporan Pengembalian
        $query = "SELECT 
                    pg.*,
                    p.kode_peminjaman,
                    u.username as nama_peminjam,
                    b.nama_barang,
                    b.harga_sewa_perhari
                FROM pengembalian pg
                LEFT JOIN peminjaman p ON pg.peminjaman_id = p.id
                LEFT JOIN users u ON p.user_id = u.id
                LEFT JOIN barang b ON p.barang_id = b.id
                WHERE DATE(pg.created_at) BETWEEN '$start_date' AND '$end_date'
                ORDER BY pg.created_at DESC";
        
        $result = mysqli_query($connect, $query);
        if ($result) {
            while ($row = mysqli_fetch_assoc($result)) {
                $reports[] = $row;
            }
            $total_data = count($reports);
        }
        break;
        
    case 'log_aktivitas':
        // Laporan Log Aktivitas
        $query = "SELECT 
                    la.*,
                    u.username
                FROM log_aktivitas la
                LEFT JOIN users u ON la.user_id = u.id
                WHERE DATE(la.created_at) BETWEEN '$start_date' AND '$end_date'
                ORDER BY la.created_at DESC";
        
        $result = mysqli_query($connect, $query);
        if ($result) {
            while ($row = mysqli_fetch_assoc($result)) {
                $reports[] = $row;
            }
            $total_data = count($reports);
        }
        break;
        
    case 'kategori':
        // Laporan Kategori
        $query = "SELECT 
                    k.*,
                    COUNT(b.id) as total_barang
                FROM kategori k
                LEFT JOIN barang b ON k.id = b.kategori_id
                GROUP BY k.id
                ORDER BY k.created_at DESC";
        
        $result = mysqli_query($connect, $query);
        if ($result) {
            while ($row = mysqli_fetch_assoc($result)) {
                $reports[] = $row;
            }
            $total_data = count($reports);
        }
        break;
}

// Hitung total pendapatan dan denda untuk periode ini - PERBAIKAN DI SINI
$qPendapatan = "SELECT COALESCE(SUM(total_harga), 0) as total_pendapatan 
                FROM peminjaman 
                WHERE status = 'selesai' 
                AND DATE(created_at) BETWEEN '$start_date' AND '$end_date'";
$resultPendapatan = mysqli_query($connect, $qPendapatan);
if ($resultPendapatan) {
    $row = mysqli_fetch_assoc($resultPendapatan);
    $total_pendapatan = $row['total_pendapatan'] ?? 0;
} else {
    $total_pendapatan = 0;
}

$qDenda = "SELECT COALESCE(SUM(denda), 0) as total_denda 
           FROM pengembalian 
           WHERE DATE(created_at) BETWEEN '$start_date' AND '$end_date'";
$resultDenda = mysqli_query($connect, $qDenda);
if ($resultDenda) {
    $row = mysqli_fetch_assoc($resultDenda);
    $total_denda = $row['total_denda'] ?? 0;
} else {
    $total_denda = 0;
}

// Fungsi untuk format angka dengan aman
if (!function_exists('safeNumberFormat')) {
    function safeNumberFormat($number, $decimals = 0, $decimal_separator = ',', $thousands_separator = '.') {
        if ($number === null || $number === '') {
            return '0';
        }
        
        // Pastikan $number adalah numerik
        $number = (float) $number;
        
        return number_format($number, $decimals, $decimal_separator, $thousands_separator);
    }
}
?>

<?php 
include '../../partials/header.php'; 
$page = 'laporan'; 
include '../../partials/sidebar.php'; 
?>

<div class="content-wrapper">
    <div class="page-title">
        <i class="fas fa-chart-bar"></i>
        <span>Laporan & Analitik</span>
    </div>
        <!-- Main Card -->
        <div class="card-admin shadow-lg border-0">
            <div class="card-header-admin d-flex align-items-center justify-content-between no-print">
                <span><i class="fas fa-filter me-2 text-indigo-500"></i> Kontrol Laporan</span>
                <div class="d-flex gap-2">
                    <button onclick="exportToPDF()" class="btn btn-sm btn-rose"><i class="fas fa-file-pdf me-1"></i> Export PDF</button>
                    <button onclick="window.print()" class="btn btn-sm btn-indigo"><i class="fas fa-print me-1"></i> Cetak</button>
                    <a href="?" class="btn btn-sm btn-slate"><i class="fas fa-sync-alt"></i></a>
                </div>
            </div>
            
            <div class="card-body-admin p-4">
                <!-- Filter Section -->
                <div class="no-print mb-4 p-4 bg-slate-50 rounded-4 border">
                    <form method="GET" action="" class="row g-3 align-items-end">
                        <div class="col-md-3">
                            <label class="form-label fw-800 text-slate-700 small uppercase tracking-wider">Tipe Laporan</label>
                            <select name="tipe_laporan" class="form-select fw-600 rounded-3 border-slate-200" onchange="this.form.submit()">
                                <option value="peminjaman" <?= $tipe_laporan == 'peminjaman' ? 'selected' : '' ?>>Peminjaman</option>
                                <option value="pengembalian" <?= $tipe_laporan == 'pengembalian' ? 'selected' : '' ?>>Pengembalian</option>
                                <option value="log_aktivitas" <?= $tipe_laporan == 'log_aktivitas' ? 'selected' : '' ?>>Log Aktivitas</option>
                                <option value="kategori" <?= $tipe_laporan == 'kategori' ? 'selected' : '' ?>>Kategori</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-800 text-slate-700 small uppercase tracking-wider">Mulai</label>
                            <input type="date" name="start_date" class="form-control fw-600 rounded-3 border-slate-200" value="<?= $start_date ?>">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-800 text-slate-700 small uppercase tracking-wider">Selesai</label>
                            <input type="date" name="end_date" class="form-control fw-600 rounded-3 border-slate-200" value="<?= $end_date ?>">
                        </div>
                        <div class="col-md-3">
                            <button type="submit" class="btn btn-indigo w-100 fw-700 py-2 rounded-3 shadow-sm">
                                <i class="fas fa-search me-2"></i> Terapkan Filter
                            </button>
                        </div>
                    </form>
                </div>
                
                <!-- Quick Summary -->
                <div class="row g-4 mb-5">
                    <div class="col-md-4">
                        <div class="p-4 rounded-4 bg-white border border-slate-100 shadow-sm transition-all hover-translate-y">
                            <div class="d-flex align-items-center mb-3">
                                <div class="bg-indigo-50 text-indigo-600 p-3 rounded-3 me-3">
                                    <i class="fas fa-layer-group fa-lg"></i>
                                </div>
                                <div class="fw-800 text-slate-500 small uppercase tracking-wider">Total Records</div>
                            </div>
                            <div class="h2 fw-900 text-slate-900 mb-0"><?= safeNumberFormat($total_data) ?></div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="p-4 rounded-4 bg-white border border-slate-100 shadow-sm transition-all hover-translate-y">
                            <div class="d-flex align-items-center mb-3">
                                <div class="bg-emerald-50 text-emerald-600 p-3 rounded-3 me-3">
                                    <i class="fas fa-wallet fa-lg"></i>
                                </div>
                                <div class="fw-800 text-slate-500 small uppercase tracking-wider">Pendapatan</div>
                            </div>
                            <div class="h2 fw-900 text-emerald-600 mb-0">Rp <?= safeNumberFormat($total_pendapatan, 0, ',', '.') ?></div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="p-4 rounded-4 bg-white border border-slate-100 shadow-sm transition-all hover-translate-y">
                            <div class="d-flex align-items-center mb-3">
                                <div class="bg-rose-50 text-rose-600 p-3 rounded-3 me-3">
                                    <i class="fas fa-gavel fa-lg"></i>
                                </div>
                                <div class="fw-800 text-slate-500 small uppercase tracking-wider">Total Denda</div>
                            </div>
                            <div class="h2 fw-900 text-rose-600 mb-0">Rp <?= safeNumberFormat($total_denda, 0, ',', '.') ?></div>
                        </div>
                    </div>
                </div>
                
                <!-- Report Table Header -->
                <div class="d-flex align-items-center justify-content-between mb-4">
                    <h3 class="fw-900 text-slate-900 mb-0">
                        <span class="text-indigo-600">|</span> 
                        <?php
                        $report_titles = [
                            'peminjaman' => 'Data Peminjaman',
                            'pengembalian' => 'Data Pengembalian',
                            'log_aktivitas' => 'Log Aktivitas',
                            'kategori' => 'Daftar Kategori'
                        ];
                        echo $report_titles[$tipe_laporan];
                        ?>
                    </h3>
                </div>
                
                <!-- Report Table -->
                <div class="table-responsive rounded-4 border overflow-hidden shadow-sm">
                    <table id="reportTable" class="table-admin mb-0">
                        <thead>
                            <?php if($tipe_laporan == 'peminjaman'): ?>
                            <tr>
                                <th>No</th>
                                <th>Kode</th>
                                <th>Peminjam</th>
                                <th>Barang</th>
                                <th class="text-center">Tanggal Pinjam</th>
                                <th class="text-center">Status</th>
                                <th class="text-end">Total Harga</th>
                            </tr>
                            <?php elseif($tipe_laporan == 'pengembalian'): ?>
                            <tr>
                                <th>No</th>
                                <th>Kode</th>
                                <th>Peminjam</th>
                                <th class="text-center">Tanggal Kembali</th>
                                <th class="text-center">Kondisi</th>
                                <th class="text-end">Denda</th>
                            </tr>
                            <?php elseif($tipe_laporan == 'log_aktivitas'): ?>
                            <tr>
                                <th>No</th>
                                <th>User</th>
                                <th>Aksi</th>
                                <th>Deskripsi</th>
                                <th class="text-center">Waktu</th>
                            </tr>
                            <?php elseif($tipe_laporan == 'kategori'): ?>
                            <tr>
                                <th>No</th>
                                <th>Nama Kategori</th>
                                <th class="text-center">Total Barang</th>
                                <th class="text-center">Tanggal Dibuat</th>
                            </tr>
                            <?php endif; ?>
                        </thead>
                        <tbody>
                            <?php if($total_data > 0): ?>
                                <?php foreach($reports as $index => $row): ?>
                                <tr>
                                    <?php if($tipe_laporan == 'peminjaman'): ?>
                                    <td class="fw-700 text-slate-400"><?= $index + 1 ?></td>
                                    <td><div class="fw-800 text-indigo-600"><?= htmlspecialchars($row['kode_peminjaman']) ?></div></td>
                                    <td><div class="fw-700 text-slate-800"><?= htmlspecialchars($row['nama_peminjam']) ?></div></td>
                                    <td><div class="fw-700 text-slate-800"><?= htmlspecialchars($row['nama_barang']) ?></div></td>
                                    <td class="text-center fw-600 text-slate-600"><?= date('d M Y', strtotime($row['tgl_pinjam'])) ?></td>
                                    <td class="text-center">
                                        <?php $st = strtolower($row['status']); ?>
                                        <span class="status-badge status-<?= $st ?>"><?= ucfirst($st) ?></span>
                                    </td>
                                    <td class="text-end fw-800 text-slate-900">Rp <?= safeNumberFormat($row['total_harga'], 0, ',', '.') ?></td>
                                    
                                    <?php elseif($tipe_laporan == 'pengembalian'): ?>
                                    <td class="fw-700 text-slate-400"><?= $index + 1 ?></td>
                                    <td><div class="fw-800 text-indigo-600"><?= htmlspecialchars($row['kode_peminjaman']) ?></div></td>
                                    <td><div class="fw-700 text-slate-800"><?= htmlspecialchars($row['nama_peminjam']) ?></div></td>
                                    <td class="text-center fw-600 text-slate-600"><?= date('d M Y', strtotime($row['tgl_kembali'])) ?></td>
                                    <td class="text-center">
                                        <?php $cond = strtolower($row['kondisi']); ?>
                                        <span class="status-badge status-<?= ($cond == 'baik' ? 'selesai' : ($cond == 'rusak_ringan' ? 'pending' : 'ditolak')) ?>">
                                            <?= ucwords(str_replace('_', ' ', $cond)) ?>
                                        </span>
                                    </td>
                                    <td class="text-end fw-800 text-rose-600">Rp <?= safeNumberFormat($row['denda'], 0, ',', '.') ?></td>
                                    
                                    <?php elseif($tipe_laporan == 'log_aktivitas'): ?>
                                    <td class="fw-700 text-slate-400"><?= $index + 1 ?></td>
                                    <td><div class="fw-800 text-slate-800"><?= htmlspecialchars($row['username'] ?? 'System') ?></div></td>
                                    <td><span class="badge bg-slate-100 text-slate-600 border fw-700"><?= htmlspecialchars($row['aksi']) ?></span></td>
                                    <td class="text-slate-600 small"><?= htmlspecialchars($row['deskripsi']) ?></td>
                                    <td class="text-center text-slate-400 small"><?= date('d/m/Y H:i', strtotime($row['created_at'])) ?></td>
                                    
                                    <?php elseif($tipe_laporan == 'kategori'): ?>
                                    <td class="fw-700 text-slate-400"><?= $index + 1 ?></td>
                                    <td><div class="fw-800 text-indigo-600"><?= htmlspecialchars($row['nama_kategori']) ?></div></td>
                                    <td class="text-center"><span class="badge bg-indigo-50 text-indigo-600 fw-800 px-3"><?= safeNumberFormat($row['total_barang']) ?> Alat</span></td>
                                    <td class="text-center text-slate-400 small"><?= date('d M Y', strtotime($row['created_at'])) ?></td>
                                    <?php endif; ?>
                                </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="7" class="text-center py-5">
                                        <div class="text-slate-300 mb-3"><i class="fas fa-database fa-4x"></i></div>
                                        <h5 class="text-slate-400 fw-700">Tidak ada data ditemukan</h5>
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
            
            <div class="p-4 bg-slate-50 border-top mt-4 d-print-none text-center">
                <small class="text-slate-400 fw-600 italic">Laporan ini dibuat secara otomatis oleh sistem HeavyHire</small>
            </div>
        </div>
    </div>
</div>

<?php include '../../partials/script.php'; ?>

<!-- PDF Export Script -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
<script>
function exportToPDF() {
    // Tentukan container yang akan diexport
    const element = document.querySelector('.content-wrapper');
    
    // Konfigurasi untuk PDF
    const opt = {
        margin:       [10, 10, 10, 10], // margin in mm [top, left, bottom, right]
        filename:     'Laporan_<?= $tipe_laporan ?>_<?= $start_date ?>_sd_<?= $end_date ?>.pdf',
        image:        { type: 'jpeg', quality: 0.98 },
        html2canvas:  { scale: 2, useCORS: true, letterRendering: true },
        jsPDF:        { unit: 'mm', format: 'a4', orientation: 'landscape' },
        pagebreak:    { mode: ['avoid-all', 'css', 'legacy'] }
    };

    // Tambahkan class khusus untuk styling PDF (sematkan style sementara)
    const style = document.createElement('style');
    style.id = 'pdf-style';
    style.innerHTML = `
        .no-print, .card-header-admin, .sidebar, .top-navbar, .sidebar-overlay { display: none !important; }
        .main-panel { margin-left: 0 !important; width: 100% !important; }
        .content-wrapper { padding: 0 !important; }
        .card-admin { box-shadow: none !important; border: none !important; transform: none !important; }
        .table-responsive { overflow: visible !important; }
        .table-admin thead th { background-color: #f1f5f9 !important; -webkit-print-color-adjust: exact; }
        .page-title { margin-bottom: 20px !important; }
    `;
    document.head.appendChild(style);

    // Jalankan export
    html2pdf().from(element).set(opt).save().then(() => {
        // Hapus style tambahan setelah selesai
        document.getElementById('pdf-style').remove();
    });
}
</script>

</body>
</html>