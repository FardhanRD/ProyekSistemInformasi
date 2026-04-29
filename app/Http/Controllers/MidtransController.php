<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Midtrans\Config;
use Midtrans\Snap;
use App\Models\KeranjangItem;
use App\Models\Order;
use App\Models\OrderItem; // Pastikan Model ini ada
use App\Models\Product;

class MidtransController extends Controller
{
    public function __construct()
    {
        // Set Midtrans configuration with validation
        $serverKey = config('midtrans.server_key');
        $clientKey = config('midtrans.client_key');

        if (empty($serverKey) || empty($clientKey)) {
            \Log::error('Midtrans keys are not properly configured in config/midtrans.php or .env');
            throw new \Exception('Midtrans configuration error: Server key or Client key is missing');
        }

        Config::$serverKey = $serverKey;
        Config::$clientKey = $clientKey;
        Config::$isProduction = config('midtrans.is_production');
        Config::$isSanitized = config('midtrans.enable_sanitization');
        Config::$is3ds = config('midtrans.enable_3ds');
    }

    /**
     * Create a new payment transaction
     */
    public function createTransaction(Request $request)
    {
        DB::beginTransaction(); // Gunakan transaction agar data aman
        try {
            $user = Auth::user();
            if (!$user) {
                return response()->json(['error' => 'User not authenticated'], 401);
            }

            // --- LOGIKA BARU: Handle "Beli Sekarang" (Single Item) vs "Checkout Keranjang" ---
            $itemsToCheckout = [];
            
            if ($request->has('product_id') && $request->has('jumlah')) {
                // Skenario A: Beli Sekarang (Langsung 1 Produk, abaikan keranjang)
                $product = Product::find($request->product_id);
                if (!$product) return response()->json(['error' => 'Produk tidak ditemukan'], 404);
                
                // Format data agar sama dengan struktur Cart
                $itemsToCheckout[] = (object) [
                    'product' => $product,
                    'product_id' => $product->id,
                    'jumlah' => $request->jumlah,
                    'harga_saat_ini' => $product->price // Pastikan nama kolom harga benar
                ];
            } else {
                // Skenario B: Checkout dari Keranjang
                $cartItems = KeranjangItem::where('user_id', $user->id)->with('product')->get();
                if ($cartItems->isEmpty()) {
                    return response()->json(['error' => 'Keranjang kosong'], 400);
                }
                $itemsToCheckout = $cartItems;
            }

            // Hitung Total
            $totalAmount = 0;
            foreach ($itemsToCheckout as $item) {
                $totalAmount += $item->jumlah * $item->harga_saat_ini;
            }

            // 1. Buat Order Utama
            $order = Order::create([
                'user_id' => $user->id,
                'total_amount' => $totalAmount,
                'status' => 'pending',
                'payment_method' => $request->payment_method ?? 'midtrans',
                'midtrans_order_id' => 'ORD-' . time() . '-' . rand(100, 999), // ID Unik
            ]);

            // 2. Simpan Detail Item ke Database (PENTING!) & Siapkan Data Midtrans
            $midtransItems = [];
            foreach ($itemsToCheckout as $item) {
                // Simpan ke tabel order_items (Anda harus buat Model & Migration ini)
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $item->product->id,
                    'quantity' => $item->jumlah,
                    'price' => $item->harga_saat_ini,
                ]);

                // Format untuk Midtrans
                $midtransItems[] = [
                    'id' => $item->product->id,
                    'price' => (int)$item->harga_saat_ini,
                    'quantity' => (int)$item->jumlah,
                    'name' => substr($item->product->name, 0, 50), // Midtrans ada limit panjang nama
                ];
            }

            // 3. Konfigurasi Parameter Snap
            $params = [
                'transaction_details' => [
                    'order_id' => $order->midtrans_order_id,
                    'gross_amount' => (int)$totalAmount,
                ],
                'customer_details' => [
                    'first_name' => $user->name,
                    'email' => $user->email,
                    'phone' => $request->phone ?? $user->phone ?? '',
                ],
                'item_details' => $midtransItems,
            ];

            // 4. Request Snap URL
            $paymentUrl = Snap::createTransaction($params)->redirect_url;

            // 5. Update Order dengan URL Pembayaran
            $order->update(['payment_url' => $paymentUrl]);

            // 6. Kosongkan Keranjang (Hanya jika checkout dari keranjang)
            if (!$request->has('product_id')) {
                KeranjangItem::where('user_id', $user->id)->delete();
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'order_id' => $order->midtrans_order_id,
                'payment_url' => $paymentUrl,
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Midtrans payment error: ' . $e->getMessage());
            return response()->json(['error' => 'Gagal: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Handle Midtrans notification callback
     */
    public function handleNotification()
    {
        try {
            $notif = new \Midtrans\Notification();

            $transaction = $notif->transaction_status;
            $type = $notif->payment_type;
            $orderId = $notif->order_id;
            $fraud = $notif->fraud_status;

            // Cari order berdasarkan midtrans_order_id
            $order = Order::where('midtrans_order_id', $orderId)->first();

            if (!$order) {
                return response()->json(['message' => 'Order not found'], 404);
            }

            // Logika Status
            if ($transaction == 'capture') {
                if ($type == 'credit_card') {
                    if ($fraud == 'challenge') {
                        $order->status = 'pending';
                    } else {
                        $order->status = 'paid';
                    }
                }
            } else if ($transaction == 'settlement') {
                $order->status = 'paid';
            } else if ($transaction == 'pending') {
                $order->status = 'pending';
            } else if ($transaction == 'deny') {
                $order->status = 'failed';
            } else if ($transaction == 'expire') {
                $order->status = 'expired';
            } else if ($transaction == 'cancel') {
                $order->status = 'cancelled';
            }

            // JIKA SUKSES BAYAR -> KURANGI STOK
            if ($order->status == 'paid' && $order->isDirty('status')) { 
                // isDirty mengecek apakah status baru saja berubah (supaya stok ga berkurang 2x)
                $this->reduceStock($order);
            }

            $order->save();
            return response()->json(['message' => 'Notification handled']);

        } catch (\Exception $e) {
            \Log::error('Webhook Error: ' . $e->getMessage());
            return response()->json(['message' => 'Error'], 500);
        }
    }

    /**
     * Reduce stock after successful payment
     */
    private function reduceStock($order)
    {
        // Load detail item yang dibeli dari tabel order_items
        $orderItems = OrderItem::where('order_id', $order->id)->get();

        foreach ($orderItems as $item) {
            $product = Product::find($item->product_id);
            if ($product) {
                // Kurangi stok
                $product->stock = $product->stock - $item->quantity;
                
                // Pastikan stok tidak minus
                if ($product->stock < 0) $product->stock = 0;
                
                $product->save();
            }
        }
    }

    public function paymentStatus(Request $request, $orderId)
    {
        $order = Order::where('midtrans_order_id', $orderId)->firstOrFail();
        return view('movr.payment.status', compact('order'));
    }
}