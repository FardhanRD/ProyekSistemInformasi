<?php
$conn = mysqli_connect('127.0.0.1', 'root', '', 'db_apk_main');
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

function dumpTable(mysqli $conn, string $table): void {
    echo "\n=== TABEL {$table} STRUCTURE ===\n\n";
    $result = mysqli_query($conn, 'DESCRIBE ' . $table);
    if (! $result) {
        echo "Gagal DESCRIBE table {$table}: " . mysqli_error($conn) . "\n";
        return;
    }
    while ($row = mysqli_fetch_assoc($result)) {
        echo $row['Field'] . ' | ' . $row['Type'] . ' | ' . $row['Null'] . "\n";
    }
}

dumpTable($conn, 'wishlist');
dumpTable($conn, 'keranjang');
dumpTable($conn, 'transaksi');

mysqli_close($conn);

