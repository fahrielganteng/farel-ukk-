<?php
// AKTIFKAN ERROR REPORTING
error_reporting(E_ALL);
ini_set('display_errors', 1);

include "../../partials/header.php";
$page = 'peminjaman';
include "../../partials/sidebar.php";

// Include action untuk mengambil data
include '../../action/peminjaman/show.php';
?>

// Security check: Hanya pemilik yang bisa lihat
if ($peminjaman->user_id != $_SESSION['user_id']) {
    header("Location: ./index.php");
    exit();
}
?>

<?php 
include "../../partials/header.php";
$page = 'peminjaman';
include "../../partials/sidebar.php";
?>

<div class="content-wrapper">
    <div class="page-title">
        <i class="fas fa-info-circle"></i>
        <span>Detail Penyewaan</span>
    </div>

    <div class="row justify-content-center">
        <div class="col-xl-10">
            <div class="card-admin shadow-sm">
                <div class="card-header-admin bg-white d-flex justify-content-between align-items-center">
                    <span><i class="fas fa-receipt me-2 text-indigo-600"></i> Informasi Transaksi #<?= htmlspecialchars($peminjaman->kode_peminjaman) ?></span>
                    <a href="./index.php" class="btn btn-sm btn-outline-secondary">
                        <i class="fas fa-list me-1"></i> Kembali ke Daftar
                    </a>
                </div>
                <div class="card-body p-4 p-md-5">
                    <div class="row g-4">
                        <!-- Status Banner -->
                        <div class="col-12 mb-3">
                            <div class="p-4 rounded-4 border d-flex align-items-center justify-content-between <?php
                                switch($peminjaman->status) {
                                    case 'disetujui': echo 'bg-emerald-50 border-emerald-100 text-emerald-800'; break;
                                    case 'dipinjam': echo 'bg-indigo-50 border-indigo-100 text-indigo-800'; break;
                                    case 'kembali': echo 'bg-slate-50 border-slate-200 text-slate-800'; break;
                                    case 'ditolak': echo 'bg-rose-50 border-rose-100 text-rose-800'; break;
                                    default: echo 'bg-amber-50 border-amber-100 text-amber-800'; break;
                                }
                            ?>">
                                <div class="d-flex align-items-center gap-3">
                                    <i class="fas <?php
                                        switch($peminjaman->status) {
                                            case 'disetujui': echo 'fa-check-circle'; break;
                                            case 'dipinjam': echo 'fa-truck-loading'; break;
                                            case 'kembali': echo 'fa-history'; break;
                                            case 'ditolak': echo 'fa-times-circle'; break;
                                            default: echo 'fa-clock'; break;
                                        }
                                    ?> fa-2x opacity-75"></i>
                                    <div>
                                        <div class="small fw-700 uppercase tracking-wider mb-1">Status Saat Ini</div>
                                        <div class="h5 fw-900 mb-0"><?= strtoupper($peminjaman->status) ?></div>
                                    </div>
                                </div>
                                <div class="text-end">
                                    <div class="small fw-700 opacity-75">Tanggal Pengajuan</div>
                                    <div class="fw-800"><?= date('d M Y', strtotime($peminjaman->tgl_pinjam)) ?></div>
                                </div>
                            </div>
                        </div>

                        <!-- Data Grid -->
                        <div class="col-md-6">
                            <div class="p-4 bg-slate-50 rounded-4 border border-slate-100 h-100">
                                <h6 class="fw-800 text-slate-800 mb-4 border-bottom pb-2">Detail Alat & Penyewa</h6>
                                
                                <div class="mb-4">
                                    <label class="small fw-700 text-slate-400 uppercase mb-1 d-block">Nama Alat Berat</label>
                                    <div class="fw-800 text-indigo-600 h5"><?= htmlspecialchars($peminjaman->nama_barang) ?></div>
                                </div>

                                <div class="mb-4">
                                    <label class="small fw-700 text-slate-400 uppercase mb-1 d-block">Jumlah Unit</label>
                                    <div class="fw-800 text-slate-800"><?= $peminjaman->jumlah ?> Unit</div>
                                </div>

                                <div class="mb-0">
                                    <label class="small fw-700 text-slate-400 uppercase mb-1 d-block">Nama Penyewa</label>
                                    <div class="fw-800 text-slate-800"><?= htmlspecialchars($peminjaman->username) ?></div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="p-4 bg-slate-50 rounded-4 border border-slate-100 h-100">
                                <h6 class="fw-800 text-slate-800 mb-4 border-bottom pb-2">Timeline & Durasi</h6>
                                
                                <div class="row g-3">
                                    <div class="col-6 mb-3">
                                        <label class="small fw-700 text-slate-400 uppercase mb-1 d-block">Mulai Sewa</label>
                                        <div class="fw-800 text-slate-800"><?= date('d F Y', strtotime($peminjaman->tgl_pinjam)) ?></div>
                                    </div>
                                    <div class="col-6 mb-3">
                                        <label class="small fw-700 text-slate-400 uppercase mb-1 d-block">Rencana Kembali</label>
                                        <div class="fw-800 text-slate-800"><?= date('d F Y', strtotime($peminjaman->tgl_kembali_rencana)) ?></div>
                                    </div>
                                    <div class="col-6">
                                        <label class="small fw-700 text-slate-400 uppercase mb-1 d-block">Durasi Sewa</label>
                                        <div class="badge bg-indigo-100 text-indigo-700 px-3 py-2 rounded-pill fw-800"><?= $peminjaman->lama_pinjam ?> Hari</div>
                                    </div>
                                    <div class="col-6">
                                        <label class="small fw-700 text-slate-400 uppercase mb-1 d-block">Realisasi Kembali</label>
                                        <div class="fw-800 <?= empty($peminjaman->tgl_kembali_aktual) || $peminjaman->tgl_kembali_aktual == '0000-00-00' ? 'text-slate-300 italic' : 'text-emerald-600' ?>">
                                            <?= empty($peminjaman->tgl_kembali_aktual) || $peminjaman->tgl_kembali_aktual == '0000-00-00' ? 'Belum Kembali' : date('d F Y', strtotime($peminjaman->tgl_kembali_aktual)) ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Actions Section (Only for Pending) -->
                        <?php if ($peminjaman->status == 'pending'): ?>
                        <div class="col-12 mt-4">
                            <div class="p-4 bg-amber-50 rounded-4 border border-amber-100 d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="fw-800 text-amber-900 mb-1"><i class="fas fa-tools me-2"></i> Kelola Pengajuan</h6>
                                    <p class="small text-amber-700 mb-0">Anda masih dapat mengubah atau membatalkan pengajuan ini sebelum diproses oleh petugas.</p>
                                </div>
                                <div class="d-flex gap-2">
                                    <a href="./edit.php?id=<?= $peminjaman->id ?>" class="btn btn-warning px-4 rounded-4 fw-800 shadow-sm border-0">
                                        <i class="fas fa-edit me-2"></i> Ubah
                                    </a>
                                    <a href="../../action/peminjaman/destroy.php?id=<?= $peminjaman->id ?>" 
                                       onclick="return confirm('Apakah Anda yakin ingin menghapus pengajuan ini?')"
                                       class="btn btn-rose text-white px-4 rounded-4 fw-800 shadow-sm border-0">
                                        <i class="fas fa-trash me-2"></i> Hapus
                                    </a>
                                </div>
                            </div>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="p-4 bg-white rounded-4 border border-slate-200 mt-4 text-center">
        <small class="text-slate-400 fw-600 italic">&copy; <?= date('Y') ?> HeavyHire - Premium Heavy Equipment Rental Solution</small>
    </div>
</div>

<?php include "../../partials/script.php"; ?>
<?php include "../../partials/footer.php"; ?>