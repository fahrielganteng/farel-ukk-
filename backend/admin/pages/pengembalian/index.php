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
        <!-- Main Card -->
        <div class="card-admin">
            <!-- Card Header -->
            <div class="card-header-admin d-flex align-items-center justify-content-between">
                <h2 class="page-title">
                    <i class="fas fa-exchange-alt"></i>
                    Data Pengembalian Alat Berat
                </h2>
                <a href="./create.php" class="btn btn-add">
                    <i class="fas fa-plus-circle"></i>
                    Tambah Pengembalian
                </a>
            </div>
            
            <!-- Card Body -->
            <div class="card-body-admin">
                <!-- Session Messages -->
                <?php if (isset($_SESSION['success'])): ?>
                    <div class="alert-container">
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
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
                    <div class="alert-container">
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            <?php
                            echo $_SESSION['error'];
                            unset($_SESSION['error']);
                            ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    </div>
                <?php endif; ?>
                
                <!-- Info Stats -->
                <div class="alert alert-info d-flex align-items-center justify-content-between">
                    <div>
                        <i class="fas fa-info-circle"></i>
                        <strong>Total <?php echo $totalPengembalian; ?> Pengembalian</strong>
                    </div>
                    <?php if ($totalPengembalian > 0): ?>
                        <div class="d-none d-md-flex">
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
                            <span class="stats-badge badge-baik">
                                <i class="fas fa-check-circle me-1"></i> Baik: <?php echo $baikCount; ?>
                            </span>
                            <span class="stats-badge badge-rusak-ringan">
                                <i class="fas fa-exclamation-triangle me-1"></i> Rusak Ringan: <?php echo $rusakRinganCount; ?>
                            </span>
                            <span class="stats-badge badge-rusak-berat">
                                <i class="fas fa-times-circle me-1"></i> Rusak Berat: <?php echo $rusakBeratCount; ?>
                            </span>
                                <i class="fas fa-money-bill-wave me-1"></i> Total Denda: Rp <?php echo number_format($totalDenda, 0, ',', '.'); ?>
                            </span>
                        </div>
                    <?php endif; ?>
                </div>
                
                <!-- Pengembalian Table -->
                <?php if ($totalPengembalian > 0): ?>
                    <div class="table-responsive">
                        <table id="pengembalianTable" class="table-admin table-hover">
                            <thead>
                                <tr>
                                    <th class="text-center" style="width: 60px;">No</th>
                                    <th>Peminjaman</th>
                                    <th>Barang</th>
                                    <th class="text-center">Tanggal Kembali</th>
                                    <th class="text-center">Kondisi</th>
                                    <th class="text-center">Denda</th>
                                    <th class="text-center" style="width: 150px;">Aksi</th>
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
                                ?>
                                <tr>
                                    <td class="text-center fw-bold text-slate-400"><?= $index + 1 ?></td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="rounded-3 bg-indigo-50 text-indigo-600 d-flex align-items-center justify-content-center me-3" style="width: 36px; height: 36px;">
                                                <i class="fas fa-file-contract"></i>
                                            </div>
                                            <div>
                                                <div class="fw-800 text-dark"><?= htmlspecialchars($item['kode_peminjaman'] ?? 'N/A') ?></div>
                                                <small class="text-slate-400">Peminjam: <?= htmlspecialchars($item['nama_peminjam'] ?? '-') ?></small>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="fw-700 text-dark"><?= htmlspecialchars($item['nama_barang'] ?? '-') ?></div>
                                        <?php if ($telatHari > 0): ?>
                                            <span class="badge bg-red-50 text-red-600 fw-700 mt-1" style="font-size: 0.65rem;">
                                                <i class="fas fa-clock me-1"></i> TELAT <?= $telatHari ?> HARI
                                            </span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-center">
                                        <div class="fw-600 text-slate-700"><?= date('d M Y', strtotime($item['tgl_kembali'])) ?></div>
                                        <div class="small text-slate-400"><?= date('H:i', strtotime($item['tgl_kembali'])) ?></div>
                                    </td>
                                    <td class="text-center">
                                        <?php 
                                            $kondClass = 'status-tersedia';
                                            if($item['kondisi'] == 'rusak_ringan') $kondClass = 'status-warning';
                                            else if($item['kondisi'] == 'rusak_berat') $kondClass = 'status-danger';
                                        ?>
                                        <span class="status-badge <?= $kondClass ?>">
                                            <i class="fas fa-<?= 
                                                $item['kondisi'] == 'baik' ? 'check-circle' : 
                                                ($item['kondisi'] == 'rusak_ringan' ? 'exclamation-triangle' : 'times-circle') 
                                            ?>"></i>
                                            <?= ucwords(str_replace('_', ' ', $item['kondisi'])) ?>
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        <?php if ($item['denda'] > 0): ?>
                                            <div class="fw-800 text-danger" style="font-size: 0.85rem;">Rp <?= number_format($item['denda'], 0, ',', '.') ?></div>
                                        <?php else: ?>
                                            <span class="text-slate-300 small italic">Tidak ada denda</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-center">
                                        <div class="d-flex justify-content-center gap-2">
                                            <a href="./detail.php?id=<?= $item['id'] ?>" class="btn btn-sm btn-light border" title="Detail">
                                                <i class="fas fa-eye text-slate-600"></i>
                                            </a>
                                            <a href="./edit.php?id=<?= $item['id'] ?>" class="btn btn-sm btn-light border" title="Edit">
                                                <i class="fas fa-edit text-indigo-600"></i>
                                            </a>
                                            <a href="../../action/pengembalian/destroy.php?id=<?= $item['id'] ?>"
                                                onclick="return confirm('Apakah Anda yakin ingin menghapus data pengembalian ini?')"
                                                class="btn btn-sm btn-light border" title="Hapus">
                                                <i class="fas fa-trash text-danger"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <div class="text-center py-5">
                        <i class="fas fa-exchange-alt fa-4x text-slate-200 mb-3"></i>
                        <h5 class="text-slate-400">Belum Ada Data Pengembalian</h5>
                        <p class="text-slate-300">Mulai dengan menambahkan data pengembalian baru menggunakan tombol di atas</p>
                    </div>
                <?php endif; ?>
                
                <!-- Footer -->
                <div class="mt-5 pt-4 border-top">
                    <p class="text-center text-slate-400 small mb-0">
                        &copy; <?= date('Y') ?> HeavyHire Management System. All Rights Reserved.
                    </p>
                </div>
            </div>
        </div>
    </div><?php include '../../partials/script.php'; ?>



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

