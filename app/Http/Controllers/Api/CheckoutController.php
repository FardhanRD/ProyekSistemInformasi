<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Ekspedisi;
use App\Models\MetodePembayaran;
use Illuminate\Http\Request;

class CheckoutController extends Controller
{
    public function options()
    {
        $shipping = Ekspedisi::query()
            ->selectRaw('MIN(ekspedisi_id) as ekspedisi_id, nama_ekspedisi, jenis_layanan, estimasi_hari, MIN(ongkir_flat) as ongkir_flat, MIN(ongkir_per_km) as ongkir_per_km, MAX(logo_url) as logo_url')
            ->where('is_active', 1)
            ->groupBy('nama_ekspedisi', 'jenis_layanan', 'estimasi_hari')
            ->orderBy('nama_ekspedisi')
            ->get()
            ->map(function ($e) {
                return [
                    'id' => $e->ekspedisi_id,
                    'name' => $e->nama_ekspedisi . ' (' . $e->jenis_layanan . ')',
                    'price' => (int) $e->ongkir_flat,
                    'est' => $e->estimasi_hari,
                    'per_km' => (int) $e->ongkir_per_km,
                    'logo_url' => $e->logo_url ? url('storage/' . $e->logo_url) : null,
                ];
            });

        $payment = MetodePembayaran::where('is_active', 1)->get()->map(function ($p) {
            return [
                'id' => $p->metode_id,
                'type' => $p->jenis,
                'name' => $p->metode,
                'instruction' => $p->instruksi,
                'logo_url' => $p->logo_url ? url('storage/' . $p->logo_url) : null,
            ];
        });

        return response()->json([
            'status' => 'success',
            'shipping' => $shipping,
            'payment' => $payment
        ], 200);
    }
}
