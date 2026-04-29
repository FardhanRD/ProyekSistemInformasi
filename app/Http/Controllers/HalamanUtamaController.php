<?php

namespace App\Http\Controllers;

use App\Models\Product; // Pastikan pakai Model Product (Inggris)
use Illuminate\Http\Request;

class HalamanUtamaController extends Controller
{
    /**
     * Menampilkan halaman home dengan produk terbaru
     */
    public function index()
    {
        // Ambil 8 produk terbaru beserta kategorinya
        $products = Product::with('category')->latest()->take(8)->get();
        
        // Kirim ke view home
        return view('movr.home', compact('products'));
    }
}