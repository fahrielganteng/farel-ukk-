<?php
session_start();
include "../../app.php";

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    die("Akses tidak valid!");
}

// Ambil data dari form
$username = escapeString($_POST['username']);
$nama_lengkap = escapeString($_POST['nama_lengkap']);
$email = escapeString($_POST['email'] ?? '');
$no_telp = escapeString($_POST['no_telp'] ?? '');
$alamat = escapeString($_POST['alamat'] ?? '');
$password = $_POST['password'];
$confirm_password = $_POST['confirm_password'];
$role = 'peminjam'; // Default role untuk registrasi

// Validasi input
$errors = [];

// Validasi username
if (empty($username)) {
    $errors[] = "Username harus diisi.";
} elseif (strlen($username) < 3) {
    $errors[] = "Username minimal 3 karakter.";
}

// Validasi nama lengkap
if (empty($nama_lengkap)) {
    $errors[] = "Nama lengkap harus diisi.";
}

// Validasi password
if (empty($password)) {
    $errors[] = "Password harus diisi.";
} elseif (strlen($password) < 6) {
    $errors[] = "Password minimal 6 karakter.";
} elseif ($password !== $confirm_password) {
    $errors[] = "Konfirmasi password tidak sama.";
}

// Validasi email jika diisi
if (!empty($email) && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $errors[] = "Format email tidak valid.";
}

// Jika ada error, kembali ke form register
if (!empty($errors)) {
    $_SESSION['register_errors'] = $errors;
    $_SESSION['register_data'] = [
        'username' => $username,
        'nama_lengkap' => $nama_lengkap,
        'email' => $email,
        'no_telp' => $no_telp,
        'alamat' => $alamat
    ];
    header("Location: ../../pages/auth/register.php");
    exit;
}

// Cek apakah username sudah digunakan di tabel users
$checkUsername = mysqli_query($connect, "SELECT * FROM users WHERE username = '$username'");
if (mysqli_num_rows($checkUsername) > 0) {
    $_SESSION['register_errors'] = ["Username '$username' sudah digunakan, silakan pilih username lain."];
    $_SESSION['register_data'] = [
        'username' => $username,
        'nama_lengkap' => $nama_lengkap,
        'email' => $email,
        'no_telp' => $no_telp,
        'alamat' => $alamat
    ];
    header("Location: ../../pages/auth/register.php");
    exit;
}

// Cek apakah email sudah digunakan (jika diisi)
if (!empty($email)) {
    $checkEmail = mysqli_query($connect, "SELECT * FROM users WHERE email = '$email'");
    if (mysqli_num_rows($checkEmail) > 0) {
        $_SESSION['register_errors'] = ["Email '$email' sudah digunakan, silakan gunakan email lain."];
        $_SESSION['register_data'] = [
            'username' => $username,
            'nama_lengkap' => $nama_lengkap,
            'email' => $email,
            'no_telp' => $no_telp,
            'alamat' => $alamat
        ];
        header("Location: ../../pages/auth/register.php");
        exit;
    }
}

// Hash password
$hashed_password = password_hash($password, PASSWORD_DEFAULT);

// Query insert ke tabel users (tanpa ID karena auto_increment)
$qInsert = "INSERT INTO users (username, nama_lengkap, email, no_telp, alamat, password, role, created_at) 
            VALUES ('$username', '$nama_lengkap', '$email', '$no_telp', '$alamat', '$hashed_password', '$role', NOW())";

if (mysqli_query($connect, $qInsert)) {
    $_SESSION['register_success'] = "Registrasi berhasil! Silakan login dengan username '$username'.";
    header("Location: ../../pages/auth/login.php");
    exit;
} else {
    $_SESSION['register_errors'] = ["Gagal melakukan registrasi: " . mysqli_error($connect)];
    $_SESSION['register_data'] = [
        'username' => $username,
        'nama_lengkap' => $nama_lengkap,
        'email' => $email,
        'no_telp' => $no_telp,
        'alamat' => $alamat
    ];
    header("Location: ../../pages/auth/register.php");
    exit;
}
?>