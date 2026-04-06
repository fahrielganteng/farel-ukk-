<?php
// AKTIFKAN ERROR REPORTING
error_reporting(E_ALL);
ini_set('display_errors', 1);

include "../../partials/header.php";
$page = 'pengembalian';
include "../../partials/sidebar.php";

// Include action untuk mengambil data
include '../../action/pengembalian/show.php';

// Hitung keterlambatan jika ada data pengembalian
$telatHari = 0;
$isTerlambat = false;

if (isset($pengembalian->tgl_kembali) && isset($pengembalian->tgl_rencana_kembali)) {
    try {
        $tglKembali = new DateTime($pengembalian->tgl_kembali);
        $tglRencana = new DateTime($pengembalian->tgl_rencana_kembali);
        
        if ($tglKembali > $tglRencana) {
            $interval = $tglRencana->diff($tglKembali);
            $telatHari = $interval->days;
            $isTerlambat = true;
        }
    } catch (Exception $e) {
        // Tangani error parsing tanggal
        error_log('Error parsing tanggal: ' . $e->getMessage());
        $telatHari = 0;
    }
}
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

    .btn-warning {
        background-color: #f6c23e !important;
        border-color: #f6c23e !important;
        color: #000 !important;
    }

    .btn-danger {
        background-color: #e74a3b !important;
        border-color: #e74a3b !important;
    }

    .btn-primary:hover, .btn-success:hover, .btn-warning:hover, .btn-danger:hover {
        transform: translateY(-2px);
        box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
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
        background-color: #f8f9fc;
        border-radius: 10px;
        padding: 20px;
        margin-bottom: 15px;
        border-left: 4px solid #4e73df;
        transition: all 0.3s ease;
    }

    .info-card:hover {
        transform: translateX(5px);
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
    }

    .info-label {
        font-size: 0.9rem;
        color: #858796;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-bottom: 8px;
    }

    .info-value {
        font-size: 1.1rem;
        color: #5a5c69;
        font-weight: 600;
    }

    .badge {
        padding: 8px 20px;
        border-radius: 20px;
        font-weight: 600;
        font-size: 0.9rem;
    }

    .bg-baik {
        background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%) !important;
        color: white !important;
    }

    .bg-rusak-ringan {
        background: linear-gradient(135deg, #fad961 0%, #f76b1c 100%) !important;
        color: white !important;
    }

    .bg-rusak-berat {
        background: linear-gradient(135deg, #ff5858 0%, #f09819 100%) !important;
        color: white !important;
    }

    .bg-denda {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%) !important;
        color: white !important;
    }

    .bg-telat {
        background: linear-gradient(135deg, #ff5858 0%, #f09819 100%) !important;
        color: white !important;
    }

    .bg-tepat-waktu {
        background: linear-gradient(135deg, #1cc88a 0%, #13855c 100%) !important;
        color: white !important;
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
        
        .info-card {
            padding: 15px;
        }
    }
</style>

<div class="container-fluid page-body-wrapper">
    <div id="main" class="p-4">
        <div class="card-admin">
            <div class="card-header-admin">
                <div class="header-container">
                    <h4 class="fw-bold text-primary page-title">
                        <i class="fas fa-exchange-alt"></i> Detail Data Pengembalian
                    </h4>
                    <a href="./index.php" class="btn btn-primary">
                        <i class="fas fa-arrow-left"></i> Kembali
                    </a>
                </div>
            </div>
            
            <div class="card-body-admin">
                <div class="row justify-content-center">
                    <div class="col-md-10">
                        <!-- Info Pengembalian -->
                        <div class="info-card">
                            <div class="info-label">ID Pengembalian</div>
                            <div class="info-value">#<?= htmlspecialchars($pengembalian->id ?? 'Data tidak ditemukan') ?></div>
                        </div>
                        
                        <!-- Info Peminjaman -->
                        <div class="info-card">
                            <div class="info-label">Peminjaman</div>
                            <div class="info-value">
                                <strong>ID: #<?= htmlspecialchars($pengembalian->peminjaman_id ?? '') ?></strong><br>
                                <small class="text-muted">
                                    <?= htmlspecialchars($pengembalian->nama_peminjam ?? 'Tidak diketahui') ?> - 
                                    <?= htmlspecialchars($pengembalian->nama_alat ?? 'Alat tidak diketahui') ?>
                                </small>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="info-card">
                                    <div class="info-label">Tanggal Kembali</div>
                                    <div class="info-value">
                                        <?= isset($pengembalian->tgl_kembali) ? date('d-m-Y', strtotime($pengembalian->tgl_kembali)) : 'Tidak tersedia' ?>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="info-card">
                                    <div class="info-label">Tanggal Rencana Kembali</div>
                                    <div class="info-value">
                                        <?= isset($pengembalian->tgl_rencana_kembali) ? date('d-m-Y', strtotime($pengembalian->tgl_rencana_kembali)) : 'Tidak tersedia' ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Status Keterlambatan -->
                        <div class="info-card">
                            <div class="info-label">Status Keterlambatan</div>
                            <div class="info-value">
                                <?php if ($isTerlambat && $telatHari > 0): ?>
                                    <span class="badge bg-telat">
                                        <i class="fas fa-clock me-1"></i>
                                        Terlambat <?= $telatHari ?> hari
                                    </span>
                                <?php else: ?>
                                    <span class="badge bg-tepat-waktu">
                                        <i class="fas fa-check-circle me-1"></i>
                                        Tepat Waktu
                                    </span>
                                <?php endif; ?>
                            </div>
                        </div>
                        
                        <div class="info-card">
                            <div class="info-label">Kondisi Alat</div>
                            <div class="info-value">
                                <?php if (isset($pengembalian->kondisi)): ?>
                                    <span class="badge 
                                        <?= $pengembalian->kondisi == 'baik' ? 'bg-baik' : 
                                           ($pengembalian->kondisi == 'rusak_ringan' ? 'bg-rusak-ringan' : 'bg-rusak-berat') ?>">
                                        <i class="fas fa-<?= 
                                            $pengembalian->kondisi == 'baik' ? 'check-circle' : 
                                            ($pengembalian->kondisi == 'rusak_ringan' ? 'exclamation-triangle' : 'times-circle') 
                                        ?> me-1"></i>
                                        <?= ucwords(str_replace('_', ' ', $pengembalian->kondisi)) ?>
                                    </span>
                                <?php else: ?>
                                    <span class="badge bg-secondary">
                                        <i class="fas fa-question-circle me-1"></i>
                                        Tidak tersedia
                                    </span>
                                <?php endif; ?>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="info-card">
                                    <div class="info-label">Denda</div>
                                    <div class="info-value">
                                        <?php if (isset($pengembalian->denda) && $pengembalian->denda > 0): ?>
                                            <span class="badge bg-denda">
                                                <i class="fas fa-money-bill-wave me-1"></i>
                                                Rp <?= number_format($pengembalian->denda, 0, ',', '.') ?>
                                            </span>
                                        <?php else: ?>
                                            <span class="badge bg-success">
                                                <i class="fas fa-check me-1"></i>
                                                Tidak Ada Denda
                                            </span>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <?php if (!empty($pengembalian->keterangan)): ?>
                        <div class="info-card">
                            <div class="info-label">Keterangan</div>
                            <div class="info-value">
                                <?= nl2br(htmlspecialchars($pengembalian->keterangan)) ?>
                            </div>
                        </div>
                        <?php endif; ?>
                        
                        <div class="info-card">
                            <div class="info-label">Tanggal Dibuat</div>
                            <div class="info-value">
                                <?= isset($pengembalian->created_at) ? date('d-m-Y H:i', strtotime($pengembalian->created_at)) : 'Tidak tersedia' ?>
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