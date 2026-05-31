<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Notifikasi;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();
        if (!$user) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 401);
        }

        $notifications = Notifikasi::where('pengguna_id', $user->pengguna_id)
            ->orderBy('created_at', 'desc')
            ->get();

        $formatted = $notifications->map(function ($notif) {
            return [
                'id' => $notif->notifikasi_id,
                'judul' => $notif->judul,
                'pesan' => $notif->pesan,
                'jenis' => $notif->jenis,
                'url_redirect' => $notif->url_redirect,
                'is_read' => (bool) $notif->is_read,
                'created_at' => $notif->created_at ? $notif->created_at->toIso8601String() : null,
            ];
        });

        return response()->json([
            'success' => true,
            'data' => $formatted
        ]);
    }

    public function read($id)
    {
        $user = auth()->user();
        if (!$user) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 401);
        }

        $notif = Notifikasi::where('notifikasi_id', $id)
            ->where('pengguna_id', $user->pengguna_id)
            ->first();

        if (!$notif) {
            return response()->json(['success' => false, 'message' => 'Notifikasi tidak ditemukan'], 404);
        }

        $notif->update(['is_read' => true]);

        return response()->json([
            'success' => true,
            'message' => 'Notifikasi ditandai sebagai dibaca'
        ]);
    }

    public function readAll()
    {
        $user = auth()->user();
        if (!$user) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 401);
        }

        Notifikasi::where('pengguna_id', $user->pengguna_id)
            ->where('is_read', false)
            ->update(['is_read' => true]);

        return response()->json([
            'success' => true,
            'message' => 'Semua notifikasi ditandai sebagai dibaca'
        ]);
    }

    public function destroy($id)
    {
        $user = auth()->user();
        if (!$user) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 401);
        }

        $notif = Notifikasi::where('notifikasi_id', $id)
            ->where('pengguna_id', $user->pengguna_id)
            ->first();

        if (!$notif) {
            return response()->json(['success' => false, 'message' => 'Notifikasi tidak ditemukan'], 404);
        }

        $notif->delete();

        return response()->json([
            'success' => true,
            'message' => 'Notifikasi berhasil dihapus'
        ]);
    }
}
