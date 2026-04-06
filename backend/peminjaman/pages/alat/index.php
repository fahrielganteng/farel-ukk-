<?php
// AKTIFKAN ERROR REPORTING
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Pastikan session_start() di awal
session_start();

// Include koneksi database
include '../../app.php';

// Query semua alat berat dengan join ke tabel kategori
$qAlat = "SELECT 
            b.*,
            k.nama_kategori as nama_kategori
          FROM barang b 
          LEFT JOIN kategori k ON b.kategori_id = k.id 
          ORDER BY b.id DESC";
$result = mysqli_query($connect, $qAlat);

if (!$result) {
    die("Query error: " . mysqli_error($connect));
}

// Simpan data ke array untuk digunakan nanti
$alats = [];
$tersediaCount = 0;
$dipinjamCount = 0;
$rusakCount = 0;
$totalAlats = 0;

while ($row = mysqli_fetch_assoc($result)) {
    $alats[] = $row;
    // Hitung status
    $status = strtolower($row['status']);
    if ($status == 'tersedia') $tersediaCount++;
    if ($status == 'dipinjam') $dipinjamCount++;
    if ($status == 'rusak') $rusakCount++;
}

// Hitung total data
$totalAlats = count($alats);
?>

<?php 
include '../../partials/header.php'; 
$page = 'alat'; 
include '../../partials/sidebar.php'; 
?>

<div class="content-wrapper">
    <div class="page-title">
        <i class="fas fa-hard-hat"></i>
        <span>Katalog Alat Berat</span>
    </div>

    <!-- Alert Messages -->
    <?php if (isset($_SESSION['success'])): ?>
        <div class="alert status-selesai mb-4 border-0 shadow-sm d-flex align-items-center">
            <i class="fas fa-check-circle me-3 fa-lg"></i>
            <div><?= $_SESSION['success']; unset($_SESSION['success']); ?></div>
        </div>
    <?php endif; ?>

    <!-- Summary Row -->
    <div class="row g-4 mb-4">
        <div class="col-md-3">
            <div class="card-admin p-4 text-center">
                <div class="h3 fw-900 text-slate-900 mb-1"><?= safeNumberFormat($tersediaCount) ?></div>
                <div class="small fw-700 text-emerald-500 uppercase tracking-wider">Tersedia</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card-admin p-4 text-center">
                <div class="h3 fw-900 text-slate-900 mb-1"><?= safeNumberFormat($dipinjamCount) ?></div>
                <div class="small fw-700 text-indigo-500 uppercase tracking-wider">Sedang Disewa</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card-admin p-4 text-center">
                <div class="h3 fw-900 text-slate-900 mb-1"><?= safeNumberFormat($rusakCount) ?></div>
                <div class="small fw-700 text-amber-500 uppercase tracking-wider">Maintenance</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card-admin p-4 text-center">
                <div class="h3 fw-900 text-slate-900 mb-1"><?= safeNumberFormat($totalAlats) ?></div>
                <div class="small fw-700 text-slate-500 uppercase tracking-wider">Total Koleksi</div>
            </div>
        </div>
    </div>

    <!-- Table Card -->
    <div class="card-admin shadow-sm overflow-hidden mt-4">
        <div class="card-header-admin bg-white d-flex justify-content-between align-items-center">
            <span><i class="fas fa-list me-2 text-indigo-600"></i> Daftar Koleksi Alat Berat</span>
            <div class="small fw-600 text-slate-400">Total: <?= $totalAlats ?> Unit</div>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table id="alatTable" class="table-admin mb-0">
                    <thead>
                        <tr>
                            <th class="text-center" style="width: 50px;">No</th>
                            <th style="width: 80px;">Foto</th>
                            <th>Info Alat & Merk</th>
                            <th>Kategori</th>
                            <th>Tahun</th>
                            <th>Harga Sewa</th>
                            <th class="text-center">Status</th>
                            <th class="text-center" style="width: 150px;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($alats as $index => $item): ?>
                        <tr>
                            <td class="text-center fw-700 text-slate-400"><?= $index + 1 ?></td>
                            <td>
                                <?php if (!empty($item['gambar'])): ?>
                                <img src="/farel-ukk-/storages/alat/<?= htmlspecialchars($item['gambar']) ?>" alt="<?= htmlspecialchars($item['nama_barang']) ?>" style="width:60px;height:50px;object-fit:cover;border-radius:8px;border:1px solid #e2e8f0;">
                                <?php else: ?>
                                <div style="width:60px;height:50px;border-radius:8px;background:#f1f5f9;display:flex;align-items:center;justify-content:center;border:1px solid #e2e8f0;">
                                    <i class="fas fa-image" style="color:#cbd5e1;font-size:1.25rem;"></i>
                                </div>
                                <?php endif; ?>
                            </td>
                            <td>
                                <div class="fw-800 text-slate-800"><?= htmlspecialchars($item['nama_barang']) ?></div>
                                <div class="small text-indigo-600 fw-700"><?= htmlspecialchars($item['merk'] ?: 'N/A') ?></div>
                            </td>
                            <td>
                                <span class="badge bg-slate-100 text-slate-600 px-3 py-2 rounded-pill fw-700">
                                    <?= htmlspecialchars($item['nama_kategori']) ?>
                                </span>
                            </td>
                            <td class="fw-700 text-slate-700"><?= htmlspecialchars($item['tahun'] ?: '-') ?></td>
                            <td>
                                <div class="fw-800 text-indigo-600">Rp <?= safeNumberFormat($item['harga_sewa_perhari'], 0, ',', '.') ?></div>
                                <div class="small text-slate-400 fw-600">/ Hari</div>
                            </td>
                            <td class="text-center">
                                <span class="status-badge status-<?= strtolower($item['status']) ?> w-100 py-2">
                                    <i class="fas fa-<?= strtolower($item['status']) == 'tersedia' ? 'check-circle' : 'exclamation-circle' ?> me-1"></i>
                                    <?= ucfirst($item['status']) ?>
                                </span>
                            </td>
                            <td class="text-center">
                                <a href="../peminjaman/create.php?id=<?= $item['id'] ?>" 
                                   class="btn btn-sm btn-indigo px-3 <?= $item['status'] != 'tersedia' ? 'disabled bg-slate-200 border-slate-200' : '' ?>" 
                                   title="Sewa Sekarang">
                                   <i class="fas fa-shopping-cart me-1"></i> Sewa
                                </a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                        
                        <?php if ($totalAlats == 0): ?>
                        <tr>
                            <td colspan="7" class="py-5 text-center text-slate-400 fw-600">
                                <i class="fas fa-box-open fa-3x mb-3 d-block opacity-25"></i>
                                Belum ada koleksi alat berat yang tersedia.
                            </td>
                        </tr>
                        <?php endif; ?>
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
    if ($('#alatTable').length) {
        $('#alatTable').DataTable({
            language: {
                url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/id.json'
            },
            pageLength: 10,
            responsive: true,
            order: [[0, 'asc']],
            columnDefs: [
                { targets: [7], orderable: false }
            ]
        });
    }
});
</script>

<?php include '../../partials/footer.php'; ?>