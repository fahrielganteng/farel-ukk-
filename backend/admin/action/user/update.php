<?php
session_start();
include '../../app.php';

// Ambil ID dari URL dan POST
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if (isset($_POST['tombol'])) {
    $username = escapeString($_POST['username']);
    $nama_lengkap = escapeString($_POST['nama_lengkap']);
    $email = escapeString($_POST['email'] ?? '');
    $no_telp = escapeString($_POST['no_telp'] ?? '');
    $alamat = escapeString($_POST['alamat'] ?? '');
    $role = escapeString($_POST['role']);

    // Cek apakah user dengan ID ini ada
    $checkId = mysqli_query($connect, "SELECT * FROM users WHERE id = $id");
    if (mysqli_num_rows($checkId) == 0) {
        $_SESSION['error'] = "User dengan ID $id tidak ditemukan.";
        header("Location: ../../pages/user/index.php");
        exit;
    }

    // Cek apakah username sudah digunakan oleh user lain
    $checkUsername = mysqli_query($connect, "SELECT * FROM users WHERE username = '$username' AND id != $id");
    if (mysqli_num_rows($checkUsername) > 0) {
        $_SESSION['error'] = "Username sudah digunakan oleh user lain.";
        header("Location: ../../pages/user/edit.php?id=$id");
        exit;
    }

    // Cek apakah email sudah digunakan oleh user lain
    if (!empty($email)) {
        $checkEmail = mysqli_query($connect, "SELECT * FROM users WHERE email = '$email' AND id != $id");
        if (mysqli_num_rows($checkEmail) > 0) {
            $_SESSION['error'] = "Email sudah digunakan oleh user lain.";
            header("Location: ../../pages/user/edit.php?id=$id");
            exit;
        }
    }

    // Jika password diisi, update password
    if (!empty($_POST['password'])) {
        $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
        $qUpdate = "UPDATE users SET 
                    username = '$username', 
                    nama_lengkap = '$nama_lengkap',
                    email = '$email',
                    no_telp = '$no_telp',
                    alamat = '$alamat',
                    password = '$password', 
                    role = '$role' 
                    WHERE id = $id";
    } else {
        $qUpdate = "UPDATE users SET 
                    username = '$username', 
                    nama_lengkap = '$nama_lengkap',
                    email = '$email',
                    no_telp = '$no_telp',
                    alamat = '$alamat',
                    role = '$role' 
                    WHERE id = $id";
    }

    $result = mysqli_query($connect, $qUpdate);
    if ($result) {
        $_SESSION['success'] = "Data user ID $id berhasil diupdate!";
        header("Location: ../../pages/user/index.php");
        exit;
    } else {
        $_SESSION['error'] = "Gagal mengupdate user: " . mysqli_error($connect);
        header("Location: ../../pages/user/edit.php?id=$id");
        exit;
    }
} else {
    $_SESSION['error'] = "Akses tidak valid!";
    header("Location: ../../pages/user/index.php");
    exit;
}
?>