<?php
require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

$sqlFile = '/Users/fardhanraihan/Downloads/db_apk_main (4).sql';

if (!file_exists($sqlFile)) {
    echo "Error: SQL file not found at $sqlFile\n";
    exit(1);
}

echo "Starting database import from $sqlFile...\n";

// 1. Drop all existing tables
echo "Dropping existing tables...\n";
DB::statement('SET FOREIGN_KEY_CHECKS = 0;');
$tables = DB::select('SHOW TABLES');
$key = 'Tables_in_' . DB::connection()->getDatabaseName();
foreach ($tables as $table) {
    $tableName = $table->$key;
    DB::statement("DROP TABLE IF EXISTS `$tableName`;");
    echo "Dropped table: $tableName\n";
}
DB::statement('SET FOREIGN_KEY_CHECKS = 1;');
echo "All existing tables dropped successfully.\n\n";

// 2. Import SQL file
echo "Executing SQL import...\n";
$lines = file($sqlFile);
$query = '';
$count = 0;

foreach ($lines as $line) {
    $trimmed = trim($line);
    // Skip comments and empty lines
    if (empty($trimmed) || str_starts_with($trimmed, '--') || str_starts_with($trimmed, '/*')) {
        continue;
    }
    $query .= $line;
    if (str_ends_with($trimmed, ';')) {
        try {
            DB::unprepared($query);
            $count++;
        } catch (\Exception $e) {
            echo "Error executing statement around count $count:\n";
            echo substr($query, 0, 200) . "...\n";
            echo "Error details: " . $e->getMessage() . "\n\n";
        }
        $query = '';
    }
}

echo "\nDatabase import completed! Executed $count statements.\n";
