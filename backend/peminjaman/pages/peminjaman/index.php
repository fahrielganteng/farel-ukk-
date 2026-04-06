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

// Query peminjaman milik user yang sedang login
$qPeminjaman = "SELECT p.*, u.username, b.nama_barang 
                FROM peminjaman p 
                LEFT JOIN users u ON p.user_id = u.id 
                LEFT JOIN barang b ON p.barang_id = b.id 
                WHERE p.user_id = '$user_id'
                ORDER BY p.id DESC";
$result = mysqli_query($connect, $qPeminjaman);

if (!$result) {
    die("Query error: " . mysqli_error($connect));
}

// Simpan data ke array
$peminjamans = [];
while ($row = mysqli_fetch_assoc($result)) {
    // Hitung durasi jika tidak ada di DB
    if (!isset($row['durasi'])) {
        $tgl1 = new DateTime($row['tgl_pinjam']);
        $tgl2 = new DateTime($row['tgl_kembali_rencana']);
        $diff = $tgl1->diff($tgl2);
        $row['durasi'] = $diff->days ?: 1; // Minimal 1 hari
    }
    $peminjamans[] = $row;
}

// Hitung total data
$totalPeminjaman = count($peminjamans);
?>

<?php 
include '../../partials/header.php'; 
$page = 'peminjaman'; 
include '../../partials/sidebar.php'; 
?>

<div class="content-wrapper">
    <div class="page-title">
        <i class="fas fa-clipboard-list"></i>
        <span>Riwayat Peminjaman</span>
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
        <?php
        $dipinjam = 0;
        $dikembalikan = 0;
        $terlambat = 0;
        $disetujui = 0;
        $ditolak = 0;
        $pending = 0;
        $today = date('Y-m-d');

        foreach ($peminjamans as $item) {
            $status = strtolower($item['status'] ?? 'pending');
            $tglKembali = $item['tgl_kembali_rencana'];
            $tglAktual = $item['tgl_kembali_aktual'] ?? '';
            
            if ($status == 'dikembalikan') $dikembalikan++;
            elseif ($status == 'terlambat' || ($today > $tglKembali && empty($tglAktual))) $terlambat++;
            elseif ($status == 'dipinjam') $dipinjam++;
            elseif ($status == 'disetujui') $disetujui++;
            elseif ($status == 'ditolak') $ditolak++;
            else $pending++;
        }
        ?>
        <div class="col-md-2">
            <div class="card-admin p-3 text-center">
                <div class="h4 fw-900 text-slate-900 mb-0"><?= safeNumberFormat($pending) ?></div>
                <div class="small fw-700 text-slate-400 uppercase">Pending</div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card-admin p-3 text-center border-start border-indigo-500 border-4">
                <div class="h4 fw-900 text-indigo-600 mb-0"><?= safeNumberFormat($disetujui) ?></div>
                <div class="small fw-700 text-slate-400 uppercase">Disetujui</div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card-admin p-3 text-center border-start border-emerald-500 border-4">
                <div class="h4 fw-900 text-emerald-600 mb-0"><?= safeNumberFormat($dipinjam) ?></div>
                <div class="small fw-700 text-slate-400 uppercase">Dipinjam</div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card-admin p-3 text-center border-start border-amber-500 border-4">
                <div class="h4 fw-900 text-amber-600 mb-0"><?= safeNumberFormat($dikembalikan) ?></div>
                <div class="small fw-700 text-slate-400 uppercase">Kembali</div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card-admin p-3 text-center border-start border-rose-500 border-4">
                <div class="h4 fw-900 text-rose-600 mb-0"><?= safeNumberFormat($terlambat) ?></div>
                <div class="small fw-700 text-slate-400 uppercase">Terlambat</div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card-admin p-3 text-center">
                <div class="h4 fw-900 text-slate-400 mb-0"><?= safeNumberFormat($ditolak) ?></div>
                <div class="small fw-700 text-slate-400 uppercase">Ditolak</div>
            </div>
        </div>
    </div>

    <!-- Table Card -->
    <div class="card-admin shadow-sm overflow-hidden">
        <div class="card-header-admin bg-white d-flex justify-content-between align-items-center">
            <span><i class="fas fa-table me-2 text-indigo-600"></i> Daftar Transaksi Peminjaman</span>
            <a href="./create.php" class="btn btn-indigo btn-sm px-3">
                <i class="fas fa-plus me-1"></i> Baru
            </a>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table id="peminjamanTable" class="table-admin mb-0">
                    <thead>
                        <tr>
                            <th class="text-center" style="width: 50px;">No</th>
                            <th>Kode & Barang</th>
                            <th>Tanggal Pinjam</th>
                            <th>Durasi & Harga</th>
                            <th class="text-center">Status</th>
                            <th class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($peminjamans as $index => $item): ?>
                        <tr>
                            <td class="text-center fw-700 text-slate-400"><?= $index + 1 ?></td>
                            <td>
                                <div class="fw-800 text-slate-800"><?= htmlspecialchars($item['nama_barang']) ?></div>
                                <div class="small text-indigo-600 fw-700"><?= htmlspecialchars($item['kode_peminjaman']) ?></div>
                            </td>
                            <td>
                                <div class="fw-700 text-slate-700"><?= date('d M Y', strtotime($item['tgl_pinjam'])) ?></div>
                                <div class="small text-slate-400">Rencana Kembali: <?= date('d M Y', strtotime($item['tgl_kembali_rencana'])) ?></div>
                            </td>
                            <td>
                                <div class="fw-700 text-slate-700"><?= $item['durasi'] ?> Hari x <?= $item['jumlah'] ?> Unit</div>
                                <div class="fw-800 text-emerald-600">Rp <?= safeNumberFormat($item['total_harga'], 0, ',', '.') ?></div>
                            </td>
                            <td class="text-center">
                                <span class="status-badge status-<?= strtolower($item['status']) ?>">
                                    <?= ucfirst($item['status']) ?>
                                </span>
                            </td>
                            <td class="text-center">
                                <div class="d-flex justify-content-center gap-2">
                                    <a href="./detail.php?id=<?= $item['id'] ?>" class="btn btn-sm btn-indigo" title="Detail">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <?php if (strtolower($item['status']) == 'pending'): ?>
                                    <a href="./edit.php?id=<?= $item['id'] ?>" class="btn btn-sm btn-amber" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <a href="../../action/peminjaman/destroy.php?id=<?= $item['id'] ?>" 
                                       class="btn btn-sm btn-rose" 
                                       title="Batal"
                                       onclick="return confirm('Batalkan pengajuan ini?')">
                                        <i class="fas fa-times"></i>
                                    </a>
                                    <?php endif; ?>
                                </div>
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
    if ($('#peminjamanTable').length) {
        $('#peminjamanTable').DataTable({
            language: {
                url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/id.json'
            },
            pageLength: 10,
            responsive: true,
            order: [[0, 'asc']],
            columnDefs: [
                { targets: [4, 5], orderable: false }
            ]
        });
    }
});
</script>

<?php include '../../partials/footer.php'; ?>