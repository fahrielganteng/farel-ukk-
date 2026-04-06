<?php
// AKTIFKAN ERROR REPORTING
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Pastikan session_start() di awal
session_start();

// Include koneksi database
include '../../app.php';

// Query semua kategori
$qKategori = "SELECT * FROM kategori ORDER BY id DESC";
$result = mysqli_query($connect, $qKategori);

if (!$result) {
    die("Query error: " . mysqli_error($connect));
}

// Simpan data ke array untuk digunakan nanti
$kategori = [];
while ($row = mysqli_fetch_assoc($result)) {
    $kategori[] = $row;
}

// Hitung total data
$totalKategori = count($kategori);
?>

<?php 
include '../../partials/header.php'; 
$page = 'kategori'; // Ganti dari 'pengguna' ke 'kategori'
include '../../partials/sidebar.php'; 
?>

<div class="content-wrapper">
        <!-- Main Card -->
        <div class="card-admin">
            <!-- Card Header -->
            <div class="card-header-admin d-flex align-items-center justify-content-between">
                <h2 class="page-title">
                    <i class="fas fa-folder"></i>
                    Data Kategori
                </h2>
                <a href="./create.php" class="btn btn btn-primary">
                    <i class="fas fa-plus-circle"></i>
                    Tambah Kategori
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
                
                <!-- Kategori Table -->
                <?php if ($totalKategori > 0): ?>
                    <div class="table-responsive">
                        <table id="kategoriTable" class="table-admin table-hover">
                            <thead>
                                <tr>
                                    <th class="text-center" style="width: 80px;">No</th>
                                    <th>Nama Kategori</th>
                                    <th>Deskripsi</th>
                                    <th class="text-center">Tanggal Dibuat</th>
                                    <th class="text-center" style="width: 150px;">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($kategori as $index => $item): ?>
                                <tr>
                                    <td class="text-center fw-bold text-slate-400"><?= $index + 1 ?></td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="rounded-3 bg-indigo-50 text-indigo-600 d-flex align-items-center justify-content-center me-3" style="width: 36px; height: 36px;">
                                                <i class="fas fa-folder"></i>
                                            </div>
                                            <div>
                                                <div class="fw-700 text-dark"><?= htmlspecialchars($item['nama_kategori']) ?></div>
                                                <small class="text-slate-400">ID: #KAT-<?= $item['id'] ?></small>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <?php if (!empty($item['deskripsi'])): ?>
                                            <div class="text-slate-500" style="max-width: 300px; font-size: 0.85rem; line-height: 1.5;">
                                                <?= htmlspecialchars($item['deskripsi']) ?>
                                            </div>
                                        <?php else: ?>
                                            <span class="text-slate-300 small italic">Tidak ada deskripsi</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-center">
                                        <div class="fw-600 text-slate-700"><?= date('d M Y', strtotime($item['created_at'])) ?></div>
                                        <div class="small text-slate-400"><?= date('H:i', strtotime($item['created_at'])) ?></div>
                                    </td>
                                    <td class="text-center">
                                        <div class="d-flex justify-content-center gap-2">    
                                            <a href="./edit.php?id=<?= $item['id'] ?>" class="btn btn-sm btn-light border" title="Edit">
                                                <i class="fas fa-edit text-indigo-600"></i>
                                            </a>
                                            <a href="../../action/kategori/destroy.php?id=<?= $item['id'] ?>"
                                                onclick="return confirm('Apakah Anda yakin ingin menghapus kategori <?= addslashes($item['nama_kategori']) ?>?')"
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
                    <div class="no-data text-center py-5">
                        <i class="fas fa-folder-open fa-3x text-slate-200 mb-3"></i>
                        <h5 class="text-slate-400">Belum Ada Data Kategori</h5>
                        <p class="text-slate-300">Mulai dengan menambahkan kategori baru menggunakan tombol di atas</p>
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
    var table = $('#kategoriTable').DataTable({
        language: {
            processing: "Memproses...",
            search: "",
            searchPlaceholder: "Cari kategori...",
            lengthMenu: "Tampilkan _MENU_ kategori per halaman",
            zeroRecords: "Tidak ada data yang ditemukan",
            info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ kategori",
            infoEmpty: "Menampilkan 0 sampai 0 dari 0 kategori",
            infoFiltered: "(disaring dari _MAX_ total kategori)",
            paginate: {
                first: "Pertama",
                last: "Terakhir",
                next: ">",
                previous: "<"
            }
        },
        pageLength: 10,
        lengthMenu: [[5, 10, 25, 50], [5, 10, 25, 50]],
        order: [[1, 'asc']], // Sort by Nama Kategori
        columnDefs: [
            {
                targets: 0, // No column
                orderable: false,
                searchable: false,
                className: 'align-middle'
            },
            {
                targets: 1, // Nama Kategori column
                className: 'align-middle'
            },
            {
                targets: 2, // Deskripsi column
                className: 'align-middle'
            },
            {
                targets: 3, // Date column
                className: 'align-middle'
            },
            {
                targets: 4, // Action column
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
    $('#kategoriTable tbody tr').each(function(index) {
        $(this).css('opacity', '0');
        $(this).delay(index * 100).animate({ opacity: 1 }, 500);
    });
});
</script>

