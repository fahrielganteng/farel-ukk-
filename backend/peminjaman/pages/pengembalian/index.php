<?php
// AKTIFKAN ERROR REPORTING
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Pastikan session_start() di awal
session_start();

// Include koneksi database
include '../../app.php';

// Ambil user_id dari session
$user_id = $_SESSION['user_id'];

// Query pengembalian milik user yang sedang login
$qPengembalian = "SELECT 
                    pb.*, 
                    p.kode_peminjaman,
                    p.user_id,
                    p.barang_id,
                    p.tgl_pinjam,
                    p.tgl_kembali_rencana,
                    p.status as status_peminjaman,
                    u.username as nama_peminjam,
                    b.nama_barang,
                    b.kode_barang
                  FROM pengembalian pb
                  LEFT JOIN peminjaman p ON pb.peminjaman_id = p.id
                  LEFT JOIN users u ON p.user_id = u.id
                  LEFT JOIN barang b ON p.barang_id = b.id
                  WHERE p.user_id = '$user_id'
                  ORDER BY pb.id DESC";
$result = mysqli_query($connect, $qPengembalian);

if (!$result) {
    die("Query error: " . mysqli_error($connect));
}

// Simpan data ke array untuk digunakan nanti
$pengembalian = [];
$baikCount = 0;
$rusakCount = 0;
$totalDenda = 0;

while ($row = mysqli_fetch_assoc($result)) {
    $pengembalian[] = $row;
    // Hitung statistik
    if (strtolower($row['kondisi']) == 'baik') $baikCount++;
    else $rusakCount++;
    $totalDenda += $row['denda'];
}

// Hitung total data
$totalPengembalian = count($pengembalian);
?>

<?php 
include '../../partials/header.php'; 
$page = 'pengembalian'; 
include '../../partials/sidebar.php'; 
?>

<div class="content-wrapper">
    <div class="page-title">
        <i class="fas fa-exchange-alt"></i>
        <span>Data Pengembalian</span>
    </div>

    <!-- Alert Messages -->
    <?php if (isset($_SESSION['success'])): ?>
        <div class="alert status-selesai mb-4 border-0 shadow-sm d-flex align-items-center">
            <i class="fas fa-check-circle me-3 fa-lg"></i>
            <div><?= $_SESSION['success']; unset($_SESSION['success']); ?></div>
        </div>
    <?php endif; ?>

    <!-- Summary Stats -->
    <div class="row g-4 mb-4">
        <div class="col-md-3">
            <div class="card-admin p-3 text-center border-start border-emerald-500 border-4">
                <div class="h4 fw-900 text-emerald-600 mb-0"><?= safeNumberFormat($baikCount) ?></div>
                <div class="small fw-700 text-slate-400 uppercase">Kondisi Baik</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card-admin p-3 text-center border-start border-rose-500 border-4">
                <div class="h4 fw-900 text-rose-600 mb-0"><?= safeNumberFormat($rusakCount) ?></div>
                <div class="small fw-700 text-slate-400 uppercase">Perlu Perbaikan</div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card-admin p-3 text-center border-start border-amber-500 border-4">
                <div class="h4 fw-900 text-amber-600 mb-0">Rp <?= safeNumberFormat($totalDenda, 0, ',', '.') ?></div>
                <div class="small fw-700 text-slate-400 uppercase">Total Denda Dibayarkan</div>
            </div>
        </div>
    </div>

    <!-- Table Card -->
    <div class="card-admin shadow-sm overflow-hidden">
        <div class="card-header-admin bg-white">
            <i class="fas fa-table me-2 text-indigo-600"></i> Riwayat Pengembalian Barang
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table id="pengembalianTable" class="table-admin mb-0">
                    <thead>
                        <tr>
                            <th class="text-center" style="width: 50px;">No</th>
                            <th>Peminjaman & Barang</th>
                            <th>Tanggal Kembali</th>
                            <th>Kondisi Alat</th>
                            <th class="text-end">Denda</th>
                            <th class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($pengembalian as $index => $item): ?>
                        <tr>
                            <td class="text-center fw-700 text-slate-400"><?= $index + 1 ?></td>
                            <td>
                                <div class="fw-800 text-slate-800"><?= htmlspecialchars($item['nama_barang']) ?></div>
                                <div class="small text-indigo-600 fw-700"><?= htmlspecialchars($item['kode_peminjaman']) ?></div>
                            </td>
                            <td>
                                <div class="fw-700 text-slate-700"><?= date('d M Y', strtotime($item['tgl_kembali'])) ?></div>
                                <div class="small text-slate-400">Jadwal: <?= date('d M Y', strtotime($item['tgl_kembali_rencana'])) ?></div>
                            </td>
                            <td>
                                <span class="status-badge status-<?= strtolower(str_replace(' ', '_', $item['kondisi'])) ?>">
                                    <?= ucwords(str_replace('_', ' ', $item['kondisi'])) ?>
                                </span>
                            </td>
                            <td class="text-end fw-800 <?= $item['denda'] > 0 ? 'text-rose-600' : 'text-emerald-600' ?>">
                                Rp <?= safeNumberFormat($item['denda'], 0, ',', '.') ?>
                            </td>
                            <td class="text-center">
                                <a href="./detail.php?id=<?= $item['id'] ?>" class="btn btn-sm btn-indigo" title="Detail">
                                    <i class="fas fa-eye"></i>
                                </a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
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
    if ($('#pengembalianTable').length) {
        $('#pengembalianTable').DataTable({
            language: {
                url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/id.json'
            },
            pageLength: 10,
            responsive: true,
            order: [[0, 'asc']],
            columnDefs: [
                { targets: [5], orderable: false }
            ]
        });
    }
});
</script>

<?php include '../../partials/footer.php'; ?>