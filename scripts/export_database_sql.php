<?php

require __DIR__ . '/../vendor/autoload.php';

$app = require __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

$connection = DB::connection();
$pdo = $connection->getPdo();

$tables = [];
$driver = $connection->getDriverName();

if (in_array($driver, ['mysql', 'mariadb'], true)) {
    $rows = $connection->select('SHOW FULL TABLES WHERE Table_type = "BASE TABLE"');
    foreach ($rows as $row) {
        $rowArray = (array) $row;
        $tables[] = array_values($rowArray)[0];
    }
} else {
    $tables = $connection->select("SELECT name FROM sqlite_master WHERE type='table' AND name NOT LIKE 'sqlite_%' ORDER BY name");
    $tables = array_map(fn ($row) => (array) $row['name'] ?? array_values((array) $row)[0], $tables);
}

$dumpPath = storage_path('app/movr_database_dump.sql');
$reportPath = storage_path('app/movr_database_report.txt');

$out = [];
$out[] = "-- MOVR database export";
$out[] = "-- Generated at: " . now()->toDateTimeString();
$out[] = "SET FOREIGN_KEY_CHECKS=0;";
$out[] = "";

$report = [];
$report[] = 'Database driver: ' . $driver;
$report[] = 'Tables: ' . count($tables);
$report[] = '';

foreach ($tables as $table) {
    $count = (int) $connection->table($table)->count();
    $report[] = sprintf('%s (%d rows)', $table, $count);

    if (in_array($driver, ['mysql', 'mariadb'], true)) {
        $createRow = $connection->selectOne('SHOW CREATE TABLE `' . str_replace('`', '``', $table) . '`');
        $createData = (array) $createRow;
        $createSql = $createData['Create Table'] ?? $createData['Create View'] ?? null;
        if ($createSql) {
            $out[] = 'DROP TABLE IF EXISTS `' . str_replace('`', '``', $table) . '`;';
            $out[] = $createSql . ';';
        }
    } else {
        $schema = $connection->selectOne("SELECT sql FROM sqlite_master WHERE type='table' AND name = ?", [$table]);
        if ($schema && ! empty($schema->sql)) {
            $out[] = 'DROP TABLE IF EXISTS "' . str_replace('"', '""', $table) . '";';
            $out[] = $schema->sql . ';';
        }
    }

    $rows = $connection->table($table)->get();
    if ($rows->isNotEmpty()) {
        foreach ($rows as $row) {
            $rowArray = (array) $row;
            $columns = array_map(fn ($column) => '`' . str_replace('`', '``', $column) . '`', array_keys($rowArray));
            $values = [];
            foreach ($rowArray as $value) {
                if (is_null($value)) {
                    $values[] = 'NULL';
                } elseif (is_bool($value)) {
                    $values[] = $value ? '1' : '0';
                } elseif (is_numeric($value) && ! is_string($value)) {
                    $values[] = (string) $value;
                } else {
                    $values[] = $pdo->quote((string) $value);
                }
            }
            $out[] = 'INSERT INTO `' . str_replace('`', '``', $table) . '` (' . implode(', ', $columns) . ') VALUES (' . implode(', ', $values) . ');';
        }
    }

    $out[] = '';
}

$out[] = 'SET FOREIGN_KEY_CHECKS=1;';

file_put_contents($dumpPath, implode(PHP_EOL, $out));
file_put_contents($reportPath, implode(PHP_EOL, $report));

echo "Wrote SQL dump to: {$dumpPath}\n";
echo "Wrote table report to: {$reportPath}\n";
