<?php

namespace App\Http\Controllers;

use App\Models\Notifikasi;

class NotificationBuyerController extends Controller
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
        $notif = Notifikasi::where('notifikasi_id', $id)
            ->where('pengguna_id', auth()->user()->pengguna_id)
            ->firstOrFail();

        $notif->update(['is_read' => 1]);

        if (request()->expectsJson() || request()->ajax()) {
            return response()->json([
                'success' => true,
                'url_redirect' => $notif->url_redirect,
            ]);
        }

        if ($notif->url_redirect) {
            return redirect($notif->url_redirect);
        }

        return back();
    }

    public function readAll()
    {
        Notifikasi::where('pengguna_id', auth()->user()->pengguna_id)
            ->where('is_read', 0)
            ->update(['is_read' => 1]);

        if (request()->expectsJson() || request()->ajax()) {
            return response()->json(['success' => true]);
        }

        return back()->with('success', 'Semua notifikasi dibaca');
    }

    public function index()
    {
        $notifikasis = Notifikasi::where('pengguna_id', auth()->user()->pengguna_id)
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        Notifikasi::where('pengguna_id', auth()->user()->pengguna_id)
            ->where('is_read', 0)
            ->update(['is_read' => 1]);

        return view('buyer.notifications.index', compact('notifikasis'));
    }
}
