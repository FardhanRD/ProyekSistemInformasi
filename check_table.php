<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

$columns = DB::connection()->getSchemaBuilder()->getColumnListing('ulasan');
echo json_encode($columns, JSON_PRETTY_PRINT);
