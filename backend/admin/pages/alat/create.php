<?php
// AKTIFKAN ERROR REPORTING
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Pastikan session_start() di awal
session_start();

include '../../partials/header.php';
$page = 'alat';
include '../../partials/sidebar.php';
?>

<div class="content-wrapper">
    <div class="row justify-content-center">
        <div class="col-lg-10 col-xl-8">
            <div class="card-admin border-0 shadow-sm mt-4">
            <div class="card-header-admin">
                <div class="header-container">
                    <h4 class="fw-bold text-primary page-title">
                        <i class="fas fa-hard-hat"></i> Tambah Data Alat Berat
                    </h4>
                    <a href="./index.php" class="btn btn-primary">
                        <i class="fas fa-arrow-left"></i> Kembali
                    </a>
                </div>
            </div>
            
            <div class="card-body-admin">
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
                        <form action="../../action/alat/store.php" method="POST" enctype="multipart/form-data" class="needs-validation" novalidate>
                            <div class="row">
                                <div class="col-md-6 mb-4">
                                    <label for="kode_barang" class="form-label">Kode Barang *</label>
                                    <input type="text" name="kode_barang" class="form-control" id="kode_barang"
                                        placeholder="Contoh: EXC-001" required
                                        value="<?php echo isset($_POST['kode_barang']) ? htmlspecialchars($_POST['kode_barang']) : ''; ?>">
                                    <div class="invalid-feedback">Harap isi kode barang</div>
                                    <div class="form-text">Kode harus unik (contoh: EXC-001, BLD-001)</div>
                                </div>
                                
                                <div class="col-md-6 mb-4">
                                    <label for="nama_barang" class="form-label">Nama Alat Berat *</label>
                                    <input type="text" name="nama_barang" class="form-control" id="nama_barang"
                                        placeholder="Contoh: Excavator Mini PC75" required
                                        value="<?php echo isset($_POST['nama_barang']) ? htmlspecialchars($_POST['nama_barang']) : ''; ?>">
                                    <div class="invalid-feedback">Harap isi nama alat berat</div>
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6 mb-4">
                                    <label for="jenis" class="form-label">Jenis *</label>
                                    <select name="jenis" class="form-select" id="jenis" required>
                                        <option value="">Pilih Jenis</option>
                                        <option value="Alat Berat" <?php echo (isset($_POST['jenis']) && $_POST['jenis'] == 'Alat Berat') ? 'selected' : ''; ?>>Alat Berat</option>
                                        <option value="Genset" <?php echo (isset($_POST['jenis']) && $_POST['jenis'] == 'Genset') ? 'selected' : ''; ?>>Genset</option>
                                        <option value="Perkakas" <?php echo (isset($_POST['jenis']) && $_POST['jenis'] == 'Perkakas') ? 'selected' : ''; ?>>Perkakas</option>
                                        <option value="Truk" <?php echo (isset($_POST['jenis']) && $_POST['jenis'] == 'Truk') ? 'selected' : ''; ?>>Truk</option>
                                        <option value="Lainnya" <?php echo (isset($_POST['jenis']) && $_POST['jenis'] == 'Lainnya') ? 'selected' : ''; ?>>Lainnya</option>
                                    </select>
                                    <div class="invalid-feedback">Harap pilih jenis alat</div>
                                </div>
                                
                                <div class="col-md-6 mb-4">
                                    <label for="merk" class="form-label">Merk</label>
                                    <input type="text" name="merk" class="form-control" id="merk"
                                        placeholder="Contoh: Komatsu, Caterpillar"
                                        value="<?php echo isset($_POST['merk']) ? htmlspecialchars($_POST['merk']) : ''; ?>">
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6 mb-4">
                                    <label for="tahun" class="form-label">Tahun Produksi</label>
                                    <input type="number" name="tahun" class="form-control" id="tahun"
                                        placeholder="Contoh: 2020" min="2000" max="<?php echo date('Y'); ?>"
                                        value="<?php echo isset($_POST['tahun']) ? htmlspecialchars($_POST['tahun']) : ''; ?>">
                                    <div class="form-text">Kosongkan jika tidak diketahui</div>
                                </div>
                                
                                <div class="col-md-6 mb-4">
                                    <label for="jumlah" class="form-label">Jumlah Unit *</label>
                                    <input type="number" name="jumlah" class="form-control" id="jumlah"
                                        placeholder="Contoh: 3" required min="1"
                                        value="<?php echo isset($_POST['jumlah']) ? htmlspecialchars($_POST['jumlah']) : '1'; ?>">
                                    <div class="invalid-feedback">Jumlah minimal 1 unit</div>
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6 mb-4">
                                    <label for="status" class="form-label">Status *</label>
                                    <select name="status" class="form-select" id="status" required>
                                        <option value="tersedia" <?php echo (isset($_POST['status']) && $_POST['status'] == 'tersedia') ? 'selected' : 'selected'; ?>>Tersedia</option>
                                        <option value="dipinjam" <?php echo (isset($_POST['status']) && $_POST['status'] == 'dipinjam') ? 'selected' : ''; ?>>Dipinjam</option>
                                        <option value="rusak" <?php echo (isset($_POST['status']) && $_POST['status'] == 'rusak') ? 'selected' : ''; ?>>Rusak</option>
                                        <option value="hilang" <?php echo (isset($_POST['status']) && $_POST['status'] == 'hilang') ? 'selected' : ''; ?>>Hilang</option>
                                    </select>
                                    <div class="invalid-feedback">Harap pilih status</div>
                                </div>
                                
                                <div class="col-md-6 mb-4">
                                    <label for="harga_sewa_perhari" class="form-label">Harga Sewa per Hari *</label>
                                    <div class="input-group">
                                        <span class="input-group-text">Rp</span>
                                        <input type="number" name="harga_sewa_perhari" class="form-control" id="harga_sewa_perhari"
                                            placeholder="Contoh: 1500000" required min="0"
                                            value="<?php echo isset($_POST['harga_sewa_perhari']) ? htmlspecialchars($_POST['harga_sewa_perhari']) : ''; ?>">
                                    </div>
                                    <div class="invalid-feedback">Harap isi harga sewa</div>
                                </div>
                            </div>
                            
                            <div class="mb-4">
                                <label for="deskripsi" class="form-label">Deskripsi</label>
                                <textarea name="deskripsi" class="form-control" id="deskripsi" rows="3"
                                    placeholder="Contoh: Excavator mini kapasitas 7.5 ton, kondisi baru..."><?php echo isset($_POST['deskripsi']) ? htmlspecialchars($_POST['deskripsi']) : ''; ?></textarea>
                                <div class="form-text">Deskripsi singkat tentang alat (opsional)</div>
                            </div>

                            <!-- Image Upload -->
                            <div class="mb-4">
                                <label class="form-label fw-bold">Foto Alat Berat (Opsional)</label>
                                <div class="border rounded-3 p-4 text-center" id="uploadZone" style="border-style:dashed!important;border-color:#cbd5e1;cursor:pointer;" onclick="document.getElementById('gambar').click()">
                                    <img id="imagePreview" src="" alt="" style="max-height:180px;display:none;border-radius:10px;margin-bottom:0.75rem;">
                                    <div id="uploadPlaceholder">
                                        <i class="fas fa-cloud-upload-alt fa-3x mb-2" style="color:#94a3b8;"></i>
                                        <p class="fw-600 mb-1" style="color:#64748b;">Klik untuk upload foto alat</p>
                                        <p class="small" style="color:#94a3b8;">PNG, JPG, JPEG, WEBP (Maks. 5MB)</p>
                                    </div>
                                    <input type="file" name="gambar" id="gambar" class="d-none" accept="image/*">
                                </div>
                            </div>
                            
                            <div class="d-flex justify-content-between mt-5">
                                <a href="./index.php" class="btn btn-outline-secondary">
                                    <i class="fas fa-times"></i> Batal
                                </a>
                                <button type="submit" class="btn btn-success" name="tombol">
                                    <i class="fas fa-plus"></i> Tambah Alat Berat
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
                    </div>
        </div>
    </div>
</div>
<?php include '../../partials/footer.php'; ?>

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

// Auto calculate total harga
document.getElementById('jumlah').addEventListener('input', function() {
    const jumlah = parseInt(this.value) || 1;
    const harga = parseFloat(document.getElementById('harga_sewa_perhari').value) || 0;
});

// Image preview
document.getElementById('gambar').addEventListener('change', function() {
    if (this.files && this.files[0]) {
        var reader = new FileReader();
        reader.onload = function(e) {
            document.getElementById('imagePreview').src = e.target.result;
            document.getElementById('imagePreview').style.display = 'block';
            document.getElementById('uploadPlaceholder').style.display = 'none';
        };
        reader.readAsDataURL(this.files[0]);
    }
});
</script>