<?php
include '../../app.php';

if (isset($_POST['tombol'])) {
    $username = escapeString($_POST['username']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $role = escapeString($_POST['role']);

    // Validasi username unik
    $checkUser = mysqli_query($connect, "SELECT * FROM users WHERE username = '$username'");
    if (mysqli_num_rows($checkUser) > 0) {
        $_SESSION['error'] = "Username sudah digunakan, silahkan pilih username lain.";
        header("Location: ../../pages/user/create.php");
        exit;
    }

    // Query insert sesuai struktur yang benar
    $qInsert = "INSERT INTO users (username, password, role) 
                VALUES ('$username', '$password', '$role')";

    if (mysqli_query($connect, $qInsert)) {
        $_SESSION['success'] = "User berhasil ditambahkan!";
        header("Location: ../../pages/user/index.php");
        exit;
    } else {
        $_SESSION['error'] = "Gagal menambahkan user: " . mysqli_error($connect);
        header("Location: ../../pages/user/create.php");
        exit;
    }
}
?>