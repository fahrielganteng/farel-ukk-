<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

$page = 'alat';

if (!isset($_SESSION['user_id']) || !isset($_SESSION['status']) || $_SESSION['status'] != 'login') {
    header("Location: ../../../pages/auth/login.php?pesan=belum_login");
    exit();
}

include '../../app.php';

// Query semua alat berat
$qAlat = "SELECT * FROM barang ORDER BY id DESC";
$result = mysqli_query($connect, $qAlat);

if (!$result) {
    die("Query error: " . mysqli_error($connect));
}

$alats = [];
while ($row = mysqli_fetch_assoc($result)) {
    $alats[] = $row;
}

$totalAlats = count($alats);
?>

<?php 
include '../../partials/header.php'; 
include '../../partials/sidebar.php'; 
?>

<!-- Custom CSS for enhanced styling -->
<style>
:root {
    --primary-gradient: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    --success-gradient: linear-gradient(135deg, #84fab0 0%, #8fd3f4 100%);
    --danger-gradient: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
    --card-shadow: 0 10px 40px rgba(0,0,0,0.08);
    --hover-shadow: 0 15px 50px rgba(0,0,0,0.12);
}

.content-wrapper {
    background: #f8fafc;
    min-height: 100vh;
    padding: 2rem;
}

/* Page Title Styling */
.page-title {
    font-size: 2.2rem;
    font-weight: 600;
    background: var(--primary-gradient);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    margin-bottom: 2rem;
    padding: 1rem 0;
    position: relative;
}

.page-title::after {
    content: '';
    position: absolute;
    bottom: 0;
    left: 0;
    width: 100px;
    height: 4px;
    background: var(--primary-gradient);
    border-radius: 2px;
}

/* Alert Styling */
.alert {
    border: none;
    border-radius: 15px;
    padding: 1rem 1.5rem;
    margin-bottom: 1.5rem;
    box-shadow: 0 5px 20px rgba(0,0,0,0.05);
    animation: slideIn 0.3s ease-out;
}

@keyframes slideIn {
    from {
        transform: translateY(-20px);
        opacity: 0;
    }
    to {
        transform: translateY(0);
        opacity: 1;
    }
}

.alert-success {
    background: linear-gradient(135deg, #e6f9ed 0%, #c8f0d9 100%);
    color: #0a5d2e;
}

.alert-danger {
    background: linear-gradient(135deg, #fee9e7 0%, #fdd4d1 100%);
    color: #9b1c1c;
}

/* Card Styling */
.card-admin {
    background: white;
    border-radius: 25px;
    box-shadow: var(--card-shadow);
    overflow: hidden;
    transition: all 0.3s ease;
}

.card-admin:hover {
    box-shadow: var(--hover-shadow);
}

.card-header-admin {
    background: linear-gradient(135deg, #f8faff 0%, #f0f4ff 100%);
    padding: 1.5rem 2rem;
    border-bottom: 2px solid rgba(102, 126, 234, 0.1);
    font-size: 1.2rem;
    font-weight: 600;
    color: #2d3748;
}

/* Button Styling */
.btn {
    padding: 0.6rem 1.2rem;
    border-radius: 12px;
    font-weight: 500;
    transition: all 0.3s ease;
    border: none;
    position: relative;
    overflow: hidden;
}

.btn::before {
    content: '';
    position: absolute;
    top: 50%;
    left: 50%;
    width: 0;
    height: 0;
    border-radius: 50%;
    background: rgba(255,255,255,0.3);
    transform: translate(-50%, -50%);
    transition: width 0.6s, height 0.6s;
}

.btn:hover::before {
    width: 300px;
    height: 300px;
}

.btn-primary {
    background: var(--primary-gradient);
    color: white;
    box-shadow: 0 5px 15px rgba(102, 126, 234, 0.4);
}

.btn-primary:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(102, 126, 234, 0.5);
}

.btn-light {
    background: white;
    border: 1px solid #e2e8f0;
    color: #4a5568;
}

.btn-light:hover {
    background: #f7fafc;
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(0,0,0,0.1);
}

/* Table Styling */
.table-responsive {
    padding: 1.5rem;
}

.table-admin {
    width: 100%;
    border-collapse: separate;
    border-spacing: 0 12px;
}

.table-admin thead th {
    background: #f8fafc;
    color: #4a5568;
    font-weight: 600;
    font-size: 0.9rem;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    padding: 1rem;
    border: none;
}

.table-admin tbody tr {
    background: white;
    border-radius: 15px;
    box-shadow: 0 4px 15px rgba(0,0,0,0.02);
    transition: all 0.3s ease;
}

.table-admin tbody tr:hover {
    transform: translateY(-3px);
    box-shadow: 0 10px 30px rgba(102, 126, 234, 0.15);
}

.table-admin td {
    padding: 1.2rem 1rem;
    border: none;
    vertical-align: middle;
}

/* Badge Styling */
.badge {
    padding: 0.5rem 1rem;
    border-radius: 25px;
    font-weight: 500;
    font-size: 0.85rem;
    background: linear-gradient(135deg, #f1f5f9 0%, #e9eff7 100%);
    color: #334155;
    box-shadow: 0 2px 8px rgba(0,0,0,0.02);
}

.status-badge {
    padding: 0.6rem 1.2rem;
    border-radius: 30px;
    font-size: 0.85rem;
    font-weight: 600;
    display: inline-flex;
    align-items: center;
    gap: 8px;
    box-shadow: 0 4px 12px rgba(0,0,0,0.05);
}

.status-tersedia {
    background: linear-gradient(135deg, #dcfce7 0%, #bbf7d0 100%);
    color: #166534;
}

.status-rusak {
    background: linear-gradient(135deg, #fee2e2 0%, #fecaca 100%);
    color: #991b1b;
}

/* Text Styling */
.fw-700 {
    font-weight: 700;
    color: #1e293b;
}

.text-slate-400 {
    color: #94a3b8;
}

.text-dark {
    color: #0f172a;
}

/* Empty State Styling */
.empty-state {
    text-align: center;
    padding: 4rem 2rem;
    background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
    border-radius: 25px;
    margin: 1rem;
}

.empty-state i {
    font-size: 4rem;
    background: linear-gradient(135deg, #94a3b8 0%, #64748b 100%);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    margin-bottom: 1.5rem;
}

.empty-state h5 {
    font-size: 1.5rem;
    font-weight: 600;
    color: #334155;
    margin-bottom: 0.5rem;
}

.empty-state p {
    color: #64748b;
}

.empty-state a {
    background: var(--primary-gradient);
    color: white;
    padding: 0.8rem 2rem;
    border-radius: 12px;
    text-decoration: none;
    display: inline-block;
    margin-top: 1.5rem;
    font-weight: 500;
    transition: all 0.3s ease;
    box-shadow: 0 5px 20px rgba(102, 126, 234, 0.3);
}

.empty-state a:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 30px rgba(102, 126, 234, 0.4);
}

/* Action Buttons Group */
.action-buttons {
    display: flex;
    gap: 8px;
    justify-content: center;
}

.action-buttons .btn {
    padding: 0.5rem 1rem;
    border-radius: 10px;
}

/* Responsive Adjustments */
@media (max-width: 768px) {
    .content-wrapper {
        padding: 1rem;
    }
    
    .page-title {
        font-size: 1.8rem;
    }
    
    .table-admin td {
        padding: 1rem 0.8rem;
    }
    
    .action-buttons {
        flex-direction: column;
        gap: 5px;
    }
}
</style>

<!-- Content Wrapper -->
<div class="content-wrapper">
    
    <!-- Page Title -->
    <h1 class="page-title">
        <i class="fas fa-hammer me-3"></i>
        Manajemen Alat Berat
    </h1>

    <!-- Alert Messages -->
    <?php if (isset($_GET['pesan']) && $_GET['pesan'] == 'tambah_sukses'): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle me-2"></i>
            <strong>Berhasil!</strong> Data alat telah ditambahkan.
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php elseif (isset($_GET['pesan']) && $_GET['pesan'] == 'edit_sukses'): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle me-2"></i>
            <strong>Berhasil!</strong> Data alat telah diperbarui.
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php elseif (isset($_GET['pesan']) && $_GET['pesan'] == 'hapus_sukses'): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle me-2"></i>
            <strong>Berhasil!</strong> Data alat telah dihapus.
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php elseif (isset($_GET['pesan']) && $_GET['pesan'] == 'error'): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-circle me-2"></i>
            <strong>Gagal!</strong> Terjadi kesalahan saat memproses data.
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <!-- Main Card -->
    <div class="card-admin">
        <div class="card-header-admin" style="display: flex; justify-content: space-between; align-items: center;">
            <div>
                <i class="fas fa-list me-2"></i>Daftar Alat Berat
                <span class="badge ms-2" style="background: var(--primary-gradient); color: white;">Total: <?php echo $totalAlats; ?></span>
            </div>
            <a href="create.php" class="btn btn-primary">
                <i class="fas fa-plus me-2"></i>Tambah Alat Baru
            </a>
        </div>
        
        <div class="card-body-admin">
            <?php if ($totalAlats > 0): ?>
                <div class="table-responsive">
                    <table class="table-admin">
                        <thead>
                            <tr>
                                <th class="text-center">No</th>
                                <th class="text-center">Gambar</th>
                                <th>Nama Alat</th>
                                <th>Kategori</th>
                                <th class="text-center">Status</th>
                                <th class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            $no = 1;
                            foreach ($alats as $alat): 
                            ?>
                            <tr>
                                <td class="text-center">
                                    <span class="fw-bold" style="color: #94a3b8;">#<?php echo str_pad($no++, 2, '0', STR_PAD_LEFT); ?></span>
                                </td>
                                <td class="text-center">
                                    <?php if (!empty($alat['gambar'])): ?>
                                        <img src="/farel-ukk-/storages/alat/<?php echo htmlspecialchars($alat['gambar']); ?>" alt="Gambar Alat" style="width: 60px; height: 60px; object-fit: cover; border-radius: 8px; border: 1px solid #e2e8f0;">
                                    <?php else: ?>
                                        <div style="width: 60px; height: 60px; background: #f1f5f9; border-radius: 8px; display: flex; align-items: center; justify-content: center; color: #94a3b8; border: 1px dashed #cbd5e1; margin: 0 auto;">
                                            <i class="fas fa-image"></i>
                                        </div>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <div class="fw-700 text-dark"><?php echo htmlspecialchars($alat['nama_barang']); ?></div>
                                    <small style="color: #94a3b8;">
                                        <i class="fas fa-barcode me-1"></i>ID: ALAT-<?php echo str_pad($alat['id'], 4, '0', STR_PAD_LEFT); ?>
                                    </small>
                                </td>
                                <td>
                                    <span class="badge">
                                        <i class="fas fa-tag me-1"></i>
                                        <?php 
                                        // Query kategori
                                        $qKat = "SELECT nama_kategori FROM kategori WHERE id = " . $alat['kategori_id'];
                                        $resKat = mysqli_query($connect, $qKat);
                                        $kat = mysqli_fetch_assoc($resKat);
                                        echo $kat['nama_kategori'] ?? '-';
                                        ?>
                                    </span>
                                </td>
                                <td class="text-center">
                                    <?php $stClass = ($alat['status'] == 'tersedia') ? 'status-tersedia' : 'status-rusak'; ?>
                                    <span class="status-badge <?php echo $stClass; ?>">
                                        <i class="fas <?php echo ($alat['status'] == 'tersedia') ? 'fa-check-circle' : 'fa-exclamation-circle'; ?>"></i>
                                        <?php echo ucfirst($alat['status']); ?>
                                    </span>
                                </td>
                                <td class="text-center">
                                    <div class="action-buttons">
                                        <a href="detail.php?id=<?php echo $alat['id']; ?>" class="btn btn-sm btn-light" title="Detail">
                                            <i class="fas fa-eye" style="color: #667eea;"></i>
                                        </a>
                                        <a href="edit.php?id=<?php echo $alat['id']; ?>" class="btn btn-sm btn-light" title="Edit">
                                            <i class="fas fa-edit" style="color: #764ba2;"></i>
                                        </a>
                                        <a href="../../action/alat/destroy.php?id=<?php echo $alat['id']; ?>" class="btn btn-sm btn-light" title="Hapus" onclick="return confirm('Yakin ingin menghapus data ini?')">
                                            <i class="fas fa-trash" style="color: #f5576c;"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <div class="empty-state">
                    <i class="fas fa-tools"></i>
                    <h5>Belum Ada Data Alat Berat</h5>
                    <p>Mulai dengan menambahkan alat berat pertama Anda</p>
                    <a href="create.php">
                        <i class="fas fa-plus me-2"></i>Tambah Alat Pertama
                    </a>
                </div>
            <?php endif; ?>
        </div>
    </div>

</div><!-- End content-wrapper -->

<?php include '../../partials/footer.php'; ?>
<?php include '../../partials/script.php'; ?>