<?php
// AKTIFKAN ERROR REPORTING
error_reporting(E_ALL);
ini_set('display_errors', 1);

include "../../partials/header.php";
$page = 'pengembalian'; // Ubah page menjadi pengembalian
include "../../partials/sidebar.php";

// Include action untuk mengambil data
include '../../action/pengembalian/show.php';
?>

// Security check: Hanya pemilik yang bisa lihat
if ($pengembalian->user_id != $_SESSION['user_id']) {
    header("Location: ./index.php");
    exit();
}
?>

<?php 
include "../../partials/header.php";
$page = 'pengembalian';
include "../../partials/sidebar.php";
?>

<div class="content-wrapper">
    <div class="page-title">
        <i class="fas fa-history"></i>
        <span>Detail Riwayat Sewa</span>
    </div>

    <div class="row justify-content-center">
        <div class="col-xl-10">
            <div class="card-admin shadow-sm">
                <div class="card-header-admin bg-white d-flex justify-content-between align-items-center">
                    <span><i class="fas fa-check-double me-2 text-indigo-600"></i> Bukti Pengembalian #<?= htmlspecialchars($pengembalian->id) ?></span>
                    <a href="./index.php" class="btn btn-sm btn-outline-secondary">
                        <i class="fas fa-list me-1"></i> Kembali ke Riwayat
                    </a>
                </div>
                <div class="card-body p-4 p-md-5">
                    <div class="row g-4">
                        <!-- Summary Section -->
                        <div class="col-12 mb-4">
                            <div class="row g-3">
                                <div class="col-md-4">
                                    <div class="p-4 rounded-4 bg-indigo-50 border border-indigo-100 text-center">
                                        <div class="small fw-700 text-indigo-400 uppercase tracking-wider mb-1">Durasi Sewa</div>
                                        <div class="h4 fw-900 text-indigo-900 mb-0"><?= $pengembalian->lama_pinjam ?> Hari</div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="p-4 rounded-4 bg-emerald-50 border border-emerald-100 text-center">
                                        <div class="small fw-700 text-emerald-400 uppercase tracking-wider mb-1">Status Alat</div>
                                        <div class="h4 fw-900 text-emerald-900 mb-0"><?= strtoupper(str_replace('_', ' ', $pengembalian->kondisi)) ?></div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="p-4 rounded-4 <?= $pengembalian->denda > 0 ? 'bg-rose-50 border-rose-100' : 'bg-slate-50 border-slate-100' ?> text-center">
                                        <div class="small fw-700 <?= $pengembalian->denda > 0 ? 'text-rose-400' : 'text-slate-400' ?> uppercase tracking-wider mb-1">Total Denda</div>
                                        <div class="h4 fw-900 <?= $pengembalian->denda > 0 ? 'text-rose-900' : 'text-slate-900' ?> mb-0">Rp <?= number_format($pengembalian->denda, 0, ',', '.') ?></div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Info Grid -->
                        <div class="col-md-6">
                            <div class="p-4 bg-white rounded-4 border border-slate-100 h-100 shadow-tiny">
                                <h6 class="fw-800 text-slate-800 mb-4 border-bottom pb-2"><i class="fas fa-tractor me-2 text-indigo-600"></i> Detail Aset</h6>
                                <div class="mb-4">
                                    <label class="small fw-700 text-slate-400 d-block mb-1">Alat Berat</label>
                                    <div class="fw-800 text-slate-900 h6"><?= htmlspecialchars($pengembalian->nama_alat) ?></div>
                                </div>
                                <div class="mb-4">
                                    <label class="small fw-700 text-slate-400 d-block mb-1">Unit Disewa</label>
                                    <div class="fw-800 text-slate-900"><?= $pengembalian->jumlah_pinjam ?> Unit</div>
                                </div>
                                <div class="mb-0">
                                    <label class="small fw-700 text-slate-400 d-block mb-1">Kode Transaksi</label>
                                    <div class="badge bg-slate-100 text-slate-600 px-3 py-2 rounded-pill fw-800"><?= htmlspecialchars($pengembalian->kode_peminjaman) ?></div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="p-4 bg-white rounded-4 border border-slate-100 h-100 shadow-tiny">
                                <h6 class="fw-800 text-slate-800 mb-4 border-bottom pb-2"><i class="fas fa-calendar-check me-2 text-indigo-600"></i> Timeline Sewa</h6>
                                <div class="row g-4">
                                    <div class="col-6">
                                        <label class="small fw-700 text-slate-400 d-block mb-1">Mulai</label>
                                        <div class="fw-800 text-slate-800"><?= date('d M Y', strtotime($pengembalian->tgl_pinjam)) ?></div>
                                    </div>
                                    <div class="col-6">
                                        <label class="small fw-700 text-slate-400 d-block mb-1">Rencana Kembali</label>
                                        <div class="fw-800 text-slate-800"><?= date('d M Y', strtotime($pengembalian->tgl_rencana_kembali)) ?></div>
                                    </div>
                                    <div class="col-12">
                                        <label class="small fw-700 text-slate-400 d-block mb-1">Dikembalikan Tanggal</label>
                                        <div class="fw-900 text-indigo-600 h5 mb-0"><?= date('d F Y', strtotime($pengembalian->tgl_kembali)) ?></div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Keterangan -->
                        <div class="col-12 mt-2">
                            <div class="p-4 bg-slate-50 rounded-4 border border-slate-100">
                                <label class="small fw-700 text-slate-400 d-block mb-2"><i class="fas fa-comment-alt me-2 text-indigo-600"></i> Catatan Pemeriksaan</label>
                                <div class="text-slate-700 fw-600 italic">
                                    <?= !empty($pengembalian->keterangan) ? nl2br(htmlspecialchars($pengembalian->keterangan)) : 'Tidak ada catatan tambahan.' ?>
                                </div>
                            </div>
                        </div>

                        <!-- Warning for Late Returns -->
                        <?php 
                        $tglRencana = new DateTime($pengembalian->tgl_rencana_kembali);
                        $tglKembali = new DateTime($pengembalian->tgl_kembali);
                        if ($tglKembali > $tglRencana): 
                            $diff = $tglRencana->diff($tglKembali)->days;
                        ?>
                        <div class="col-12">
                            <div class="alert bg-rose-50 border-rose-100 text-rose-800 p-4 rounded-4 mb-0 d-flex align-items-center">
                                <i class="fas fa-exclamation-triangle fa-2x me-3 opacity-50"></i>
                                <div>
                                    <div class="fw-900 h6 mb-1">Keterlambatan Pengembalian</div>
                                    <div class="fw-600">Alat dikembalikan terlambat <strong><?= $diff ?> Hari</strong> dari jadwal rencana. Denda keterlambatan telah dikenakan secara otomatis.</div>
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