<?php

namespace App\Http\Controllers;

use App\Models\AlamatPengguna;
use App\Models\Transaksi;
use App\Models\TransaksiDetail;
use App\Models\Keranjang;
use App\Models\Voucher;
use App\Models\Ekspedisi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Schema;

class CheckoutController extends Controller
{
    protected function selectedCartItems($user): Collection
    {
        $selectedIds = session('checkout_cart_ids', []);
        $query = Keranjang::with(['detail.produk.images'])
            ->where('pengguna_id', $user->pengguna_id);

        if (! empty($selectedIds)) {
            $query->whereIn('keranjang_id', array_map('intval', $selectedIds));
        }

        $items = $query->get();

        if (empty($selectedIds) && $items->isNotEmpty()) {
            session(['checkout_cart_ids' => $items->pluck('keranjang_id')->values()->all()]);
        }

        return $items;
    }

    public function storeSelection(Request $request)
    {
        $user = Auth::user();
        if (! $user) {
            return redirect()->route('login');
        }

        $ids = json_decode((string) $request->input('keranjang_ids', '[]'), true);
        if (! is_array($ids)) {
            $ids = [];
        }

        $ids = array_values(array_filter(array_map('intval', $ids)));
        if (empty($ids)) {
            return back()->with('error', 'Pilih minimal 1 item untuk checkout');
        }

        $allowedIds = Keranjang::where('pengguna_id', $user->pengguna_id)
            ->whereIn('keranjang_id', $ids)
            ->pluck('keranjang_id')
            ->values()
            ->all();

        if (empty($allowedIds)) {
            return back()->with('error', 'Item checkout tidak valid');
        }

        session(['checkout_cart_ids' => $allowedIds]);

        return redirect()->route('checkout.index');
    }

    public function index()
    {
        $user = Auth::user();
        if (! $user) return redirect()->route('login');
        $cart = $this->selectedCartItems($user);
        $addresses = $user->addresses()->get();
        $ekspedisis = Ekspedisi::query()
            ->selectRaw('MIN(ekspedisi_id) as ekspedisi_id, nama_ekspedisi, jenis_layanan, estimasi_hari, MIN(ongkir_flat) as ongkir_flat')
            ->where('is_active', 1)
            ->groupBy('nama_ekspedisi', 'jenis_layanan', 'estimasi_hari')
            ->orderBy('nama_ekspedisi')
            ->get();

        // default ekspedisi termurah
        $ekspedisis = $ekspedisis->sortBy('ongkir_flat')->values();

        $voucher = null;
        if (session('applied_voucher_code')) {
            $voucher = Voucher::where('kode_voucher', session('applied_voucher_code'))->first();
        }

        // metode pembayaran aktif untuk step D (grouped by jenis)
        try {
            $metodes = \App\Models\MetodePembayaran::where('is_active', 1)->get()->groupBy('jenis');
        } catch (\Throwable $e) {
            $metodes = collect();
        }

        $subtotalProduk = $cart->reduce(function ($carry, $item) {
            return $carry + ((float) ($item->detail->harga ?? 0) * (int) ($item->jumlah ?? 1));
        }, 0);

        // juga kirimkan alias 'subtotal' agar view yang menggunakan nama tersebut aman
        $subtotal = $subtotalProduk;

        return view('buyer.checkout.index', compact('cart','addresses','ekspedisis','voucher','metodes','subtotalProduk','subtotal'));
    }


    private function validateVoucherOrFail(string $kodeVoucher, float $subtotal, float $ongkir): array
    {
        $voucher = Voucher::where('kode_voucher', $kodeVoucher)->where('is_active', 1)->first();
        if (! $voucher) {
            return [false, null, 0, 'Kode voucher tidak valid'];
        }

        // tanggal
        if (now()->lt($voucher->berlaku_mulai) || now()->gt($voucher->berlaku_sampai)) {
            return [false, null, 0, 'Voucher sudah kadaluarsa'];
        }

        // kuota
        if ($voucher->kuota !== null) {
            $kuotaTersisa = (int) $voucher->kuota - (int) ($voucher->kuota_terpakai ?? 0);
            if ($kuotaTersisa <= 0) {
                return [false, null, 0, 'Kuota voucher sudah habis'];
            }
        }

        // min belanja
        if ($subtotal < (float) $voucher->min_belanja) {
            return [false, null, 0, 'Minimum pembelian Rp ' . number_format((int) $voucher->min_belanja, 0, ',', '.') . ' belum terpenuhi'];
        }

        // diskon
        $discount = 0;
        if ($voucher->jenis_diskon === 'persen') {
            $discount = ($subtotal * ((float) $voucher->nilai_diskon / 100));
            $maks = $voucher->maks_diskon !== null ? (float) $voucher->maks_diskon : $subtotal;
            $discount = min($discount, $maks);
        } elseif ($voucher->jenis_diskon === 'nominal') {
            $discount = min((float) $voucher->nilai_diskon, $subtotal);
        } elseif ($voucher->jenis_diskon === 'ongkir') {
            $diskonOngkir = min((float) $voucher->nilai_diskon, $ongkir);
            $discount = $diskonOngkir;
        }

        return [true, $voucher, $discount, null];
    }

    public function applyVoucher(Request $request)
    {
        $user = Auth::user();
        if (! $user) {
            return response()->json(['valid' => false, 'message' => 'Unauthorized'], 401);
        }

        $request->validate([
            'kode_voucher' => 'required|string|max:50',
        ]);

        $cartItems = $this->selectedCartItems($user);
        if ($cartItems->isEmpty()) {
            return response()->json(['valid' => false, 'message' => 'Keranjang kosong'], 422);
        }

        $subtotal = 0;
        foreach ($cartItems as $c) {
            $subtotal += (float) $c->detail->harga * (int) $c->jumlah;
        }

        // ongkir dihitung dari ekspedisi yang dipilih saat ini (jika ada)
        $ongkir = 0;
        $ekspedisiId = $request->input('ekspedisi_id');
        if ($ekspedisiId) {
            $e = Ekspedisi::where('ekspedisi_id', $ekspedisiId)->first();
            $ongkir = $e ? (float) ($e->ongkir_flat ?? 0) : 0;
        }

        [$valid, $voucher, $discount, $err] = $this->validateVoucherOrFail($request->input('kode_voucher'), $subtotal, $ongkir);
        if (! $valid) {
            session()->forget(['applied_voucher_code', 'applied_voucher_discount', 'applied_voucher_id']);
            return response()->json(['valid' => false, 'message' => $err]);
        }

        session([
            'applied_voucher_code' => $voucher->kode_voucher,
            'applied_voucher_discount' => $discount,
            'applied_voucher_id' => $voucher->voucher_id,
        ]);

        return response()->json([
            'valid' => true,
            'voucher_id' => $voucher->voucher_id,
            'diskon_amount' => (int) $discount,
            'diskon_text' => 'Rp ' . number_format((int) $discount, 0, ',', '.'),
        ]);
    }

    public function process(Request $request)
    {
        $user = Auth::user();
        if (! $user) return redirect()->route('login');

        $request->validate([
            'alamat_id' => 'required|integer|exists:alamat_pengguna,alamat_id',
            'ekspedisi_id' => 'required|integer|exists:ekspedisi,ekspedisi_id',
            'metode_id' => 'required|integer|exists:metode_pembayaran,metode_id',
        ]);

        $cartItems = $this->selectedCartItems($user);
        if ($cartItems->isEmpty()) return back()->with('error','Keranjang kosong');

        DB::beginTransaction();
        try {
            $subtotal = 0;
            foreach ($cartItems as $c) {
                $subtotal += (float) $c->detail->harga * (int) $c->jumlah;
            }

            $ongkir = (float) (Ekspedisi::where('ekspedisi_id', $request->ekspedisi_id)->value('ongkir_flat') ?? 0);
            $biayaLayanan = 0;

            // Tambahkan kode unik jika metode pembayaran adalah transfer bank
            $kodeUnik = 0;
            $metodePembayaran = \App\Models\MetodePembayaran::find($request->metode_id);
            if ($metodePembayaran && str_contains($metodePembayaran->jenis, 'transfer')) {
                $kodeUnik = rand(100, 999);
            }

            $discount = 0;
            $voucherId = null;

            $voucherCode = session('applied_voucher_code');
            if ($voucherCode) {
                [$valid, $voucher, $d, $err] = $this->validateVoucherOrFail($voucherCode, $subtotal, $ongkir);
                if ($valid) {
                    $discount = $d;
                    $voucherId = $voucher->voucher_id;
                } else {
                    DB::rollBack();
                    return back()->with('error', $err);
                }
            }

            // diskon voucher
            $totalBayar = max(0, ($subtotal + $ongkir + $biayaLayanan + $kodeUnik) - $discount);

            $kode = 'INV-'.date('Ymd').'-'.rand(100,999);

            $trans = Transaksi::create([
                'pengguna_id'=>$user->pengguna_id,
                'alamat_id'=>$request->alamat_id,
                'ekspedisi_id'=>$request->ekspedisi_id,
                'voucher_id'=>$voucherId,
                'kode_transaksi'=>$kode,
                'subtotal'=>$subtotal,
                'diskon_voucher'=>$discount,
                'ongkos_kirim'=>$ongkir,
                'total_harga'=>$totalBayar,
                'status'=>'menunggu_pembayaran',
                'tanggal'=>now()
            ]);

            foreach ($cartItems as $c) {
                TransaksiDetail::create([
                    'transaksi_id'=>$trans->transaksi_id,
                    'detail_produk_id'=>$c->detail->detail_produk_id,
                    'nama_produk_snap'=>$c->detail->nama_produk ?? $c->detail->produk->nama_produk,
                    'harga_snap'=>$c->detail->harga,
                    'ukuran_snap'=>$c->detail->ukuran,
                    'warna_snap'=>null,
                    'quantity'=>$c->jumlah,
                    'subtotal'=>$c->detail->harga * $c->jumlah
                ]);
            }

            // pembayaran
            $statusPembayaran = 'menunggu';
            $pembayaran = \App\Models\Pembayaran::create([
                'transaksi_id' => $trans->transaksi_id,
                'metode_id' => (int)$request->metode_id,
                'jumlah_pembayaran' => $totalBayar,
                'kode_unik' => $kodeUnik > 0 ? $kodeUnik : null,
                'status_pembayaran' => 'menunggu',
                'tanggal_pembayaran' => null,
                'expired_at' => now()->addHours(24),
            ]);

            // pesanan
            if (\Schema::hasTable('pesanan')) {
                \App\Models\Pesanan::create([
                    'transaksi_id' => $trans->transaksi_id,
                    'ekspedisi_id' => $request->ekspedisi_id,
                    'no_resi' => null,
                    'status_pesanan' => 'menunggu_konfirmasi',
                    'alamat_pengiriman' => trim(($user->addresses()->where('alamat_id',$request->alamat_id)->value('alamat_lengkap') ?? '-') . ', ' . ($user->addresses()->where('alamat_id',$request->alamat_id)->value('kota') ?? '-') . ', ' . ($user->addresses()->where('alamat_id',$request->alamat_id)->value('provinsi') ?? '-')),
                    'waktu_diambil' => null,
                    'estimasi_tiba' => now()->addDays(3)->toDateString(),
                ]);
            }

            // update kuota voucher
            if ($voucherId) {
                Voucher::where('voucher_id', $voucherId)->update([
                    'kuota_terpakai' => DB::raw('COALESCE(kuota_terpakai, 0) + 1')
                ]);
            }

            Keranjang::where('pengguna_id', $user->pengguna_id)
                ->whereIn('keranjang_id', $cartItems->pluck('keranjang_id')->all())
                ->delete();
            DB::commit();
            session()->forget(['applied_voucher_code', 'applied_voucher_discount', 'applied_voucher_id', 'checkout_cart_ids']);

            return redirect()->route('payment.show', ['kode_transaksi' => $trans->kode_transaksi]);
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error','Terjadi kesalahan: '.$e->getMessage());
        }
    }
}
