<?php
include 'admin/app.php';

echo '<h3>Barang Table Columns:</h3><pre>';
$res = mysqli_query($connect, "DESCRIBE barang");
while($row = mysqli_fetch_assoc($res)) {
    echo $row['Field'] . ' (' . $row['Type'] . ")\n";
}
echo '</pre>';

// Add gambar column if missing
$check = mysqli_query($connect, "SHOW COLUMNS FROM barang LIKE 'gambar'");
if (mysqli_num_rows($check) == 0) {
    $r = mysqli_query($connect, "ALTER TABLE barang ADD COLUMN gambar VARCHAR(255) NULL AFTER deskripsi");
    echo $r ? '<p style="color:green">✅ Column gambar added.</p>' : '<p style="color:red">❌ Error: ' . mysqli_error($connect) . '</p>';
} else {
    echo '<p style="color:green">✅ Column gambar already exists.</p>';
}

// Create directory
$dir = realpath(__DIR__ . '/..') . '/storages/alat/';
if (!is_dir($dir)) {
    mkdir($dir, 0755, true);
    echo '<p style="color:green">✅ Directory created: ' . $dir . '</p>';
} else {
    echo '<p style="color:green">✅ Directory exists: ' . $dir . '</p>';
}
echo '<hr><a href="admin/pages/alat/index.php">Go to Admin Alat</a>';
?>
