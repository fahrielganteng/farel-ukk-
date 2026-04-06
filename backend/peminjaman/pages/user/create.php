<?php
// AKTIFKAN ERROR REPORTING
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Pastikan session_start() di awal
session_start();

include '../../partials/header.php';
$page = 'pengguna';
include '../../partials/sidebar.php';
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

    .form-control, .form-select {
        border: 1px solid #d1d3e2 !important;
        border-radius: 0.35rem !important;
        padding: 0.75rem 1rem !important;
        font-size: 0.9rem !important;
        background-color: #ffffff !important;
        color: #000000 !important;
    }

    .form-control:focus, .form-select:focus {
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
                        <i class="fas fa-user-plus"></i> Tambah Data User
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
                    <div class="col-md-8">
                        <form action="../../action/user/store.php" method="POST" class="needs-validation" novalidate>
                            <div class="mb-4">
                                <label for="username" class="form-label">Username *</label>
                                <input type="text" name="username" class="form-control" id="username"
                                    placeholder="Masukkan username.." required
                                    value="<?php echo isset($_POST['username']) ? htmlspecialchars($_POST['username']) : ''; ?>">
                                <div class="invalid-feedback">Harap isi username</div>
                                <div class="form-text">Username harus unik dan belum terdaftar</div>
                            </div>
                            
                            <div class="row mb-4">
                                <div class="col-md-6">
                                    <label for="password" class="form-label">Password *</label>
                                    <input type="password" name="password" class="form-control" id="password"
                                        placeholder="Masukkan password.." required
                                        minlength="6">
                                    <div class="invalid-feedback">Password minimal 6 karakter</div>
                                </div>
                                <div class="col-md-6">
                                    <label for="confirm_password" class="form-label">Konfirmasi Password *</label>
                                    <input type="password" name="confirm_password" class="form-control" id="confirm_password"
                                        placeholder="Konfirmasi password.." required>
                                    <div class="invalid-feedback">Konfirmasi password harus sama</div>
                                </div>
                            </div>
                            
                            <div class="mb-4">
                                <label for="role" class="form-label">Role *</label>
                                <select name="role" class="form-select" id="role" required>
                                    <option value="">Pilih Role</option>
                                    <option value="admin" <?php echo (isset($_POST['role']) && $_POST['role'] == 'admin') ? 'selected' : ''; ?>>Admin</option>
                                    <option value="petugas" <?php echo (isset($_POST['role']) && $_POST['role'] == 'petugas') ? 'selected' : ''; ?>>Petugas</option>
                                    <option value="peminjam" <?php echo (isset($_POST['role']) && $_POST['role'] == 'peminjam') ? 'selected' : ''; ?>>Peminjam</option>
                                </select>
                                <div class="invalid-feedback">Harap pilih role</div>
                            </div>
                            
                            <div class="d-flex justify-content-between mt-5">
                                <a href="./index.php" class="btn btn-outline-secondary">
                                    <i class="fas fa-times"></i> Batal
                                </a>
                                <button type="submit" class="btn btn-success" name="tombol">
                                    <i class="fas fa-plus"></i> Tambah User
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
            // Validasi password confirmation
            var password = document.getElementById('password');
            var confirmPassword = document.getElementById('confirm_password');
            
            if (password.value !== confirmPassword.value) {
                confirmPassword.setCustomValidity('Password tidak sama');
                confirmPassword.reportValidity();
                event.preventDefault();
                event.stopPropagation();
            } else {
                confirmPassword.setCustomValidity('');
            }
            
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
    const passwordInputs = document.querySelectorAll('input[type="password"]');
    passwordInputs.forEach(function(input) {
        const toggleButton = document.createElement('button');
        toggleButton.type = 'button';
        toggleButton.innerHTML = '<i class="fas fa-eye"></i>';
        toggleButton.className = 'btn btn-outline-secondary position-absolute end-0 top-50 translate-middle-y';
        toggleButton.style.right = '10px';
        toggleButton.style.zIndex = '5';
        toggleButton.style.background = 'transparent';
        toggleButton.style.border = 'none';
        
        const inputGroup = input.parentElement;
        inputGroup.style.position = 'relative';
        inputGroup.appendChild(toggleButton);
        
        toggleButton.addEventListener('click', function() {
            const type = input.getAttribute('type') === 'password' ? 'text' : 'password';
            input.setAttribute('type', type);
            this.innerHTML = type === 'password' ? '<i class="fas fa-eye"></i>' : '<i class="fas fa-eye-slash"></i>';
        });
    });
});
</script>