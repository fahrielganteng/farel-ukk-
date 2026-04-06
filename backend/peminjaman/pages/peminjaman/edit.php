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

// Security check: Hanya pemilik yang bisa edit
if ($peminjaman->user_id != $_SESSION['user_id']) {
    header("Location: ./index.php");
    exit();
}

// Bisnis Logic: Hanya status 'pending' yang bisa diedit oleh peminjam
if ($peminjaman->status != 'pending') {
    $_SESSION['error'] = "Peminjaman yang sudah diproses tidak dapat diubah.";
    header("Location: ./index.php");
    exit();
}
?>

<?php 
include '../../partials/header.php'; 
$page = 'peminjaman'; 
include '../../partials/sidebar.php'; 
?>

<div class="content-wrapper">
    <div class="page-title">
        <i class="fas fa-edit"></i>
        <span>Ubah Pengajuan Sewa</span>
    </div>

    <!-- Alert Messages -->
    <?php if (isset($_SESSION['error'])): ?>
        <div class="alert status-ditolak mb-4 border-0 shadow-sm d-flex align-items-center">
            <i class="fas fa-exclamation-circle me-3 fa-lg"></i>
            <div><?= $_SESSION['error']; unset($_SESSION['error']); ?></div>
        </div>
    <?php endif; ?>

    <div class="row justify-content-center">
        <div class="col-xl-9">
            <div class="card-admin shadow-sm">
                <div class="card-header-admin bg-white d-flex justify-content-between align-items-center">
                    <span><i class="fas fa-file-signature me-2 text-indigo-600"></i> Kode Transaksi: <strong><?= htmlspecialchars($peminjaman->kode_peminjaman) ?></strong></span>
                    <a href="./index.php" class="btn btn-sm btn-outline-secondary">
                        <i class="fas fa-arrow-left me-1"></i> Kembali
                    </a>
                </div>
                <div class="card-body p-4 p-md-5">
                    <form action="../../action/peminjaman/update.php?id=<?= $peminjaman->id ?>" method="POST" class="needs-validation" novalidate>
                        <!-- Hidden Fields for Security -->
                        <input type="hidden" name="user_id" value="<?= $_SESSION['user_id'] ?>">
                        <input type="hidden" name="status" value="pending">
                        <input type="hidden" name="kode_peminjaman" value="<?= htmlspecialchars($peminjaman->kode_peminjaman) ?>">
                        
                        <div class="row g-4">
                            <!-- Pilih Alat -->
                            <div class="col-md-12">
                                <label for="barang_id" class="form-label-admin">Pilih Alat Berat <span class="text-rose-500">*</span></label>
                                <div class="input-group-admin">
                                    <span class="input-group-text-admin"><i class="fas fa-tractor"></i></span>
                                    <select name="barang_id" class="form-select-admin" id="barang_id" required>
                                        <?php 
                                        $query_barang = "SELECT id, nama_barang, stok FROM barang WHERE stok > 0 OR id = " . (int)$peminjaman->barang_id;
                                        $result_barang = mysqli_query($connect, $query_barang);
                                        while($item = mysqli_fetch_assoc($result_barang)): 
                                            $selected = ($peminjaman->barang_id == $item['id']) ? 'selected' : '';
                                        ?>
                                            <option value="<?= $item['id'] ?>" data-stok="<?= $item['stok'] ?>" <?= $selected ?>>
                                                <?= htmlspecialchars($item['nama_barang']) ?> (Tersedia: <?= $item['stok'] ?> Unit)
                                            </option>
                                        <?php endwhile; ?>
                                    </select>
                                </div>
                                <div class="invalid-feedback">Harap pilih alat yang ingin disewa.</div>
                            </div>

                            <!-- Jumlah & Tanggal -->
                            <div class="col-md-4">
                                <label for="jumlah" class="form-label-admin">Jumlah Unit <span class="text-rose-500">*</span></label>
                                <div class="input-group-admin">
                                    <span class="input-group-text-admin"><i class="fas fa-layer-group"></i></span>
                                    <input type="number" name="jumlah" class="form-control-admin" id="jumlah"
                                        value="<?= htmlspecialchars($peminjaman->jumlah) ?>" required min="1">
                                </div>
                                <div class="small text-slate-400 mt-2" id="stok-info">Maksimum unit sesuai stok tersedia.</div>
                            </div>

                            <div class="col-md-4">
                                <label for="tgl_pinjam" class="form-label-admin">Mulai Sewa <span class="text-rose-500">*</span></label>
                                <div class="input-group-admin">
                                    <span class="input-group-text-admin"><i class="fas fa-calendar-alt"></i></span>
                                    <input type="date" name="tgl_pinjam" class="form-control-admin" id="tgl_pinjam"
                                        value="<?= date('Y-m-d', strtotime($peminjaman->tgl_pinjam)) ?>" required>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <label for="tgl_kembali_rencana" class="form-label-admin">Rencana Kembali <span class="text-rose-500">*</span></label>
                                <div class="input-group-admin">
                                    <span class="input-group-text-admin"><i class="fas fa-calendar-check"></i></span>
                                    <input type="date" name="tgl_kembali_rencana" class="form-control-admin" id="tgl_kembali_rencana" 
                                        value="<?= date('Y-m-d', strtotime($peminjaman->tgl_kembali_rencana)) ?>" required>
                                </div>
                                <div class="small text-indigo-600 mt-2 fw-700" id="lama_pinjam_info">Durasi: <?= $peminjaman->lama_pinjam ?> hari</div>
                            </div>

                            <!-- Submit -->
                            <div class="col-12 mt-5">
                                <div class="d-flex gap-3">
                                    <button type="submit" name="tombol" class="btn btn-indigo py-3 px-5 rounded-4 shadow-sm fw-800">
                                        <i class="fas fa-save me-2"></i> Simpan Perubahan
                                    </button>
                                    <a href="./index.php" class="btn btn-outline-slate py-3 px-4 rounded-4 fw-800 border-2">
                                        Batal
                                    </a>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="p-4 bg-white rounded-4 border border-slate-200 mt-4 text-center">
        <small class="text-slate-400 fw-600 italic">&copy; <?= date('Y') ?> HeavyHire - Premium Heavy Equipment Rental Solution</small>
    </div>
</div>

<?php include '../../partials/script.php'; ?>

<script>
$(document).ready(function() {
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

    // Update max jumlah berdasarkan stok barang
    $('#barang_id').on('change', function() {
        const stok = $(this).find(':selected').data('stok');
        const jumlahInput = $('#jumlah');
        const stokInfo = $('#stok-info');
        
        if (stok) {
            jumlahInput.attr('max', stok);
            stokInfo.text('Maksimum: ' + stok + ' unit');
        }
    });

    // Hitung lama pinjam
    function hitungLamaPinjam() {
        const tglPinjam = $('#tgl_pinjam').val();
        const tglKembali = $('#tgl_kembali_rencana').val();
        const infoElement = $('#lama_pinjam_info');
        
        if (tglPinjam && tglKembali) {
            const tgl1 = new Date(tglPinjam);
            const tgl2 = new Date(tglKembali);
            const diffTime = tgl2 - tgl1;
            const diffDays = Math.max(0, Math.ceil(diffTime / (1000 * 60 * 60 * 24)));
            
            infoElement.text('Durasi: ' + diffDays + ' hari');
            if (diffDays <= 0) {
                infoElement.addClass('text-rose-500').removeClass('text-indigo-600');
            } else {
                infoElement.addClass('text-indigo-600').removeClass('text-rose-500');
            }
        }
    }

    $('#tgl_pinjam, #tgl_kembali_rencana').on('change', hitungLamaPinjam);

    // Initial Trigger
    $('#barang_id').trigger('change');
    hitungLamaPinjam();
});
</script>

<?php include '../../partials/footer.php'; ?>