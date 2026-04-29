<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\KeranjangItem;
use App\Models\Order;
use Illuminate\Support\Facades\Auth;
use Midtrans\Config;
use Midtrans\Snap;

class CheckoutController extends Controller
{
    private function configureMidtrans(): ?string
    {
        $serverKey = config('midtrans.server_key');
        $clientKey = config('midtrans.client_key');

        if (empty($serverKey) || empty($clientKey)) {
            \Log::error('Midtrans keys are not properly configured in config/midtrans.php or .env');
            return 'Konfigurasi Midtrans belum lengkap. Isi MIDTRANS_SERVER_KEY dan MIDTRANS_CLIENT_KEY di file .env';
        }

        if (str_contains($serverKey, 'CHANGE_ME') || str_contains($clientKey, 'CHANGE_ME')) {
            \Log::warning('Midtrans keys still using placeholder values');
            return 'MIDTRANS_SERVER_KEY dan MIDTRANS_CLIENT_KEY masih placeholder. Ganti dengan key Sandbox asli dari dashboard Midtrans.';
        }

        Config::$serverKey = $serverKey;
        Config::$clientKey = $clientKey;
        Config::$isProduction = config('midtrans.is_production');
        Config::$isSanitized = config('midtrans.enable_sanitization');
        Config::$is3ds = config('midtrans.enable_3ds');

        return null;
    }

    /**
     * Menangani tombol "Beli" (Direct Buy) dari Halaman Utama
     */
    public function buyNow(Request $request)
    {
        // Validasi
        $request->validate([
            'product_id' => 'required|exists:products,id',
        ]);

        // Ambil data produk dari database
        $product = Product::findOrFail($request->product_id);

        // Siapkan data untuk halaman checkout
        $checkoutItem = [
            'id' => $product->id,
            'name' => $product->name,
            'price' => $product->price,
            'image' => $product->image,
            'qty' => 1, // Default beli 1 dulu
            'total' => $product->price
        ];

        // Simpan data ke SESSION sementara
        session(['checkout_type' => 'direct']);
        session(['checkout_items' => [$checkoutItem]]);

        // Arahkan user ke halaman checkout
        return redirect()->route('checkout.index');
    }

    /**
     * Menampilkan Halaman Checkout dengan Perhitungan Total
     */
    public function index()
    {
        // Ambil item dari session atau cart
        $items = session('checkout_items');

        // If no items in session, get from user's cart
        if (!$items) {
            if (Auth::check()) {
                $cartItems = KeranjangItem::where('pembeli_id', Auth::id())->with('produk')->get();

                if ($cartItems->isEmpty()) {
                    return redirect()->route('home')->with('error', 'Tidak ada item untuk di-checkout');
                }

                $items = [];
                foreach ($cartItems as $cart) {
                    $items[] = [
                        'id' => $cart->produk->id,
                        'name' => $cart->produk->name,
                        'price' => $cart->harga_saat_ini,
                        'image' => $cart->produk->image,
                        'qty' => $cart->jumlah,
                        'total' => $cart->jumlah * $cart->harga_saat_ini
                    ];
                }
            } else {
                return redirect()->route('home')->with('error', 'Tidak ada item untuk di-checkout');
            }
        }

        // 1. Hitung Subtotal (Harga Barang)
        $subtotal = collect($items)->sum('total');

        // 2. Tentukan Biaya Tambahan (Bisa diubah sesuai kebutuhan)
        $shippingCost = 15000; // Ongkos Kirim
        $serviceFee = 1000;    // Biaya Layanan

        // 3. Hitung Grand Total (Total Bayar)
        $grandTotal = $subtotal + $shippingCost + $serviceFee;

        // Kirim semua variabel perhitungan ke View
        return view('movr.checkout.index', compact('items', 'subtotal', 'shippingCost', 'serviceFee', 'grandTotal'));
    }

    /**
     * Process direct checkout from the checkout page
     */
    public function processCheckout(Request $request)
    {
        $midtransConfigError = $this->configureMidtrans();
        if ($midtransConfigError !== null) {
            return response()->json(['error' => $midtransConfigError], 422);
        }

        if (!Auth::check()) {
            return response()->json(['error' => 'User not authenticated'], 401);
        }

        // Validate the payment method selection
        $request->validate([
            'payment_method' => 'required|string',
            'phone' => 'nullable|string'
        ]);

        $items = session('checkout_items');

        if (!$items) {
            if (Auth::check()) {
                // Perbaikan query: gunakan 'pembeli_id' sesuai migrasi Anda sebelumnya
                $cartItems = KeranjangItem::where('pembeli_id', Auth::id())->with('produk')->get();

                if ($cartItems->isEmpty()) {
                    return response()->json(['error' => 'Keranjang kosong'], 400);
                }

                $items = [];
                foreach ($cartItems as $cart) {
                    $items[] = [
                        'id' => $cart->produk->id,
                        'name' => $cart->produk->name,
                        'price' => $cart->harga_saat_ini,
                        'image' => $cart->produk->image,
                        'qty' => $cart->jumlah,
                        'total' => $cart->jumlah * $cart->harga_saat_ini
                    ];
                }
            } else {
                return response()->json(['error' => 'Tidak ada item untuk di-checkout'], 400);
            }
        }

        $subtotal = collect($items)->sum('total');
        $shippingCost = 15000;
        $serviceFee = 1000;
        $totalAmount = $subtotal + $shippingCost + $serviceFee;

        // Create order
        $order = Order::create([
            'user_id' => Auth::id(),
            'total_amount' => $totalAmount,
            'status' => 'pending',
            'payment_method' => $request->payment_method,
        ]);

        // Save order items to order_items table
        foreach ($items as $item) {
            \App\Models\OrderItem::create([
                'order_id' => $order->id,
                'product_id' => $item['id'],
                'quantity' => $item['qty'],
                'price' => $item['price'],
                'subtotal' => $item['total'],
            ]);
        }

        // Prepare transaction details
        $transaction_details = [
            'order_id' => $order->id . '-' . time(), // Order ID unik
            'gross_amount' => (int)$totalAmount, 
        ];

        // Prepare customer details
        $customer_details = [
            'first_name' => Auth::user()->name,
            'email' => Auth::user()->email,
            'phone' => $request->phone ?? '08123456789', // Berikan default jika null agar tidak error
        ];

        // --- PERBAIKAN PENTING: ITEM DETAILS ---
        $item_details = [];
        foreach ($items as $item) {
            $item_details[] = [
                'id' => $item['id'],
                'price' => (int)$item['price'],
                'quantity' => $item['qty'],
                // Midtrans membatasi nama item max 50 karakter
                'name' => substr($item['name'], 0, 50), 
            ];
        }

        // WAJIB: Masukkan Ongkir sebagai Item
        if ($shippingCost > 0) {
            $item_details[] = [
                'id' => 'SHIPPING',
                'price' => (int)$shippingCost,
                'quantity' => 1,
                'name' => 'Biaya Pengiriman',
            ];
        }

        // WAJIB: Masukkan Biaya Layanan sebagai Item
        if ($serviceFee > 0) {
            $item_details[] = [
                'id' => 'SERVICE',
                'price' => (int)$serviceFee,
                'quantity' => 1,
                'name' => 'Biaya Layanan',
            ];
        }
        // Total harga di $item_details SEKARANG SUDAH SAMA dengan $transaction_details['gross_amount']
        // ----------------------------------------

        // Snap API parameter
        $params = [
            'transaction_details' => $transaction_details,
            'customer_details' => $customer_details,
            'item_details' => $item_details,
            // --- PERBAIKAN UTAMA: CALLBACKS ---
            // Ini yang memperbaiki masalah redirect ke Example Domain
            'callbacks' => [
                'finish' => route('home'), // Arahkan kembali ke Home atau halaman Success Anda
            ]
            // ----------------------------------
        ];

        // ... (Sisa kode ke bawah sama persis: enabled_payments, try-catch, dll) ...
        // ... Copy paste kode payment option & try catch Anda yang lama di sini ...
        
        $enabled_payments = config('midtrans.payment_options', ['credit_card', 'bank_transfer', 'gopay', 'shopeepay', 'ovo', 'dana', 'bca_va', 'bni_va', 'bri_va', 'permata_va']);

        if ($request->payment_method === 'credit_card') {
            $params['enabled_payments'] = ['credit_card'];
        } elseif ($request->payment_method === 'gopay') {
            $params['enabled_payments'] = ['gopay'];
        } elseif ($request->payment_method === 'ewallet') {
            $params['enabled_payments'] = ['shopeepay', 'ovo', 'dana'];
        } elseif ($request->payment_method === 'bank_transfer') {
            $params['enabled_payments'] = ['bank_transfer'];
        } else {
            $params['enabled_payments'] = $enabled_payments; 
        }

        try {
            \Log::info('Creating Midtrans transaction with params:', $params);

            $snapResponse = Snap::createTransaction($params);
            $paymentUrl = $snapResponse->redirect_url;

            \Log::info('Midtrans transaction created successfully', [
                'order_id' => $transaction_details['order_id'],
                'redirect_url' => $paymentUrl
            ]);

            $order->update([
                'midtrans_order_id' => $transaction_details['order_id'],
                'payment_url' => $paymentUrl,
            ]);

            session()->forget(['checkout_items', 'checkout_type']);

            return response()->json([
                'success' => true,
                'redirect_url' => $paymentUrl
            ]);
        } catch (\Exception $e) {
            \Log::error('Midtrans payment error: ' . $e->getMessage());

            if (str_contains($e->getMessage(), '401 API response')) {
                return response()->json([
                    'error' => 'Server Key Midtrans tidak valid atau tidak cocok dengan mode Sandbox. Periksa MIDTRANS_SERVER_KEY di file .env.',
                ], 422);
            }

            return response()->json([
                'error' => 'Gagal membuat transaksi: ' . $e->getMessage(),
            ], 500);
        }
    }
}