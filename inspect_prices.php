<?php

$envFile = __DIR__ . '/.env';
$lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
$env = [];
foreach ($lines as $line) {
    if (strpos(trim($line), '#') === 0) continue;
    list($name, $value) = explode('=', $line, 2) + [NULL, NULL];
    if ($name !== NULL) {
        $env[trim($name)] = trim($value, '"\' ');
    }
}

$dbHost = $env['DB_HOST'] ?? '127.0.0.1';
$dbPort = $env['DB_PORT'] ?? '3306';
$dbName = $env['DB_DATABASE'] ?? 'db_apk_main';
$dbUser = $env['DB_USERNAME'] ?? 'root';
$dbPass = $env['DB_PASSWORD'] ?? '';

try {
    $dsn = "mysql:host={$dbHost};port={$dbPort};dbname={$dbName};charset=utf8mb4";
    $pdo = new PDO($dsn, $dbUser, $dbPass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);

    $stmt = $pdo->prepare("SELECT * FROM produk WHERE nama_produk LIKE :q");
    $stmt->execute(['q' => '%LAICA Move Mat%']);
    $products = $stmt->fetchAll();

    foreach ($products as $p) {
        echo "Product ID: {$p['produk_id']}\n";
        echo "Name: {$p['nama_produk']}\n";
        echo "Base Price (harga_dasar) in DB: {$p['harga_dasar']}\n";
        
        $stmtDet = $pdo->prepare("SELECT * FROM detail_produk WHERE produk_id = :id");
        $stmtDet->execute(['id' => $p['produk_id']]);
        $details = $stmtDet->fetchAll();
        foreach ($details as $d) {
            echo "  Variant ID: {$d['detail_produk_id']}, Size: {$d['ukuran']}, Price in DB: {$d['harga']}\n";
        }
    }

} catch (\Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
}
