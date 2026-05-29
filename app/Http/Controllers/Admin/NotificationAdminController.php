<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Notifikasi;

class NotificationAdminController extends Controller
{
    public function unread()
    {
        $notifs = Notifikasi::where('pengguna_id', auth()->user()->pengguna_id)
            ->where('is_read', 0)
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get()
            ->map(fn ($n) => [
                'id' => $n->notifikasi_id,
                'judul' => $n->judul,
                'pesan' => $n->pesan,
                'jenis' => $n->jenis,
                'url' => $n->url_redirect,
                'waktu' => optional($n->created_at)->diffForHumans() ?? '-',
            ])
            ->values();

        return response()->json([
            'notifs' => $notifs,
            'count' => $notifs->count(),
        ]);
    }

    public function markRead($id)
    {
        Notifikasi::where('notifikasi_id', $id)
            ->where('pengguna_id', auth()->user()->pengguna_id)
            ->update(['is_read' => 1]);

        return response()->json(['success' => true]);
    }

    public function readAll()
    {
        Notifikasi::where('pengguna_id', auth()->user()->pengguna_id)
            ->where('is_read', 0)
            ->update(['is_read' => 1]);

        return response()->json(['success' => true]);
    }
}
