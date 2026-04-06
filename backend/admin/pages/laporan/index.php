<?php
// AKTIFKAN ERROR REPORTING
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();
include '../../app.php';

// Memproses input filter
$tgl_mulai = isset($_GET['tgl_mulai']) ? $_GET['tgl_mulai'] : '';
$tgl_sampai = isset($_GET['tgl_sampai']) ? $_GET['tgl_sampai'] : '';
$status = isset($_GET['status']) ? $_GET['status'] : '';

$where_clauses = [];
if (!empty($tgl_mulai)) {
    $where_clauses[] = "p.tgl_pinjam >= '" . mysqli_real_escape_string($connect, $tgl_mulai) . "'";
}
if (!empty($tgl_sampai)) {
    $where_clauses[] = "p.tgl_pinjam <= '" . mysqli_real_escape_string($connect, $tgl_sampai) . "'";
}
if (!empty($status)) {
    $where_clauses[] = "p.status = '" . mysqli_real_escape_string($connect, $status) . "'";
}

$where_sql = "";
if (count($where_clauses) > 0) {
    $where_sql = "WHERE " . implode(" AND ", $where_clauses);
}

// Query laporan peminjaman dengan filter
$query = "SELECT p.*, u.username, b.nama_barang, b.kode_barang 
          FROM peminjaman p 
          LEFT JOIN users u ON p.user_id = u.id 
          LEFT JOIN barang b ON p.barang_id = b.id 
          $where_sql 
          ORDER BY p.tgl_pinjam DESC";

$result = mysqli_query($connect, $query);

$laporan = [];
if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        $laporan[] = $row;
    }
}
$totalData = count($laporan);

$page = 'laporan';
include '../../partials/header.php';
include '../../partials/sidebar.php';
?>

<div class="content-wrapper">
    <div class="card-admin mb-4">
        <div class="card-header-admin d-flex align-items-center justify-content-between">
            <h2 class="page-title">
                <i class="fas fa-file-invoice me-2 text-primary"></i> Laporan Peminjaman
            </h2>
            <div>
                <a href="cetak.php?tgl_mulai=<?= urlencode($tgl_mulai) ?>&tgl_sampai=<?= urlencode($tgl_sampai) ?>&status=<?= urlencode($status) ?>" target="_blank" class="btn btn-primary">
                    <i class="fas fa-print me-2"></i> Cetak Laporan
                </a>
            </div>
        </div>

        <div class="card-body-admin">
            <!-- Filter Form -->
            <form method="GET" action="index.php" class="row g-3 mb-4 align-items-end p-3 rounded" style="background-color: #f8f9fa;">
                <div class="col-md-3">
                    <label for="tgl_mulai" class="form-label fw-bold small text-muted">Tanggal Mulai</label>
                    <input type="date" class="form-control" name="tgl_mulai" id="tgl_mulai" value="<?= htmlspecialchars($tgl_mulai) ?>">
                </div>
                <div class="col-md-3">
                    <label for="tgl_sampai" class="form-label fw-bold small text-muted">Tanggal Sampai</label>
                    <input type="date" class="form-control" name="tgl_sampai" id="tgl_sampai" value="<?= htmlspecialchars($tgl_sampai) ?>">
                </div>
                <div class="col-md-3">
                    <label for="status" class="form-label fw-bold small text-muted">Status</label>
                    <select name="status" id="status" class="form-select">
                        <option value="">-- Semua Status --</option>
                        <option value="pending" <?= ($status == 'pending') ? 'selected' : '' ?>>Pending</option>
                        <option value="disetujui" <?= ($status == 'disetujui') ? 'selected' : '' ?>>Disetujui</option>
                        <option value="dipinjam" <?= ($status == 'dipinjam') ? 'selected' : '' ?>>Dipinjam</option>
                        <option value="selesai" <?= ($status == 'selesai') ? 'selected' : '' ?>>Selesai/Dikembalikan</option>
                        <option value="ditolak" <?= ($status == 'ditolak') ? 'selected' : '' ?>>Ditolak</option>
                        <option value="dibatalkan" <?= ($status == 'dibatalkan') ? 'selected' : '' ?>>Dibatalkan</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <div style="display: flex; gap: 8px;">
                        <button type="submit" class="btn btn-secondary flex-grow-1">
                            <i class="fas fa-filter me-1"></i> Filter
                        </button>
                        <a href="index.php" class="btn btn-outline-secondary">
                            <i class="fas fa-sync-alt"></i> Reset
                        </a>
                    </div>
                </div>
            </form>

            <!-- Status Alert -->
            <?php if (!empty($tgl_mulai) || !empty($tgl_sampai) || !empty($status)): ?>
            <div class="alert alert-info d-flex align-items-center">
                <i class="fas fa-info-circle me-2"></i>
                Menampilkan hasil untuk: 
                <strong class="ms-1">
                    <?= !empty($tgl_mulai) ? "Dari Tgl: " . date('d-m-Y', strtotime($tgl_mulai)) : "" ?>
                    <?= (!empty($tgl_mulai) && !empty($tgl_sampai)) ? " s/d " : "" ?>
                    <?= !empty($tgl_sampai) ? "Sampai Tgl: " . date('d-m-Y', strtotime($tgl_sampai)) : "" ?>
                    <?= (!empty($status)) ? " | Status: " . ucfirst($status) : "" ?>
                </strong>
            </div>
            <?php endif; ?>

            <?php if ($totalData > 0): ?>
                <div class="table-responsive">
                    <table class="table-admin table-hover">
                        <thead>
                            <tr>
                                <th class="text-center" style="width: 60px;">No</th>
                                <th>Transaksi</th>
                                <th>Peminjam</th>
                                <th>Barang & Jadwal</th>
                                <th class="text-center">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($laporan as $index => $item): ?>
                            <tr>
                                <td class="text-center fw-bold text-slate-400"><?= $index + 1 ?></td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="rounded-3 bg-slate-100 text-slate-500 d-flex align-items-center justify-content-center me-3" style="width: 36px; height: 36px;">
                                            <i class="fas fa-file-invoice"></i>
                                        </div>
                                        <div class="fw-800 text-dark"><?= htmlspecialchars($item['kode_peminjaman'] ?? '-') ?></div>
                                    </div>
                                </td>
                                <td>
                                    <div class="fw-600 text-slate-700"><?= htmlspecialchars($item['username'] ?? 'User Dihapus') ?></div>
                                </td>
                                <td>
                                    <div class="fw-700 text-dark"><?= htmlspecialchars($item['nama_barang'] ?? 'Barang Dihapus') ?></div>
                                    <div class="small text-slate-400 mt-1">
                                        <i class="fas fa-calendar-alt me-1"></i> 
                                        <?= date('d M Y', strtotime($item['tgl_pinjam'])) ?> 
                                        <i class="fas fa-arrow-right mx-1 opacity-50"></i> 
                                        <?= date('d M Y', strtotime($item['tgl_kembali_rencana'])) ?>
                                    </div>
                                </td>
                                <td class="text-center">
                                    <?php 
                                    $st = strtolower($item['status']);
                                    $badgeClass = 'status-available'; // Default
                                    
                                    if($st == 'selesai') $badgeClass = 'status-tersedia';
                                    else if($st == 'dipinjam') $badgeClass = 'status-disetujui';
                                    else if($st == 'pending') $badgeClass = 'status-warning';
                                    else if($st == 'ditolak' || $st == 'dibatalkan') $badgeClass = 'status-danger';
                                    ?>
                                    <span class="status-badge <?= $badgeClass ?>">
                                        <i class="fas fa-<?= 
                                            ($st == 'selesai') ? 'check-circle' : 
                                            (($st == 'dipinjam') ? 'truck' : 
                                            (($st == 'pending') ? 'clock' : 'times-circle')) 
                                        ?>"></i>
                                        <?= ucfirst($st) ?>
                                    </span>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <div class="text-center py-5">
                    <i class="fas fa-box-open fa-4x text-slate-200 mb-3"></i>
                    <h5 class="text-slate-400">Data Tidak Ditemukan</h5>
                    <p class="text-slate-300">Tidak ada transaksi peminjaman yang cocok dengan filter tanggal/status tersebut.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php 
// Sertakan scripts
include '../../partials/footer.php'; 
include '../../partials/script.php'; 
?>
