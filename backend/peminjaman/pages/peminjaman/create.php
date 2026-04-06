<?php
// AKTIFKAN ERROR REPORTING
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Pastikan session_start() di awal
session_start();

include '../../app.php';
include '../../partials/header.php';
$page = 'peminjaman';
include '../../partials/sidebar.php';

// Ambil barang untuk dropdown
$barang = mysqli_query($connect, "SELECT id, nama_barang, stok FROM barang WHERE stok > 0");

// Ambil user_id dari session
$user_id = $_SESSION['user_id'];
$username = $_SESSION['username'];
?>

<div class="content-wrapper">
    <div class="page-title">
        <i class="fas fa-plus-circle"></i>
        <span>Pengajuan Sewa Alat</span>
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
                    <span><i class="fas fa-file-signature me-2 text-indigo-600"></i> Lengkapi Formulir Penyewaan</span>
                    <a href="./index.php" class="btn btn-sm btn-outline-secondary">
                        <i class="fas fa-arrow-left me-1"></i> Kembali
                    </a>
                </div>
                <div class="card-body p-4 p-md-5">
                    <form action="../../action/peminjaman/store.php" method="POST" class="needs-validation" novalidate>
                        <!-- Hidden User ID -->
                        <input type="hidden" name="user_id" value="<?= $user_id ?>">
                        
                        <div class="row g-4">
                            <!-- Kode Otomatis -->
                            <div class="col-md-6">
                                <label for="kode_peminjaman" class="form-label-admin">Kode Transaksi</label>
                                <div class="input-group-admin">
                                    <span class="input-group-text-admin"><i class="fas fa-hashtag"></i></span>
                                    <input type="text" name="kode_peminjaman" class="form-control-admin bg-slate-50" id="kode_peminjaman"
                                        value="<?php echo 'PINJ-' . date('Ymd') . sprintf('%03d', rand(1, 999)); ?>" readonly>
                                </div>
                            </div>

                            <!-- Nama Peminjam (Readonly) -->
                            <div class="col-md-6">
                                <label class="form-label-admin">Penyewa</label>
                                <div class="input-group-admin">
                                    <span class="input-group-text-admin"><i class="fas fa-user-check"></i></span>
                                    <input type="text" class="form-control-admin bg-slate-50" value="<?= htmlspecialchars($username) ?>" readonly>
                                </div>
                            </div>

                            <!-- Pilih Alat -->
                            <div class="col-md-12">
                                <label for="barang_id" class="form-label-admin">Pilih Alat Berat <span class="text-rose-500">*</span></label>
                                <div class="input-group-admin">
                                    <span class="input-group-text-admin"><i class="fas fa-tractor"></i></span>
                                    <select name="barang_id" class="form-select-admin" id="barang_id" required>
                                        <option value="">-- Pilih Alat --</option>
                                        <?php while($item = mysqli_fetch_assoc($barang)): ?>
                                            <option value="<?= $item['id'] ?>" data-stok="<?= $item['stok'] ?>">
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
                                        placeholder="0" required min="1" max="1">
                                </div>
                                <div class="small text-slate-400 mt-2" id="stok-info">Maksimum: 1 unit</div>
                            </div>

                            <div class="col-md-4">
                                <label for="tgl_pinjam" class="form-label-admin">Mulai Sewa <span class="text-rose-500">*</span></label>
                                <div class="input-group-admin">
                                    <span class="input-group-text-admin"><i class="fas fa-calendar-alt"></i></span>
                                    <input type="date" name="tgl_pinjam" class="form-control-admin" id="tgl_pinjam"
                                        value="<?= date('Y-m-d') ?>" required>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <label for="tgl_kembali_rencana" class="form-label-admin">Rencana Kembali <span class="text-rose-500">*</span></label>
                                <div class="input-group-admin">
                                    <span class="input-group-text-admin"><i class="fas fa-calendar-check"></i></span>
                                    <input type="date" name="tgl_kembali_rencana" class="form-control-admin" id="tgl_kembali_rencana" required>
                                </div>
                                <div class="small text-indigo-600 mt-2 fw-700" id="lama_pinjam_info">Durasi: 0 hari</div>
                            </div>

                            <!-- Submit -->
                            <div class="col-12 mt-5">
                                <div class="d-flex gap-3">
                                    <button type="submit" name="tombol" class="btn btn-indigo py-3 px-5 rounded-4 shadow-sm fw-800">
                                        <i class="fas fa-paper-plane me-2"></i> Ajukan Penyewaan
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
            
            <div class="mt-4 p-4 bg-emerald-50 rounded-4 border border-emerald-100">
                <div class="d-flex gap-3">
                    <div class="text-emerald-600"><i class="fas fa-info-circle fa-2x"></i></div>
                    <div>
                        <h6 class="fw-800 text-slate-800 mb-1">Informasi Penting</h6>
                        <p class="small text-slate-500 mb-0">Setelah pengajuan dikirim, petugas kami akan meninjau ketersediaan unit. Anda akan menerima notifikasi status <strong>Disetujui</strong> atau <strong>Ditolak</strong> segera setelah peninjauan selesai.</p>
                    </div>
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
        } else {
            jumlahInput.attr('max', 1);
            stokInfo.text('Maksimum: 1 unit');
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
            
            // Perbedaan dalam milidetik
            const diffTime = tgl2 - tgl1;
            
            // Konversi ke hari (ditambah 1 hari karena biasanya inklusif)
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

    // Set tanggal minimal
    const today = new Date().toISOString().split('T')[0];
    $('#tgl_pinjam').attr('min', today);
    
    // Set default tgl kembali 3 hari lagi
    const defaultKembali = new Date();
    defaultKembali.setDate(defaultKembali.getDate() + 3);
    $('#tgl_kembali_rencana').val(defaultKembali.toISOString().split('T')[0]).attr('min', today);
    
    hitungLamaPinjam();
});
</script>

<?php include '../../partials/footer.php'; ?>