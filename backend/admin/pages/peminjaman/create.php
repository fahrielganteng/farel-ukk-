<?php
// AKTIFKAN ERROR REPORTING
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Pastikan session_start() di awal
session_start();

include '../../app.php';
include '../../partials/header.php';
$page = 'peminjaman';
include '../../partials/sidebar.php';

// Reset pointer query
$users = mysqli_query($connect, "SELECT id, username FROM users WHERE role = 'peminjam'");
$barang = mysqli_query($connect, "SELECT id, nama_barang, stok FROM barang WHERE stok > 0");
$kategori = mysqli_query($connect, "SELECT id, nama_kategori FROM kategori ORDER BY nama_kategori");
?>


<div class="content-wrapper">
    <div class="row justify-content-center">
        <div class="col-lg-10 col-xl-8">
            <div class="card-admin border-0 shadow-sm mt-4">
            <div class="card-header-admin">
                <div class="header-container">
                    <h4 class="fw-bold text-primary page-title">
                        <i class="fas fa-plus-circle"></i> Tambah Data Peminjaman
                    </h4>
                    <a href="./index.php" class="btn btn-primary">
                        <i class="fas fa-arrow-left"></i> Kembali
                    </a>
                </div>
            </div>
            
            <div class="card-body-admin">
                <!-- Tampilkan pesan error/success -->
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
                
                <div class="row justify-content-center">
                    <div class="col-md-8">
                        <form action="../../action/peminjaman/store.php" method="POST" class="needs-validation" novalidate>
                            <div class="mb-4">
                                <label for="kode_peminjaman" class="form-label">Kode Peminjaman *</label>
                                <input type="text" name="kode_peminjaman" class="form-control" id="kode_peminjaman"
                                    placeholder="Contoh: PINJ-001" required
                                    value="<?php echo 'PINJ-' . date('Ymd') . sprintf('%03d', rand(1, 999)); ?>">
                                <div class="invalid-feedback">Harap isi kode peminjaman</div>
                                <div class="form-text">Kode peminjaman harus unik</div>
                            </div>
                            
                            <div class="row mb-4">
                                <div class="col-md-6">
                                    <label for="user_id" class="form-label">Peminjam *</label>
                                    <select name="user_id" class="form-select" id="user_id" required>
                                        <option value="">Pilih Peminjam</option>
                                        <?php 
                                        if ($users && mysqli_num_rows($users) > 0) {
                                            while($user = mysqli_fetch_assoc($users)): 
                                        ?>
                                            <option value="<?= $user['id'] ?>" <?= (isset($_POST['user_id']) && $_POST['user_id'] == $user['id']) ? 'selected' : '' ?>>
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
                                <div class="col-md-6">
                                    <label for="kategori_id" class="form-label">Kategori *</label>
                                    <select name="kategori_id" class="form-select" id="kategori_id" required>
                                        <option value="">Pilih Kategori</option>
                                        <?php 
                                        if ($kategori && mysqli_num_rows($kategori) > 0) {
                                            while($kat = mysqli_fetch_assoc($kategori)): 
                                        ?>
                                            <option value="<?= $kat['id'] ?>" <?= (isset($_POST['kategori_id']) && $_POST['kategori_id'] == $kat['id']) ? 'selected' : '' ?>>
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
                            </div>
                            
                            <div class="row mb-4">
                                <div class="col-md-6">
                                    <label for="barang_id" class="form-label">Barang *</label>
                                    <select name="barang_id" class="form-select" id="barang_id" required>
                                        <option value="">Pilih Barang</option>
                                        <?php 
                                        if ($barang && mysqli_num_rows($barang) > 0) {
                                            while($item = mysqli_fetch_assoc($barang)): 
                                        ?>
                                            <option value="<?= $item['id'] ?>" data-stok="<?= $item['stok'] ?>" <?= (isset($_POST['barang_id']) && $_POST['barang_id'] == $item['id']) ? 'selected' : '' ?>>
                                                <?= htmlspecialchars($item['nama_barang']) ?> (Stok: <?= $item['stok'] ?>)
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
                                        placeholder="Masukkan jumlah" required min="1" max="1">
                                    <div class="invalid-feedback">Jumlah harus lebih dari 0</div>
                                    <div class="form-text" id="stok-info">Maksimum: 1</div>
                                </div>
                            </div>
                            
                            <div class="row mb-4">
                                <div class="col-md-6">
                                    <label for="tgl_pinjam" class="form-label">Tanggal Pinjam *</label>
                                    <input type="date" name="tgl_pinjam" class="form-control" id="tgl_pinjam"
                                        value="<?= date('Y-m-d') ?>" required>
                                    <div class="invalid-feedback">Harap pilih tanggal pinjam</div>
                                </div>
                                <div class="col-md-6">
                                    <label for="tgl_kembali_rencana" class="form-label">Tanggal Kembali (Rencana) *</label>
                                    <input type="date" name="tgl_kembali_rencana" class="form-control" id="tgl_kembali_rencana" required>
                                    <div class="invalid-feedback">Harap pilih tanggal kembali rencana</div>
                                    <div class="form-text" id="lama_pinjam_info">Lama pinjam: 0 hari</div>
                                </div>
                            </div>

                            <div class="mb-4">
                                <label for="status" class="form-label">Status Peminjaman *</label>
                                <select name="status" class="form-select" id="status" required>
                                    <option value="pending" selected>Pending</option>
                                    <option value="disetujui">Disetujui</option>
                                    <option value="dipinjam">Dipinjam</option>
                                    <option value="ditolak">Ditolak</option>
                                </select>
                                <div class="form-text">
                                    Default status adalah "Pending"
                                </div>
                            </div>
                            
                            <div class="d-flex justify-content-between mt-5">
                                <a href="./index.php" class="btn btn-outline-secondary">
                                    <i class="fas fa-times"></i> Batal
                                </a>
                                <button type="submit" class="btn btn-success" name="tombol">
                                    <i class="fas fa-plus"></i> Tambah Peminjaman
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

// Update max jumlah berdasarkan stok barang
document.getElementById('barang_id').addEventListener('change', function() {
    const selectedOption = this.options[this.selectedIndex];
    const stok = selectedOption.getAttribute('data-stok');
    const jumlahInput = document.getElementById('jumlah');
    const stokInfo = document.getElementById('stok-info');
    
    if (stok && stok > 0) {
        jumlahInput.max = stok;
        stokInfo.textContent = 'Maksimum: ' + stok;
    } else {
        jumlahInput.max = 1;
        stokInfo.textContent = 'Maksimum: 1';
        jumlahInput.value = 1;
    }
});

// Hitung lama pinjam
function hitungLamaPinjam() {
    const tglPinjam = document.getElementById('tgl_pinjam').value;
    const tglKembali = document.getElementById('tgl_kembali_rencana').value;
    const infoElement = document.getElementById('lama_pinjam_info');
    
    if (tglPinjam && tglKembali) {
        const tgl1 = new Date(tglPinjam);
        const tgl2 = new Date(tglKembali);
        const diffTime = Math.abs(tgl2 - tgl1);
        const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
        
        infoElement.textContent = 'Lama pinjam: ' + diffDays + ' hari';
    }
}

document.getElementById('tgl_pinjam').addEventListener('change', hitungLamaPinjam);
document.getElementById('tgl_kembali_rencana').addEventListener('change', hitungLamaPinjam);

// Set tanggal kembali minimal besok
document.addEventListener('DOMContentLoaded', function() {
    const today = new Date();
    const tomorrow = new Date(today);
    tomorrow.setDate(tomorrow.getDate() + 1);
    
    const tglPinjamInput = document.getElementById('tgl_pinjam');
    const tglKembaliInput = document.getElementById('tgl_kembali_rencana');
    
    tglPinjamInput.min = today.toISOString().split('T')[0];
    tglKembaliInput.min = tomorrow.toISOString().split('T')[0];
    
    // Set default tgl kembali rencana 3 hari dari sekarang
    const defaultKembali = new Date(today);
    defaultKembali.setDate(defaultKembali.getDate() + 3);
    tglKembaliInput.value = defaultKembali.toISOString().split('T')[0];
    
    hitungLamaPinjam();
});
</script>