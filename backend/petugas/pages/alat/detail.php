<?php
// AKTIFKAN ERROR REPORTING
error_reporting(E_ALL);
ini_set('display_errors', 1);

include "../../partials/header.php";
$page = 'alat';
include "../../partials/sidebar.php";

// Include action untuk mengambil data
include '../../action/alat/show.php';
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

    .header-container {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 25px;
        padding-bottom: 15px;
        border-bottom: 2px solid #f0f2f5;
    }

    .info-card {
        background: #f8f9ff;
        border-radius: 10px;
        padding: 20px;
        margin-bottom: 15px;
        border-left: 4px solid #4e73df;
    }

    .info-label {
        font-size: 0.9rem;
        color: #6c757d;
        font-weight: 600;
        margin-bottom: 5px;
    }

    .info-value {
        font-size: 1.2rem;
        color: #2e3742;
        font-weight: 700;
    }

    .badge-status {
        padding: 8px 20px;
        border-radius: 20px;
        font-weight: 600;
        font-size: 0.85rem;
    }

    .badge-tersedia {
        background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%);
        color: white;
    }

    .badge-dipinjam {
        background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
        color: white;
    }

    .badge-rusak {
        background: linear-gradient(135deg, #fa709a 0%, #fee140 100%);
        color: white;
    }

    .badge-hilang {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
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
                        <i class="fas fa-hard-hat"></i> Detail Alat Berat
                    </h4>
                    <a href="./index.php" class="btn btn-primary">
                        <i class="fas fa-arrow-left"></i> Kembali
                    </a>
                </div>
            </div>
            
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="info-card">
                            <div class="info-label">Kode Barang</div>
                            <div class="info-value"><?= htmlspecialchars($alat->kode_barang ?? '') ?></div>
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="info-card">
                            <div class="info-label">Nama Alat Berat</div>
                            <div class="info-value"><?= htmlspecialchars($alat->nama_barang ?? '') ?></div>
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="info-card">
                            <div class="info-label">Jenis</div>
                            <div class="info-value"><?= htmlspecialchars($alat->jenis ?? '') ?></div>
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="info-card">
                            <div class="info-label">Merk</div>
                            <div class="info-value"><?= htmlspecialchars($alat->merk ?? '-') ?></div>
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="info-card">
                            <div class="info-label">Tahun Produksi</div>
                            <div class="info-value"><?= htmlspecialchars($alat->tahun ?? '-') ?></div>
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="info-card">
                            <div class="info-label">Jumlah Unit</div>
                            <div class="info-value"><?= htmlspecialchars($alat->jumlah ?? '1') ?> Unit</div>
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="info-card">
                            <div class="info-label">Jumlah Tersedia</div>
                            <div class="info-value"><?= htmlspecialchars($alat->jumlah_tersedia ?? '0') ?> Unit</div>
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="info-card">
                            <div class="info-label">Status</div>
                            <div class="info-value">
                                <?php
                                $status = $alat->status ?? 'tersedia';
                                $badgeClass = '';
                                switch($status) {
                                    case 'tersedia': $badgeClass = 'badge-tersedia'; break;
                                    case 'dipinjam': $badgeClass = 'badge-dipinjam'; break;
                                    case 'rusak': $badgeClass = 'badge-rusak'; break;
                                    case 'hilang': $badgeClass = 'badge-hilang'; break;
                                    default: $badgeClass = 'badge-tersedia';
                                }
                                ?>
                                <span class="badge-status <?= $badgeClass ?>">
                                    <i class="fas fa-<?= 
                                        $status == 'tersedia' ? 'check-circle' : 
                                        ($status == 'dipinjam' ? 'clock' : 
                                        ($status == 'rusak' ? 'tools' : 'exclamation-triangle')) 
                                    ?> me-1"></i>
                                    <?= ucfirst($status) ?>
                                </span>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="info-card">
                            <div class="info-label">Harga Sewa per Hari</div>
                            <div class="info-value">Rp <?= number_format($alat->harga_sewa_perhari ?? 0, 0, ',', '.') ?></div>
                        </div>
                    </div>
                    
                    <div class="col-12">
                        <div class="info-card">
                            <div class="info-label">Deskripsi</div>
                            <div class="info-value" style="font-weight: normal; font-size: 1rem;">
                                <?= nl2br(htmlspecialchars($alat->deskripsi ?? 'Tidak ada deskripsi')) ?>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="d-flex justify-content-between mt-5">
                    <a href="./index.php" class="btn btn-outline-secondary">
                        <i class="fas fa-list"></i> Daftar Alat Berat
                    </a>
                    <div>
                        <a href="./edit.php?id=<?= $alat->id ?>" class="btn btn-warning me-2">
                            <i class="fas fa-edit"></i> Edit
                        </a>
                        <a href="../../action/alat/destroy.php?id=<?= $alat->id ?>" 
                           onclick="return confirm('Apakah Anda yakin ingin menghapus alat <?= addslashes($alat->nama_barang) ?>?')"
                           class="btn btn-danger">
                            <i class="fas fa-trash"></i> Hapus
                        </a>
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