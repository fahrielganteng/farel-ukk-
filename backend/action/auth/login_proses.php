<?php
session_start();
include '../app.php'; // Path ke app.php di root

// Validasi method POST
if ($_SERVER['REQUEST_METHOD'] != 'POST') {
    header("Location: ../../../pages/auth/login.php");
    exit();
}

// Validasi input
if (empty($_POST['username']) || empty($_POST['password'])) {
    $_SESSION['login_error'] = "Username dan password wajib diisi!";
    header("Location: ../../../pages/auth/login.php?pesan=gagal");
    exit();
}

$username = mysqli_real_escape_string($connect, $_POST['username']);
$raw_pass = $_POST['password'];

// Cek di tabel users
$qUser = mysqli_query($connect, "SELECT * FROM users WHERE username='$username' LIMIT 1");

if (!$qUser) {
    $_SESSION['login_error'] = "Terjadi kesalahan pada server!";
    header("Location: ../../../../pages/auth/login.php?pesan=gagal");
    exit();
}

if (mysqli_num_rows($qUser) > 0) {
    $data = mysqli_fetch_assoc($qUser);
    
    // Verifikasi password
    if (password_verify($raw_pass, $data['password'])) {
        // Set session
        $_SESSION['user_id'] = $data['id'];
        $_SESSION['username'] = $data['username'];
        $_SESSION['role'] = $data['role'];
        $_SESSION['nama_lengkap'] = $data['nama_lengkap'];
        $_SESSION['status'] = "login";

        // Redirect berdasarkan role
        switch($data['role']) {
            case 'admin':
                header("Location: ../../admin/pages/dashboard/index.php");
                exit();
                
            case 'petugas':
                header("Location: ../../petugas/pages/dashboard/index.php");
                exit();
                
            case 'peminjam':
                header("Location: ../../peminjaman/pages/dashboard/index.php");
                exit();
                
            default:
                $_SESSION['login_error'] = "Role tidak valid! Hubungi administrator.";
                header("Location: ../../../pages/auth/login.php?pesan=gagal");
                exit();
        }
    } else {
        // Password salah
        $_SESSION['login_error'] = "Password salah! Silakan coba lagi.";
        header("Location: ../../pages/auth/login.php?pesan=gagal");
        exit();
    }
} else {
    // Username tidak ditemukan
    $_SESSION['login_error'] = "Username tidak ditemukan!";
    header("Location: ../../pages/auth/login.php?pesan=gagal");
    exit();
}
?>