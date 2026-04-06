<?php
$hostname = 'localhost';
$username = 'root';
$password = '';
$database = 'peminjaman_alat_berat';

// Establish connection
$connect = mysqli_connect($hostname, $username, $password, $database);

// Check connection
if (!$connect) {
    die("Connection failed: " . mysqli_connect_error());
}
