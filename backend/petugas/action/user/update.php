<?php
include '../../app.php';

// Ambil ID dari URL
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if (isset($_POST['tombol'])) {
    $username = escapeString($_POST['username']);
    $role = escapeString($_POST['role']);

    // Jika password diisi, update password
    if (!empty($_POST['password'])) {
        $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
        $qUpdate = "UPDATE users SET 
                    username = '$username', 
                    password = '$password', 
                    role = '$role' 
                    WHERE id = $id";
    } else {
        $qUpdate = "UPDATE users SET 
                    username = '$username', 
                    role = '$role' 
                    WHERE id = $id";
    }

    $result = mysqli_query($connect, $qUpdate);
    if ($result) {
        $_SESSION['success'] = "Data user berhasil diupdate!";
        header("Location: ../../pages/user/index.php");
        exit;
    } else {
        $_SESSION['error'] = "Gagal mengupdate user: " . mysqli_error($connect);
        header("Location: ../../pages/user/edit.php?id=$id");
        exit;
    }
}
?>