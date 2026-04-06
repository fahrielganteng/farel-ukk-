<?php
// AKTIFKAN ERROR REPORTING
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Pastikan session_start() di awal
session_start();

include '../../partials/header.php'; 
$page = 'peminjaman'; 
include '../../partials/sidebar.php'; 

// Include action untuk mengambil data
include '../../action/peminjaman/show.php';

// DEBUG: Cek data peminjaman
echo "<!-- DEBUG: Peminjaman data -->";
echo "<!-- ID: " . ($peminjaman->id ?? 'NULL') . " -->";
echo "<!-- Kode: " . ($peminjaman->kode_peminjaman ?? 'NULL') . " -->";
echo "<!-- Status: " . ($peminjaman->status ?? 'NULL') . " -->";
?>

<div class="content-wrapper">
    <div class="row justify-content-center">
        <div class="col-lg-10 col-xl-8">
            <div class="card-admin border-0 shadow-sm mt-4">
            <div class="card-header-admin">
                <div class="header-container">
                    <h4 class="fw-bold text-primary page-title">
                        <i class="fas fa-edit"></i> Edit Data Peminjaman
                    </h4>
                    <a href="./index.php" class="btn btn-primary">
                        <i class="fas fa-arrow-left"></i> Kembali
                    </a>
                </div>
            </div>
            
            <div class="card-body-admin">
                <!-- Tampilkan pesan error/success dari session -->
                <?php if (isset($_SESSION['error'])): ?>
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        <?php
                        echo $_SESSION['error'];
                        unset($_SESSION['error']);
                        ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                <?php endif; ?>

                <?php if (isset($_SESSION['success'])): ?>
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <i class="fas fa-check-circle me-2"></i>
                        <?php
                        echo $_SESSION['success'];
                        unset($_SESSION['success']);
                        ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                <?php endif; ?>
                
                <!-- DEBUG INFO -->
                <div class="alert alert-info">
                    <i class="fas fa-info-circle me-2"></i>
                    <strong>Data yang sedang diedit:</strong><br>
                    ID: <?= $peminjaman->id ?? 'NULL' ?><br>
                    Kode: <?= $peminjaman->kode_peminjaman ?? 'NULL' ?><br>
                    Peminjam: <?= $peminjaman->username ?? 'NULL' ?><br>
                    Barang: <?= $peminjaman->nama_barang ?? 'NULL' ?><br>
                    Kategori: <?= $peminjaman->nama_kategori ?? 'NULL' ?>
                </div>
                
                <div class="row justify-content-center">
                    <div class="col-md-8">
                        <!-- HAPUS PARAMETER ID DI ACTION, GUNAKAN HIDDEN FIELD -->
                        <form action="../../action/peminjaman/update.php" method="POST" class="needs-validation" novalidate>
                            <!-- TAMBAH INPUT HIDDEN UNTUK ID -->
                            <input type="hidden" name="id" value="<?= $peminjaman->id ?>">
                            <input type="hidden" name="tombol" value="1">
                            
                            <div class="mb-4">
                                <label for="kode_peminjaman" class="form-label">Kode Peminjaman *</label>
                                <input type="text" name="kode_peminjaman" class="form-control" id="kode_peminjaman"
                                    placeholder="Contoh: PINJ-001" required
                                    value="<?= htmlspecialchars($peminjaman->kode_peminjaman ?? '') ?>">
                                <div class="invalid-feedback">Harap isi kode peminjaman</div>
                            </div>
                            
                            <div class="row mb-4">
                                <div class="col-md-6">
                                    <label for="user_id" class="form-label">Peminjam *</label>
                                    <select name="user_id" class="form-select" id="user_id" required>
                                        <option value="">Pilih Peminjam</option>
                                        <?php 
                                        if ($users && mysqli_num_rows($users) > 0) {
                                            // Reset pointer result set
                                            mysqli_data_seek($users, 0);
                                            while($user = mysqli_fetch_assoc($users)): 
                                                $selected = ($peminjaman->user_id ?? '') == $user['id'] ? 'selected' : '';
                                        ?>
                                            <option value="<?= $user['id'] ?>" <?= $selected ?>>
                                                <?= htmlspecialchars($user['username']) ?>
                                            </option>
                                        <?php 
                                            endwhile;
                                        } else {
                                            echo '<option value="" disabled>-- Tidak ada peminjam --</option>';
                                        }
                                        ?>
                                    </select>
                                    <div class="invalid-feedback">Harap pilih peminjam</div>
                                </div>
                                
                                <!-- HAPUS FIELD KATEGORI ID INI -->
                                <!--
                                <div class="col-md-6">
                                    <label for="kategori_id" class="form-label">Kategori *</label>
                                    <select name="kategori_id" class="form-select" id="kategori_id" required>
                                        <option value="">Pilih Kategori</option>
                                        <?php 
                                        if ($kategori && mysqli_num_rows($kategori) > 0) {
                                            while($kat = mysqli_fetch_assoc($kategori)): 
                                                $selected = ($peminjaman->kategori_id ?? '') == $kat['id'] ? 'selected' : '';
                                        ?>
                                            <option value="<?= $kat['id'] ?>" <?= $selected ?>>
                                                <?= htmlspecialchars($kat['nama_kategori']) ?>
                                            </option>
                                        <?php 
                                            endwhile;
                                        } else {
                                            echo '<option value="" disabled>-- Tidak ada kategori --</option>';
                                        }
                                        ?>
                                    </select>
                                    <div class="invalid-feedback">Harap pilih kategori</div>
                                </div>
                                -->
                                
                                <!-- TAMBAH INFORMASI KATEGORI (READ-ONLY) -->
                                <div class="col-md-6">
                                    <label class="form-label">Kategori Barang</label>
                                    <div class="form-control bg-light" readonly>
                                        <?= htmlspecialchars($peminjaman->nama_kategori ?? 'Tidak diketahui') ?>
                                    </div>
                                    <div class="form-text">Kategori tidak bisa diubah karena terikat dengan barang</div>
                                </div>
                            </div>
                            
                            <div class="row mb-4">
                                <div class="col-md-6">
                                    <label for="barang_id" class="form-label">Barang *</label>
                                    <select name="barang_id" class="form-select" id="barang_id" required>
                                        <option value="">Pilih Barang</option>
                                        <?php 
                                        if ($barang && mysqli_num_rows($barang) > 0) {
                                            // Reset pointer result set
                                            mysqli_data_seek($barang, 0);
                                            while($item = mysqli_fetch_assoc($barang)): 
                                                $selected = ($peminjaman->barang_id ?? '') == $item['id'] ? 'selected' : '';
                                        ?>
                                            <option value="<?= $item['id'] ?>" <?= $selected ?>>
                                                <?= htmlspecialchars($item['nama_barang']) ?>
                                            </option>
                                        <?php 
                                            endwhile;
                                        } else {
                                            echo '<option value="" disabled>-- Tidak ada barang --</option>';
                                        }
                                        ?>
                                    </select>
                                    <div class="invalid-feedback">Harap pilih barang</div>
                                </div>
                                <div class="col-md-6">
                                    <label for="jumlah" class="form-label">Jumlah *</label>
                                    <input type="number" name="jumlah" class="form-control" id="jumlah"
                                        placeholder="Masukkan jumlah" required min="1"
                                        value="<?= $peminjaman->jumlah ?? '1' ?>">
                                    <div class="invalid-feedback">Jumlah harus lebih dari 0</div>
                                </div>
                            </div>
                            
                            <div class="row mb-4">
                                <div class="col-md-6">
                                    <label for="tgl_pinjam" class="form-label">Tanggal Pinjam *</label>
                                    <input type="date" name="tgl_pinjam" class="form-control" id="tgl_pinjam"
                                        value="<?= isset($peminjaman->tgl_pinjam) && $peminjaman->tgl_pinjam != '0000-00-00' ? date('Y-m-d', strtotime($peminjaman->tgl_pinjam)) : date('Y-m-d') ?>" required>
                                    <div class="invalid-feedback">Harap pilih tanggal pinjam</div>
                                </div>
                                <div class="col-md-6">
                                    <label for="tgl_kembali_rencana" class="form-label">Tanggal Kembali (Rencana) *</label>
                                    <input type="date" name="tgl_kembali_rencana" class="form-control" id="tgl_kembali_rencana" 
                                        value="<?= isset($peminjaman->tgl_kembali_rencana) && $peminjaman->tgl_kembali_rencana != '0000-00-00' ? date('Y-m-d', strtotime($peminjaman->tgl_kembali_rencana)) : date('Y-m-d', strtotime('+3 days')) ?>" required>
                                    <div class="invalid-feedback">Harap pilih tanggal kembali rencana</div>
                                </div>
                            </div>
                            
                            <div class="row mb-4">
                                <div class="col-md-6">
                                    <label for="tgl_kembali_aktual" class="form-label">Tanggal Kembali (Aktual)</label>
                                    <input type="date" name="tgl_kembali_aktual" class="form-control" id="tgl_kembali_aktual"
                                        value="<?= isset($peminjaman->tgl_kembali_aktual) && $peminjaman->tgl_kembali_aktual != '0000-00-00' ? date('Y-m-d', strtotime($peminjaman->tgl_kembali_aktual)) : '' ?>">
                                    <div class="form-text">Kosongkan jika belum dikembalikan</div>
                                </div>
                                <div class="col-md-6">
                                    <label for="status" class="form-label">Status Peminjaman *</label>
                                    <select name="status" class="form-select" id="status" required>
                                        <option value="pending" <?= ($peminjaman->status ?? 'pending') == 'pending' ? 'selected' : '' ?>>Pending</option>
                                        <option value="disetujui" <?= ($peminjaman->status ?? 'pending') == 'disetujui' ? 'selected' : '' ?>>Disetujui</option>
                                        <option value="ditolak" <?= ($peminjaman->status ?? 'pending') == 'ditolak' ? 'selected' : '' ?>>Ditolak</option>
                                        <option value="dipinjam" <?= ($peminjaman->status ?? 'pending') == 'dipinjam' ? 'selected' : '' ?>>Dipinjam</option>
                                        <!-- PERBAIKI: Status 'dikembalikan' seharusnya 'selesai' -->
                                        <option value="selesai" <?= ($peminjaman->status ?? 'pending') == 'selesai' ? 'selected' : '' ?>>Selesai</option>
                                        <option value="terlambat" <?= ($peminjaman->status ?? 'pending') == 'terlambat' ? 'selected' : '' ?>>Terlambat</option>
                                    </select>
                                    <div class="form-text">
                                        Pilih status sesuai kondisi peminjaman saat ini
                                    </div>
                                </div>
                            </div>
                            
                            <div class="mb-4">
                                <label for="keterangan" class="form-label">Keterangan</label>
                                <textarea name="keterangan" class="form-control" id="keterangan" rows="3"
                                    placeholder="Masukkan keterangan tambahan (opsional)"><?= htmlspecialchars($peminjaman->keterangan ?? '') ?></textarea>
                            </div>
                            
                            <div class="mb-4">
                                <div class="form-text" id="lama_pinjam_info">
                                    <?php
                                    if (isset($peminjaman->tgl_pinjam) && isset($peminjaman->tgl_kembali_rencana) && 
                                        $peminjaman->tgl_pinjam != '0000-00-00' && $peminjaman->tgl_kembali_rencana != '0000-00-00') {
                                        $tgl1 = new DateTime($peminjaman->tgl_pinjam);
                                        $tgl2 = new DateTime($peminjaman->tgl_kembali_rencana);
                                        $diff = $tgl1->diff($tgl2)->days;
                                        echo "Lama pinjam: " . $diff . " hari";
                                    } else {
                                        echo "Lama pinjam: 0 hari";
                                    }
                                    ?>
                                </div>
                            </div>
                            
                            <div class="mb-4">
                                <div class="form-text" id="total_harga_info">
                                    <?php
                                    if (isset($peminjaman->total_harga)) {
                                        echo "Total harga: Rp " . number_format($peminjaman->total_harga, 0, ',', '.');
                                    } else {
                                        echo "Total harga: Rp 0";
                                    }
                                    ?>
                                </div>
                            </div>
                            
                            <div class="d-flex justify-content-between mt-5">
                                <a href="./index.php" class="btn btn-outline-secondary">
                                    <i class="fas fa-times"></i> Batal
                                </a>
                                <button type="submit" class="btn btn-success">
                                    <i class="fas fa-save"></i> Simpan Perubahan
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
                    </div>
        </div>
    </div>
</div>
<?php include '../../partials/footer.php'; ?>

<?php include '../../partials/script.php'; ?>

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

// Hitung lama pinjam
function hitungLamaPinjam() {
    const tglPinjam = document.getElementById('tgl_pinjam').value;
    const tglKembali = document.getElementById('tgl_kembali_rencana').value;
    const infoElement = document.getElementById('lama_pinjam_info');
    
    if (tglPinjam && tglKembali) {
        const tgl1 = new Date(tglPinjam);
        const tgl2 = new Date(tglKembali);
        
        // Pastikan tanggal kembali setelah tanggal pinjam
        if (tgl2 < tgl1) {
            infoElement.innerHTML = '<span class="text-danger">Tanggal kembali harus setelah tanggal pinjam!</span>';
            return;
        }
        
        const diffTime = Math.abs(tgl2 - tgl1);
        const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
        
        infoElement.textContent = 'Lama pinjam: ' + diffDays + ' hari';
    }
}

// Event listeners untuk tanggal
document.getElementById('tgl_pinjam').addEventListener('change', hitungLamaPinjam);
document.getElementById('tgl_kembali_rencana').addEventListener('change', hitungLamaPinjam);

// Validasi tanggal kembali tidak boleh sebelum tanggal pinjam
document.getElementById('tgl_kembali_rencana').addEventListener('change', function() {
    const tglPinjam = document.getElementById('tgl_pinjam').value;
    const tglKembali = this.value;
    
    if (tglPinjam && tglKembali) {
        const tgl1 = new Date(tglPinjam);
        const tgl2 = new Date(tglKembali);
        
        if (tgl2 < tgl1) {
            alert('Tanggal kembali tidak boleh sebelum tanggal pinjam!');
            this.value = '';
        }
    }
});

// Inisialisasi
document.addEventListener('DOMContentLoaded', function() {
    // Trigger change event untuk menghitung lama pinjam awal
    hitungLamaPinjam();
});
</script>