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
        <!-- Main Card -->
        <div class="card-admin">
            <!-- Card Header -->
            <div class="card-header-admin d-flex align-items-center justify-content-between">
                <h2 class="page-title">
                    <i class="fas fa-clipboard-list"></i>
                    Data Peminjaman
                </h2>
                <a href="./create.php" class="btn btn btn-primary">
                    <i class="fas fa-plus-circle"></i>
                    Tambah Peminjaman
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
                        <strong>Total <?php echo $totalPeminjaman; ?> Peminjaman</strong>
                    </div>
                    <?php if ($totalPeminjaman > 0): ?>
                        <div class="d-none d-md-flex flex-wrap">
                            <?php foreach ($status_counts as $status => $count): 
                                if ($count > 0): 
                                    $statusText = ucfirst($status);
                                    $icon = '';
                                    switch($status) {
                                        case 'pending': $icon = 'clock'; break;
                                        case 'disetujui': $icon = 'check'; break;
                                        case 'ditolak': $icon = 'times'; break;
                                        case 'dipinjam': $icon = 'box-open'; break;
                                        case 'selesai': $icon = 'check-circle'; break;
                                        case 'terlambat': $icon = 'exclamation-triangle'; break;
                                        case 'dikembalikan': $icon = 'undo'; break;
                                        default: $icon = 'question';
                                    }
                            ?>
                                <span class="stats-badge">
                                    <i class="fas fa-<?= $icon ?> me-1"></i> 
                                    <?= $statusText ?>: <?= $count ?>
                                </span>
                            <?php 
                                endif;
                            endforeach; 
                            ?>
                        </div>
                    <?php endif; ?>
                </div>
                
                <!-- Filter Kategori & Status -->
                <div class="row g-3 mb-4">
                    <div class="col-md-3">
                        <label class="form-label small fw-700 text-slate-500">Filter Kategori</label>
                        <select id="filterKategori" class="form-select border-slate-200">
                            <option value="">Semua Kategori</option>
                            <?php 
                            mysqli_data_seek($kategori_filter, 0);
                            while($kat = mysqli_fetch_assoc($kategori_filter)): 
                            ?>
                                <option value="<?= htmlspecialchars($kat['nama_kategori']) ?>">
                                    <?= htmlspecialchars($kat['nama_kategori']) ?>
                                </option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                </div>
                
                <!-- Peminjaman Table -->
                <?php if ($totalPeminjaman > 0): ?>
                    <div class="table-responsive">
                        <table id="peminjamanTable" class="table-admin table-hover">
                            <thead>
                                <tr>
                                    <th class="text-center" style="width: 60px;">No</th>
                                    <th style="width: 120px;">Kode</th>
                                    <th>Peminjam</th>
                                    <th>Barang</th>
                                    <th>Jadwal</th>
                                    <th class="text-center">Status</th>
                                    <th class="text-center" style="width: 150px;">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($peminjamans as $index => $item): 
                                    $status = strtolower($item['status'] ?? 'pending');
                                    $stClass = 'status-' . $status;
                                ?>
                                <tr>
                                    <td class="text-center fw-bold text-slate-400"><?= $index + 1 ?></td>
                                    <td>
                                        <div class="fw-800 text-indigo-600" style="font-size: 0.85rem;"><?= htmlspecialchars($item['kode_peminjaman']) ?></div>
                                    </td>
                                    <td>
                                        <div class="fw-700 text-dark"><?= htmlspecialchars($item['username']) ?></div>
                                        <small class="text-slate-400">Peminjam</small>
                                    </td>
                                    <td>
                                        <div class="fw-700 text-dark"><?= htmlspecialchars($item['nama_barang']) ?></div>
                                        <div class="badge bg-slate-100 text-slate-500 fw-600" style="font-size: 0.7rem;"><?= $item['jumlah'] ?> Unit</div>
                                    </td>
                                    <td>
                                        <div class="d-flex flex-column gap-1">
                                            <div class="small fw-600 text-slate-700">
                                                <i class="fas fa-calendar-alt me-1 text-indigo-400"></i> Pinjam: <?= date('d M', strtotime($item['tgl_pinjam'])) ?>
                                            </div>
                                            <div class="small fw-600 text-slate-400">
                                                <i class="fas fa-undo me-1"></i> Kembali: <?= date('d M', strtotime($item['tgl_kembali_rencana'])) ?>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="text-center">
                                        <span class="status-badge <?= $stClass ?>">
                                            <?php 
                                            $icon = 'fa-clock';
                                            if($status == 'disetujui' || $status == 'selesai') $icon = 'fa-check';
                                            else if($status == 'ditolak') $icon = 'fa-times';
                                            else if($status == 'dipinjam') $icon = 'fa-truck-loading';
                                            ?>
                                            <i class="fas <?= $icon ?>"></i>
                                            <?= ucfirst($status) ?>
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        <div class="d-flex justify-content-center gap-2">
                                            <a href="./detail.php?id=<?= $item['id'] ?>" class="btn btn-sm btn-light border" title="Detail">
                                                <i class="fas fa-eye text-slate-600"></i>
                                            </a>
                                            <a href="./edit.php?id=<?= $item['id'] ?>" class="btn btn-sm btn-light border" title="Edit">
                                                <i class="fas fa-edit text-indigo-600"></i>
                                            </a>
                                            <a href="../../action/peminjaman/destroy.php?id=<?= $item['id'] ?>"
                                                onclick="return confirm('Apakah Anda yakin ingin menghapus peminjaman <?= addslashes($item['kode_peminjaman']) ?>?')"
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
                        <i class="fas fa-clipboard fa-4x text-slate-200 mb-3"></i>
                        <h4 class="text-slate-400">Belum Ada Data Peminjaman</h4>
                        <p class="text-slate-300">Mulai dengan menambahkan peminjaman baru</p>
                        <a href="./create.php" class="btn btn-primary mt-3">
                            <i class="fas fa-plus-circle me-1"></i>
                            Tambah Peminjaman
                        </a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div><?php include '../../partials/script.php'; ?>
<?php include '../../partials/footer.php'; ?>



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

