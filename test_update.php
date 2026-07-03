<?php

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$kernel->bootstrap();

$request = Illuminate\Http\Request::create('/admin/category/19', 'POST', [
    '_method' => 'PUT',
    'nama_kategori' => 'Tennis Rackets',
    'is_active' => '1',
    'parent_id' => '18',
    'urutan' => '0'
]);
$app->instance('request', $request);

$controller = new App\Http\Controllers\Admin\CategoryController();
try {
    $response = $controller->update($request, 19);
    echo "SUCCESS: Redirected to " . $response->getTargetUrl() . "\n";
    // Print the category state from DB
    $cat = App\Models\Kategori::find(19);
    echo "DB State: " . json_encode($cat) . "\n";
} catch (\Illuminate\Validation\ValidationException $e) {
    echo "VALIDATION FAILED:\n";
    print_r($e->errors());
} catch (\Exception $e) {
    echo "EXCEPTION:\n";
    echo $e->getMessage() . "\n";
}
