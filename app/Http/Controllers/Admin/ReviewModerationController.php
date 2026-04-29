<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Ulasan;
use App\Services\AdminLogger;
use Illuminate\Http\Request;

class ReviewModerationController extends Controller
{
    public function index(Request $request)
    {
        $query = Ulasan::with(['product', 'pembeli', 'moderator'])->latest();

        if ($request->filled('moderation_status')) {
            $query->where('moderation_status', $request->moderation_status);
        }

        $data = $query->paginate(20);
        return view('movr.admin.reviews.index', compact('data'));
    }

    public function moderate(Request $request, Ulasan $ulasan, AdminLogger $logger)
    {
        $validated = $request->validate([
            'moderation_status' => 'required|in:approved,rejected,pending',
            'admin_reply' => 'nullable|string|max:1000',
        ]);

        $ulasan->update([
            'moderation_status' => $validated['moderation_status'],
            'admin_reply' => $validated['admin_reply'] ?? $ulasan->admin_reply,
            'moderated_at' => now(),
            'moderated_by' => auth()->id(),
        ]);

        $logger->logActivity(auth()->id(), 'review', 'moderate', 'Moderasi review produk', [
            'review_id' => $ulasan->id,
            'status' => $validated['moderation_status'],
        ]);

        return response()->json($ulasan->fresh()->load('moderator'));
    }

    public function destroy(Ulasan $ulasan, AdminLogger $logger)
    {
        $id = $ulasan->id;
        $ulasan->delete();

        $logger->logActivity(auth()->id(), 'review', 'delete', 'Hapus review bermasalah', ['review_id' => $id]);

        return response()->json(['message' => 'Review dihapus']);
    }
}
