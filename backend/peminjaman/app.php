<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

include '../../../../config/escapeString.php';
include '../../../../config/koneksi.php';

if (!isset($_SESSION['user_id']) || !isset($_SESSION['status']) || $_SESSION['status'] != 'login' || $_SESSION['role'] != 'peminjam') {
    header("Location: /farel-ukk-/backend/pages/auth/login.php?pesan=belum_login");
    exit();
}

if (!function_exists('safeNumberFormat')) {
    function safeNumberFormat($number, $decimals = 0, $dec_point = '.', $thousands_sep = ',') {
        return number_format((float)($number ?? 0), $decimals, $dec_point, $thousands_sep);
    }
}
?>
