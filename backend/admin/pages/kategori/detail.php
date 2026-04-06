<?php
// AKTIFKAN ERROR REPORTING
error_reporting(E_ALL);
ini_set('display_errors', 1);

include "../../partials/header.php";
$page = 'kategori'; // Ganti dari 'pengguna' ke 'kategori'
include "../../partials/sidebar.php";

// Include action untuk mengambil data
include '../../action/kategori/show.php';
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

    .info-card {
        background-color: #f8f9fa;
        border-radius: 8px;
        padding: 20px;
        margin-bottom: 20px;
        border-left: 4px solid #4e73df;
    }

    .info-label {
        font-size: 0.9rem;
        color: #6c757d;
        font-weight: 600;
        margin-bottom: 5px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .info-value {
        font-size: 1.2rem;
        color: #000000;
        font-weight: 500;
    }

    .info-value .badge {
        font-size: 0.9rem;
        padding: 8px 15px;
        border-radius: 20px;
    }

    .deskripsi-box {
        background-color: #f8f9fa;
        border-radius: 8px;
        padding: 20px;
        border: 1px solid #e3e6f0;
        margin-top: 10px;
        color: #5a5c69;
        line-height: 1.6;
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
        <div class="card-admin">
            <div class="card-header-admin">
                <div class="header-container">
                    <h4 class="fw-bold text-primary page-title">
                        <i class="fas fa-folder"></i> Detail Kategori
                    </h4>
                    <a href="./index.php" class="btn btn-primary">
                        <i class="fas fa-arrow-left"></i> Kembali
                    </a>
                </div>
            </div>
            
            <div class="card-body-admin">
                <div class="row justify-content-center">
                    <div class="col-md-8">
                        <div class="info-card">
                            <div class="info-label">ID Kategori</div>
                            <div class="info-value">#<?= htmlspecialchars($kategori->id ?? '') ?></div>
                        </div>
                        
                        <div class="info-card">
                            <div class="info-label">Nama Kategori</div>
                            <div class="info-value"><?= htmlspecialchars($kategori->nama_kategori ?? '') ?></div>
                        </div>
                        
                        <div class="info-card">
                            <div class="info-label">Deskripsi</div>
                            <?php if (!empty($kategori->deskripsi)): ?>
                                <div class="deskripsi-box">
                                    <?= nl2br(htmlspecialchars($kategori->deskripsi)) ?>
                                </div>
                            <?php else: ?>
                                <div class="info-value text-muted">Tidak ada deskripsi</div>
                            <?php endif; ?>
                        </div>
                        
                        <div class="info-card">
                            <div class="info-label">Tanggal Dibuat</div>
                            <div class="info-value">
                                <?= isset($kategori->created_at) ? date('d-m-Y H:i', strtotime($kategori->created_at)) : 'Tidak tersedia' ?>
                            </div>
                        </div>
                        
                        <div class="d-flex justify-content-between mt-5">
                            <a href="./index.php" class="btn btn-outline-secondary">
                                <i class="fas fa-list"></i> Daftar Kategori
                            </a>
                            <div>
                                <a href="./edit.php?id=<?= $kategori->id ?>" class="btn btn-warning me-2">
                                    <i class="fas fa-edit"></i> Edit
                                </a>
                                <a href="../../action/kategori/destroy.php?id=<?= $kategori->id ?>" 
                                   onclick="return confirm('Apakah Anda yakin ingin menghapus kategori <?= addslashes($kategori->nama_kategori) ?>?')"
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