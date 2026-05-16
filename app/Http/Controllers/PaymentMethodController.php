<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;

class PaymentMethodController extends Controller
{
    public function index()
    {
        $methods = Schema::hasTable('metode_pembayaran') ? \App\Models\MetodePembayaran::where('is_active',1)->get() : collect();
        return view('payment.methods', compact('methods'));
    }
}
