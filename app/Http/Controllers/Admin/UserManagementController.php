<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\AdminLogger;
use Illuminate\Http\Request;

class UserManagementController extends Controller
{
    public function index(Request $request)
    {
        $query = User::query()->where('role', '!=', 'admin')->withCount('orders');

        if ($request->filled('search')) {
            $search = (string) $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $data = $query->latest()->paginate(20);
        return view('movr.admin.users.index', compact('data'));
    }

    public function purchaseHistory(User $user)
    {
        return response()->json($user->orders()->with('items.product', 'items.variant.masterProduct')->latest()->paginate(20));
    }

    public function block(Request $request, User $user, AdminLogger $logger)
    {
        $validated = $request->validate([
            'reason' => 'nullable|string|max:255',
        ]);

        $user->update([
            'is_blocked' => true,
            'blocked_at' => now(),
            'blocked_reason' => $validated['reason'] ?? 'Diblokir oleh admin',
        ]);

        $logger->logActivity(auth()->id(), 'user_management', 'block_user', 'Blokir user', ['user_id' => $user->id]);

        return response()->json($user->fresh());
    }

    public function unblock(User $user, AdminLogger $logger)
    {
        $user->update([
            'is_blocked' => false,
            'blocked_at' => null,
            'blocked_reason' => null,
        ]);

        $logger->logActivity(auth()->id(), 'user_management', 'unblock_user', 'Buka blokir user', ['user_id' => $user->id]);

        return response()->json($user->fresh());
    }
}
