<?php
// AKTIFKAN ERROR REPORTING
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Pastikan session_start() di awal
session_start();

// Include koneksi database
include '../../app.php';

// Query semua peminjaman dengan join termasuk kategori
$qPeminjaman = "SELECT p.*, u.username, b.nama_barang, k.nama_kategori 
          FROM peminjaman p 
          JOIN users u ON p.user_id = u.id 
          JOIN barang b ON p.barang_id = b.id 
          JOIN kategori k ON b.kategori_id = k.id
          ORDER BY p.created_at DESC";
                
$result = mysqli_query($connect, $qPeminjaman);

if (!$result) {
    die("Query error: " . mysqli_error($connect));
}

// Simpan data ke array
$peminjamans = [];
while ($row = mysqli_fetch_assoc($result)) {
    $peminjamans[] = $row;
}

// Hitung total data
$totalPeminjaman = count($peminjamans);

// Hitung status berdasarkan database
$status_counts = [
    'pending' => 0,
    'disetujui' => 0,
    'ditolak' => 0,
    'dipinjam' => 0,
    'selesai' => 0,
    'terlambat' => 0,
    'dikembalikan' => 0
];

foreach ($peminjamans as $item) {
    $status = $item['status'] ?? 'pending';
    if (isset($status_counts[$status])) {
        $status_counts[$status]++;
    } else {
        $status_counts['pending']++; // fallback
    }
}

// Query untuk filter kategori
$kategori_filter = mysqli_query($connect, "SELECT id, nama_kategori FROM kategori ORDER BY nama_kategori");
?>

<?php 
include '../../partials/header.php'; 
$page = 'peminjaman'; 
include '../../partials/sidebar.php'; 
?>

<div class="content-wrapper">
    <div class="page-title">
        <i class="fas fa-clipboard-list"></i>
        <span>Data Peminjaman</span>
    </div>
        <!-- Main Card -->
        <div class="card-admin">
            <div class="card-header-admin d-flex align-items-center justify-content-between">
                <span><i class="fas fa-clipboard-list me-2 text-indigo-500"></i> Daftar Peminjaman</span>
            </div>
            
            <div class="card-body-admin p-0">
                <!-- Session Messages -->
                <?php if (isset($_SESSION['success'])): ?>
                    <div class="p-3">
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
                                Total <?= $totalPeminjaman ?> Transaksi
                            </div>
                        </div>
                        <div class="col">
                            <div class="d-flex flex-wrap gap-2">
                                <?php foreach ($status_counts as $status => $count): if ($count > 0): ?>
                                    <span class="badge bg-white text-slate-700 border fw-700 rounded-pill px-3 py-2 shadow-sm">
                                        <?= ucfirst($status) ?>: <?= $count ?>
                                    </span>
                                <?php endif; endforeach; ?>
                            </div>
                        </div>
                        <div class="col-md-auto">
                            <select id="filterKategori" class="form-select fw-600 rounded-3 border-slate-200">
                                <option value="">Semua Kategori</option>
                                <?php while($kat = mysqli_fetch_assoc($kategori_filter)): ?>
                                    <option value="<?= htmlspecialchars($kat['nama_kategori']) ?>"><?= htmlspecialchars($kat['nama_kategori']) ?></option>
                                <?php endwhile; ?>
                            </select>
                        </div>
                    </div>
                </div>
                
                <!-- Peminjaman Table -->
                <div class="table-responsive">
                    <table id="peminjamanTable" class="table-admin mb-0">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Kode</th>
                                <th>Peminjam</th>
                                <th>Barang</th>
                                <th>Kategori</th>
                                <th class="text-center">Tanggal Pinjam</th>
                                <th class="text-center">Status</th>
                                <th class="text-end">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($peminjamans as $index => $item): 
                                $st = strtolower($item['status']);
                            ?>
                            <tr>
                                <td class="fw-700 text-slate-400"><?= $index + 1 ?></td>
                                <td>
                                    <div class="fw-800 text-indigo-600"><?= htmlspecialchars($item['kode_peminjaman']) ?></div>
                                </td>
                                <td>
                                    <div class="fw-700 text-slate-800"><?= htmlspecialchars($item['username']) ?></div>
                                </td>
                                <td>
                                    <div class="fw-700 text-slate-800"><?= htmlspecialchars($item['nama_barang']) ?></div>
                                    <small class="text-slate-400"><?= $item['jumlah'] ?> Unit</small>
                                </td>
                                <td>
                                    <span class="badge bg-slate-100 text-slate-600 border fw-700"><?= htmlspecialchars($item['nama_kategori'] ?? 'N/A') ?></span>
                                </td>
                                <td class="text-center">
                                    <div class="small fw-700 text-slate-600"><?= date('d M Y', strtotime($item['tgl_pinjam'])) ?></div>
                                    <div class="text-slate-400 smaller" style="font-size: 0.75rem;">Hingga: <?= date('d M Y', strtotime($item['tgl_kembali_rencana'])) ?></div>
                                </td>
                                <td class="text-center">
                                    <span class="status-badge status-<?= $st ?>">
                                        <i class="fas fa-<?= ($st == 'selesai' || $st == 'disetujui' ? 'check' : ($st == 'ditolak' ? 'times' : 'clock')) ?>"></i>
                                        <?= ucfirst($st) ?>
                                    </span>
                                </td>
                                <td class="text-end">
                                    <div class="d-flex justify-content-end gap-2">
                                        <a href="detail.php?id=<?= $item['id'] ?>" class="btn btn-sm btn-indigo px-3">
                                            <i class="fas fa-eye me-1"></i> Detail
                                        </a>
                                        <a href="edit.php?id=<?= $item['id'] ?>" class="btn btn-sm btn-amber px-3 text-white">
                                            <i class="fas fa-edit me-1"></i> Edit
                                        </a>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>

                <?php if ($totalPeminjaman == 0): ?>
                    <div class="text-center py-5 border-top">
                        <i class="fas fa-clipboard fa-4x text-slate-200 mb-3"></i>
                        <h5 class="text-slate-400 italic">Belum ada data peminjaman</h5>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php include '../../partials/script.php'; ?>
<?php include '../../partials/footer.php'; ?>

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
    var table = $('#peminjamanTable').DataTable({
        language: {
            processing: "Memproses...",
            search: "",
            searchPlaceholder: "Cari kode, peminjam, atau barang...",
            lengthMenu: "Tampilkan _MENU_ peminjaman per halaman",
            zeroRecords: "Tidak ada data yang ditemukan",
            info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ peminjaman",
            infoEmpty: "Menampilkan 0 sampai 0 dari 0 peminjaman",
            infoFiltered: "(disaring dari _MAX_ total peminjaman)",
            paginate: {
                first: "Pertama",
                last: "Terakhir",
                next: ">",
                previous: "<"
            }
        },
        pageLength: 10,
        lengthMenu: [[5, 10, 25, 50], [5, 10, 25, 50]],
        order: [[0, 'desc']],
        columnDefs: [
            {
                targets: 0,
                orderable: false,
                searchable: false,
                className: 'align-middle'
            },
            {
                targets: 4,
                className: 'align-middle',
                orderable: true,
                searchable: true
            },
            {
                targets: 5,
                className: 'align-middle',
                orderable: true,
                searchable: false
            },
            {
                targets: 6,
                className: 'align-middle',
                orderable: true,
                searchable: true
            },
            {
                targets: 7,
                orderable: false,
                searchable: false,
                className: 'align-middle'
            }
        ]
    });
    
    // Filter berdasarkan kategori
    $('#filterKategori').on('change', function() {
        var kategori = $(this).val();
        table.column(4).search(kategori).draw();
    });
    
    // Filter berdasarkan status
    $('#filterStatus').on('change', function() {
        var status = $(this).val();
        table.column(6).search(status).draw();
    });
    
    // Add animation to table rows
    $('#peminjamanTable tbody tr').each(function(index) {
        $(this).css('opacity', '0');
        $(this).delay(index * 100).animate({ opacity: 1 }, 500);
    });
    
    // Tooltip for action buttons
    $('[title]').tooltip({
        trigger: 'hover',
        placement: 'top'
    });
});
</script>

</body>
</html>