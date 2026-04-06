<?php
// AKTIFKAN ERROR REPORTING
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Pastikan session_start() di awal
session_start();

include '../../partials/header.php';
$page = 'kategori'; // Ganti dari 'pengguna' ke 'kategori'
include '../../partials/sidebar.php';
?>

<div class="content-wrapper">
    <div class="row justify-content-center">
        <div class="col-lg-10 col-xl-8">
            <div class="card-admin border-0 shadow-sm mt-4">
            <div class="card-header-admin">
                <div class="header-container">
                    <h4 class="fw-bold text-primary page-title">
                        <i class="fas fa-plus-circle"></i> Tambah Kategori
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
                    <div class="col-md-8">
                        <form action="../../action/kategori/store.php" method="POST" class="needs-validation" novalidate>
                            <div class="mb-4">
                                <label for="nama_kategori" class="form-label">Nama Kategori *</label>
                                <input type="text" name="nama_kategori" class="form-control" id="nama_kategori"
                                    placeholder="Masukkan nama kategori.." required
                                    value="<?php echo isset($_POST['nama_kategori']) ? htmlspecialchars($_POST['nama_kategori']) : ''; ?>">
                                <div class="invalid-feedback">Harap isi nama kategori</div>
                                <div class="form-text">Nama kategori harus unik dan deskriptif</div>
                            </div>
                            
                            <div class="mb-4">
                                <label for="deskripsi" class="form-label">Deskripsi</label>
                                <textarea name="deskripsi" class="form-control" id="deskripsi" 
                                    rows="4" placeholder="Masukkan deskripsi kategori (opsional).."><?php echo isset($_POST['deskripsi']) ? htmlspecialchars($_POST['deskripsi']) : ''; ?></textarea>
                                <div class="form-text">Deskripsi tentang kategori ini</div>
                            </div>
                            
                            <div class="d-flex justify-content-between mt-5">
                                <a href="./index.php" class="btn btn-outline-secondary">
                                    <i class="fas fa-times"></i> Batal
                                </a>
                                <button type="submit" class="btn btn-success" name="tombol">
                                    <i class="fas fa-plus"></i> Tambah Kategori
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
</script>