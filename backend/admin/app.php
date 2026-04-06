<?php
include '../../../../config/escapeString.php';
include '../../../../config/koneksi.php';

$kategori_query = mysqli_query($connect, "SELECT * FROM kategori ORDER BY nama_kategori ASC");
