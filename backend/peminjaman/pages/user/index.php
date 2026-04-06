<?php
// AKTIFKAN ERROR REPORTING
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Pastikan session_start() di awal
session_start();

// Include koneksi database
include '../../app.php';

// Include koneksi database
include '../../app.php';

// Ambil data user yang sedang login
$user_id = $_SESSION['user_id'];
$qCurrentUser = "SELECT * FROM users WHERE id = '$user_id'";
$resultUser = mysqli_query($connect, $qCurrentUser);

if (!$resultUser) {
    die("Query error: " . mysqli_error($connect));
}

$userData = mysqli_fetch_assoc($resultUser);
?>

<?php 
include '../../partials/header.php'; 
$page = 'user'; 
include '../../partials/sidebar.php'; 
?>

<div class="content-wrapper">
    <div class="page-title">
        <i class="fas fa-user-circle"></i>
        <span>Profil Saya</span>
    </div>

    <!-- Alert Messages -->
    <?php if (isset($_SESSION['success'])): ?>
        <div class="alert status-selesai mb-4 border-0 shadow-sm d-flex align-items-center">
            <i class="fas fa-check-circle me-3 fa-lg"></i>
            <div><?= $_SESSION['success']; unset($_SESSION['success']); ?></div>
        </div>
    <?php endif; ?>

    <div class="row g-4">
        <!-- Profile Info Card -->
        <div class="col-xl-4">
            <div class="card-admin shadow-sm text-center p-5">
                <div class="mb-4">
                    <div class="d-inline-flex p-1 bg-indigo-50 rounded-circle border border-indigo-100 shadow-sm mb-3">
                        <div class="p-4 bg-indigo-600 rounded-circle text-white shadow" style="background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%) !important;">
                            <i class="fas fa-user fa-4x"></i>
                        </div>
                    </div>
                    <h3 class="fw-900 text-slate-800 mb-1"><?= htmlspecialchars($userData['username']) ?></h3>
                    <div class="badge bg-indigo-100 text-indigo-600 fw-800 px-3 py-2 rounded-pill mb-4">
                        <?= strtoupper($userData['role']) ?>
                    </div>
                </div>
                
                <div class="d-flex flex-column gap-3">
                    <a href="./edit.php?id=<?= $userData['id'] ?>" class="btn btn-indigo py-3 rounded-4 shadow-sm fw-800">
                        <i class="fas fa-edit me-2"></i> Edit Profil
                    </a>
                    <a href="../../../action/auth/logout.php" class="btn btn-outline-danger py-3 rounded-4 fw-800 border-2" onclick="return confirm('Apakah Anda yakin ingin keluar?')">
                        <i class="fas fa-sign-out-alt me-2"></i> Keluar Sesi
                    </a>
                </div>
            </div>
        </div>

        <!-- Account Details Card -->
        <div class="col-xl-8">
            <div class="card-admin shadow-sm h-100">
                <div class="card-header-admin bg-white">
                    <span><i class="fas fa-id-card me-2 text-indigo-600"></i> Detail Akun Pengguna</span>
                </div>
                <div class="card-body p-4 p-md-5">
                    <div class="row g-4 lg-g-5">
                        <div class="col-md-6 border-bottom border-slate-100 pb-3">
                            <label class="small fw-700 text-slate-400 uppercase tracking-wider d-block mb-1">Username</label>
                            <div class="h5 fw-800 text-slate-800 mb-0"><?= htmlspecialchars($userData['username']) ?></div>
                        </div>
                        <div class="col-md-6 border-bottom border-slate-100 pb-3">
                            <label class="small fw-700 text-slate-400 uppercase tracking-wider d-block mb-1">Status Akun</label>
                            <div class="d-flex align-items-center gap-2">
                                <span class="p-1 bg-emerald-500 rounded-circle"></span>
                                <div class="h5 fw-800 text-emerald-600 mb-0">Aktif</div>
                            </div>
                        </div>
                        <div class="col-md-6 border-bottom border-slate-100 pb-3">
                            <label class="small fw-700 text-slate-400 uppercase tracking-wider d-block mb-1">Member Sejak</label>
                            <div class="h5 fw-800 text-slate-800 mb-0"><?= date('d F Y', strtotime($userData['created_at'])) ?></div>
                        </div>
                        <div class="col-md-6 border-bottom border-slate-100 pb-3">
                            <label class="small fw-700 text-slate-400 uppercase tracking-wider d-block mb-1">Role Utama</label>
                            <div class="h5 fw-800 text-slate-800 mb-0"><?= ucfirst($userData['role']) ?></div>
                        </div>
                        <div class="col-12 mt-4">
                            <div class="p-4 bg-slate-50 rounded-4 border border-slate-200">
                                <div class="d-flex gap-3">
                                    <div class="text-indigo-600"><i class="fas fa-shield-alt fa-2x"></i></div>
                                    <div>
                                        <h6 class="fw-800 text-slate-800 mb-1">Privasi & Keamanan</h6>
                                        <p class="small text-slate-500 mb-0">Password Anda dienkripsi dan aman dalam sistem kami. Anda dapat memperbarui password melalui menu edit profil.</p>
                                    </div>
                                </div>
                            </div>
                        </div>
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
<?php include '../../partials/footer.php'; ?>