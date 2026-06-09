<?php

$conn = mysqli_connect('127.0.0.1', 'root', '', 'db_apk_main');
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

echo "=== TABEL EKSPEDISI STRUCTURE ===\n\n";
$result = mysqli_query($conn, 'DESCRIBE ekspedisi');
while($row = mysqli_fetch_assoc($result)) {
    echo $row['Field'] . ' | ' . $row['Type'] . ' | ' . $row['Null'] . "\n";
}

echo "\n=== TABEL EKSPEDISI DATA ===\n\n";
$result = mysqli_query($conn, 'SELECT * FROM ekspedisi');
while($row = mysqli_fetch_assoc($result)) {
    print_r($row);
}

mysqli_close($conn);
