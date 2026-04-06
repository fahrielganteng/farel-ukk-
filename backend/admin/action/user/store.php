<?php
session_start();
include '../../app.php';

if (isset($_POST['tombol'])) {
    // Ambil ID dari input form
    $id = isset($_POST['id']) ? (int)escapeString($_POST['id']) : 0;
    $username = escapeString($_POST['username']);
    $nama_lengkap = escapeString($_POST['nama_lengkap']);
    $email = escapeString($_POST['email'] ?? '');
    $no_telp = escapeString($_POST['no_telp'] ?? '');
    $alamat = escapeString($_POST['alamat'] ?? '');
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $role = escapeString($_POST['role']);

    // Validasi: ID harus diisi
    if ($id <= 0) {
        $_SESSION['error'] = "ID User harus diisi dan harus angka positif.";
        header("Location: ../../pages/user/create.php");
        exit;
    }

    // Cek apakah ID sudah digunakan
    $checkId = mysqli_query($connect, "SELECT * FROM users WHERE id = $id");
    if (mysqli_num_rows($checkId) > 0) {
        $_SESSION['error'] = "ID $id sudah digunakan, silahkan pilih ID lain.";
        header("Location: ../../pages/user/create.php");
        exit;
    }

    // Validasi username unik
    $checkUser = mysqli_query($connect, "SELECT * FROM users WHERE username = '$username'");
    if (mysqli_num_rows($checkUser) > 0) {
        $_SESSION['error'] = "Username sudah digunakan, silahkan pilih username lain.";
        header("Location: ../../pages/user/create.php");
        exit;
    }

    // Validasi email unik jika diisi
    if (!empty($email)) {
        $checkEmail = mysqli_query($connect, "SELECT * FROM users WHERE email = '$email'");
        if (mysqli_num_rows($checkEmail) > 0) {
            $_SESSION['error'] = "Email sudah digunakan, silahkan gunakan email lain.";
            header("Location: ../../pages/user/create.php");
            exit;
        }
    }

    // Query insert - Sertakan kolom id
    $qInsert = "INSERT INTO users (id, username, nama_lengkap, email, no_telp, alamat, password, role, created_at) 
                VALUES ($id, '$username', '$nama_lengkap', '$email', '$no_telp', '$alamat', '$password', '$role', NOW())";

    if (mysqli_query($connect, $qInsert)) {
        $_SESSION['success'] = "User dengan ID $id berhasil ditambahkan!";
        header("Location: ../../pages/user/index.php");
        exit;
    } else {
        $_SESSION['error'] = "Gagal menambahkan user: " . mysqli_error($connect);
        header("Location: ../../pages/user/create.php");
        exit;
    }
} else {
    $_SESSION['error'] = "Akses tidak valid!";
    header("Location: ../../pages/user/index.php");
    exit;
}
?>