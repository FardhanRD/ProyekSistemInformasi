<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Ekspedisi;
use App\Models\Notifikasi;
use App\Models\Pesanan;
use App\Models\TrackingLog;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;

class ShippingController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->get('search');

        $ekspedisi = Ekspedisi::orderBy('nama_ekspedisi')->get();

        $trackingAktif = Pesanan::with(['transaksi.buyer', 'ekspedisi'])
            ->whereNotIn('status_pesanan', ['diterima', 'bermasalah'])
            ->when($search, function ($query) use ($search) {
                $query->whereHas('transaksi', function ($transaksi) use ($search) {
                    $transaksi->where('kode_transaksi', 'like', "%{$search}%")
                        ->orWhereHas('pengguna', function ($pengguna) use ($search) {
                            $pengguna->where('nama_pengguna', 'like', "%{$search}%");
                        });
                });
            })
            ->orderByDesc('updated_at')
            ->paginate(15)
            ->withQueryString();

        return view('admin.shipping.index', [
            'ekspedisi' => $ekspedisi,
            'trackingAktif' => $trackingAktif,
        ]);
    }

    public function storeEkspedisi(Request $request)
    {
        $data = $request->validate([
            'nama_ekspedisi' => 'required|string|max:100',
            'jenis_layanan' => 'required|string|max:80',
            'estimasi_hari' => 'nullable|string|max:30',
            'ongkir_flat' => 'nullable|numeric|min:0',
            'logo_url' => 'nullable|string|max:500',
            'is_active' => 'nullable|boolean',
        ]);

        $data['is_active'] = $request->boolean('is_active');
        Ekspedisi::create($data);

        return back()->with('success', 'Ekspedisi berhasil ditambahkan.');
    }

    public function updateEkspedisi(Request $request, $id)
    {
        $ekspedisi = Ekspedisi::findOrFail($id);
        $data = $request->validate([
            'nama_ekspedisi' => 'required|string|max:100',
            'jenis_layanan' => 'required|string|max:80',
            'estimasi_hari' => 'nullable|string|max:30',
            'ongkir_flat' => 'nullable|numeric|min:0',
            'logo_url' => 'nullable|string|max:500',
            'is_active' => 'nullable|boolean',
        ]);

        $data['is_active'] = $request->boolean('is_active');
        $ekspedisi->update($data);

        return back()->with('success', 'Ekspedisi berhasil diperbarui.');
    }

    public function destroyEkspedisi($id)
    {
        Ekspedisi::findOrFail($id)->delete();
        return back()->with('success', 'Ekspedisi berhasil dihapus.');
    }

    public function toggleEkspedisi($id)
    {
        $ekspedisi = Ekspedisi::findOrFail($id);
        $ekspedisi->update(['is_active' => !$ekspedisi->is_active]);

        return back()->with('success', 'Status ekspedisi berhasil diubah.');
    }

    public function updateResi(Request $request, $pesanan_id)
    {
        $data = $request->validate([
            'no_resi' => 'required|string|max:100',
        ]);

        $pesanan = Pesanan::with('transaksi.buyer')->findOrFail($pesanan_id);
        
        DB::beginTransaction();
        try {
            $pesanan->update([
                'no_resi' => $data['no_resi'],
                'status_pesanan' => 'dalam_pengiriman',
            ]);
            
            $pesanan->transaksi->update(['status' => 'dikirim']);

            TrackingLog::create([
                'pesanan_id' => $pesanan->pesanan_id,
                'status' => 'Paket Dikirim',
                'deskripsi' => 'Nomor resi: ' . $data['no_resi'],
                'waktu_update' => now(),
            ]);

            $buyerId = $pesanan->transaksi?->buyer?->pengguna_id;
            if ($buyerId) {
                Notifikasi::create([
                    'pengguna_id' => $buyerId,
                    'judul' => '🚚 Pesanan Dikirim',
                    'pesan' => 'Pesanan ' . ($pesanan->transaksi?->kode_transaksi ?? '-') . ' sedang dikirim. Resi: ' . $data['no_resi'],
                    'jenis' => 'pengiriman',
                    'url_redirect' => '/tracking/' . ($pesanan->transaksi?->kode_transaksi ?? ''),
                    'is_read' => 0,
                    'created_at' => now(),
                ]);
            }

            DB::commit();
            return response()->json([
                'success' => true,
                'message' => 'Resi berhasil disimpan'
            ], 200, ['Content-Type' => 'application/json']);
        } catch (\Throwable $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
                'error' => class_basename($e)
            ], 500, ['Content-Type' => 'application/json']);
        }
    }

    public function updateStatus(Request $request, $pesanan_id)
    {
        $data = $request->validate([
            'status' => 'required|in:menunggu_konfirmasi,dikemas,siap_kirim,diserahkan_ke_kurir,dalam_pengiriman,tiba_di_tujuan,diterima,bermasalah',
            'deskripsi' => 'nullable|string|max:1000',
            'lokasi' => 'nullable|string|max:200',
        ]);

        $pesanan = Pesanan::with('transaksi.buyer')->findOrFail($pesanan_id);

        DB::beginTransaction();
        try {
            $pesanan->update(['status_pesanan' => $data['status']]);

            $statusTransaksiMap = [
                'dikemas' => 'diproses',
                'siap_kirim' => 'diproses',
                'diserahkan_ke_kurir' => 'dikirim',
                'dalam_pengiriman' => 'dikirim',
                'tiba_di_tujuan' => 'dikirim',
                'diterima' => 'selesai',
            ];

            if (isset($statusTransaksiMap[$data['status']])) {
                $pesanan->transaksi?->update([
                    'status' => $statusTransaksiMap[$data['status']],
                ]);
            }

            TrackingLog::create([
                'pesanan_id' => $pesanan->pesanan_id,
                'status' => match ($data['status']) {
                    'dalam_pengiriman' => 'Paket Dikirim',
                    'diterima' => 'Paket Diterima',
                    'dikemas' => 'Pesanan Dikemas',
                    default => ucfirst(str_replace('_', ' ', $data['status'])),
                },
                'deskripsi' => $data['deskripsi'] ?? 'Status pengiriman diperbarui oleh admin.',
                'lokasi' => $data['lokasi'] ?? null,
                'waktu_update' => now(),
            ]);

            $buyerId = $pesanan->transaksi?->buyer?->pengguna_id;
            if ($buyerId) {
                $labelNotif = [
                    'dalam_pengiriman' => ['judul' => '📦 Pesanan Dikirim', 'pesan' => 'Pesanan ' . ($pesanan->transaksi?->kode_transaksi ?? '-') . ' sedang dalam pengiriman. Resi: ' . ($pesanan->no_resi ?? '-')],
                    'diterima' => ['judul' => '✅ Pesanan Selesai', 'pesan' => 'Pesanan ' . ($pesanan->transaksi?->kode_transaksi ?? '-') . ' telah diterima. Jangan lupa beri rating!'],
                    'dikemas' => ['judul' => '📦 Pesanan Dikemas', 'pesan' => 'Pesanan ' . ($pesanan->transaksi?->kode_transaksi ?? '-') . ' sedang dikemas oleh penjual.'],
                ];

                if (isset($labelNotif[$data['status']])) {
                    Notifikasi::create([
                        'pengguna_id' => $buyerId,
                        'judul' => $labelNotif[$data['status']]['judul'],
                        'pesan' => $labelNotif[$data['status']]['pesan'],
                        'jenis' => 'pengiriman',
                        'url_redirect' => '/orders',
                        'is_read' => 0,
                        'created_at' => now(),
                    ]);
                }
            }

            DB::commit();
            return response()->json([
                'success' => true,
                'message' => 'Status berhasil diperbarui'
            ], 200, ['Content-Type' => 'application/json']);
        } catch (\Throwable $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
                'error' => class_basename($e)
            ], 500, ['Content-Type' => 'application/json']);
        }
    }

    public function storeTrackingLog(Request $request)
    {
        $data = $request->validate([
            'pesanan_id' => 'required|exists:pesanan,pesanan_id',
            'status' => 'nullable|string|max:100',
            'deskripsi' => 'required|string|max:1000',
            'lokasi' => 'nullable|string|max:200',
        ]);

        TrackingLog::create([
            'pesanan_id' => $data['pesanan_id'],
            'status' => $data['status'] ?? 'manual',
            'deskripsi' => $data['deskripsi'],
            'lokasi' => $data['lokasi'] ?? null,
            'waktu_update' => now(),
        ]);

        $pesanan = Pesanan::with('transaksi')->findOrFail($data['pesanan_id']);
        $buyerId = $pesanan->transaksi?->pengguna_id;
        if ($buyerId) {
            kirimNotifikasi(
                $buyerId,
                'Tracking pesanan diperbarui',
                $data['deskripsi'],
                'tracking',
                url('/tracking/' . ($pesanan->transaksi?->kode_transaksi ?? ''))
            );
        }

        return back()->with('success', 'Tracking log manual berhasil ditambahkan.');
    }
}
