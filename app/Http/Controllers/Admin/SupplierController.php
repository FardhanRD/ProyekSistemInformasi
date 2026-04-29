<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Supplier;
use Illuminate\Http\Request;

class SupplierController extends Controller
{
    public function index()
    {
        $suppliers = Supplier::latest()->get();
        $categories = Category::orderBy('name')->get(['id', 'name']);

        return view('movr.admin.suppliers.index', compact('suppliers', 'categories'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'store_name' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'owner_name' => 'required|string|max:255',
            'address' => 'required|string|max:500',
            'phone_number' => 'required|string|max:25',
        ]);

        $category = Category::findOrFail($validated['category_id']);

        Supplier::create([
            'store_name' => $validated['store_name'],
            'category' => $category->name,
            'owner_name' => $validated['owner_name'],
            'address' => $validated['address'],
            'phone_number' => $validated['phone_number'],
        ]);

        return redirect()->route('admin.suppliers.index')->with('success', 'Supplier berhasil ditambahkan.');
    }

    public function destroy(Supplier $supplier)
    {
        $supplierName = $supplier->store_name;
        $supplier->delete();

        return redirect()->route('admin.suppliers.index')->with('success', "Supplier {$supplierName} berhasil dihapus.");
    }
}
