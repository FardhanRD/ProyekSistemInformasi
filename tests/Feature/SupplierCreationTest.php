<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use App\Models\Pengguna;
use App\Models\Supplier;
use Illuminate\Http\Request;

class SupplierCreationTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();

        Schema::dropIfExists('supplier');
        Schema::dropIfExists('pengguna');

        Schema::create('pengguna', function ($table) {
            $table->increments('pengguna_id');
            $table->string('nama_pengguna')->nullable();
            $table->string('username')->unique()->nullable();
            $table->string('email')->unique()->nullable();
            $table->string('no_telepon')->nullable();
            $table->string('sandi')->nullable();
            $table->string('role')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('supplier', function ($table) {
            $table->increments('supplier_id');
            $table->integer('pengguna_id')->unsigned()->unique();
            $table->string('nama_toko');
            $table->string('nama_owner');
            $table->string('kategori_supplier')->nullable();
            $table->string('no_telepon')->nullable();
            $table->string('email')->nullable();
            $table->text('alamat_toko')->nullable();
            $table->decimal('latitude', 10, 8)->nullable();
            $table->decimal('longitude', 11, 8)->nullable();
            $table->string('foto_toko')->nullable();
            $table->boolean('is_verified')->default(false);
            $table->timestamps();
        });
    }

    public function tearDown(): void
    {
        Schema::dropIfExists('supplier');
        Schema::dropIfExists('pengguna');

        parent::tearDown();
    }

    public function test_create_multiple_suppliers_successfully()
    {
        $admin = Pengguna::create([
            'nama_pengguna' => 'Admin',
            'username' => 'admin',
            'email' => 'admin@example.com',
            'sandi' => bcrypt('password'),
            'role' => 'admin'
        ]);

        $this->be($admin);

        $controller = app(\App\Http\Controllers\Admin\SupplierController::class);

        // First supplier
        $req1 = Request::create('/admin/supplier/store', 'POST', [
            'nama_toko' => 'Supplier Toko 1',
            'nama_owner' => 'Owner 1',
            'kategori_supplier' => 'Fashion',
            'no_telepon' => '0812345678',
            'email' => 'supplier1@example.com',
            'alamat_toko' => 'Alamat Toko 1',
            'is_verified' => '1',
        ]);
        $response1 = $controller->store($req1);
        if ($response1 instanceof \Illuminate\Http\RedirectResponse) {
            fwrite(STDOUT, "\nRedirect target: " . $response1->getTargetUrl() . "\n");
            fwrite(STDOUT, "Errors: " . json_encode(session()->get('errors')) . "\n");
            fwrite(STDOUT, "Error Message: " . session()->get('error') . "\n");
        }
        $this->assertEquals(302, $response1->getStatusCode());

        // Second supplier (previously this failed due to unique constraint)
        $req2 = Request::create('/admin/supplier/store', 'POST', [
            'nama_toko' => 'Supplier Toko 2',
            'nama_owner' => 'Owner 2',
            'kategori_supplier' => 'Electronics',
            'no_telepon' => '0812345679',
            'email' => 'supplier2@example.com',
            'alamat_toko' => 'Alamat Toko 2',
            'is_verified' => '0',
        ]);
        $response2 = $controller->store($req2);
        $this->assertEquals(302, $response2->getStatusCode());

        // Assert database holds both
        $this->assertDatabaseHas('supplier', [
            'nama_toko' => 'Supplier Toko 1',
            'is_verified' => 1
        ]);
        $this->assertDatabaseHas('supplier', [
            'nama_toko' => 'Supplier Toko 2',
            'is_verified' => 0
        ]);

        // Assert pengguna records are created
        $this->assertDatabaseHas('pengguna', [
            'email' => 'supplier1@example.com',
            'role' => 'supplier'
        ]);
        $this->assertDatabaseHas('pengguna', [
            'email' => 'supplier2@example.com',
            'role' => 'supplier'
        ]);
    }
}
