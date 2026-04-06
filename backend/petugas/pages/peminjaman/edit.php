<?php
// AKTIFKAN ERROR REPORTING
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Pastikan session_start() di awal
session_start();

include '../../partials/header.php'; 
$page = 'peminjaman'; 
include '../../partials/sidebar.php'; 

// Include action untuk mengambil data
include '../../action/peminjaman/show.php';
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

    .form-label {
        font-weight: 600;
        color: #5a5c69;
        margin-bottom: 8px;
    }

    .form-control, .form-select {
        border: 1px solid #d1d3e2 !important;
        border-radius: 0.35rem !important;
        padding: 0.75rem 1rem !important;
        font-size: 0.9rem !important;
    }

    .form-control:focus, .form-select:focus {
        border-color: #bac8f3 !important;
        box-shadow: 0 0 0 0.2rem rgba(78, 115, 223, 0.25) !important;
    }

    .form-control[readonly], .form-control[disabled] {
        background-color: #f8f9fc !important;
        color: #6c757d !important;
        border-color: #e3e6f0 !important;
        cursor: not-allowed;
    }

    .form-text {
        color: #858796 !important;
        font-size: 0.85rem;
        margin-top: 5px;
    }

    .info-card {
        background: #f8f9ff;
        border-radius: 10px;
        padding: 20px;
        margin-bottom: 15px;
        border-left: 4px solid #4e73df;
    }

    .info-label {
        font-size: 0.9rem;
        color: #6c757d;
        margin-bottom: 5px;
        font-weight: 600;
    }

    .info-value {
        font-size: 1.1rem;
        color: #000000;
        font-weight: 500;
    }

    .status-badge {
        padding: 8px 20px;
        border-radius: 20px;
        font-weight: 600;
        font-size: 0.85rem;
        display: inline-block;
    }

    .badge-pending {
        background: #fef3c7;
        color: #92400e;
    }

    .badge-dipinjam {
        background: #dbeafe;
        color: #1e40af;
    }

    .badge-selesai {
        background: #d1fae5;
        color: #065f46;
    }

    .badge-ditolak {
        background: #fee2e2;
        color: #991b1b;
    }

    .badge-terlambat {
        background: #fed7aa;
        color: #9a3412;
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

    .alert-info {
        background-color: #d1ecf1 !important;
        color: #0c5460 !important;
        border-left: 4px solid #17a2b8 !important;
    }

    .header-container {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 25px;
        padding-bottom: 15px;
        border-bottom: 2px solid #f0f2f5;
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
                        <i class="fas fa-edit"></i> Update Status Peminjaman
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
                
                <!-- Info Penting -->
                <div class="alert alert-info">
                    <i class="fas fa-info-circle me-2"></i>
                    <strong>Informasi:</strong> Anda hanya dapat mengubah status peminjaman. Data lain tidak dapat diubah untuk menjaga konsistensi sistem.
                </div>
                
                <div class="row justify-content-center">
                    <div class="col-md-8">
                        <!-- Informasi Peminjaman (Read-only) -->
                        <div class="info-card">
                            <div class="info-label">Kode Peminjaman</div>
                            <div class="info-value"><?= htmlspecialchars($peminjaman->kode_peminjaman ?? '') ?></div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="info-card">
                                    <div class="info-label">Peminjam</div>
                                    <div class="info-value"><?= htmlspecialchars($peminjaman->username ?? '') ?></div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="info-card">
                                    <div class="info-label">Barang</div>
                                    <div class="info-value"><?= htmlspecialchars($peminjaman->nama_barang ?? '') ?></div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="info-card">
                                    <div class="info-label">Jumlah</div>
                                    <div class="info-value"><?= $peminjaman->jumlah ?? '0' ?> unit</div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="info-card">
                                    <div class="info-label">Kategori</div>
                                    <div class="info-value"><?= htmlspecialchars($peminjaman->nama_kategori ?? 'Tidak ada') ?></div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="info-card">
                                    <div class="info-label">Tanggal Pinjam</div>
                                    <div class="info-value">
                                        <?= isset($peminjaman->tgl_pinjam) ? date('d-m-Y', strtotime($peminjaman->tgl_pinjam)) : '' ?>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="info-card">
                                    <div class="info-label">Tanggal Kembali Rencana</div>
                                    <div class="info-value">
                                        <?= isset($peminjaman->tgl_kembali_rencana) ? date('d-m-Y', strtotime($peminjaman->tgl_kembali_rencana)) : '' ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="info-card">
                                    <div class="info-label">Tanggal Kembali Aktual</div>
                                    <div class="info-value">
                                        <?= isset($peminjaman->tgl_kembali_aktual) && $peminjaman->tgl_kembali_aktual != '0000-00-00' ? 
                                            date('d-m-Y', strtotime($peminjaman->tgl_kembali_aktual)) : 
                                            '<span class="text-muted">Belum dikembalikan</span>' ?>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="info-card">
                                    <div class="info-label">Lama Pinjam</div>
                                    <div class="info-value"><?= $peminjaman->lama_pinjam ?? '0' ?> hari</div>
                                </div>
                            </div>
                        </div>
                        
                        <?php if (!empty($peminjaman->total_harga)): ?>
                        <div class="info-card">
                            <div class="info-label">Total Harga</div>
                            <div class="info-value">Rp <?= number_format($peminjaman->total_harga, 0, ',', '.') ?></div>
                        </div>
                        <?php endif; ?>
                        
                        <?php if (!empty($peminjaman->keterangan)): ?>
                        <div class="info-card">
                            <div class="info-label">Keterangan</div>
                            <div class="info-value"><?= nl2br(htmlspecialchars($peminjaman->keterangan)) ?></div>
                        </div>
                        <?php endif; ?>
                        
                        <div class="info-card bg-light">
                            <div class="info-label">Status Saat Ini</div>
                            <div class="info-value">
                                <?php 
                                $status = $peminjaman->status ?? 'pending';
                                $statusClass = '';
                                $statusText = '';
                                
                                switch($status) {
                                    case 'dipinjam':
                                        $statusClass = 'badge-dipinjam';
                                        $statusText = 'Dipinjam';
                                        break;
                                    case 'selesai':
                                        $statusClass = 'badge-selesai';
                                        $statusText = 'Selesai';
                                        break;
                                    case 'terlambat':
                                        $statusClass = 'badge-terlambat';
                                        $statusText = 'Terlambat';
                                        break;
                                    case 'disetujui':
                                        $statusClass = 'badge-disetujui';
                                        $statusText = 'Disetujui';
                                        break;
                                    case 'ditolak':
                                        $statusClass = 'badge-ditolak';
                                        $statusText = 'Ditolak';
                                        break;
                                    default:
                                        $statusClass = 'badge-pending';
                                        $statusText = 'Pending';
                                }
                                
                                echo '<span class="status-badge ' . $statusClass . '">' . $statusText . '</span>';
                                ?>
                            </div>
                        </div>
                        
                        <!-- Form Update Status -->
                        <div class="card mt-4">
                            <div class="card-header bg-light">
                                <h5 class="mb-0">
                                    <i class="fas fa-sync-alt me-2"></i>
                                    Update Status Peminjaman
                                </h5>
                            </div>
                            <div class="card-body">
                                <form action="../../action/peminjaman/update.php" method="POST" class="needs-validation" novalidate>
                                    <input type="hidden" name="id" value="<?= $peminjaman->id ?>">
                                    <input type="hidden" name="tombol" value="1">
                                    
                                    <!-- Hidden fields untuk data yang tidak boleh diubah -->
                                    <input type="hidden" name="user_id" value="<?= $peminjaman->user_id ?>">
                                    <input type="hidden" name="barang_id" value="<?= $peminjaman->barang_id ?>">
                                    <input type="hidden" name="jumlah" value="<?= $peminjaman->jumlah ?>">
                                    <input type="hidden" name="tgl_pinjam" value="<?= $peminjaman->tgl_pinjam ?>">
                                    <input type="hidden" name="tgl_kembali_rencana" value="<?= $peminjaman->tgl_kembali_rencana ?>">
                                    <input type="hidden" name="tgl_kembali_aktual" value="<?= $peminjaman->tgl_kembali_aktual ?>">
                                    <input type="hidden" name="keterangan" value="<?= htmlspecialchars($peminjaman->keterangan ?? '') ?>">
                                    
                                    <div class="mb-4">
                                        <label for="status" class="form-label">Status Baru *</label>
                                        <select name="status" class="form-select" id="status" required>
                                            <option value="">Pilih Status Baru</option>
                                            <option value="pending" <?= ($peminjaman->status ?? 'pending') == 'pending' ? 'selected' : '' ?>>Pending</option>
                                            <option value="disetujui" <?= ($peminjaman->status ?? 'pending') == 'disetujui' ? 'selected' : '' ?>>Disetujui</option>
                                            <option value="ditolak" <?= ($peminjaman->status ?? 'pending') == 'ditolak' ? 'selected' : '' ?>>Ditolak</option>
                                            <option value="dipinjam" <?= ($peminjaman->status ?? 'pending') == 'dipinjam' ? 'selected' : '' ?>>Dipinjam</option>
                                            <option value="selesai" <?= ($peminjaman->status ?? 'pending') == 'selesai' ? 'selected' : '' ?>>Selesai</option>
                                            <option value="terlambat" <?= ($peminjaman->status ?? 'pending') == 'terlambat' ? 'selected' : '' ?>>Terlambat</option>
                                        </select>
                                        <div class="form-text">
                                            Pilih status baru untuk peminjaman ini. Perubahan status akan mempengaruhi stok barang.
                                        </div>
                                        <div class="invalid-feedback">Harap pilih status baru</div>
                                    </div>
                                    
                                    <div class="mb-4">
                                        <label for="status_keterangan" class="form-label">Keterangan Perubahan Status</label>
                                        <textarea name="status_keterangan" class="form-control" id="status_keterangan" rows="3"
                                            placeholder="Masukkan alasan perubahan status (opsional)"></textarea>
                                        <div class="form-text">
                                            Jelaskan alasan perubahan status (misal: "Barang sudah dikembalikan", "Peminjaman ditolak karena...", dll)
                                        </div>
                                    </div>
                                    
                                    <!-- Warning untuk perubahan status yang mempengaruhi stok -->
                                    <div id="statusWarning" class="alert alert-warning" style="display: none;">
                                        <i class="fas fa-exclamation-triangle me-2"></i>
                                        <span id="warningText"></span>
                                    </div>
                                    
                                    <div class="d-flex justify-content-between mt-4">
                                        <a href="./index.php" class="btn btn-outline-secondary">
                                            <i class="fas fa-times"></i> Batal
                                        </a>
                                        <button type="submit" class="btn btn-success">
                                            <i class="fas fa-save"></i> Update Status
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                        
                        <!-- Status Change Log (jika ada) -->
                        <div class="card mt-4">
                            <div class="card-body">
                                <?php
                                // Query untuk mendapatkan riwayat status dari log_aktivitas
                                $log_query = "SELECT * FROM log_aktivitas 
                                            WHERE tipe_data = 'peminjaman' 
                                            AND data_id = '{$peminjaman->id}'
                                            AND aksi LIKE '%status%'
                                            ORDER BY created_at DESC
                                            LIMIT 5";
                                $log_result = mysqli_query($connect, $log_query);
                                
                                if ($log_result && mysqli_num_rows($log_result) > 0):
                                ?>
                                    <div class="list-group">
                                        <?php while($log = mysqli_fetch_assoc($log_result)): ?>
                                        <div class="list-group-item border-0">
                                            <div class="d-flex w-100 justify-content-between">
                                                <h6 class="mb-1"><?= htmlspecialchars($log['aksi']) ?></h6>
                                                <small class="text-muted"><?= date('d M Y H:i', strtotime($log['created_at'])) ?></small>
                                            </div>
                                            <?php if (!empty($log['deskripsi'])): ?>
                                            <p class="mb-1 small text-muted"><?= htmlspecialchars($log['deskripsi']) ?></p>
                                            <?php endif; ?>
                                            <small class="text-muted">
                                                <i class="fas fa-user me-1"></i> User ID: <?= $log['user_id'] ?>
                                                <?php if (!empty($log['ip_address'])): ?>
                                                • <i class="fas fa-network-wired me-1"></i> IP: <?= $log['ip_address'] ?>
                                                <?php endif; ?>
                                            </small>
                                        </div>
                                        <?php endwhile; ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php include '../../partials/footer.php'; ?>
    </div>
</div>

<?php include '../../partials/script.php'; ?>

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

// Tampilkan warning berdasarkan perubahan status
document.getElementById('status').addEventListener('change', function() {
    const currentStatus = "<?= $peminjaman->status ?? 'pending' ?>";
    const newStatus = this.value;
    const warningDiv = document.getElementById('statusWarning');
    const warningText = document.getElementById('warningText');
    
    warningDiv.style.display = 'none';
    
    // Rules untuk perubahan status
    if (currentStatus === newStatus) {
        return;
    }
    
    // Warning untuk perubahan yang mempengaruhi stok
    const statusAffectsStock = ['dipinjam', 'selesai'];
    const oldAffectsStock = statusAffectsStock.includes(currentStatus);
    const newAffectsStock = statusAffectsStock.includes(newStatus);
    
    if (oldAffectsStock && !newAffectsStock) {
        // Dari mengurangi stok ke tidak mengurangi stok
        warningText.textContent = 'PERINGATAN: Status akan diubah dari "' + currentStatus + '" ke "' + newStatus + '". Stok barang akan ditambahkan kembali.';
        warningDiv.style.display = 'block';
        warningDiv.className = 'alert alert-warning';
    } else if (!oldAffectsStock && newAffectsStock) {
        // Dari tidak mengurangi stok ke mengurangi stok
        warningText.textContent = 'PERINGATAN: Status akan diubah dari "' + currentStatus + '" ke "' + newStatus + '". Stok barang akan dikurangi.';
        warningDiv.style.display = 'block';
        warningDiv.className = 'alert alert-warning';
    } else if (oldAffectsStock && newAffectsStock && currentStatus !== newStatus) {
        // Antar status yang sama-sama mempengaruhi stok
        warningText.textContent = 'PERINGATAN: Status akan diubah dari "' + currentStatus + '" ke "' + newStatus + '". Penyesuaian stok akan dilakukan.';
        warningDiv.style.display = 'block';
        warningDiv.className = 'alert alert-warning';
    }
    
    // Warning khusus untuk status tertentu
    if (newStatus === 'ditolak') {
        warningText.textContent = 'PERINGATAN: Status akan diubah ke "Ditolak". Pastikan peminjaman benar-benar ditolak.';
        warningDiv.style.display = 'block';
        warningDiv.className = 'alert alert-danger';
    } else if (newStatus === 'terlambat') {
        warningText.textContent = 'PERINGATAN: Status akan diubah ke "Terlambat". Pastikan peminjaman sudah melewati batas waktu pengembalian.';
        warningDiv.style.display = 'block';
        warningDiv.className = 'alert alert-danger';
    } else if (newStatus === 'selesai' && currentStatus !== 'dipinjam') {
        warningText.textContent = 'PERINGATAN: Mengubah status ke "Selesai" tanpa melalui status "Dipinjam". Pastikan barang sudah dikembalikan dengan benar.';
        warningDiv.style.display = 'block';
        warningDiv.className = 'alert alert-danger';
    }
});

// Konfirmasi sebelum submit
document.querySelector('form').addEventListener('submit', function(e) {
    const currentStatus = "<?= $peminjaman->status ?? 'pending' ?>";
    const newStatus = document.getElementById('status').value;
    
    if (currentStatus === newStatus) {
        if (!confirm('Status tidak berubah. Apakah Anda yakin ingin melanjutkan?')) {
            e.preventDefault();
            return false;
        }
    } else {
        if (!confirm('Apakah Anda yakin ingin mengubah status dari "' + currentStatus + '" ke "' + newStatus + '"?')) {
            e.preventDefault();
            return false;
        }
    }
    
    return true;
});
</script>