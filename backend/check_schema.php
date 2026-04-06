<?php
include 'admin/app.php';
$result = mysqli_query($connect, "DESCRIBE barang");
echo '<pre>';
while($row = mysqli_fetch_assoc($result)) {
    print_r($row);
}
echo '</pre>';
?>
