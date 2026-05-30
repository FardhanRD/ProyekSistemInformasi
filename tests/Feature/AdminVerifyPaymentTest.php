<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Foundation\Testing\WithFaker;
use App\Models\Pengguna;
use App\Models\Transaksi;
use App\Models\Pembayaran;
use App\Models\Pesanan;
use Illuminate\Http\Request;

class AdminVerifyPaymentTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();

        // Create minimal schema for testing in-memory sqlite
        Schema::dropIfExists('pembayaran');
        Schema::dropIfExists('pesanan');
        Schema::dropIfExists('transaksi');
        Schema::dropIfExists('pengguna');

        Schema::create('pengguna', function ($table) {
            $table->increments('pengguna_id');
            $table->string('nama_pengguna')->nullable();
            $table->string('sandi')->nullable();
            $table->string('role')->nullable();
            $table->timestamps();
        });

        Schema::create('transaksi', function ($table) {
            $table->increments('transaksi_id');
            $table->integer('pengguna_id')->unsigned()->nullable();
            $table->string('kode_transaksi')->nullable();
            $table->string('status')->nullable();
            $table->timestamp('tanggal')->nullable();
        });

        Schema::create('pembayaran', function ($table) {
            $table->increments('pembayaran_id');
            $table->integer('transaksi_id')->unsigned();
            $table->string('status_pembayaran')->nullable();
            $table->timestamp('tanggal_pembayaran')->nullable();
        });

        Schema::create('notifikasi', function ($table) {
            $table->increments('notifikasi_id');
            $table->integer('pengguna_id')->unsigned();
            $table->string('judul')->nullable();
            $table->text('pesan')->nullable();
            $table->string('jenis')->nullable();
            $table->string('url_redirect')->nullable();
            $table->boolean('is_read')->default(false);
            $table->timestamp('created_at')->nullable();
        });

        Schema::create('pesanan', function ($table) {
            $table->increments('pesanan_id');
            $table->integer('transaksi_id')->unsigned();
            $table->string('status_pesanan')->nullable();
        });
    }

    public function tearDown(): void
    {
        Schema::dropIfExists('pembayaran');
        Schema::dropIfExists('pesanan');
        Schema::dropIfExists('transaksi');
        Schema::dropIfExists('pengguna');

        parent::tearDown();
    }

    public function test_admin_verifying_payment_updates_transaksi_and_pesanan()
    {
        // create admin user
        $admin = Pengguna::create(["nama_pengguna" => 'Admin', 'sandi' => 'x', 'role' => 'admin']);

        // create buyer
        $buyer = Pengguna::create(["nama_pengguna" => 'Buyer', 'sandi' => 'x', 'role' => 'buyer']);

        // create transaksi
        $trans = Transaksi::create([
            'pengguna_id' => $buyer->pengguna_id,
            'kode_transaksi' => 'TRX-TEST-1',
            'status' => 'menunggu_pembayaran',
            'tanggal' => now(),
        ]);

        // create pembayaran
        $pembayaran = Pembayaran::create([
            'transaksi_id' => $trans->transaksi_id,
            'status_pembayaran' => 'menunggu',
        ]);

        // create pesanan
        $pesanan = Pesanan::create([
            'transaksi_id' => $trans->transaksi_id,
            'status_pesanan' => 'menunggu_konfirmasi',
        ]);

        // Call controller directly (avoid route registration issues in test env)
        $this->be($admin);
        $controller = app(\App\Http\Controllers\Admin\CustomerOrderController::class);
        $req = Request::create('/','POST', [], [], [], ['HTTP_ACCEPT' => 'application/json']);
        $response = $controller->verify($req, $trans->transaksi_id);

        // dump response content for debugging
        fwrite(STDOUT, "\nResponse content: " . $response->getContent() . "\n");
        $this->assertEquals(200, $response->getStatusCode());
        $json = json_decode($response->getContent(), true);
        $this->assertTrue(!empty($json['success']));

        $this->assertDatabaseHas('pembayaran', [
            'transaksi_id' => $trans->transaksi_id,
            'status_pembayaran' => 'berhasil',
        ]);

        $this->assertDatabaseHas('transaksi', [
            'transaksi_id' => $trans->transaksi_id,
            'status' => 'pembayaran_dikonfirmasi',
        ]);

        $this->assertDatabaseHas('pesanan', [
            'transaksi_id' => $trans->transaksi_id,
            'status_pesanan' => 'dikonfirmasi',
        ]);
    }
}
