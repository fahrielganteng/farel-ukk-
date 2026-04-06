<?php
// AKTIFKAN ERROR REPORTING
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Pastikan session_start() di awal
session_start();

include "../../partials/header.php";
$page = 'peminjaman';
include "../../partials/sidebar.php";

// Include action untuk mengambil data
include '../../action/peminjaman/show.php';
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
        background: #f8f9ff;
        border-radius: 10px;
        padding: 20px;
        margin-bottom: 15px;
        border-left: 4px solid #4e73df;
        transition: all 0.3s ease;
    }

    .info-card:hover {
        transform: translateX(5px);
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.08);
    }

    .info-label {
        font-size: 0.9rem;
        color: #6c757d;
        margin-bottom: 5px;
        font-weight: 600;
    }

    .info-value {
        font-size: 1.1rem;
        color: #000000;
        font-weight: 500;
    }

    /* STATUS BADGE - SESUAI DATABASE */
    .status-badge {
        padding: 10px 25px;
        border-radius: 25px;
        font-weight: 600;
        font-size: 0.9rem;
        display: inline-block;
        min-width: 140px;
        text-align: center;
        box-shadow: 0 4px 10px rgba(0,0,0,0.1);
        transition: all 0.3s ease;
        text-transform: capitalize;
    }

    .status-badge:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 15px rgba(0,0,0,0.15);
    }

    /* WARNA STATUS SESUAI DATABASE */
    .status-pending {
        background: linear-gradient(135deg, #ffd700 0%, #ffa500 100%);
        color: #333;
        border: 1px solid #ffc107;
    }

    .status-disetujui {
        background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
        color: white;
        border: 1px solid #28a745;
    }

    .status-ditolak {
        background: linear-gradient(135deg, #dc3545 0%, #e83e8c 100%);
        color: white;
        border: 1px solid #dc3545;
    }

    .status-dipinjam {
        background: linear-gradient(135deg, #007bff 0%, #6610f2 100%);
        color: white;
        border: 1px solid #007bff;
    }

    .status-selesai {
        background: linear-gradient(135deg, #17a2b8 0%, #138496 100%);
        color: white;
        border: 1px solid #17a2b8;
    }

    .status-terlambat {
        background: linear-gradient(135deg, #fd7e14 0%, #dc3545 100%);
        color: white;
        border: 1px solid #fd7e14;
    }

    .status-dikembalikan {
        background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%);
        color: #333;
        border: 1px solid #28a745;
    }

    .header-container {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 25px;
        padding-bottom: 15px;
        border-bottom: 2px solid #f0f2f5;
    }

    /* Tombol aksi */
    .action-buttons {
        display: flex;
        gap: 10px;
        margin-top: 30px;
        padding-top: 20px;
        border-top: 1px solid #e3e6f0;
    }

    .btn-custom {
        border-radius: 8px;
        padding: 10px 20px;
        font-weight: 600;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        transition: all 0.3s ease;
    }

    .btn-custom:hover {
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
    }

    .btn-back {
        background: #6c757d !important;
        border-color: #6c757d !important;
        color: white;
    }

    .btn-edit {
        background: linear-gradient(135deg, #fad961 0%, #f76b1c 100%) !important;
        border: none !important;
        color: white !important;
    }

    .btn-delete {
        background: linear-gradient(135deg, #ff5858 0%, #f09819 100%) !important;
        border: none !important;
        color: white !important;
    }

    .btn-kembalikan {
        background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%) !important;
        border: none !important;
        color: #333 !important;
        font-weight: 700;
    }

    /* Badge untuk informasi tambahan */
    .info-badge {
        background: #e9ecef;
        color: #495057;
        padding: 5px 15px;
        border-radius: 15px;
        font-size: 0.85rem;
        margin-left: 10px;
    }

    .harga-info {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white !important;
        padding: 10px 20px;
        border-radius: 10px;
        font-weight: 700;
        margin-top: 10px;
        display: inline-block;
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
        
        .status-badge {
            min-width: 120px;
            padding: 8px 15px;
            font-size: 0.8rem;
        }
        
        .action-buttons {
            flex-direction: column;
        }
        
        .action-buttons .btn {
            width: 100%;
            justify-content: center;
        }
    }
</style>

<div class="container-fluid page-body-wrapper">
    <div id="main" class="p-4">
        <div class="card">
            <div class="card-header">
                <div class="header-container">
                    <h4 class="fw-bold text-primary page-title">
                        <i class="fas fa-clipboard-list me-2"></i> 
                        Detail Data Peminjaman
                        <span class="badge bg-info ms-2">
                            ID: <?= $peminjaman->id ?? 'N/A' ?>
                        </span>
                    </h4>
                    <a href="./index.php" class="btn btn-primary">
                        <i class="fas fa-arrow-left me-2"></i> Kembali ke Daftar
                    </a>
                </div>
            </div>
            
            <div class="card-body">
                <div class="row justify-content-center">
                    <div class="col-md-10 col-lg-8">
                        <!-- Info Utama -->
                        <div class="info-card">
                            <div class="row align-items-center">
                                <div class="col-md-8">
                                    <div class="info-label">Kode Peminjaman</div>
                                    <div class="info-value fw-bold text-primary fs-4">
                                        <?= htmlspecialchars($peminjaman->kode_peminjaman ?? '') ?>
                                    </div>
                                </div>
                                <div class="col-md-4 text-end">
                                    <?php 
                                    $status = $peminjaman->status ?? 'pending';
                                    $statusClass = 'status-' . $status;
                                    $statusText = ucfirst($status);
                                    ?>
                                    <span class="status-badge <?= $statusClass ?>">
                                        <i class="fas fa-circle me-1" style="font-size: 0.7rem;"></i>
                                        <?= $statusText ?>
                                    </span>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Informasi Peminjam -->
                        <div class="info-card">
                            <div class="info-label">Informasi Peminjam</div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="info-label">Nama Peminjam</div>
                                    <div class="info-value">
                                        <?= htmlspecialchars($peminjaman->username ?? '') ?>
                                        <span class="info-badge">User ID: <?= $peminjaman->user_id ?? 'N/A' ?></span>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="info-label">Tanggal Permohonan</div>
                                    <div class="info-value">
                                        <?= isset($peminjaman->created_at) ? date('d-m-Y H:i', strtotime($peminjaman->created_at)) : 'N/A' ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Informasi Barang -->
                        <div class="info-card">
                            <div class="info-label">Informasi Barang</div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="info-label">Nama Barang</div>
                                    <div class="info-value">
                                        <?= htmlspecialchars($peminjaman->nama_barang ?? '') ?>
                                        <span class="info-badge">ID: <?= $peminjaman->barang_id ?? 'N/A' ?></span>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="info-label">Kategori</div>
                                    <div class="info-value">
                                        <?= !empty($peminjaman->nama_kategori) ? 
                                            '<span class="badge bg-info">' . htmlspecialchars($peminjaman->nama_kategori) . '</span>' : 
                                            '<span class="text-muted">Tidak ada</span>' ?>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="info-label">Jumlah</div>
                                    <div class="info-value fw-bold">
                                        <?= $peminjaman->jumlah ?? '0' ?> unit
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Informasi Waktu -->
                        <div class="info-card">
                            <div class="info-label">Informasi Waktu Peminjaman</div>
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="info-label">Tanggal Pinjam</div>
                                    <div class="info-value fw-bold text-primary">
                                        <?= isset($peminjaman->tgl_pinjam) ? date('d-m-Y', strtotime($peminjaman->tgl_pinjam)) : 'N/A' ?>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="info-label">Rencana Kembali</div>
                                    <div class="info-value fw-bold <?= 
                                        ($status == 'terlambat') ? 'text-danger' : 'text-success' ?>">
                                        <?= isset($peminjaman->tgl_kembali_rencana) ? 
                                            date('d-m-Y', strtotime($peminjaman->tgl_kembali_rencana)) : 'N/A' ?>
                                        <?php if ($status == 'terlambat'): ?>
                                            <i class="fas fa-exclamation-triangle ms-2"></i>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="info-label">Aktual Kembali</div>
                                    <div class="info-value fw-bold <?= 
                                        (!empty($peminjaman->tgl_kembali_aktual) && $peminjaman->tgl_kembali_aktual != '0000-00-00') ? 
                                        'text-success' : 'text-warning' ?>">
                                        <?= !empty($peminjaman->tgl_kembali_aktual) && $peminjaman->tgl_kembali_aktual != '0000-00-00' ? 
                                            date('d-m-Y', strtotime($peminjaman->tgl_kembali_aktual)) : 
                                            'Belum dikembalikan' ?>
                                    </div>
                                </div>
                            </div>
                            <div class="mt-3">
                                <div class="info-label">Lama Pinjam</div>
                                <div class="info-value">
                                    <span class="harga-info">
                                        <?= $peminjaman->lama_pinjam ?? '0' ?> hari
                                    </span>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Informasi Harga -->
                        <?php if (!empty($peminjaman->total_harga)): ?>
                        <div class="info-card">
                            <div class="info-label">Informasi Biaya</div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="info-label">Harga Sewa per Hari</div>
                                    <div class="info-value">
                                        Rp <?= number_format($peminjaman->harga_sewa_perhari ?? 0, 0, ',', '.') ?>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="info-label">Total Biaya</div>
                                    <div class="info-value fw-bold text-success fs-5">
                                        Rp <?= number_format($peminjaman->total_harga ?? 0, 0, ',', '.') ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php endif; ?>
                        
                        <!-- Keterangan -->
                        <?php if (!empty($peminjaman->keterangan)): ?>
                        <div class="info-card">
                            <div class="info-label">Keterangan</div>
                            <div class="info-value">
                                <div class="alert alert-light border">
                                    <i class="fas fa-comment-dots me-2 text-muted"></i>
                                    <?= nl2br(htmlspecialchars($peminjaman->keterangan)) ?>
                                </div>
                            </div>
                        </div>
                        <?php endif; ?>
                        
                        <!-- Tombol Aksi -->
                        <div class="action-buttons">
                            <a href="./index.php" class="btn btn-custom btn-back">
                                <i class="fas fa-list me-2"></i> Daftar Peminjaman
                            </a>
                            
                            <div class="d-flex gap-2">
                                <!-- Tombol Edit -->
                                <a href="./edit.php?id=<?= $peminjaman->id ?>" class="btn btn-custom btn-edit">
                                    <i class="fas fa-edit me-2"></i> Edit
                                </a>
                                
                                <!-- Tombol Kembalikan (hanya untuk status dipinjam) -->
                                <?php if ($status == 'dipinjam'): ?>
                                <a href="../pengembalian/create.php?peminjaman_id=<?= $peminjaman->id ?>" 
                                   class="btn btn-custom btn-kembalikan">
                                    <i class="fas fa-undo me-2"></i> Proses Pengembalian
                                </a>
                                <?php endif; ?>
                                
                                <!-- Tombol Hapus -->
                                <a href="../../action/peminjaman/destroy.php?id=<?= $peminjaman->id ?>" 
                                   onclick="return confirm('Apakah Anda yakin ingin menghapus peminjaman <?= addslashes($peminjaman->kode_peminjaman ?? '') ?>?\n\nTindakan ini tidak dapat dibatalkan!')"
                                   class="btn btn-custom btn-delete">
                                    <i class="fas fa-trash me-2"></i> Hapus
                                </a>
                            </div>
                        </div>
                        
                        <!-- Info Terakhir Update -->
                        <div class="text-center mt-4 text-muted">
                            <small>
                                <i class="fas fa-history me-1"></i>
                                Terakhir update: 
                                <?= isset($peminjaman->updated_at) && $peminjaman->updated_at != '0000-00-00 00:00:00' ? 
                                    date('d-m-Y H:i', strtotime($peminjaman->updated_at)) : 
                                    'Belum pernah diupdate' ?>
                            </small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php include '../../partials/footer.php'; ?>
    </div>
</div>

<?php include '../../partials/script.php'; ?>

<script>
// Animasi untuk info cards
document.addEventListener('DOMContentLoaded', function() {
    const infoCards = document.querySelectorAll('.info-card');
    
    infoCards.forEach((card, index) => {
        card.style.opacity = '0';
        card.style.transform = 'translateY(20px)';
        
        setTimeout(() => {
            card.style.transition = 'all 0.5s ease';
            card.style.opacity = '1';
            card.style.transform = 'translateY(0)';
        }, index * 100);
    });
    
    // Tambahkan tooltip untuk tombol
    const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    const tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
});
</script>