<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Ekspedisi;
use App\Models\Pesanan;
use App\Models\TrackingLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;

class ShippingController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->get('search');

        $ekspedisi = Ekspedisi::orderBy('nama_ekspedisi')->get();

        $trackingAktif = Pesanan::with(['transaksi.pengguna', 'ekspedisi'])
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

    public function updateResi(Request $request)
    {
        $data = $request->validate([
            'pesanan_id' => 'required|exists:pesanan,pesanan_id',
            'no_resi' => 'required|string|max:100',
        ]);

        $pesanan = Pesanan::findOrFail($data['pesanan_id']);
        $pesanan->update(['no_resi' => $data['no_resi']]);

        TrackingLog::create([
            'pesanan_id' => $pesanan->pesanan_id,
            'status' => $pesanan->status_pesanan,
            'deskripsi' => 'Nomor resi diperbarui: ' . $data['no_resi'],
            'lokasi' => null,
            'waktu_update' => now(),
        ]);

        $buyerId = $pesanan->transaksi?->pengguna_id;
        if ($buyerId) {
            kirimNotifikasi(
                $buyerId,
                'Nomor resi pesanan diperbarui',
                'Nomor resi untuk pesanan ' . ($pesanan->transaksi?->kode_transaksi ?? '-') . ' sudah ditambahkan.',
                'shipping',
                url('/orders/' . ($pesanan->transaksi?->kode_transaksi ?? ''))
            );
        }

        return back()->with('success', 'Nomor resi berhasil diperbarui.');
    }

    public function updateStatus(Request $request)
    {
        $data = $request->validate([
            'pesanan_id' => 'required|exists:pesanan,pesanan_id',
            'status_pesanan' => 'required|in:menunggu_konfirmasi,dikemas,siap_kirim,diserahkan_ke_kurir,dalam_pengiriman,tiba_di_tujuan,diterima,bermasalah',
            'deskripsi' => 'nullable|string|max:1000',
            'lokasi' => 'nullable|string|max:200',
        ]);

        $pesanan = Pesanan::findOrFail($data['pesanan_id']);
        $pesanan->update(['status_pesanan' => $data['status_pesanan']]);

        TrackingLog::create([
            'pesanan_id' => $pesanan->pesanan_id,
            'status' => $data['status_pesanan'],
            'deskripsi' => $data['deskripsi'] ?? 'Status pengiriman diperbarui oleh admin.',
            'lokasi' => $data['lokasi'] ?? null,
            'waktu_update' => now(),
        ]);

        $buyerId = $pesanan->transaksi?->pengguna_id;
        if ($buyerId) {
            kirimNotifikasi(
                $buyerId,
                'Status pengiriman pesanan berubah',
                'Pesanan ' . ($pesanan->transaksi?->kode_transaksi ?? '-') . ' sekarang berstatus ' . $data['status_pesanan'] . '.',
                'shipping',
                url('/tracking/' . ($pesanan->transaksi?->kode_transaksi ?? ''))
            );
        }

        return back()->with('success', 'Status pengiriman berhasil diperbarui.');
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
