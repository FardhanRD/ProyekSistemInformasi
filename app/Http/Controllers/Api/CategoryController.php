<?php
namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use App\Models\Kategori;
class CategoryController extends Controller {
    public function index() {
        $categories = Kategori::whereNull('parent_id')->select('kategori_id as id', 'nama_kategori as name')->get();
        return response()->json(['status'=>'success', 'data'=>$categories], 200);
    }
}