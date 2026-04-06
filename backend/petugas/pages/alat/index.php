<?php
// AKTIFKAN ERROR REPORTING
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Pastikan session_start() di awal
session_start();

// Include koneksi database
include '../../app.php';

// Query semua alat berat
$qAlat = "SELECT * FROM barang ORDER BY id DESC";
$result = mysqli_query($connect, $qAlat);

if (!$result) {
    die("Query error: " . mysqli_error($connect));
}

// Simpan data ke array untuk digunakan nanti
$alats = [];
while ($row = mysqli_fetch_assoc($result)) {
    $alats[] = $row;
}

// Hitung total data
$totalAlats = count($alats);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    
    <style>
        /* ===== GLOBAL STYLING ===== */
        body {
            background-color: #f8f9fc;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        /* Sidebar adjustment */
        #main {
            margin-left: 260px;
            margin-top: 70px;
            padding: 25px;
            width: calc(100% - 260px);
            min-height: calc(100vh - 70px);
            transition: all 0.3s ease;
            background-color: #f8f9fc;
        }

        @media (max-width: 768px) {
            #main {
                margin-left: 0;
                width: 100%;
                padding: 15px;
            }
        }

        /* Card styling */
        .main-card {
            background: #ffffff;
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
            border: 1px solid #e3e6f0;
            overflow: hidden;
        }

        .card-header-custom {
            background: #ffffff;
            border-bottom: 2px solid #f0f2f5;
            padding: 20px 30px;
        }

        .card-body-custom {
            padding: 30px;
        }

        /* Header styling */
        .page-title {
            font-size: 1.8rem;
            font-weight: 700;
            color: #4e73df;
            margin: 0;
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .page-title i {
            font-size: 1.5rem;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        /* Button styling */
        .btn-add-alat {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            border-radius: 10px;
            padding: 12px 25px;
            font-weight: 600;
            font-size: 1rem;
            color: white;
            display: flex;
            align-items: center;
            gap: 10px;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(102, 126, 234, 0.3);
        }

        .btn-add-alat:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 25px rgba(102, 126, 234, 0.4);
            color: white;
        }

        /* Alert styling */
        .alert-container {
            border-radius: 10px;
            overflow: hidden;
            margin-bottom: 25px;
            border: none;
            box-shadow: 0 3px 10px rgba(0, 0, 0, 0.05);
        }

        .alert-success {
            background: linear-gradient(to right, #d4edda, #c3e6cb);
            border-left: 5px solid #28a745;
            color: #155724;
            padding: 15px 20px;
        }

        .alert-danger {
            background: linear-gradient(to right, #f8d7da, #f5c6cb);
            border-left: 5px solid #dc3545;
            color: #721c24;
            padding: 15px 20px;
        }

        .alert-info {
            background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
            border: none;
            color: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 15px rgba(240, 147, 251, 0.3);
        }

        .alert-info i {
            font-size: 1.2rem;
            margin-right: 10px;
        }

        /* Stats badges */
        .stats-badge {
            background: rgba(255, 255, 255, 0.9);
            padding: 8px 15px;
            border-radius: 20px;
            margin: 0 5px;
            font-weight: 600;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }

        .badge-tersedia {
            background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%);
            color: white;
        }

        .badge-dipinjam {
            background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
            color: white;
        }

        .badge-rusak {
            background: linear-gradient(135deg, #fa709a 0%, #fee140 100%);
            color: white;
        }

        .badge-hilang {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }

        /* Table styling */
        .dataTable {
            width: 100% !important;
            border-collapse: separate !important;
            border-spacing: 0 !important;
        }

        .dataTable thead th {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            padding: 15px 20px;
            font-weight: 600;
            text-transform: uppercase;
            font-size: 0.85rem;
            letter-spacing: 0.5px;
        }

        .dataTable tbody td {
            padding: 18px 20px;
            border-bottom: 1px solid #f0f2f5;
            vertical-align: middle;
            color: #5a5c69;
        }

        .dataTable tbody tr {
            transition: all 0.2s ease;
        }

        .dataTable tbody tr:hover {
            background-color: #f8f9ff;
            transform: translateX(5px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
        }

        /* Status badges in table */
        .status-badge {
            padding: 8px 20px;
            border-radius: 20px;
            font-weight: 600;
            font-size: 0.85rem;
            display: inline-block;
            min-width: 100px;
            text-align: center;
        }

        /* Action buttons */
        .action-buttons {
            display: flex;
            gap: 8px;
            justify-content: center;
        }

        .btn-action {
            border-radius: 8px;
            padding: 8px 15px;
            font-size: 0.9rem;
            display: flex;
            align-items: center;
            gap: 6px;
            transition: all 0.2s ease;
        }

        .btn-view {
            background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
            color: white;
            border: none;
        }

        .btn-edit {
            background: linear-gradient(135deg, #fad961 0%, #f76b1c 100%);
            color: white;
            border: none;
        }

        .btn-delete {
            background: linear-gradient(135deg, #ff5858 0%, #f09819 100%);
            color: white;
            border: none;
        }

        .btn-action:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
            color: white;
        }

        /* No data state */
        .no-data {
            padding: 50px 20px;
            text-align: center;
            color: #6c757d;
        }

        .no-data i {
            font-size: 4rem;
            margin-bottom: 20px;
            opacity: 0.3;
        }

        .no-data h5 {
            font-size: 1.5rem;
            margin-bottom: 10px;
            font-weight: 600;
        }

        .no-data p {
            font-size: 1.1rem;
            opacity: 0.8;
        }

        /* Footer styling */
        .footer-custom {
            margin-top: 30px;
            text-align: center;
            color: #6c757d;
            font-size: 0.9rem;
            padding: 20px;
            border-top: 1px solid #e3e6f0;
            background: white;
            border-radius: 0 0 12px 12px;
        }

        /* Responsive adjustments */
        @media (max-width: 992px) {
            .card-body-custom {
                padding: 20px;
            }
            
            .action-buttons {
                flex-direction: column;
                gap: 5px;
            }
            
            .btn-action {
                justify-content: center;
            }
        }

        @media (max-width: 768px) {
            .card-header-custom {
                flex-direction: column;
                gap: 15px;
                padding: 15px;
            }
            
            .page-title {
                font-size: 1.5rem;
            }
            
            .btn-add-alat {
                width: 100%;
                justify-content: center;
            }
        }

        @media (max-width: 576px) {
            .stats-badge {
                display: block;
                margin: 5px 0;
                width: 100%;
                text-align: center;
            }
            
            .status-badge {
                min-width: 80px;
                padding: 6px 12px;
                font-size: 0.8rem;
            }
        }
    </style>
</head>
<body>

<?php 
include '../../partials/header.php'; 
$page = 'alat'; 
include '../../partials/sidebar.php'; 
?>

<div class="container-fluid">
    <div id="main">
        <!-- Main Card -->
        <div class="main-card">
            <!-- Card Header -->
            <div class="card-header-custom d-flex align-items-center justify-content-between">
                <h2 class="page-title">
                    <i class="fas fa-hard-hat"></i>
                    Data Alat Berat
                </h2>
                <a href="./create.php" class="btn btn-add-alat">
                    <i class="fas fa-plus-circle"></i>
                    Tambah Alat
                </a>
            </div>
            
            <!-- Card Body -->
            <div class="card-body-custom">
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
                <?php
                $tersediaCount = 0;
                $dipinjamCount = 0;
                $rusakCount = 0;
                $hilangCount = 0;
                $totalHarga = 0;
                
                foreach ($alats as $alat) {
                    if ($alat['status'] == 'tersedia') $tersediaCount++;
                    if ($alat['status'] == 'dipinjam') $dipinjamCount++;
                    if ($alat['status'] == 'rusak') $rusakCount++;
                    if ($alat['status'] == 'hilang') $hilangCount++;
                    $totalHarga += $alat['harga_sewa_perhari'] * $alat['jumlah'];
                }
                ?>
                
                <div class="alert alert-info d-flex align-items-center justify-content-between">
                    <div>
                        <i class="fas fa-info-circle"></i>
                        <strong>Total <?php echo $totalAlats; ?> Alat Berat Terdaftar</strong>
                    </div>
                    <?php if ($totalAlats > 0): ?>
                        <div class="d-none d-md-flex">
                            <span class="stats-badge badge-tersedia">
                                <i class="fas fa-check-circle me-1"></i> Tersedia: <?php echo $tersediaCount; ?>
                            </span>
                            <span class="stats-badge badge-dipinjam">
                                <i class="fas fa-clock me-1"></i> Dipinjam: <?php echo $dipinjamCount; ?>
                            </span>
                            <span class="stats-badge badge-rusak">
                                <i class="fas fa-tools me-1"></i> Rusak: <?php echo $rusakCount; ?>
                            </span>
                            <span class="stats-badge badge-hilang">
                                <i class="fas fa-exclamation-triangle me-1"></i> Hilang: <?php echo $hilangCount; ?>
                            </span>
                        </div>
                    <?php endif; ?>
                </div>
                
                <!-- Alat Table -->
                <?php if ($totalAlats > 0): ?>
                    <div class="table-responsive">
                        <table id="alatTable" class="table" style="width:100%">
                            <thead>
                                <tr>
                                    <th class="text-center text-light">No</th>
                                    <th class="text-center text-light">Gambar</th>
                                    <th class="text-center text-light">Kode</th>
                                    <th class="text-center text-light">Nama Alat</th>
                                    <th class="text-center text-light">Jenis</th>
                                    <th class="text-center text-light">Stok</th>
                                    <th class="text-center text-light">Status</th>
                                    <th class="text-center text-light">Harga/Hari</th>
                                    <th class="text-center text-light">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($alats as $index => $item): ?>
                                <tr>
                                    <td class="text-center fw-bold"><?= $index + 1 ?></td>
                                    <td class="text-center">
                                        <?php if (!empty($item['gambar'])): ?>
                                            <img src="/farel-ukk-/storages/alat/<?php echo htmlspecialchars($item['gambar']); ?>" alt="Gambar Alat" style="width: 60px; height: 60px; object-fit: cover; border-radius: 8px; border: 1px solid #e2e8f0;">
                                        <?php else: ?>
                                            <div style="width: 60px; height: 60px; background: #f1f5f9; border-radius: 8px; display: flex; align-items: center; justify-content: center; color: #94a3b8; border: 1px dashed #cbd5e1; margin: 0 auto;">
                                                <i class="fas fa-image"></i>
                                            </div>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="rounded-circle bg-light p-2 me-3">
                                                <i class="fas fa-hard-hat text-warning"></i>
                                            </div>
                                            <div>
                                                <strong><?= htmlspecialchars($item['kode_barang']) ?></strong>
                                                <div class="small text-muted">ID: #<?= $item['id'] ?></div>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <strong><?= htmlspecialchars($item['nama_barang']) ?></strong>
                                        <?php if (!empty($item['merk'])): ?>
                                            <div class="small text-muted">Merk: <?= htmlspecialchars($item['merk']) ?></div>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge bg-info text-white">
                                            <?= htmlspecialchars($item['jenis']) ?>
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        <div class="text-primary fw-semibold">
                                            <?= $item['jumlah'] ?> / <?= $item['jumlah'] ?>
                                        </div>
                                        <div class="small text-muted">
                                            Tersedia / Total
                                        </div>
                                    </td>
                                    <td class="text-center">
                                        <?php
                                        $badgeClass = '';
                                        switch($item['status']) {
                                            case 'tersedia': $badgeClass = 'badge-tersedia'; break;
                                            case 'dipinjam': $badgeClass = 'badge-dipinjam'; break;
                                            case 'rusak': $badgeClass = 'badge-rusak'; break;
                                            case 'hilang': $badgeClass = 'badge-hilang'; break;
                                            default: $badgeClass = 'badge-tersedia';
                                        }
                                        ?>
                                        <span class="status-badge <?= $badgeClass ?>">
                                            <i class="fas fa-<?= 
                                                $item['status'] == 'tersedia' ? 'check-circle' : 
                                                ($item['status'] == 'dipinjam' ? 'clock' : 
                                                ($item['status'] == 'rusak' ? 'tools' : 'exclamation-triangle')) 
                                            ?> me-1"></i>
                                            <?= ucfirst($item['status']) ?>
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        <div class="text-success fw-bold">
                                            Rp <?= number_format($item['harga_sewa_perhari'], 0, ',', '.') ?>
                                        </div>
                                        <div class="small text-muted">
                                            per hari
                                        </div>
                                    </td>
                                    <td class="text-center">
                                        <div class="action-buttons">    
                                            <a href="./detail.php?id=<?= $item['id'] ?>" 
                                               class="btn btn-action btn-view"
                                               title="Detail Alat">
                                                <i class="fas fa-eye"></i>
                                                <span class="d-none d-lg-inline">Detail</span>
                                            </a>
                                            <a href="./edit.php?id=<?= $item['id'] ?>" 
                                               class="btn btn-action btn-edit"
                                               title="Edit Alat">
                                                <i class="fas fa-edit"></i>
                                                <span class="d-none d-lg-inline">Edit</span>
                                            </a>
                                            <a href="../../action/alat/destroy.php?id=<?= $item['id'] ?>"
                                                onclick="return confirm('Apakah Anda yakin ingin menghapus alat <?= addslashes($item['nama_barang']) ?>?')"
                                                class="btn btn-action btn-delete"
                                                title="Hapus Alat">
                                                <i class="fas fa-trash"></i>
                                                <span class="d-none d-lg-inline">Hapus</span>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <div class="no-data">
                        <i class="fas fa-hard-hat"></i>
                        <h5>Belum Ada Data Alat Berat</h5>
                        <p>Mulai dengan menambahkan alat berat baru menggunakan tombol di atas</p>
                    </div>
                <?php endif; ?>
                
                <!-- Footer -->
                <div class="footer-custom">
                    <p class="mb-0">
                        &copy; <?= date('Y') ?> Website Peminjaman Alat Berat. Hak Cipta Dilindungi.
                    </p>
                </div>
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
<!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- DataTables CSS -->
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
<script>
$(document).ready(function() {
    // Initialize DataTable
    var table = $('#alatTable').DataTable({
        language: {
            processing: "Memproses...",
            search: "",
            searchPlaceholder: "Cari nama/kode alat...",
            lengthMenu: "Tampilkan _MENU_ alat per halaman",
            zeroRecords: "Tidak ada data yang ditemukan",
            info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ alat",
            infoEmpty: "Menampilkan 0 sampai 0 dari 0 alat",
            infoFiltered: "(disaring dari _MAX_ total alat)",
            paginate: {
                first: "Pertama",
                last: "Terakhir",
                next: ">",
                previous: "<"
            }
        },
        pageLength: 10,
        lengthMenu: [[5, 10, 25, 50], [5, 10, 25, 50]],
        order: [[1, 'asc']], // Sort by Kode
        columnDefs: [
            {
                targets: 0, // No column
                orderable: false,
                searchable: false,
                className: 'align-middle'
            },
            {
                targets: 1, // Gambar column
                orderable: false,
                searchable: false,
                className: 'align-middle'
            },
            {
                targets: 5, // Stok column
                className: 'align-middle'
            },
            {
                targets: 6, // Status column
                className: 'align-middle'
            },
            {
                targets: 7, // Harga column
                className: 'align-middle'
            },
            {
                targets: 8, // Action column
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
    $('#alatTable tbody tr').each(function(index) {
        $(this).css('opacity', '0');
        $(this).delay(index * 100).animate({ opacity: 1 }, 500);
    });
});
</script>

</body>
</html>