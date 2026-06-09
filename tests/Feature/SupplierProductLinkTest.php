<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Support\Facades\Schema;
use App\Models\Pengguna;
use App\Models\ProdukSupplier;
use Illuminate\Http\Request;

class SupplierProductLinkTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();

        Schema::dropIfExists('produk_supplier');
        Schema::dropIfExists('pengguna');

        Schema::create('pengguna', function ($table) {
            $table->increments('pengguna_id');
            $table->string('nama_pengguna')->nullable();
            $table->string('sandi')->nullable();
            $table->string('role')->nullable();
            $table->timestamps();
        });

        Schema::create('produk_supplier', function ($table) {
            $table->increments('produk_supplier_id');
            $table->integer('supplier_id')->unsigned();
            $table->integer('produk_id')->unsigned();
            $table->decimal('harga_modal', 10, 2)->default(0);
            $table->text('catatan')->nullable();
            $table->timestamps();
        });
    }

    public function tearDown(): void
    {
        Schema::dropIfExists('produk_supplier');
        Schema::dropIfExists('pengguna');

        parent::tearDown();
    }

    public function test_update_relation_redirects_for_web_request()
    {
        $admin = Pengguna::create(['nama_pengguna' => 'Admin', 'role' => 'admin']);
        $this->be($admin);

        $relation = ProdukSupplier::forceCreate([
            'supplier_id' => 1,
            'produk_id' => 1,
            'harga_modal' => 0.00,
        ]);

        $controller = app(\App\Http\Controllers\Admin\SupplierProductController::class);
        $req = Request::create("/admin/supplier-product/{$relation->produk_supplier_id}", 'PUT', [
            'harga_modal' => 150000,
            'catatan' => 'Test Catatan',
        ]);

        $response = $controller->update($req, $relation->produk_supplier_id);

        $this->assertEquals(302, $response->getStatusCode());
        $this->assertDatabaseHas('produk_supplier', [
            'produk_supplier_id' => $relation->produk_supplier_id,
            'harga_modal' => 150000.00,
            'catatan' => 'Test Catatan',
        ]);
    }

    public function test_update_relation_returns_json_for_json_request()
    {
        $admin = Pengguna::create(['nama_pengguna' => 'Admin', 'role' => 'admin']);
        $this->be($admin);

        $relation = ProdukSupplier::forceCreate([
            'supplier_id' => 1,
            'produk_id' => 1,
            'harga_modal' => 0.00,
        ]);

        $controller = app(\App\Http\Controllers\Admin\SupplierProductController::class);
        $req = Request::create("/admin/supplier-product/{$relation->produk_supplier_id}", 'PUT', [
            'harga_modal' => 120000,
        ]);
        $req->headers->set('Accept', 'application/json');

        $response = $controller->update($req, $relation->produk_supplier_id);

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertStringContainsString('Data berhasil diperbarui.', $response->getContent());
        $this->assertDatabaseHas('produk_supplier', [
            'produk_supplier_id' => $relation->produk_supplier_id,
            'harga_modal' => 120000.00,
        ]);
    }
}
