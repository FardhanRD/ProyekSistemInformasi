<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CustomerDashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        
        // Get all orders for the current user
        $orders = Order::where('user_id', $user->id)
            ->with(['items.product'])
            ->orderBy('created_at', 'desc')
            ->get();
        
        // Get order statistics
        $totalOrders = $orders->count();
        $totalSpent = $orders->where('status', 'paid')->sum('total_amount');
        $pendingOrders = $orders->where('status', 'pending')->count();
        $completedOrders = $orders->whereIn('status', ['paid', 'delivered'])->count();
        
        return view('movr.customer.dashboard', compact(
            'orders', 
            'totalOrders', 
            'totalSpent', 
            'pendingOrders', 
            'completedOrders'
        ));
    }

    public function show($id)
    {
        $order = Order::where('user_id', Auth::id())
            ->with(['items.product'])
            ->findOrFail($id);
            
        return view('movr.customer.order_show', compact('order'));
    }
}