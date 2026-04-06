<?php
// AKTIFKAN ERROR REPORTING
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Pastikan session_start() di awal
session_start();

include '../../partials/header.php'; 
$page = 'pengguna'; 
include '../../partials/sidebar.php'; 

// Include action untuk mengambil data
include '../../action/user/show.php';
?>

<div class="content-wrapper">
    <div class="row justify-content-center">
        <div class="col-lg-10 col-xl-8">
            <div class="card-admin border-0 shadow-sm mt-4">
            <div class="card-header-admin">
                <div class="header-container">
                    <h4 class="fw-bold text-primary page-title">
                        <i class="fas fa-user-edit"></i> Edit Data User
                    </h4>
                    <a href="./index.php" class="btn btn-primary">
                        <i class="fas fa-arrow-left"></i> Kembali
                    </a>
                </div>
            </div>
            
            <div class="card-body-admin">
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
                
                <div class="row justify-content-center">
                    <div class="col-md-8">
                        <form action="../../action/user/update.php?id=<?= $user->id ?>" method="POST" class="needs-validation" novalidate>
                            <!-- ID User (readonly) -->
                            <div class="mb-4">
                                <label for="id" class="form-label">ID User</label>
                                <input type="text" class="form-control" id="id_display"
                                    value="#<?= htmlspecialchars($user->id ?? '') ?>" readonly disabled
                                    style="background-color: #f8f9fa; color: #6c757d;">
                                <input type="hidden" name="id" value="<?= $user->id ?>">
                                <div class="form-text">ID User tidak dapat diubah</div>
                            </div>
                            
                            <!-- Username -->
                            <div class="mb-4">
                                <label for="username" class="form-label">Username *</label>
                                <input type="text" name="username" class="form-control" id="username"
                                    placeholder="Masukkan username.." required 
                                    value="<?= htmlspecialchars($user->username ?? '') ?>">
                                <div class="invalid-feedback">Harap isi username</div>
                            </div>
                            
                            <!-- Nama Lengkap -->
                            <div class="mb-4">
                                <label for="nama_lengkap" class="form-label">Nama Lengkap *</label>
                                <input type="text" name="nama_lengkap" class="form-control" id="nama_lengkap"
                                    placeholder="Masukkan nama lengkap.." required
                                    value="<?= htmlspecialchars($user->nama_lengkap ?? '') ?>">
                                <div class="invalid-feedback">Harap isi nama lengkap</div>
                            </div>
                            
                            <!-- Email -->
                            <div class="mb-4">
                                <label for="email" class="form-label">Email</label>
                                <input type="email" name="email" class="form-control" id="email"
                                    placeholder="Masukkan email.."
                                    value="<?= htmlspecialchars($user->email ?? '') ?>">
                                <div class="form-text">Contoh: nama@contoh.com</div>
                            </div>
                            
                            <!-- No Telepon -->
                            <div class="mb-4">
                                <label for="no_telp" class="form-label">No. Telepon</label>
                                <input type="tel" name="no_telp" class="form-control" id="no_telp"
                                    placeholder="Masukkan nomor telepon.."
                                    value="<?= htmlspecialchars($user->no_telp ?? '') ?>">
                                <div class="form-text">Contoh: 081234567890</div>
                            </div>
                            
                            <!-- Password -->
                            <div class="mb-4">
                                <label for="password" class="form-label">Password</label>
                                <input type="password" name="password" class="form-control" id="password"
                                    placeholder="Masukkan password baru..">
                                <div class="form-text">Kosongkan jika tidak ingin mengubah password</div>
                            </div>
                            
                            <!-- Alamat -->
                            <div class="mb-4">
                                <label for="alamat" class="form-label">Alamat</label>
                                <textarea name="alamat" class="form-control form-textarea" id="alamat"
                                    placeholder="Masukkan alamat.." rows="3"><?= htmlspecialchars($user->alamat ?? '') ?></textarea>
                            </div>
                            
                            <!-- Role -->
                            <div class="mb-4">
                                <label for="role" class="form-label">Role *</label>
                                <select name="role" class="form-select" id="role" required>
                                    <option value="">Pilih Role</option>
                                    <option value="admin" <?php echo ($user->role == 'admin') ? 'selected' : ''; ?>>Admin</option>
                                    <option value="petugas" <?php echo ($user->role == 'petugas') ? 'selected' : ''; ?>>Petugas</option>
                                    <option value="peminjam" <?php echo ($user->role == 'peminjam') ? 'selected' : ''; ?>>Peminjam</option>
                                </select>
                                <div class="invalid-feedback">Harap pilih role user</div>
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

// Toggle password visibility
document.addEventListener('DOMContentLoaded', function() {
    const passwordInput = document.getElementById('password');
    if (passwordInput) {
        const toggleButton = document.createElement('button');
        toggleButton.type = 'button';
        toggleButton.innerHTML = '<i class="fas fa-eye"></i>';
        toggleButton.className = 'btn btn-outline-secondary position-absolute end-0 top-50 translate-middle-y';
        toggleButton.style.right = '10px';
        toggleButton.style.zIndex = '5';
        toggleButton.style.background = 'transparent';
        toggleButton.style.border = 'none';
        
        const inputGroup = passwordInput.parentElement;
        inputGroup.style.position = 'relative';
        inputGroup.appendChild(toggleButton);
        
        toggleButton.addEventListener('click', function() {
            const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
            passwordInput.setAttribute('type', type);
            this.innerHTML = type === 'password' ? '<i class="fas fa-eye"></i>' : '<i class="fas fa-eye-slash"></i>';
        });
    }
});
</script>