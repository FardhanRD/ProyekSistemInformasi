<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\GambarProduk;
use App\Models\Produk;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;

class MediaController extends Controller
{
    public function index(Request $request)
    {
        if (!Schema::hasTable('gambar_produk') || !Schema::hasTable('produk')) {
            return view('admin.media.index', [
                'media' => collect(),
                'produk_list' => collect(),
                'produk_filter' => null,
            ]);
        }

        $produk_id = $request->get('produk_id');

        $media = GambarProduk::with(['produk'])
            ->when($produk_id, fn($q) => $q->where('produk_id', $produk_id))
            ->orderBy('produk_id')
            ->orderBy('urutan')
            ->paginate(24)
            ->withQueryString();

        $produk_list = Produk::where('is_active', 1)
            ->orderBy('nama_produk')
            ->get();

        return view('admin.media.index', [
            'media' => $media,
            'produk_list' => $produk_list,
            'produk_filter' => $produk_id,
        ]);
    }

    public function upload(Request $request)
    {
        $request->validate([
            'produk_id' => 'required|exists:produk,produk_id',
            'gambar' => 'required|image|mimes:jpg,jpeg,png,webp|max:10240',
        ]);

        $file = $request->file('gambar');
            $filename = time() . '_' . $file->getClientOriginalName();
            $directory = 'products/' . $request->get('produk_id');
            
            // Store file
            Storage::disk('public')->putFileAs($directory, $file, $filename);
            
            $path = $directory . '/' . $filename;

        $maxUrutan = GambarProduk::where('produk_id', $request->get('produk_id'))->max('urutan') ?? -1;

        GambarProduk::create([
            'produk_id' => $request->get('produk_id'),
            'url_gambar' => $path,
            'alt_text' => $request->get('alt_text', 'Product Image'),
            'urutan' => $maxUrutan + 1,
        ]);

        return redirect()->route('admin.media.index', ['produk_id' => $request->get('produk_id')])
            ->with('success', 'Gambar berhasil diunggah.');
    }

    public function setThumbnail($id)
    {
        $media = GambarProduk::findOrFail($id);
        $produk_id = $media->produk_id;

        // Set all other images in same product to higher urutan
        GambarProduk::where('produk_id', $produk_id)
            ->where('gambar_id', '!=', $id)
            ->update(['urutan' => DB::raw('urutan + 1')]);

        // Set this image as thumbnail (urutan = 0)
        $media->update(['urutan' => 0]);

        return redirect()->route('admin.media.index', ['produk_id' => $produk_id])
            ->with('success', 'Gambar ditetapkan sebagai thumbnail.');
    }

    public function destroy($id)
    {
        $media = GambarProduk::findOrFail($id);
        $produk_id = $media->produk_id;

        if ($media->url_gambar && Storage::disk('public')->exists($media->url_gambar)) {
            Storage::disk('public')->delete($media->url_gambar);
        }

        $media->delete();

        return redirect()->route('admin.media.index', ['produk_id' => $produk_id])
            ->with('success', 'Gambar berhasil dihapus.');
    }
}
