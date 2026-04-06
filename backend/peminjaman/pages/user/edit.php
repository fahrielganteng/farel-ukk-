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
if ($user->id != $_SESSION['user_id']) {
    header("Location: ./index.php");
    exit();
}
?>

<div class="content-wrapper">
    <div class="page-title">
        <i class="fas fa-user-edit"></i>
        <span>Pengaturan Profil</span>
    </div>

    <!-- Alert Messages -->
    <?php if (isset($_SESSION['error'])): ?>
        <div class="alert status-ditolak mb-4 border-0 shadow-sm d-flex align-items-center">
            <i class="fas fa-exclamation-circle me-3 fa-lg"></i>
            <div><?= $_SESSION['error']; unset($_SESSION['error']); ?></div>
        </div>
    <?php endif; ?>

    <div class="row justify-content-center">
        <div class="col-xl-8">
            <div class="card-admin shadow-sm">
                <div class="card-header-admin bg-white d-flex justify-content-between align-items-center">
                    <span><i class="fas fa-id-card me-2 text-indigo-600"></i> Edit Detail Profil Anda</span>
                    <a href="./index.php" class="btn btn-sm btn-outline-secondary">
                        <i class="fas fa-arrow-left me-1"></i> Kembali
                    </a>
                </div>
                <div class="card-body p-4 p-md-5">
                    <form action="../../action/user/update.php?id=<?= $user->id ?>" method="POST" class="needs-validation" novalidate>
                        <!-- Role (Hidden for Security) -->
                        <input type="hidden" name="role" value="<?= htmlspecialchars($user->role) ?>">
                        
                        <div class="row g-4">
                            <div class="col-md-12">
                                <label for="username" class="form-label-admin">Username <span class="text-rose-500">*</span></label>
                                <div class="input-group-admin">
                                    <span class="input-group-text-admin"><i class="fas fa-at"></i></span>
                                    <input type="text" name="username" class="form-control-admin" id="username" 
                                           value="<?= htmlspecialchars($user->username ?? '') ?>" required>
                                </div>
                                <div class="small text-slate-400 mt-2 italic">Username digunakan untuk login ke aplikasi.</div>
                            </div>
                            
                            <div class="col-md-12">
                                <label for="password" class="form-label-admin">Password Baru</label>
                                <div class="input-group-admin">
                                    <span class="input-group-text-admin"><i class="fas fa-lock"></i></span>
                                    <input type="password" name="password" class="form-control-admin" id="password" 
                                           placeholder="Kosongkan jika tidak ingin mengubah password">
                                </div>
                                <div class="small text-amber-600 mt-2 fw-600">
                                    <i class="fas fa-info-circle me-1"></i> Kosongkan jika password tidak ingin diubah.
                                </div>
                            </div>

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
            
            <div class="mt-4 p-4 bg-indigo-50 rounded-4 border border-indigo-100">
                <div class="d-flex gap-3">
                    <div class="text-indigo-600"><i class="fas fa-shield-check fa-2x"></i></div>
                    <div>
                        <h6 class="fw-800 text-slate-800 mb-1">Keamanan Akun</h6>
                        <p class="small text-slate-500 mb-0">Role Anda saat ini adalah <strong><?= strtoupper($user->role) ?></strong>. Perubahan role hanya dapat dilakukan oleh Administrator sistem melalui panel Admin Utama.</p>
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
// Validasi form Bootstrap sederhana
(function () {
    'use strict'
    var forms = document.querySelectorAll('.needs-validation')
    Array.prototype.slice.call(forms).forEach(function (form) {
        form.addEventListener('submit', function (event) {
            if (!form.checkValidity()) {
                event.preventDefault()
                event.stopPropagation()
            }
            form.classList.add('was-validated')
        }, false)
    })
})()
</script>

<?php include '../../partials/footer.php'; ?>