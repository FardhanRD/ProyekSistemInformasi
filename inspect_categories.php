<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$categories = App\Models\Kategori::all(['kategori_id', 'nama_kategori', 'slug', 'parent_id', 'is_active']);
foreach ($categories as $c) {
    echo "ID: {$c->kategori_id} | Name: {$c->nama_kategori} | Slug: {$c->slug} | Parent: {$c->parent_id} | Active: {$c->is_active}\n";
}
