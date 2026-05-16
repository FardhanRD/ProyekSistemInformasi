<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;

$columns = DB::select('SHOW COLUMNS FROM pengguna WHERE `Key`="PRI"');
foreach ($columns as $col) {
    echo $col->Field . ' (Primary Key)' . PHP_EOL;
}

// Also check all columns for pengguna table
echo "\n=== ALL COLUMNS IN PENGGUNA TABLE ===\n";
$allColumns = DB::select('SHOW COLUMNS FROM pengguna');
foreach ($allColumns as $col) {
    $key = $col->Key ? "({$col->Key})" : '';
    echo $col->Field . " " . $col->Type . " " . $key . PHP_EOL;
}
