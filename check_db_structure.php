<?php
$conn = mysqli_connect('127.0.0.1', 'root', '', 'db_apk_main');
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

echo "=== TABEL TRANSAKSI STRUCTURE ===\n\n";
$result = mysqli_query($conn, 'DESCRIBE transaksi');
while($row = mysqli_fetch_assoc($result)) {
    echo $row['Field'] . ' | ' . $row['Type'] . ' | ' . $row['Null'] . "\n";
}

echo "\n=== TABEL KERANJANG STRUCTURE ===\n\n";
$result = mysqli_query($conn, 'DESCRIBE keranjang');
while($row = mysqli_fetch_assoc($result)) {
    echo $row['Field'] . ' | ' . $row['Type'] . ' | ' . $row['Null'] . "\n";
}

mysqli_close($conn);
