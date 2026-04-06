<?php
// AKTIFKAN ERROR REPORTING
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Pastikan session_start() di awal
session_start();

include '../../partials/header.php'; 
$page = 'pengembalian';
include '../../partials/sidebar.php'; 

// Include koneksi database
include '../../app.php';

// DAPATKAN ID dari URL
$id = $_GET['id'] ?? 0;

if (!$id) {
    $_SESSION['error'] = "ID pengembalian tidak valid!";
    header("Location: index.php");
    exit();
}

// 1. AMBIL DATA PENGEMBALIAN berdasarkan ID
// HAPUS 'u.nama_lengkap' karena kolom ini TIDAK ADA di tabel users
$queryPengembalian = "SELECT pg.*, 
                      p.kode_peminjaman, p.user_id, p.barang_id, p.jumlah,
                      p.tgl_pinjam, p.tgl_kembali_rencana, p.tgl_kembali_aktual,
                      u.username,  -- HANYA username, TIDAK ADA nama_lengkap
                      b.nama_barang, b.kode_barang
                      FROM pengembalian pg
                      JOIN peminjaman p ON pg.peminjaman_id = p.id
                      JOIN users u ON p.user_id = u.id
                      JOIN barang b ON p.barang_id = b.id
                      WHERE pg.id = '$id'";

$resultPengembalian = mysqli_query($connect, $queryPengembalian);

if (!$resultPengembalian || mysqli_num_rows($resultPengembalian) == 0) {
    $_SESSION['error'] = "Data pengembalian tidak ditemukan!";
    header("Location: index.php");
    exit();
}

$pengembalian = mysqli_fetch_assoc($resultPengembalian);

// 2. AMBIL DATA PEMINJAMAN yang MASIH DIPINJAM (untuk dropdown)
$queryPeminjaman = "SELECT p.id, p.kode_peminjaman, p.tgl_pinjam, 
                   u.username,  -- HANYA username
                   b.nama_barang, b.kode_barang
                   FROM peminjaman p
                   JOIN users u ON p.user_id = u.id
                   JOIN barang b ON p.barang_id = b.id
                   WHERE p.status = 'dipinjam' 
                   OR p.id = '{$pengembalian['peminjaman_id']}'  
                   ORDER BY p.tgl_pinjam DESC";

$resultPeminjaman = mysqli_query($connect, $queryPeminjaman);
$peminjamanList = [];
while ($row = mysqli_fetch_assoc($resultPeminjaman)) {
    $peminjamanList[] = $row;
}
?>

<style>
    /* Reset Background Colors */
    body, #main, .container-fluid, .page-body-wrapper {
        background-color: #f8f9fc !important;
    }
    
    #main {
        margin-left: 260px;
        margin-top: 70px;
        padding: 20px;
        width: calc(100% - 260px);
        box-sizing: border-box;
        background-color: #f8f9fc !important;
    }

    .card {
        border-radius: 12px;
        box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.15) !important;
        border: 1px solid #e3e6f0 !important;
        background-color: #ffffff !important;
        margin-bottom: 20px;
    }

    .card-header {
        background-color: #ffffff !important;
        border-bottom: 1px solid #e3e6f0 !important;
        padding: 20px 25px !important;
        border-radius: 12px 12px 0 0 !important;
    }

    .card-body {
        padding: 25px !important;
        background-color: #ffffff !important;
    }

    .page-title {
        font-size: 1.75rem;
        font-weight: 700;
        color: #4e73df;
        margin-bottom: 0;
    }

    .btn-primary {
        background-color: #4e73df !important;
        border-color: #4e73df !important;
        border-radius: 8px;
        padding: 10px 20px;
        font-weight: 600;
        display: inline-flex;
        align-items: center;
        gap: 8px;
    }

    .btn-success {
        background-color: #1cc88a !important;
        border-color: #1cc88a !important;
        border-radius: 8px;
        padding: 10px 20px;
        font-weight: 600;
        display: inline-flex;
        align-items: center;
        gap: 8px;
    }

    .btn-primary:hover, .btn-success:hover {
        transform: translateY(-2px);
        box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
    }

    .form-label {
        font-weight: 600;
        color: #5a5c69;
        margin-bottom: 8px;
    }

    .form-control, .form-select, .form-textarea {
        border: 1px solid #d1d3e2 !important;
        border-radius: 0.35rem !important;
        padding: 0.75rem 1rem !important;
        font-size: 0.9rem !important;
        background-color: #ffffff !important;
        color: #000000 !important;
    }

    .form-control:focus, .form-select:focus, .form-textarea:focus {
        border-color: #bac8f3 !important;
        box-shadow: 0 0 0 0.2rem rgba(78, 115, 223, 0.25) !important;
    }

    .form-text {
        color: #858796 !important;
        font-size: 0.85rem;
        margin-top: 5px;
    }

    .alert {
        border-radius: 8px;
        border: none;
        padding: 15px 20px;
        margin-bottom: 20px;
    }

    .alert-danger {
        background-color: #f8d7da !important;
        color: #721c24 !important;
        border-left: 4px solid #e74a3b !important;
    }

    .alert-success {
        background-color: #d4edda !important;
        color: #155724 !important;
        border-left: 4px solid #1cc88a !important;
    }

    .header-container {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 25px;
        padding-bottom: 15px;
        border-bottom: 2px solid #f0f2f5;
    }

    .info-box {
        background-color: #f8f9fa;
        border-left: 4px solid #4e73df;
        padding: 15px;
        border-radius: 6px;
        margin-bottom: 20px;
    }

    .info-box h6 {
        color: #4e73df;
        margin-bottom: 5px;
    }

    @media (max-width: 768px) {
        #main {
            margin-left: 0;
            width: 100%;
            padding: 15px;
        }
        
        .header-container {
            flex-direction: column;
            align-items: flex-start;
            gap: 15px;
        }
        
        .page-title {
            font-size: 1.5rem;
        }
    }
</style>

<div class="container-fluid page-body-wrapper">
    <div id="main" class="p-4">
        <div class="card">
            <div class="card-header">
                <div class="header-container">
                    <h4 class="fw-bold text-primary page-title">
                        <i class="fas fa-exchange-alt"></i> Edit Data Pengembalian
                    </h4>
                    <a href="./index.php" class="btn btn-primary">
                        <i class="fas fa-arrow-left"></i> Kembali
                    </a>
                </div>
            </div>
            
            <div class="card-body">
                <!-- Tampilkan pesan error/success dari session -->
                <?php if (isset($_SESSION['error'])): ?>
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        <?php
                        echo $_SESSION['error'];
                        unset($_SESSION['error']);
                        ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                <?php endif; ?>

                <?php if (isset($_SESSION['success'])): ?>
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <i class="fas fa-check-circle me-2"></i>
                        <?php
                        echo $_SESSION['success'];
                        unset($_SESSION['success']);
                        ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                <?php endif; ?>
                
                <!-- INFO BOX Data yang sedang diedit -->
                <div class="info-box">
                    <h6><i class="fas fa-info-circle me-2"></i>Data Saat Ini</h6>
                    <p class="mb-1"><strong>Peminjaman:</strong> <?= $pengembalian['kode_peminjaman'] ?></p>
                    <p class="mb-1"><strong>Peminjam:</strong> <?= $pengembalian['username'] ?></p>
                    <p class="mb-1"><strong>Alat Berat:</strong> <?= $pengembalian['nama_barang'] ?> (<?= $pengembalian['kode_barang'] ?>)</p>
                    <p class="mb-0"><strong>Tanggal Pinjam:</strong> <?= date('d-m-Y', strtotime($pengembalian['tgl_pinjam'])) ?></p>
                </div>
                
                <div class="row justify-content-center">
                    <div class="col-md-8">
                        <form action="../../action/pengembalian/update.php" method="POST" class="needs-validation" novalidate>
                            <!-- Hidden input untuk ID pengembalian -->
                            <input type="hidden" name="id" value="<?= $pengembalian['id'] ?>">
                            
                            <!-- Hidden input untuk ID peminjaman (tidak bisa diubah) -->
                            <input type="hidden" name="peminjaman_id" value="<?= $pengembalian['peminjaman_id'] ?>">
                            
                            <div class="mb-4">
                                <label class="form-label">Peminjaman</label>
                                <input type="text" class="form-control" 
                                    value="Kode: <?= $pengembalian['kode_peminjaman'] ?> | 
                                           Peminjam: <?= $pengembalian['username'] ?> | 
                                           Alat: <?= $pengembalian['nama_barang'] ?>" 
                                    readonly>
                                <div class="form-text">Data peminjaman tidak dapat diubah</div>
                            </div>
                            
                            <div class="row mb-4">
                                <div class="col-md-6">
                                    <label for="tgl_kembali" class="form-label">Tanggal Kembali *</label>
                                    <input type="date" name="tgl_kembali" class="form-control" id="tgl_kembali"
                                        required value="<?= date('Y-m-d', strtotime($pengembalian['tgl_kembali'])) ?>">
                                    <div class="invalid-feedback">Harap isi tanggal kembali</div>
                                </div>
                                <div class="col-md-6">
                                    <label for="kondisi" class="form-label">Kondisi Alat *</label>
                                    <select name="kondisi" class="form-select" id="kondisi" required>
                                        <option value="baik" <?= ($pengembalian['kondisi'] == 'baik') ? 'selected' : '' ?>>Baik</option>
                                        <option value="rusak_ringan" <?= ($pengembalian['kondisi'] == 'rusak_ringan') ? 'selected' : '' ?>>Rusak Ringan</option>
                                        <option value="rusak_berat" <?= ($pengembalian['kondisi'] == 'rusak_berat') ? 'selected' : '' ?>>Rusak Berat</option>
                                    </select>
                                    <div class="invalid-feedback">Harap pilih kondisi alat</div>
                                </div>
                            </div>
                            
                            <div class="mb-4">
                                <label for="denda" class="form-label">Denda (Rp)</label>
                                <input type="number" name="denda" class="form-control" id="denda"
                                    placeholder="0" min="0" step="1000"
                                    value="<?= $pengembalian['denda'] ?>">
                                <div class="form-text">Isi jika ada denda keterlambatan/kerusakan</div>
                            </div>
                            
                            <div class="mb-4">
                                <label for="keterangan" class="form-label">Keterangan</label>
                                <textarea name="keterangan" class="form-control form-textarea" id="keterangan" 
                                    rows="3" placeholder="Tambahkan keterangan jika perlu..."><?= htmlspecialchars($pengembalian['keterangan']) ?></textarea>
                                <div class="form-text">Deskripsi kondisi atau alasan denda (jika ada)</div>
                            </div>
                            
                            <div class="d-flex justify-content-between mt-5">
                                <a href="./index.php" class="btn btn-outline-secondary">
                                    <i class="fas fa-times"></i> Batal
                                </a>
                                <button type="submit" class="btn btn-success" name="tombol">
                                    <i class="fas fa-save"></i> Simpan Perubahan
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <?php include '../../partials/footer.php'; ?>
    </div>
</div>

<?php include '../../partials/script.php'; ?>

<!-- Font Awesome -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

<script>
// Validasi form
(function() {
    'use strict';
    var forms = document.querySelectorAll('.needs-validation');
    Array.prototype.slice.call(forms).forEach(function(form) {
        form.addEventListener('submit', function(event) {
            if (!form.checkValidity()) {
                event.preventDefault();
                event.stopPropagation();
            }
            form.classList.add('was-validated');
        }, false);
    });
})();

// Set tanggal maksimum ke hari ini
document.addEventListener('DOMContentLoaded', function() {
    const today = new Date().toISOString().split('T')[0];
    document.getElementById('tgl_kembali').max = today;
});
</script>