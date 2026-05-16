<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DetailProduk;
use App\Models\Promo;
use App\Models\Produk;
use App\Models\Voucher;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;

class PromotionController extends Controller
{
    public function index(Request $request)
    {
        $voucherQuery = Voucher::query()->orderByDesc('created_at');
        $diskonQuery = Promo::query()->where('jenis', 'diskon_produk')->orderByDesc('created_at');
        $flashSaleQuery = Promo::query()->where('jenis', 'flash_sale')->orderByDesc('created_at');

        $vouchers = $voucherQuery->paginate(10, ['*'], 'vouchers_page')->withQueryString();
        $diskonProduk = $diskonQuery->paginate(10, ['*'], 'diskon_page')->withQueryString();
        $flashSale = $flashSaleQuery->paginate(10, ['*'], 'flash_page')->withQueryString();

        $products = Produk::where('is_active', 1)->orderBy('nama_produk')->get();
        $variants = DetailProduk::with(['produk'])->where('is_active', 1)->orderBy('nama_produk')->get();

        return view('admin.promotion.index', [
            'vouchers' => $vouchers,
            'diskonProduk' => $diskonProduk,
            'flashSale' => $flashSale,
            'products' => $products,
            'variants' => $variants,
        ]);
    }

    public function storeVoucher(Request $request)
    {
        $data = $request->validate([
            'kode_voucher' => 'nullable|string|max:50|unique:voucher,kode_voucher',
            'nama_voucher' => 'required|string|max:150',
            'jenis_diskon' => 'required|in:persen,nominal,ongkir',
            'nilai_diskon' => 'required|numeric|min:0',
            'min_belanja' => 'nullable|numeric|min:0',
            'maks_diskon' => 'nullable|numeric|min:0',
            'kuota' => 'nullable|integer|min:1',
            'berlaku_mulai' => 'required|date',
            'berlaku_sampai' => 'required|date|after_or_equal:berlaku_mulai',
            'is_active' => 'nullable|boolean',
        ]);

        $data['kode_voucher'] = $data['kode_voucher'] ?: $this->generateVoucherCode($data['nama_voucher']);
        $data['min_belanja'] = $data['min_belanja'] ?? 0;
        $data['is_active'] = $request->boolean('is_active');
        $data['kuota_terpakai'] = 0;

        Voucher::create($data);

        return back()->with('success', 'Voucher berhasil ditambahkan.');
    }

    public function updateVoucher(Request $request, $id)
    {
        $voucher = Voucher::findOrFail($id);
        $data = $request->validate([
            'kode_voucher' => 'required|string|max:50|unique:voucher,kode_voucher,' . $voucher->voucher_id . ',voucher_id',
            'nama_voucher' => 'required|string|max:150',
            'jenis_diskon' => 'required|in:persen,nominal,ongkir',
            'nilai_diskon' => 'required|numeric|min:0',
            'min_belanja' => 'nullable|numeric|min:0',
            'maks_diskon' => 'nullable|numeric|min:0',
            'kuota' => 'nullable|integer|min:1',
            'berlaku_mulai' => 'required|date',
            'berlaku_sampai' => 'required|date|after_or_equal:berlaku_mulai',
            'is_active' => 'nullable|boolean',
        ]);

        $data['min_belanja'] = $data['min_belanja'] ?? 0;
        $data['is_active'] = $request->boolean('is_active');

        $voucher->update($data);

        return back()->with('success', 'Voucher berhasil diperbarui.');
    }

    public function destroyVoucher($id)
    {
        Voucher::findOrFail($id)->delete();
        return back()->with('success', 'Voucher berhasil dihapus.');
    }

    public function storePromo(Request $request)
    {
        $data = $request->validate([
            'jenis' => 'required|in:diskon_produk,flash_sale',
            'nama_promo' => 'required|string|max:150',
            'produk_id' => 'nullable|exists:produk,produk_id',
            'detail_produk_id' => 'nullable|exists:detail_produk,detail_produk_id',
            'persen_diskon' => 'required|numeric|min:0|max:100',
            'nominal_diskon' => 'nullable|numeric|min:0',
            'stok_flash_sale' => 'nullable|integer|min:1',
            'mulai' => 'required|date',
            'selesai' => 'required|date|after_or_equal:mulai',
            'is_active' => 'nullable|boolean',
        ]);

        $data['is_active'] = $request->boolean('is_active');
        $data['nominal_diskon'] = $data['nominal_diskon'] ?? null;
        $data['stok_flash_sale'] = $data['jenis'] === 'flash_sale' ? ($data['stok_flash_sale'] ?? 1) : null;

        Promo::create($data);

        return back()->with('success', 'Promo berhasil ditambahkan.');
    }

    public function updatePromo(Request $request, $id)
    {
        $promo = Promo::findOrFail($id);
        $data = $request->validate([
            'jenis' => 'required|in:diskon_produk,flash_sale',
            'nama_promo' => 'required|string|max:150',
            'produk_id' => 'nullable|exists:produk,produk_id',
            'detail_produk_id' => 'nullable|exists:detail_produk,detail_produk_id',
            'persen_diskon' => 'required|numeric|min:0|max:100',
            'nominal_diskon' => 'nullable|numeric|min:0',
            'stok_flash_sale' => 'nullable|integer|min:1',
            'mulai' => 'required|date',
            'selesai' => 'required|date|after_or_equal:mulai',
            'is_active' => 'nullable|boolean',
        ]);

        $data['is_active'] = $request->boolean('is_active');
        $data['nominal_diskon'] = $data['nominal_diskon'] ?? null;
        $data['stok_flash_sale'] = $data['jenis'] === 'flash_sale' ? ($data['stok_flash_sale'] ?? 1) : null;

        $promo->update($data);

        return back()->with('success', 'Promo berhasil diperbarui.');
    }

    public function destroyPromo($id)
    {
        Promo::findOrFail($id)->delete();
        return back()->with('success', 'Promo berhasil dihapus.');
    }

    protected function generateVoucherCode(string $name): string
    {
        $base = strtoupper(preg_replace('/[^A-Z0-9]/', '', strtoupper(substr($name, 0, 6))) ?: 'VCHR');
        return $base . '-' . now()->format('ymdHis');
    }
}
