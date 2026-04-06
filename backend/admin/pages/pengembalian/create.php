<?php
// AKTIFKAN ERROR REPORTING
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Pastikan session_start() di awal
session_start();

include '../../partials/header.php'; 
$page = 'pengembalian'; 
include '../../partials/sidebar.php'; 

// Include koneksi untuk mendapatkan data peminjaman
include '../../app.php';

// PERBAIKAN QUERY: Tambahkan kondisi tgl_kembali_aktual IS NULL
// Ganti query dengan yang lebih akurat
$qPeminjaman = "SELECT 
                    p.id, 
                    p.kode_peminjaman,
                    p.user_id,
                    p.barang_id,
                    p.tgl_pinjam,
                    p.tgl_kembali_rencana,
                    p.tgl_kembali_aktual,
                    p.jumlah as jumlah_pinjam,
                    p.status as status_peminjaman,
                    u.username as nama_peminjam,
                    b.nama_barang,
                    b.kode_barang,
                    b.stok,
                    b.status as status_barang
                FROM peminjaman p 
                LEFT JOIN users u ON p.user_id = u.id
                LEFT JOIN barang b ON p.barang_id = b.id 
                WHERE p.status = 'dipinjam' 
                ORDER BY p.tgl_kembali_rencana ASC";
                
$resultPeminjaman = mysqli_query($connect, $qPeminjaman);
$peminjamanList = [];

if ($resultPeminjaman) {
    while ($row = mysqli_fetch_assoc($resultPeminjaman)) {
        $peminjamanList[] = $row;
    }
} else {
    echo "<div class='alert alert-danger'>Query error: " . mysqli_error($connect) . "</div>";
    $peminjamanList = [];
}

$totalPeminjamanAktif = count($peminjamanList);
?>

<div class="content-wrapper">
        <!-- Main Card -->
        <div class="card-admin">
            <!-- Card Header -->
            <div class="card-header-admin d-flex align-items-center justify-content-between">
                <h2 class="page-title">
                    <i class="fas fa-plus-circle"></i>
                    Tambah Data Pengembalian
                </h2>
                <div>
                    <a href="./index.php" class="btn btn-back">
                        <i class="fas fa-arrow-left"></i> Kembali ke Daftar
                    </a>
                </div>
            </div>
            
            <!-- Card Body -->
            <div class="card-body-admin">
                <!-- Debug Info -->
                <?php if (isset($_GET['debug'])): 
                    // Cek data di database untuk debugging
                    $qDebug = "SELECT id, kode_peminjaman, status, tgl_kembali_aktual FROM peminjaman WHERE status = 'dipinjam'";
                    $resultDebug = mysqli_query($connect, $qDebug);
                    $debugData = [];
                    if ($resultDebug) {
                        while ($row = mysqli_fetch_assoc($resultDebug)) {
                            $debugData[] = $row;
                        }
                    }
                ?>
                <div class="debug-info">
                    <strong>Debug Information:</strong><br>
                    <strong>Query yang digunakan:</strong><br>
                    <code><?php echo htmlspecialchars($qPeminjaman); ?></code><br><br>
                    <strong>Total peminjaman dengan status 'dipinjam':</strong> <?php echo count($debugData); ?><br>
                    <?php if (count($debugData) > 0): ?>
                    <table class="table table-admin table-sm mt-2">
                        <tr>
                            <th>ID</th>
                            <th>Kode</th>
                            <th>Status</th>
                            <th>Tgl Kembali Aktual</th>
                        </tr>
                        <?php foreach ($debugData as $item): ?>
                        <tr>
                            <td><?php echo $item['id']; ?></td>
                            <td><?php echo $item['kode_peminjaman']; ?></td>
                            <td><?php echo $item['status']; ?></td>
                            <td><?php echo $item['tgl_kembali_aktual'] ? $item['tgl_kembali_aktual'] : 'NULL'; ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </table>
                    <?php endif; ?>
                    <a href="create.php" class="btn btn-sm btn-secondary mt-2">Tutup Debug</a>
                </div>
                <?php endif; ?>
                
                <!-- Session Messages -->
                <?php if (isset($_SESSION['success'])): ?>
                    <div class="alert-container">
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <i class="fas fa-check-circle me-2"></i>
                            <?php
                            echo $_SESSION['success'];
                            unset($_SESSION['success']);
                            ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    </div>
                <?php endif; ?>

                <?php if (isset($_SESSION['error'])): ?>
                    <div class="alert-container">
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            <?php
                            echo $_SESSION['error'];
                            unset($_SESSION['error']);
                            ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    </div>
                <?php endif; ?>
                
                <!-- Info Box -->
                <div class="info-box">
                    <h6><i class="fas fa-info-circle"></i> INFORMASI PEMINJAMAN</h6>
                    <p class="mb-2">Pilih peminjaman yang akan dikembalikan dari daftar di bawah.</p>
                    
                    <?php if ($totalPeminjamanAktif == 0): ?>
                        <div class="alert alert-warning">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            <strong>Tidak ada peminjaman aktif yang dapat dikembalikan.</strong>
                            <p class="mb-0 mt-2">
                                <strong>Kemungkinan penyebab:</strong>
                                <ol>
                                    <li>Ada alat yang sedang dipinjam (status: dipinjam)</li>
                                    <li>Peminjaman belum direkam pengembaliannya</li>
                                    <li>Peminjaman belum memiliki tanggal kembali aktual</li>
                                </ol>
                                <div class="mt-3">
                                    <a href="?debug=1" class="btn btn-sm btn-info me-2">Debug Database</a>
                                    <a href="../../pages/peminjaman/index.php" class="btn btn-sm btn-primary">Lihat Peminjaman</a>
                                </div>
                            </p>
                        </div>
                        
                        <!-- Quick Fix Button -->
                        <div class="mt-3">
                            <form method="post" action="?fix=1" onsubmit="return confirm('Yakin ingin reset data peminjaman?')">
                                <button type="submit" class="btn btn-sm btn-danger">
                                    <i class="fas fa-sync-alt"></i> Reset Data Peminjaman Bermasalah
                                </button>
                                <small class="text-muted ms-2">(Hanya untuk admin)</small>
                            </form>
                        </div>
                    <?php else: ?>
                        <div class="alert alert-info">
                            <i class="fas fa-check-circle me-2"></i>
                            <strong>Ditemukan <?php echo $totalPeminjamanAktif; ?> peminjaman aktif.</strong>
                            <p class="mb-0 mt-2">Pilih salah satu peminjaman di bawah untuk proses pengembalian.</p>
                        </div>
                    <?php endif; ?>
                </div>
                
                <!-- Handle Quick Fix -->
                <?php 
                if (isset($_GET['fix'])) {
                    // Reset peminjaman yang bermasalah
                    $fixQuery = "UPDATE peminjaman 
                                SET tgl_kembali_aktual = NULL 
                                WHERE status = 'dipinjam' 
                                AND tgl_kembali_aktual IS NOT NULL";
                    
                    if (mysqli_query($connect, $fixQuery)) {
                        $affected = mysqli_affected_rows($connect);
                        echo "<div class='alert alert-success'>✅ Berhasil reset $affected peminjaman! Halaman akan direfresh...</div>";
                        echo "<script>setTimeout(() => location.href='create.php', 2000);</script>";
                    }
                }
                ?>
                
                <!-- Pengembalian Form -->
                <form action="../../action/pengembalian/store.php" method="POST" class="needs-validation" novalidate>
                    <!-- Peminjaman Selection -->
                    <div class="mb-4">
                        <label for="peminjaman_id" class="form-label">Peminjaman *</label>
                        <select name="peminjaman_id" class="form-select" id="peminjaman_id" required
                                <?php echo ($totalPeminjamanAktif == 0) ? 'disabled' : ''; ?>>
                            <option value="">Pilih Peminjaman</option>
                            <?php foreach ($peminjamanList as $peminjaman): 
                                // Hitung hari telat jika ada
                                $telatHari = 0;
                                $hariIni = new DateTime();
                                $tglRencana = new DateTime($peminjaman['tgl_kembali_rencana']);
                                
                                if ($hariIni > $tglRencana) {
                                    $selisih = $tglRencana->diff($hariIni);
                                    $telatHari = $selisih->days;
                                }
                            ?>
                                <option value="<?= $peminjaman['id'] ?>" 
                                        data-tgl-rencana="<?= $peminjaman['tgl_kembali_rencana'] ?>"
                                        data-tgl-pinjam="<?= $peminjaman['tgl_pinjam'] ?>"
                                        data-jumlah-pinjam="<?= $peminjaman['jumlah_pinjam'] ?>">
                                    #<?= $peminjaman['id'] ?> - <?= htmlspecialchars($peminjaman['kode_peminjaman'] ?? '') ?> 
                                    (<?= htmlspecialchars($peminjaman['nama_peminjam'] ?? '') ?> - <?= htmlspecialchars($peminjaman['nama_barang'] ?? '') ?>)
                                    <?php if ($telatHari > 0): ?>
                                        <span class="telat-badge">Telat <?= $telatHari ?> hari</span>
                                    <?php endif; ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <div class="invalid-feedback">Harap pilih peminjaman</div>
                        <?php if ($totalPeminjamanAktif == 0): ?>
                            <div class="form-text text-danger">
                                <i class="fas fa-exclamation-triangle me-1"></i>
                                Tidak ada peminjaman aktif. Pastikan ada alat yang sedang dipinjam.
                            </div>
                        <?php else: ?>
                            <div class="form-text">
                                Pilih peminjaman yang akan dikembalikan dari daftar di atas
                            </div>
                        <?php endif; ?>
                    </div>
                    
                    <!-- Tanggal Kembali -->
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <label for="tgl_kembali" class="form-label">Tanggal Kembali *</label>
                            <input type="date" name="tgl_kembali" class="form-control" id="tgl_kembali" required
                                   value="<?= date('Y-m-d') ?>">
                            <div class="invalid-feedback">Harap isi tanggal kembali</div>
                            <div class="form-text" id="tgl_kembali_info">Tanggal pengembalian alat</div>
                        </div>
                        <div class="col-md-6">
                            <label for="kondisi" class="form-label">Kondisi Alat *</label>
                            <select name="kondisi" class="form-select" id="kondisi" required>
                                <option value="">Pilih Kondisi</option>
                                <option value="baik">Baik</option>
                                <option value="rusak_ringan">Rusak Ringan</option>
                                <option value="rusak_berat">Rusak Berat</option>
                            </select>
                            <div class="invalid-feedback">Harap pilih kondisi alat</div>
                            <div class="form-text">Kondisi alat saat dikembalikan</div>
                        </div>
                    </div>
                    
                    <!-- Denda -->
                    <div class="mb-4">
                        <label for="denda" class="form-label">Denda (Rp)</label>
                        <input type="number" name="denda" class="form-control" id="denda"
                               placeholder="0" min="0" step="1000" value="0">
                        <div class="invalid-feedback">Denda tidak boleh negatif</div>
                        <div class="form-text">Isi jika ada denda keterlambatan/kerusakan</div>
                        <div class="mt-2">
                            <div id="denda_info" class="text-muted"></div>
                            <div id="telat_info" class="text-danger fw-bold" style="display: none;"></div>
                        </div>
                    </div>
                    
                    <!-- Keterangan -->
                    <div class="mb-4">
                        <label for="keterangan" class="form-label">Keterangan</label>
                        <textarea name="keterangan" class="form-control" id="keterangan" 
                                  rows="3" placeholder="Tambahkan keterangan jika perlu..."></textarea>
                        <div class="form-text">Deskripsi kondisi atau alasan denda (jika ada)</div>
                    </div>
                    
                    <!-- Submit Buttons -->
                    <div class="d-flex justify-content-between mt-5">
                        <a href="./index.php" class="btn btn-back">
                            <i class="fas fa-times"></i> Batal
                        </a>
                        <button type="submit" class="btn btn-submit" name="tombol"
                                <?php echo ($totalPeminjamanAktif == 0) ? 'disabled' : ''; ?>>
                            <i class="fas fa-save"></i> Simpan Pengembalian
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div><?php include '../../partials/script.php'; ?>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<script>
// Validasi form
(function() {
    'use strict';
    var forms = document.querySelectorAll('.needs-validation');
    Array.prototype.slice.call(forms).forEach(function(form) {
        form.addEventListener('submit', function(event) {
            if (!form.checkValidity()) {
                event.preventDefault();
                event.stopPropagation();
            }
            form.classList.add('was-validated');
        }, false);
    });
})();

// Set tanggal minimal hari ini
document.addEventListener('DOMContentLoaded', function() {
    const today = new Date();
    const todayStr = today.toISOString().split('T')[0];
    const tglKembaliInput = document.getElementById('tgl_kembali');
    
    if (tglKembaliInput) {
        tglKembaliInput.value = todayStr;
        tglKembaliInput.min = todayStr;
    }
});

// Fungsi untuk menghitung denda otomatis
function hitungDendaOtomatis(tglRencana, tglKembali) {
    const tgl1 = new Date(tglRencana);
    const tgl2 = new Date(tglKembali);
    
    if (tgl2 > tgl1) {
        const diffTime = Math.abs(tgl2 - tgl1);
        const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
        
        // Hitung denda: Rp 100,000 per hari telat
        const dendaPerHari = 100000;
        const totalDenda = diffDays * dendaPerHari;
        
        return {
            telatHari: diffDays,
            totalDenda: totalDenda
        };
    }
    
    return {
        telatHari: 0,
        totalDenda: 0
    };
}

// Update info ketika peminjaman dipilih
const peminjamanSelect = document.getElementById('peminjaman_id');
if (peminjamanSelect) {
    peminjamanSelect.addEventListener('change', function() {
        const selectedOption = this.options[this.selectedIndex];
        const tglRencana = selectedOption.getAttribute('data-tgl-rencana');
        const tglPinjam = selectedOption.getAttribute('data-tgl-pinjam');
        const jumlahPinjam = selectedOption.getAttribute('data-jumlah-pinjam');
        
        if (tglRencana) {
            // Format tanggal untuk ditampilkan
            const tglRencanaFormatted = new Date(tglRencana).toLocaleDateString('id-ID');
            const tglPinjamFormatted = new Date(tglPinjam).toLocaleDateString('id-ID');
            
            // Update info
            document.getElementById('tgl_kembali_info').innerHTML = 
                `Rencana kembali: ${tglRencanaFormatted} | Tanggal pinjam: ${tglPinjamFormatted}`;
            
            // Reset denda info
            document.getElementById('telat_info').style.display = 'none';
            document.getElementById('denda').value = 0;
        }
    });
}

// Update denda ketika tanggal kembali diubah
const tglKembaliInput = document.getElementById('tgl_kembali');
if (tglKembaliInput) {
    tglKembaliInput.addEventListener('change', function() {
        const peminjamanSelect = document.getElementById('peminjaman_id');
        if (!peminjamanSelect || peminjamanSelect.selectedIndex === 0) return;
        
        const selectedOption = peminjamanSelect.options[peminjamanSelect.selectedIndex];
        const tglRencana = selectedOption.getAttribute('data-tgl-rencana');
        const tglKembali = this.value;
        
        if (tglRencana && tglKembali) {
            const dendaInfo = hitungDendaOtomatis(tglRencana, tglKembali);
            
            if (dendaInfo.telatHari > 0) {
                document.getElementById('telat_info').style.display = 'block';
                document.getElementById('telat_info').innerHTML = 
                    `<i class="fas fa-exclamation-triangle"></i> Telat ${dendaInfo.telatHari} hari. Denda otomatis: Rp ${dendaInfo.totalDenda.toLocaleString('id-ID')}`;
                
                // Set nilai denda otomatis
                document.getElementById('denda').value = dendaInfo.totalDenda;
            } else {
                document.getElementById('telat_info').style.display = 'none';
                document.getElementById('denda').value = 0;
            }
        }
    });
}

// Alert jika tidak ada peminjaman
<?php if ($totalPeminjamanAktif == 0): ?>
document.addEventListener('DOMContentLoaded', function() {
    setTimeout(function() {
        alert('Tidak ada peminjaman aktif yang dapat dikembalikan.\n\nPastikan:\n1. Ada alat yang sedang dipinjam (status: dipinjam)\n2. Peminjaman belum direkam pengembaliannya\n3. Peminjaman belum memiliki tanggal kembali aktual');
    }, 500);
});
<?php endif; ?>
</script>

