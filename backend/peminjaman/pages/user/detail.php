<?php
// AKTIFKAN ERROR REPORTING
error_reporting(E_ALL);
ini_set('display_errors', 1);

include "../../partials/header.php";
$page = 'pengguna';
include "../../partials/sidebar.php";

// Include action untuk mengambil data
include '../../action/user/show.php';
?>

<style>
    /* ... (style tetap sama) ... */
</style>

<div class="container-fluid page-body-wrapper">
    <div id="main" class="p-4">
        <div class="card">
            <div class="card-header">
                <div class="header-container">
                    <h4 class="fw-bold text-primary page-title">
                        <i class="fas fa-user-circle"></i> Detail Data User
                    </h4>
                    <a href="./index.php" class="btn btn-primary">
                        <i class="fas fa-arrow-left"></i> Kembali
                    </a>
                </div>
            </div>
            
            <div class="card-body">
                <div class="row justify-content-center">
                    <div class="col-md-8">
                        <div class="info-card">
                            <div class="info-label">ID User</div>
                            <div class="info-value">#<?= htmlspecialchars($user->id ?? '') ?></div>
                        </div>
                        
                        <div class="info-card">
                            <div class="info-label">Username</div>
                            <div class="info-value"><?= htmlspecialchars($user->username ?? '') ?></div>
                        </div>
                        
                        <div class="info-card">
                            <div class="info-label">Role</div>
                            <div class="info-value">
                                <span class="badge 
                                    <?= $user->role == 'admin' ? 'bg-danger' : 
                                       ($user->role == 'petugas' ? 'bg-primary' : 'bg-success') ?>">
                                    <i class="fas fa-<?= 
                                        $user->role == 'admin' ? 'user-shield' : 
                                        ($user->role == 'petugas' ? 'user-tie' : 'user') 
                                    ?> me-1"></i>
                                    <?= ucfirst($user->role) ?>
                                </span>
                            </div>
                        </div>
                        
                        <div class="info-card">
                            <div class="info-label">Tanggal Dibuat</div>
                            <div class="info-value">
                                <?= isset($user->created_at) ? date('d-m-Y H:i', strtotime($user->created_at)) : 'Tidak tersedia' ?>
                            </div>
                        </div>
                        
                        <div class="d-flex justify-content-between mt-5">
                            <a href="./index.php" class="btn btn-outline-secondary">
                                <i class="fas fa-list"></i> Daftar User
                            </a>
                            <div>
                                <a href="./edit.php?id=<?= $user->id ?>" class="btn btn-warning me-2">
                                    <i class="fas fa-edit"></i> Edit
                                </a>
                                <a href="../../action/user/destroy.php?id=<?= $user->id ?>" 
                                   onclick="return confirm('Apakah Anda yakin ingin menghapus user <?= addslashes($user->username) ?>?')"
                                   class="btn btn-danger">
                                    <i class="fas fa-trash"></i> Hapus
                                </a>
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

<!-- Font Awesome -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">