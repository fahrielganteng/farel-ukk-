<?php
// AKTIFKAN ERROR REPORTING
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Pastikan session_start() di awal
session_start();

// Include koneksi database
include '../../app.php';

// Query semua user
$qUser = "SELECT * FROM users ORDER BY id DESC";
$result = mysqli_query($connect, $qUser);

if (!$result) {
    die("Query error: " . mysqli_error($connect));
}

// Simpan data ke array untuk digunakan nanti
$users = [];
while ($row = mysqli_fetch_assoc($result)) {
    $users[] = $row;
}

// Hitung total data
$totalUsers = count($users);
?>

<?php 
include '../../partials/header.php'; 
$page = 'user'; 
include '../../partials/sidebar.php'; 
?>

<div class="content-wrapper">
    <!-- Main Card -->
    <div class="card-admin">
        <!-- Card Header -->
        <div class="card-header-admin d-flex align-items-center justify-content-between">
            <h2 class="page-title">
                <i class="fas fa-users"></i>
                Data User
            </h2>
            <a href="./create.php" class="btn btn btn-primary">
                <i class="fas fa-plus-circle"></i>
                Tambah User
            </a>
        </div>
        
        <!-- Card Body -->
        <div class="card-body-admin">
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
            
            <!-- Info Stats -->
            <div class="alert alert-info d-flex align-items-center justify-content-between">
                <div>
                    <i class="fas fa-info-circle"></i>
                    <strong>Total <?php echo $totalUsers; ?> User Terdaftar</strong>
                </div>
                <?php if ($totalUsers > 0): ?>
                    <div class="d-none d-md-flex">
                        <?php 
                        $adminCount = 0;
                        $petugasCount = 0;
                        $peminjamCount = 0;
                        foreach ($users as $user) {
                            if ($user['role'] == 'admin') $adminCount++;
                            if ($user['role'] == 'petugas') $petugasCount++;
                            if ($user['role'] == 'peminjam') $peminjamCount++;
                        }
                        ?>
                        <span class="stats-badge badge-admin">
                            <i class="fas fa-user-shield me-1"></i> Admin: <?php echo $adminCount; ?>
                        </span>
                        <span class="stats-badge badge-petugas">
                            <i class="fas fa-user-tie me-1"></i> Petugas: <?php echo $petugasCount; ?>
                        </span>
                        <span class="stats-badge badge-peminjam">
                            <i class="fas fa-user me-1"></i> Peminjam: <?php echo $peminjamCount; ?>
                        </span>
                    </div>
                <?php endif; ?>
            </div>
            
            <!-- Users Table -->
            <?php if ($totalUsers > 0): ?>
                <div class="table-responsive">
                    <table id="userTable" class="table-admin table-hover">
                        <thead>
                            <tr>
                                <th class="text-center" style="width: 60px;">No</th>
                                <th>Username</th>
                                <th>Informasi Kontak</th>
                                <th class="text-center">Role</th>
                                <th class="text-center">Bergabung</th>
                                <th class="text-center" style="width: 150px;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($users as $index => $item): ?>
                            <tr>
                                <td class="text-center fw-bold text-slate-400"><?= $index + 1 ?></td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="rounded-3 bg-slate-100 text-slate-500 d-flex align-items-center justify-content-center me-3" style="width: 40px; height: 40px;">
                                            <i class="fas fa-user"></i>
                                        </div>
                                        <div>
                                            <div class="fw-700 text-dark"><?= htmlspecialchars($item['username']) ?></div>
                                            <small class="text-slate-400">ID: #USR-<?= $item['id'] ?></small>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div class="fw-600 text-slate-700"><?= htmlspecialchars($item['nama_lengkap']) ?></div>
                                    <div class="small text-slate-400">
                                        <i class="fas fa-envelope me-1"></i> <?= !empty($item['email']) ? htmlspecialchars($item['email']) : '-' ?>
                                    </div>
                                </td>
                                <td class="text-center">
                                    <?php 
                                        $roleClass = 'status-available'; // Default
                                        if($item['role'] == 'admin') $roleClass = 'status-warning';
                                        else if($item['role'] == 'petugas') $roleClass = 'status-disetujui';
                                    ?>
                                    <span class="status-badge <?= $roleClass ?>">
                                        <i class="fas fa-<?= 
                                            $item['role'] == 'admin' ? 'user-shield' : 
                                            ($item['role'] == 'petugas' ? 'user-tie' : 'user') 
                                        ?>"></i>
                                        <?= ucfirst($item['role']) ?>
                                    </span>
                                </td>
                                <td class="text-center">
                                    <div class="fw-600 text-slate-700"><?= date('d M Y', strtotime($item['created_at'])) ?></div>
                                    <div class="small text-slate-400"><?= date('H:i', strtotime($item['created_at'])) ?></div>
                                </td>
                                <td class="text-center">
                                    <div class="d-flex justify-content-center gap-2">    
                                        <a href="./detail.php?id=<?= $item['id'] ?>" class="btn btn-sm btn-light border" title="Detail">
                                            <i class="fas fa-eye text-slate-600"></i>
                                        </a>
                                        <a href="./edit.php?id=<?= $item['id'] ?>" class="btn btn-sm btn-light border" title="Edit">
                                            <i class="fas fa-edit text-indigo-600"></i>
                                        </a>
                                        <a href="javascript:void(0)" 
                                           onclick="confirmDelete('<?= addslashes($item['username']) ?>', <?= $item['id'] ?>)"
                                           class="btn btn-sm btn-light border" title="Hapus">
                                            <i class="fas fa-trash text-danger"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <div class="text-center py-5">
                    <i class="fas fa-users-slash fa-4x text-slate-200 mb-3"></i>
                    <h5 class="text-slate-400">Belum Ada Data User</h5>
                    <p class="text-slate-300">Mulai dengan menambahkan user baru menggunakan tombol di atas</p>
                </div>
            <?php endif; ?>
            
            <!-- Footer -->
            <div class="mt-5 pt-4 border-top">
                <p class="text-center text-slate-400 small mb-0">
                    &copy; <?= date('Y') ?> HeavyHire Management System. All Rights Reserved.
                </p>
            </div>
        </div>
    </div>
</div>

<?php include '../../partials/script.php'; ?>

<!-- DataTables CSS & JS -->
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
<script type="text/javascript" charset="utf8" src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
<script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>

<!-- SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<style>
/* Custom styling untuk DataTable */
.dataTables_wrapper .dataTables_length,
.dataTables_wrapper .dataTables_filter {
    margin-bottom: 20px;
}

.dataTables_wrapper .dataTables_filter input {
    margin-left: 10px;
    padding: 8px 15px;
    border-radius: 8px;
    border: 1px solid #e2e8f0;
    width: 300px;
}

.dataTables_wrapper .dataTables_filter input:focus {
    outline: none;
    border-color: #4361ee;
    box-shadow: 0 0 0 3px rgba(67, 97, 238, 0.1);
}

.dataTables_wrapper .dataTables_length select {
    margin: 0 10px;
    padding: 8px 15px;
    border-radius: 8px;
    border: 1px solid #e2e8f0;
    background-color: white;
}

.dataTables_wrapper .dataTables_length select:focus {
    outline: none;
    border-color: #4361ee;
}

.dataTables_wrapper .dataTables_info {
    padding-top: 20px;
    color: #64748b;
    font-size: 14px;
}

.dataTables_wrapper .dataTables_paginate {
    padding-top: 20px;
}

.dataTables_wrapper .paginate_button {
    padding: 8px 14px !important;
    margin: 0 3px !important;
    border-radius: 8px !important;
    border: 1px solid #e2e8f0 !important;
    background: white !important;
    color: #1e293b !important;
    cursor: pointer !important;
    transition: all 0.3s ease !important;
}

.dataTables_wrapper .paginate_button.current {
    background: #4361ee !important;
    color: white !important;
    border-color: #4361ee !important;
}

.dataTables_wrapper .paginate_button:hover {
    background: #f1f5f9 !important;
    color: #4361ee !important;
    border-color: #cbd5e1 !important;
}

.dataTables_wrapper .paginate_button.current:hover {
    background: #4361ee !important;
    color: white !important;
}

.dataTables_wrapper .dataTables_processing {
    background: rgba(255, 255, 255, 0.9);
    border-radius: 8px;
    padding: 15px;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
}

/* Hover effect untuk tabel */
#userTable tbody tr {
    transition: all 0.3s ease;
    cursor: pointer;
}

#userTable tbody tr:hover {
    background-color: #f8fafc;
    transform: translateX(5px);
}
</style>

<script>
$(document).ready(function() {
    // Initialize DataTable
    if ($('#userTable').length > 0) {
        var table = $('#userTable').DataTable({
            language: {
                processing: "Memproses...",
                search: "🔍 Cari:",
                searchPlaceholder: "Cari username, nama, atau email...",
                lengthMenu: "Tampilkan _MENU_ user per halaman",
                zeroRecords: "Tidak ada data yang ditemukan",
                info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ user",
                infoEmpty: "Menampilkan 0 sampai 0 dari 0 user",
                infoFiltered: "(disaring dari _MAX_ total user)",
                paginate: {
                    first: "« Pertama",
                    last: "Terakhir »",
                    next: "Berikutnya →",
                    previous: "← Sebelumnya"
                }
            },
            pageLength: 10,
            lengthMenu: [[5, 10, 25, 50, -1], [5, 10, 25, 50, "Semua"]],
            order: [[0, 'asc']], // Sort by No column
            columnDefs: [
                {
                    targets: 0, // No column
                    orderable: false,
                    searchable: false,
                    className: 'text-center align-middle'
                },
                {
                    targets: 1, // Username column
                    orderable: true,
                    searchable: true,
                    className: 'align-middle'
                },
                {
                    targets: 2, // Informasi Kontak column
                    orderable: true,
                    searchable: true,
                    className: 'align-middle'
                },
                {
                    targets: 3, // Role column
                    orderable: true,
                    searchable: true,
                    className: 'text-center align-middle'
                },
                {
                    targets: 4, // Bergabung column
                    orderable: true,
                    searchable: false,
                    className: 'text-center align-middle'
                },
                {
                    targets: 5, // Aksi column
                    orderable: false,
                    searchable: false,
                    className: 'text-center align-middle'
                }
            ],
            drawCallback: function() {
                // Add animation to rows
                $('#userTable tbody tr').each(function(index) {
                    $(this).css('opacity', '0');
                    $(this).delay(index * 50).animate({ opacity: 1 }, 300);
                });
            },
            initComplete: function() {
                // Add placeholder to search box
                $('.dataTables_filter input').attr('placeholder', 'Cari username, nama, atau email...');
                $('.dataTables_filter input').css('padding', '10px 15px');
                
                // Add custom class for styling
                $('.dataTables_length select').addClass('form-select');
            }
        });
        
        // Optional: Add custom search highlight effect
        $('#userTable tbody').on('mouseenter', 'tr', function() {
            $(this).addClass('bg-slate-50');
        }).on('mouseleave', 'tr', function() {
            $(this).removeClass('bg-slate-50');
        });
    }
});

// SweetAlert2 confirmation for delete
function confirmDelete(username, userId) {
    Swal.fire({
        title: 'Apakah Anda yakin?',
        text: `User "${username}" akan dihapus secara permanen!`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Ya, hapus!',
        cancelButtonText: 'Batal',
        reverseButtons: true,
        showLoaderOnConfirm: true,
        preConfirm: async () => {
            try {
                // Redirect to delete action
                window.location.href = `../../action/user/destroy.php?id=${userId}`;
            } catch (error) {
                Swal.showValidationMessage(`Request failed: ${error}`);
            }
        },
        allowOutsideClick: () => !Swal.isLoading()
    }).then((result) => {
        if (result.isConfirmed) {
            Swal.fire({
                title: 'Terhapus!',
                text: 'User berhasil dihapus.',
                icon: 'success',
                timer: 1500,
                showConfirmButton: false
            }).then(() => {
                // Reload page after 1.5 seconds
                location.reload();
            });
        }
    });
}

// Auto-hide alerts after 3 seconds
$(document).ready(function() {
    setTimeout(function() {
        $('.alert').fadeOut('slow');
    }, 3000);
});

// Add animation to stats badges
$(document).ready(function() {
    $('.stats-badge').each(function(index) {
        $(this).css('opacity', '0');
        $(this).delay(index * 200).animate({ opacity: 1 }, 500);
    });
});
</script>           