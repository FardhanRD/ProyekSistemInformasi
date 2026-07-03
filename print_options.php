<?php
include 'vendor/autoload.php';
$app = include 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

// Let's load the categories using the Eloquent model if it exists, or raw queries.
// Let's see what model we have. Let's do raw query representation.
// Each category has: kategori_id, parent_id, nama_kategori
class CategoryNode {
    public $kategori_id;
    public $nama_kategori;
    public $parent_id;
    public $children = [];
}

$all = [];
foreach (DB::table('kategori')->get() as $row) {
    $node = new CategoryNode();
    $node->kategori_id = $row->kategori_id;
    $node->nama_kategori = $row->nama_kategori;
    $node->parent_id = $row->parent_id;
    $all[$row->kategori_id] = $node;
}

foreach ($all as $node) {
    if ($node->parent_id && isset($all[$node->parent_id])) {
        $all[$node->parent_id]->children[] = $node;
    }
}

$kategoris = [];
foreach ($all as $node) {
    if (!$node->parent_id) {
        $kategoris[] = $node;
    }
}

foreach($kategoris as $root) {
    if(count($root->children) > 0) {
        echo "OptGroup: " . $root->nama_kategori . "\n";
        foreach($root->children as $sub) {
            if(count($sub->children) > 0) {
                echo "  OptGroup: " . $sub->nama_kategori . "\n";
                foreach($sub->children as $leaf) {
                    echo "    Option: '" . "   " . $leaf->nama_kategori . "' (value: " . $leaf->kategori_id . ")\n";
                }
            } else {
                echo "  Option: '" . "  " . $sub->nama_kategori . "' (value: " . $sub->kategori_id . ")\n";
            }
        }
    }
}
