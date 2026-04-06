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

    .btn-warning {
        background-color: #f6c23e !important;
        border-color: #f6c23e !important;
        border-radius: 8px;
        padding: 10px 20px;
        font-weight: 600;
        display: inline-flex;
        align-items: center;
        gap: 8px;
    }

    .btn-danger {
        background-color: #e74a3b !important;
        border-color: #e74a3b !important;
        border-radius: 8px;
        padding: 10px 20px;
        font-weight: 600;
        display: inline-flex;
        align-items: center;
        gap: 8px;
    }

    .header-container {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 25px;
        padding-bottom: 15px;
        border-bottom: 2px solid #f0f2f5;
    }

    .info-card {
        background: #f8f9fc;
        border-radius: 10px;
        padding: 20px;
        margin-bottom: 15px;
        border-left: 4px solid #4e73df;
    }

    .info-label {
        font-size: 0.9rem;
        color: #858796;
        margin-bottom: 5px;
        font-weight: 600;
    }

    .info-value {
        font-size: 1.1rem;
        color: #5a5c69;
        font-weight: 500;
    }

    .badge {
        padding: 8px 15px;
        font-size: 0.85rem;
        font-weight: 600;
        border-radius: 20px;
    }

    .bg-danger { background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%) !important; }
    .bg-primary { background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%) !important; }
    .bg-success { background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%) !important; }

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
        <div class="card-admin">
            <div class="card-header-admin">
                <div class="header-container">
                    <h4 class="fw-bold text-primary page-title">
                        <i class="fas fa-user-circle"></i> Detail Data User
                    </h4>
                    <a href="./index.php" class="btn btn-primary">
                        <i class="fas fa-arrow-left"></i> Kembali
                    </a>
                </div>
            </div>
            
            <div class="card-body-admin">
                <div class="row justify-content-center">
                    <div class="col-md-8">
                        <!-- ID User -->
                        <div class="info-card">
                            <div class="info-label">ID User</div>
                            <div class="info-value">#<?= htmlspecialchars($user->id ?? '') ?></div>
                        </div>
                        
                        <!-- Username -->
                        <div class="info-card">
                            <div class="info-label">Username</div>
                            <div class="info-value"><?= htmlspecialchars($user->username ?? '') ?></div>
                        </div>
                        
                        <!-- Nama Lengkap -->
                        <div class="info-card">
                            <div class="info-label">Nama Lengkap</div>
                            <div class="info-value"><?= htmlspecialchars($user->nama_lengkap ?? 'Tidak tersedia') ?></div>
                        </div>
                        
                        <!-- Email -->
                        <div class="info-card">
                            <div class="info-label">Email</div>
                            <div class="info-value"><?= !empty($user->email) ? htmlspecialchars($user->email) : 'Tidak tersedia' ?></div>
                        </div>
                        
                        <!-- No. Telepon -->
                        <div class="info-card">
                            <div class="info-label">No. Telepon</div>
                            <div class="info-value"><?= !empty($user->no_telp) ? htmlspecialchars($user->no_telp) : 'Tidak tersedia' ?></div>
                        </div>
                        
                        <!-- Alamat -->
                        <?php if (!empty($user->alamat)): ?>
                        <div class="info-card">
                            <div class="info-label">Alamat</div>
                            <div class="info-value"><?= nl2br(htmlspecialchars($user->alamat)) ?></div>
                        </div>
                        <?php endif; ?>
                        
                        <!-- Role -->
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
                        
                        <!-- Tanggal Dibuat -->
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