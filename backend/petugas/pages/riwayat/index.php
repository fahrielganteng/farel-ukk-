<?php
// AKTIFKAN ERROR REPORTING
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Pastikan session_start() di awal
session_start();

// Include koneksi database
include '../../app.php';

// Filter parameter
$filter_status = isset($_GET['status']) ? $_GET['status'] : '';
$filter_tanggal = isset($_GET['tanggal']) ? $_GET['tanggal'] : '';
$filter_user = isset($_GET['user']) ? (int)$_GET['user'] : 0;

// Query dasar
$qWhere = "WHERE 1=1";

// Filter berdasarkan status
if (!empty($filter_status) && $filter_status != 'semua') {
    $qWhere .= " AND p.status = '" . mysqli_real_escape_string($connect, $filter_status) . "'";
}

// Filter berdasarkan tanggal
if (!empty($filter_tanggal)) {
    $qWhere .= " AND DATE(p.created_at) = '" . mysqli_real_escape_string($connect, $filter_tanggal) . "'";
}

// Filter berdasarkan user (jika admin ingin melihat riwayat user tertentu)
if ($filter_user > 0) {
    $qWhere .= " AND p.user_id = $filter_user";
}

// Query semua peminjaman dengan join lengkap
$qPeminjaman = "SELECT p.*, 
                u.username, u.role as user_role,
                b.nama_barang, b.kode_barang, b.harga_sewa_perhari,
                pg.tgl_kembali as tgl_pengembalian, pg.kondisi, pg.denda, pg.keterangan as keterangan_pengembalian
                FROM peminjaman p 
                LEFT JOIN users u ON p.user_id = u.id 
                LEFT JOIN barang b ON p.barang_id = b.id 
                LEFT JOIN pengembalian pg ON p.id = pg.peminjaman_id 
                $qWhere
                ORDER BY p.id DESC";

$result = mysqli_query($connect, $qPeminjaman);

if (!$result) {
    die("Query error: " . mysqli_error($connect));
}

// Simpan data ke array
$riwayat = [];
while ($row = mysqli_fetch_assoc($result)) {
    // Hitung total harga jika belum ada
    if (empty($row['total_harga']) && !empty($row['harga_sewa_perhari']) && !empty($row['lama_pinjam'])) {
        $row['total_harga'] = $row['harga_sewa_perhari'] * $row['lama_pinjam'];
    }
    $riwayat[] = $row;
}

// Hitung total data
$totalRiwayat = count($riwayat);

// Hitung statistik
$stats = [
    'total' => $totalRiwayat,
    'pending' => 0,
    'disetujui' => 0,
    'ditolak' => 0,
    'dipinjam' => 0,
    'selesai' => 0,
    'terlambat' => 0
];

// Hitung total pendapatan
$totalPendapatan = 0;
$totalDenda = 0;

foreach ($riwayat as $item) {
    $status = $item['status'] ?? 'pending';
    $stats[$status]++;
    
    // Hitung pendapatan untuk status selesai
    if ($status == 'selesai' && !empty($item['total_harga'])) {
        $totalPendapatan += $item['total_harga'];
    }
    
    // Hitung total denda
    if (!empty($item['denda'])) {
        $totalDenda += $item['denda'];
    }
}

// Ambil daftar user untuk filter
$qUsers = "SELECT id, username FROM users WHERE role = 'peminjam' ORDER BY username";
$users_result = mysqli_query($connect, $qUsers);
$users = [];
while ($user = mysqli_fetch_assoc($users_result)) {
    $users[] = $user;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Riwayat Peminjaman - Admin Panel</title>
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- DataTables CSS -->
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/buttons/2.4.1/css/buttons.bootstrap5.min.css">
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.bootstrap5.min.css">
    
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

        /* Stats cards */
        .stats-card {
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 20px;
            color: white;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease;
        }

        .stats-card:hover {
            transform: translateY(-5px);
        }

        .stats-card.total { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); }
        .stats-card.pending { background: linear-gradient(135deg, #6c757d 0%, #495057 100%); }
        .stats-card.disetujui { background: linear-gradient(135deg, #28a745 0%, #20c997 100%); }
        .stats-card.ditolak { background: linear-gradient(135deg, #dc3545 0%, #e83e8c 100%); }
        .stats-card.dipinjam { background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%); }
        .stats-card.selesai { background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%); }
        .stats-card.terlambat { background: linear-gradient(135deg, #ff5858 0%, #f09819 100%); }
        .stats-card.pendapatan { background: linear-gradient(135deg, #fd7e14 0%, #ffc107 100%); }
        .stats-card.denda { background: linear-gradient(135deg, #17a2b8 0%, #20c997 100%); }

        .stats-number {
            font-size: 2rem;
            font-weight: 700;
            margin-bottom: 5px;
        }

        .stats-label {
            font-size: 0.9rem;
            opacity: 0.9;
        }

        /* Filter form */
        .filter-card {
            background: #f8f9ff;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 25px;
            border: 1px solid #e3e6f0;
        }

        .filter-title {
            font-size: 1.2rem;
            font-weight: 600;
            color: #4e73df;
            margin-bottom: 15px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        /* Status badges */
        .status-badge {
            padding: 6px 15px;
            border-radius: 20px;
            font-weight: 600;
            font-size: 0.8rem;
            display: inline-block;
            text-align: center;
            min-width: 90px;
        }

        .badge-pending { background-color: #6c757d; color: white; }
        .badge-disetujui { background-color: #28a745; color: white; }
        .badge-ditolak { background-color: #dc3545; color: white; }
        .badge-dipinjam { background-color: #f093fb; color: white; }
        .badge-selesai { background-color: #43e97b; color: white; }
        .badge-terlambat { background-color: #ff5858; color: white; }

        /* Kondisi badges */
        .kondisi-badge {
            padding: 4px 10px;
            border-radius: 15px;
            font-size: 0.75rem;
            font-weight: 600;
        }

        .kondisi-baik { background-color: #d4edda; color: #155724; }
        .kondisi-ringan { background-color: #fff3cd; color: #856404; }
        .kondisi-berat { background-color: #f8d7da; color: #721c24; }

        /* Table styling */
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
            padding: 15px 20px;
            border-bottom: 1px solid #f0f2f5;
            vertical-align: middle;
        }

        .dataTable tbody tr {
            transition: all 0.2s ease;
        }

        .dataTable tbody tr:hover {
            background-color: #f8f9ff;
        }

        /* Action buttons */
        .btn-action {
            border-radius: 6px;
            padding: 6px 12px;
            font-size: 0.85rem;
            display: flex;
            align-items: center;
            gap: 5px;
            transition: all 0.2s ease;
        }

        .btn-view { background-color: #4e73df; color: white; border: none; }
        .btn-print { background-color: #36b9cc; color: white; border: none; }
        .btn-export { background-color: #1cc88a; color: white; border: none; }

        .btn-action:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
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

        /* Footer */
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

        /* Responsive */
        @media (max-width: 992px) {
            .card-body-custom {
                padding: 20px;
            }
            
            .stats-card {
                padding: 15px;
            }
            
            .stats-number {
                font-size: 1.5rem;
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
            
            .filter-card {
                padding: 15px;
            }
        }
    </style>
</head>
<body>

<?php 
include '../../partials/header.php'; 
$page = 'peminjaman'; 
include '../../partials/sidebar.php'; 
?>

<div class="container-fluid">
    <div id="main">
        <!-- Main Card -->
        <div class="main-card">
            <!-- Card Header -->
            <div class="card-header-custom d-flex align-items-center justify-content-between">
                <h2 class="page-title">
                    <i class="fas fa-history"></i>
                    Riwayat Peminjaman
                </h2>
                <div>
                    <button id="btnPrint" class="btn btn-warning me-2">
                        <i class="fas fa-print"></i> Cetak
                    </button>
                    <button id="btnExport" class="btn btn-success">
                        <i class="fas fa-file-export"></i> Export
                    </button>
                </div>
            </div>
            
            <!-- Card Body -->
            <div class="card-body-custom">
                <!-- Session Messages -->
                <?php if (isset($_SESSION['success'])): ?>
                    <div class="alert alert-success alert-dismissible fade show mb-4" role="alert">
                        <i class="fas fa-check-circle me-2"></i>
                        <?php
                        echo $_SESSION['success'];
                        unset($_SESSION['success']);
                        ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                <?php endif; ?>

                <?php if (isset($_SESSION['error'])): ?>
                    <div class="alert alert-danger alert-dismissible fade show mb-4" role="alert">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        <?php
                        echo $_SESSION['error'];
                        unset($_SESSION['error']);
                        ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                <?php endif; ?>
                
                <!-- Stats Cards -->
                <div class="row mb-4">
                    <div class="col-xl-3 col-md-6 mb-4">
                        <div class="stats-card total">
                            <div class="stats-number"><?= $stats['total'] ?></div>
                            <div class="stats-label">Total Transaksi</div>
                            <i class="fas fa-clipboard-list fa-2x float-end opacity-50"></i>
                        </div>
                    </div>
                    <div class="col-xl-3 col-md-6 mb-4">
                        <div class="stats-card selesai">
                            <div class="stats-number"><?= $stats['selesai'] ?></div>
                            <div class="stats-label">Selesai</div>
                            <i class="fas fa-check-circle fa-2x float-end opacity-50"></i>
                        </div>
                    </div>
                    <div class="col-xl-3 col-md-6 mb-4">
                        <div class="stats-card pendapatan">
                            <div class="stats-number">Rp <?= number_format($totalPendapatan, 0, ',', '.') ?></div>
                            <div class="stats-label">Total Pendapatan</div>
                            <i class="fas fa-money-bill-wave fa-2x float-end opacity-50"></i>
                        </div>
                    </div>
                    <div class="col-xl-3 col-md-6 mb-4">
                        <div class="stats-card denda">
                            <div class="stats-number">Rp <?= number_format($totalDenda, 0, ',', '.') ?></div>
                            <div class="stats-label">Total Denda</div>
                            <i class="fas fa-exclamation-triangle fa-2x float-end opacity-50"></i>
                        </div>
                    </div>
                </div>
                
                <!-- Detail Stats -->
                <div class="row mb-4">
                    <div class="col-md-2 col-6 mb-3">
                        <div class="stats-card pending">
                            <div class="stats-number"><?= $stats['pending'] ?></div>
                            <div class="stats-label">Pending</div>
                        </div>
                    </div>
                    <div class="col-md-2 col-6 mb-3">
                        <div class="stats-card disetujui">
                            <div class="stats-number"><?= $stats['disetujui'] ?></div>
                            <div class="stats-label">Disetujui</div>
                        </div>
                    </div>
                    <div class="col-md-2 col-6 mb-3">
                        <div class="stats-card ditolak">
                            <div class="stats-number"><?= $stats['ditolak'] ?></div>
                            <div class="stats-label">Ditolak</div>
                        </div>
                    </div>
                    <div class="col-md-2 col-6 mb-3">
                        <div class="stats-card dipinjam">
                            <div class="stats-number"><?= $stats['dipinjam'] ?></div>
                            <div class="stats-label">Dipinjam</div>
                        </div>
                    </div>
                    <div class="col-md-2 col-6 mb-3">
                        <div class="stats-card terlambat">
                            <div class="stats-number"><?= $stats['terlambat'] ?></div>
                            <div class="stats-label">Terlambat</div>
                        </div>
                    </div>
                    <div class="col-md-2 col-6 mb-3">
                        <div class="stats-card selesai">
                            <div class="stats-number"><?= $stats['selesai'] ?></div>
                            <div class="stats-label">Selesai</div>
                        </div>
                    </div>
                </div>
                
                <!-- Filter Form -->
                <div class="filter-card mb-4">
                    <h5 class="filter-title">
                        <i class="fas fa-filter"></i> Filter Riwayat
                    </h5>
                    <form method="GET" class="row g-3">
                        <div class="col-md-3">
                            <label class="form-label">Status</label>
                            <select name="status" class="form-select">
                                <option value="semua" <?= $filter_status == 'semua' || empty($filter_status) ? 'selected' : '' ?>>Semua Status</option>
                                <option value="pending" <?= $filter_status == 'pending' ? 'selected' : '' ?>>Pending</option>
                                <option value="disetujui" <?= $filter_status == 'disetujui' ? 'selected' : '' ?>>Disetujui</option>
                                <option value="ditolak" <?= $filter_status == 'ditolak' ? 'selected' : '' ?>>Ditolak</option>
                                <option value="dipinjam" <?= $filter_status == 'dipinjam' ? 'selected' : '' ?>>Dipinjam</option>
                                <option value="selesai" <?= $filter_status == 'selesai' ? 'selected' : '' ?>>Selesai</option>
                                <option value="terlambat" <?= $filter_status == 'terlambat' ? 'selected' : '' ?>>Terlambat</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Tanggal</label>
                            <input type="date" name="tanggal" class="form-control" value="<?= $filter_tanggal ?>">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Peminjam</label>
                            <select name="user" class="form-select">
                                <option value="0">Semua Peminjam</option>
                                <?php foreach ($users as $user): ?>
                                    <option value="<?= $user['id'] ?>" <?= $filter_user == $user['id'] ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($user['username']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-3 d-flex align-items-end">
                            <button type="submit" class="btn btn-primary w-100">
                                <i class="fas fa-search me-2"></i> Filter
                            </button>
                        </div>
                    </form>
                </div>
                
                <!-- Riwayat Table -->
                <?php if ($totalRiwayat > 0): ?>
                    <div class="table-responsive">
                        <table id="riwayatTable" class="table table-hover" style="width:100%">
                            <thead>
                                <tr>
                                    <th class="text-center">No</th>
                                    <th class="text-center">Kode</th>
                                    <th class="text-center">Peminjam</th>
                                    <th class="text-center">Barang</th>
                                    <th class="text-center">Tanggal</th>
                                    <th class="text-center">Durasi</th>
                                    <th class="text-center">Status</th>
                                    <th class="text-center">Kondisi</th>
                                    <th class="text-center">Biaya</th>
                                    <th class="text-center">Denda</th>
                                    <th class="text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($riwayat as $index => $item): ?>
                                <tr>
                                    <td class="text-center"><?= $index + 1 ?></td>
                                    <td class="fw-bold"><?= htmlspecialchars($item['kode_peminjaman']) ?></td>
                                    <td>
                                        <div class="fw-semibold"><?= htmlspecialchars($item['username']) ?></div>
                                        <small class="text-muted"><?= $item['user_role'] ?></small>
                                    </td>
                                    <td>
                                        <div class="fw-semibold"><?= htmlspecialchars($item['nama_barang']) ?></div>
                                        <small class="text-muted"><?= $item['kode_barang'] ?> (<?= $item['jumlah'] ?> unit)</small>
                                    </td>
                                    <td class="text-center">
                                        <div class="fw-semibold"><?= date('d/m/Y', strtotime($item['tgl_pinjam'])) ?></div>
                                        <small class="text-muted">
                                            Kembali: <?= date('d/m/Y', strtotime($item['tgl_kembali_rencana'])) ?>
                                        </small>
                                        <?php if (!empty($item['tgl_pengembalian'])): ?>
                                            <br><small class="text-success">
                                                Aktual: <?= date('d/m/Y', strtotime($item['tgl_pengembalian'])) ?>
                                            </small>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-center">
                                        <?= $item['lama_pinjam'] ?> hari
                                    </td>
                                    <td class="text-center">
                                        <?php 
                                        $status = $item['status'] ?? 'pending';
                                        $statusClass = 'badge-' . $status;
                                        echo '<span class="status-badge ' . $statusClass . '">' . ucfirst($status) . '</span>';
                                        ?>
                                    </td>
                                    <td class="text-center">
                                        <?php if (!empty($item['kondisi'])): 
                                            $kondisiClass = 'kondisi-' . ($item['kondisi'] == 'rusak_ringan' ? 'ringan' : ($item['kondisi'] == 'rusak_berat' ? 'berat' : 'baik'));
                                            $kondisiText = ucfirst(str_replace('_', ' ', $item['kondisi']));
                                        ?>
                                            <span class="kondisi-badge <?= $kondisiClass ?>"><?= $kondisiText ?></span>
                                        <?php else: ?>
                                            <span class="text-muted">-</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-end fw-bold">
                                        <?php if (!empty($item['total_harga'])): ?>
                                            Rp <?= number_format($item['total_harga'], 0, ',', '.') ?>
                                        <?php else: ?>
                                            <span class="text-muted">-</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-end">
                                        <?php if (!empty($item['denda'])): ?>
                                            <span class="text-danger fw-bold">
                                                Rp <?= number_format($item['denda'], 0, ',', '.') ?>
                                            </span>
                                        <?php else: ?>
                                            <span class="text-muted">-</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-center">
                                        <div class="d-flex gap-2 justify-content-center">
                                            <a href="./detail.php?id=<?= $item['id'] ?>" 
                                               class="btn btn-action btn-view btn-sm"
                                               title="Detail">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <button onclick="printInvoice(<?= $item['id'] ?>)" 
                                                    class="btn btn-action btn-print btn-sm"
                                                    title="Cetak Invoice">
                                                <i class="fas fa-print"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <div class="no-data">
                        <i class="fas fa-history"></i>
                        <h5>Belum Ada Riwayat Peminjaman</h5>
                        <p>Tidak ada data yang ditemukan dengan filter yang dipilih</p>
                    </div>
                <?php endif; ?>
                
                <!-- Footer -->
                <div class="footer-custom">
                    <p class="mb-0">
                        &copy; <?= date('Y') ?> Sistem Peminjaman Alat Berat. 
                        Laporan dihasilkan pada: <?= date('d/m/Y H:i:s') ?>
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

<!-- DataTables & Plugins -->
<script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
<script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/buttons/2.4.1/js/dataTables.buttons.min.js"></script>
<script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.bootstrap5.min.js"></script>
<script type="text/javascript" charset="utf8" src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
<script type="text/javascript" charset="utf8" src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
<script type="text/javascript" charset="utf8" src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
<script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.html5.min.js"></script>
<script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.print.min.js"></script>
<script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/responsive/2.5.0/js/dataTables.responsive.min.js"></script>
<script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/responsive/2.5.0/js/responsive.bootstrap5.min.js"></script>

<script>
$(document).ready(function() {
    // Initialize DataTable with export buttons
    var table = $('#riwayatTable').DataTable({
        language: {
            processing: "Memproses...",
            search: "",
            searchPlaceholder: "Cari kode, peminjam, atau barang...",
            lengthMenu: "Tampilkan _MENU_ data per halaman",
            zeroRecords: "Tidak ada data yang ditemukan",
            info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ riwayat",
            infoEmpty: "Menampilkan 0 sampai 0 dari 0 riwayat",
            infoFiltered: "(disaring dari _MAX_ total riwayat)",
            paginate: {
                first: "Pertama",
                last: "Terakhir",
                next: ">",
                previous: "<"
            }
        },
        pageLength: 25,
        lengthMenu: [[10, 25, 50, 100, -1], [10, 25, 50, 100, "Semua"]],
        order: [[0, 'asc']],
        responsive: true,
        dom: '<"row"<"col-md-6"B><"col-md-6"f>>rt<"row"<"col-md-6"l><"col-md-6"p>>',
        buttons: [
            {
                extend: 'excel',
                className: 'btn btn-success',
                text: '<i class="fas fa-file-excel me-2"></i>Excel',
                title: 'Riwayat Peminjaman',
                exportOptions: {
                    columns: ':visible'
                }
            },
            {
                extend: 'pdf',
                className: 'btn btn-danger',
                text: '<i class="fas fa-file-pdf me-2"></i>PDF',
                title: 'Riwayat Peminjaman',
                exportOptions: {
                    columns: ':visible'
                }
            },
            {
                extend: 'print',
                className: 'btn btn-warning',
                text: '<i class="fas fa-print me-2"></i>Print',
                title: 'Riwayat Peminjaman',
                exportOptions: {
                    columns: ':visible'
                },
                customize: function (win) {
                    $(win.document.body).css('font-size', '10pt');
                    $(win.document.body).find('table').addClass('compact').css('font-size', 'inherit');
                }
            },
            {
                extend: 'colvis',
                className: 'btn btn-info',
                text: '<i class="fas fa-columns me-2"></i>Kolom'
            }
        ],
        columnDefs: [
            {
                targets: 0,
                orderable: false,
                className: 'text-center'
            },
            {
                targets: 10,
                orderable: false,
                searchable: false
            }
        ]
    });
    
    // Global print button
    $('#btnPrint').on('click', function() {
        table.button('.buttons-print').trigger();
    });
    
    // Global export button
    $('#btnExport').on('click', function() {
        table.button('.buttons-excel').trigger();
    });
    
    // Print invoice function
    window.printInvoice = function(id) {
        window.open('invoice.php?id=' + id, '_blank');
    }
    
    // Auto refresh every 60 seconds
    setInterval(function() {
        table.ajax.reload(null, false);
    }, 60000);
});
</script>

</body>
</html>