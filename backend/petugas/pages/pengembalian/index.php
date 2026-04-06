<?php
// AKTIFKAN ERROR REPORTING
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Pastikan session_start() di awal
session_start();

// Include koneksi database
include '../../app.php';

// Query semua pengembalian dengan join ke peminjaman dan barang
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
                  ORDER BY pb.id DESC";
$result = mysqli_query($connect, $qPengembalian);

if (!$result) {
    die("Query error: " . mysqli_error($connect));
}

// Simpan data ke array untuk digunakan nanti
$pengembalian = [];
while ($row = mysqli_fetch_assoc($result)) {
    $pengembalian[] = $row;
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
        <!-- Main Card -->
        <div class="card-admin">
            <div class="card-header-admin d-flex align-items-center justify-content-between">
                <span><i class="fas fa-exchange-alt me-2 text-indigo-500"></i> Daftar Pengembalian</span>
            </div>
            
            <div class="card-body-admin p-0">
                <!-- Session Messages -->
                <?php if (isset($_SESSION['success'])): ?>
                    <div class="p-4">
                        <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm" style="border-radius: 10px;" role="alert">
                            <i class="fas fa-check-circle me-2"></i>
                            <?php
                            echo $_SESSION['success'];
                            unset($_SESSION['success']);
                            ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    </div>
                <?php endif; ?>

                <?php if (isset($_SESSION['error'])): ?>
                    <div class="p-4">
                        <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm" style="border-radius: 10px;" role="alert">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            <?php
                            echo $_SESSION['error'];
                            unset($_SESSION['error']);
                            ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    </div>
                <?php endif; ?>
                
                <!-- Quick Stats -->
                <div class="p-4 bg-slate-50 border-bottom">
                    <div class="row g-3 align-items-center">
                        <div class="col-md-auto">
                            <div class="fw-800 text-slate-800">
                                <i class="fas fa-info-circle text-indigo-500 me-2"></i>
                                Total <?= $totalPengembalian ?> Pengembalian
                            </div>
                        </div>
                        <div class="col">
                            <div class="d-flex flex-wrap gap-2">
                                <?php 
                                $baikCount = 0;
                                $rusakRinganCount = 0;
                                $rusakBeratCount = 0;
                                $totalDenda = 0;
                                foreach ($pengembalian as $item) {
                                    if ($item['kondisi'] == 'baik') $baikCount++;
                                    if ($item['kondisi'] == 'rusak_ringan') $rusakRinganCount++;
                                    if ($item['kondisi'] == 'rusak_berat') $rusakBeratCount++;
                                    $totalDenda += $item['denda'];
                                }
                                ?>
                                <span class="badge bg-white text-emerald-600 border border-emerald-100 fw-700 rounded-pill px-3 py-2 shadow-sm">
                                    Baik: <?= $baikCount ?>
                                </span>
                                <span class="badge bg-white text-amber-600 border border-amber-100 fw-700 rounded-pill px-3 py-2 shadow-sm">
                                    Rusak Ringan: <?= $rusakRinganCount ?>
                                </span>
                                <span class="badge bg-white text-danger border border-danger-100 fw-700 rounded-pill px-3 py-2 shadow-sm">
                                    Rusak Berat: <?= $rusakBeratCount ?>
                                </span>
                                <span class="badge bg-indigo-600 text-white border-0 fw-700 rounded-pill px-3 py-2 shadow-sm ms-md-auto">
                                    Total Denda: Rp <?= number_format($totalDenda, 0, ',', '.') ?>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Pengembalian Table -->
                <div class="table-responsive">
                    <table id="pengembalianTable" class="table-admin mb-0">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Peminjaman</th>
                                <th>Barang</th>
                                <th class="text-center">Tanggal Kembali</th>
                                <th class="text-center">Kondisi</th>
                                <th class="text-center">Denda</th>
                                <th class="text-end">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($pengembalian as $index => $item): 
                                // Hitung hari telat jika ada
                                $telatHari = 0;
                                if (!empty($item['tgl_kembali_rencana']) && !empty($item['tgl_kembali'])) {
                                    $tglRencana = new DateTime($item['tgl_kembali_rencana']);
                                    $tglKembali = new DateTime($item['tgl_kembali']);
                                    if ($tglKembali > $tglRencana) {
                                        $selisih = $tglRencana->diff($tglKembali);
                                        $telatHari = $selisih->days;
                                    }
                                }
                                $cond = strtolower($item['kondisi']);
                            ?>
                            <tr>
                                <td class="fw-700 text-slate-400"><?= $index + 1 ?></td>
                                <td>
                                    <div class="fw-800 text-indigo-600"><?= htmlspecialchars($item['kode_peminjaman'] ?? 'N/A') ?></div>
                                    <div class="small fw-600 text-slate-500">Oleh: <?= htmlspecialchars($item['nama_peminjam'] ?? 'N/A') ?></div>
                                </td>
                                <td>
                                    <div class="fw-700 text-slate-800"><?= htmlspecialchars($item['nama_barang'] ?? 'N/A') ?></div>
                                    <small class="text-slate-400">Kode: <?= htmlspecialchars($item['kode_barang'] ?? '-') ?></small>
                                </td>
                                <td class="text-center">
                                    <div class="fw-700 text-slate-700 small"><?= date('d M Y', strtotime($item['tgl_kembali'])) ?></div>
                                    <?php if ($telatHari > 0): ?>
                                        <span class="badge bg-danger bg-opacity-10 text-danger rounded-pill smaller fw-700 mt-1" style="font-size: 0.7rem;">
                                            <i class="fas fa-clock me-1"></i> Telat <?= $telatHari ?> hari
                                        </span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-center">
                                    <span class="status-badge status-<?= ($cond == 'baik' ? 'selesai' : ($cond == 'rusak_ringan' ? 'pending' : 'ditolak')) ?>">
                                        <i class="fas fa-<?= ($cond == 'baik' ? 'check' : ($cond == 'rusak_ringan' ? 'exclamation' : 'times')) ?> me-1"></i>
                                        <?= ucwords(str_replace('_', ' ', $cond)) ?>
                                    </span>
                                </td>
                                <td class="text-center">
                                    <?php if ($item['denda'] > 0): ?>
                                        <div class="fw-800 text-danger small">Rp <?= number_format($item['denda'], 0, ',', '.') ?></div>
                                    <?php else: ?>
                                        <span class="badge bg-emerald-50 text-emerald-600 border border-emerald-100 fw-700">Tidak Ada</span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-end">
                                    <a href="detail.php?id=<?= $item['id'] ?>" class="btn btn-sm btn-indigo px-3 shadow-sm rounded-pill fw-700">
                                        <i class="fas fa-eye me-1"></i> Detail
                                    </a>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>

                <?php if ($totalPengembalian == 0): ?>
                    <div class="text-center py-5 border-top">
                        <i class="fas fa-exchange-alt fa-4x text-slate-200 mb-3"></i>
                        <h5 class="text-slate-400 italic">Belum ada data pengembalian</h5>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php include '../../partials/script.php'; ?>

<!-- jQuery -->
<script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<!-- DataTables JS -->
<script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>

<script>
$(document).ready(function() {
    // Initialize DataTable
    var table = $('#pengembalianTable').DataTable({
        language: {
            processing: "Memproses...",
            search: "",
            searchPlaceholder: "Cari kode peminjaman atau nama barang...",
            lengthMenu: "Tampilkan _MENU_ data per halaman",
            zeroRecords: "Tidak ada data yang ditemukan",
            info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ data",
            infoEmpty: "Menampilkan 0 sampai 0 dari 0 data",
            infoFiltered: "(disaring dari _MAX_ total data)",
            paginate: {
                first: "Pertama",
                last: "Terakhir",
                next: ">",
                previous: "<"
            }
        },
        pageLength: 10,
        lengthMenu: [[5, 10, 25, 50], [5, 10, 25, 50]],
        order: [[0, 'desc']], // Sort by No (ID terbaru)
        columnDefs: [
            {
                targets: 0, // No column
                orderable: false,
                searchable: false,
                className: 'align-middle'
            },
            {
                targets: 3, // Tanggal Kembali column
                className: 'align-middle'
            },
            {
                targets: 4, // Kondisi column
                className: 'align-middle'
            },
            {
                targets: 5, // Denda column
                className: 'align-middle'
            },
            {
                targets: 6, // Action column
                orderable: false,
                searchable: false,
                className: 'align-middle'
            }
        ],
        initComplete: function() {
            // Custom styling for search box
            $('.dataTables_filter input').addClass('form-control form-control-lg');
            $('.dataTables_filter label').addClass('form-label fw-bold');
            
            // Custom styling for length menu
            $('.dataTables_length select').addClass('form-select form-select-lg');
            $('.dataTables_length label').addClass('form-label fw-bold');
            
            // Add margin to search box
            $('.dataTables_filter').css('margin-bottom', '20px');
        }
    });
    
    // Add animation to table rows
    $('#pengembalianTable tbody tr').each(function(index) {
        $(this).css('opacity', '0');
        $(this).delay(index * 100).animate({ opacity: 1 }, 500);
    });
});
</script>

</body>
</html>