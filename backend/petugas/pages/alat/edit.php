<?php
// AKTIFKAN ERROR REPORTING
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Pastikan session_start() di awal
session_start();

include '../../partials/header.php'; 
$page = 'alat'; 
include '../../partials/sidebar.php'; 

// Include action untuk mengambil data
include '../../action/alat/show.php';
?>

<style>
    /* ... (style sama seperti create.php) ... */
</style>

<div class="container-fluid page-body-wrapper">
    <div id="main" class="p-4">
        <div class="card">
            <div class="card-header">
                <div class="header-container">
                    <h4 class="fw-bold text-primary page-title">
                        <i class="fas fa-hard-hat"></i> Edit Data Alat Berat
                    </h4>
                    <a href="./index.php" class="btn btn-primary">
                        <i class="fas fa-arrow-left"></i> Kembali
                    </a>
                </div>
            </div>
            
            <div class="card-body">
                <!-- Tampilkan pesan error/success -->
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
                
                <div class="row justify-content-center">
                    <div class="col-md-10">
                        <form action="../../action/alat/update.php?id=<?= $alat->id ?>" method="POST" class="needs-validation" novalidate>
                            <div class="row">
                                <div class="col-md-6 mb-4">
                                    <label for="kode_barang" class="form-label">Kode Barang *</label>
                                    <input type="text" name="kode_barang" class="form-control" id="kode_barang"
                                        placeholder="Contoh: EXC-001" required
                                        value="<?= htmlspecialchars($alat->kode_barang ?? '') ?>">
                                    <div class="invalid-feedback">Harap isi kode barang</div>
                                </div>
                                
                                <div class="col-md-6 mb-4">
                                    <label for="nama_barang" class="form-label">Nama Alat Berat *</label>
                                    <input type="text" name="nama_barang" class="form-control" id="nama_barang"
                                        placeholder="Contoh: Excavator Mini PC75" required
                                        value="<?= htmlspecialchars($alat->nama_barang ?? '') ?>">
                                    <div class="invalid-feedback">Harap isi nama alat berat</div>
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6 mb-4">
                                    <label for="jenis" class="form-label">Jenis *</label>
                                    <select name="jenis" class="form-select" id="jenis" required>
                                        <option value="Alat Berat" <?= ($alat->jenis ?? '') == 'Alat Berat' ? 'selected' : '' ?>>Alat Berat</option>
                                        <option value="Genset" <?= ($alat->jenis ?? '') == 'Genset' ? 'selected' : '' ?>>Genset</option>
                                        <option value="Perkakas" <?= ($alat->jenis ?? '') == 'Perkakas' ? 'selected' : '' ?>>Perkakas</option>
                                        <option value="Truk" <?= ($alat->jenis ?? '') == 'Truk' ? 'selected' : '' ?>>Truk</option>
                                        <option value="Lainnya" <?= ($alat->jenis ?? '') == 'Lainnya' ? 'selected' : '' ?>>Lainnya</option>
                                    </select>
                                    <div class="invalid-feedback">Harap pilih jenis alat</div>
                                </div>
                                
                                <div class="col-md-6 mb-4">
                                    <label for="merk" class="form-label">Merk</label>
                                    <input type="text" name="merk" class="form-control" id="merk"
                                        placeholder="Contoh: Komatsu, Caterpillar"
                                        value="<?= htmlspecialchars($alat->merk ?? '') ?>">
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6 mb-4">
                                    <label for="tahun" class="form-label">Tahun Produksi</label>
                                    <input type="number" name="tahun" class="form-control" id="tahun"
                                        placeholder="Contoh: 2020" min="2000" max="<?php echo date('Y'); ?>"
                                        value="<?= htmlspecialchars($alat->tahun ?? '') ?>">
                                    <div class="form-text">Kosongkan jika tidak diketahui</div>
                                </div>
                                
                                <div class="col-md-6 mb-4">
                                    <label for="jumlah" class="form-label">Jumlah Unit *</label>
                                    <input type="number" name="jumlah" class="form-control" id="jumlah"
                                        placeholder="Contoh: 3" required min="1"
                                        value="<?= htmlspecialchars($alat->jumlah ?? '1') ?>">
                                    <div class="invalid-feedback">Jumlah minimal 1 unit</div>
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6 mb-4">
                                    <label for="status" class="form-label">Status *</label>
                                    <select name="status" class="form-select" id="status" required>
                                        <option value="tersedia" <?= ($alat->status ?? '') == 'tersedia' ? 'selected' : '' ?>>Tersedia</option>
                                        <option value="dipinjam" <?= ($alat->status ?? '') == 'dipinjam' ? 'selected' : '' ?>>Dipinjam</option>
                                        <option value="rusak" <?= ($alat->status ?? '') == 'rusak' ? 'selected' : '' ?>>Rusak</option>
                                        <option value="hilang" <?= ($alat->status ?? '') == 'hilang' ? 'selected' : '' ?>>Hilang</option>
                                    </select>
                                    <div class="invalid-feedback">Harap pilih status</div>
                                </div>
                                
                                <div class="col-md-6 mb-4">
                                    <label for="harga_sewa_perhari" class="form-label">Harga Sewa per Hari *</label>
                                    <div class="input-group">
                                        <span class="input-group-text">Rp</span>
                                        <input type="number" name="harga_sewa_perhari" class="form-control" id="harga_sewa_perhari"
                                            placeholder="Contoh: 1500000" required min="0"
                                            value="<?= htmlspecialchars($alat->harga_sewa_perhari ?? '') ?>">
                                    </div>
                                    <div class="invalid-feedback">Harap isi harga sewa</div>
                                </div>
                            </div>
                            
                            <div class="mb-4">
                                <label for="deskripsi" class="form-label">Deskripsi</label>
                                <textarea name="deskripsi" class="form-control" id="deskripsi" rows="3"
                                    placeholder="Contoh: Excavator mini kapasitas 7.5 ton, kondisi baru..."><?= htmlspecialchars($alat->deskripsi ?? '') ?></textarea>
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

// Auto format kode barang
document.getElementById('kode_barang').addEventListener('input', function(e) {
    this.value = this.value.toUpperCase();
});
</script>