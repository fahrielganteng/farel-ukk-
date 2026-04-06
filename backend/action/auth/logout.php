<?php
// backend/action/auth/logout.php
session_start();
session_unset();
session_destroy();

// Redirect dengan alert JavaScript
echo "<script>alert('Logout berhasil! Sampai jumpa kembali.');window.location='../../pages/auth/login.php';</script>";
exit;
