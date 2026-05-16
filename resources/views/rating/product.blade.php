@extends('layouts.app')

@section('title','Rating Produk')

@section('content')
<div class="card border-0 shadow-sm">
    <div class="card-body">
        <h1 class="h4">Ulas Produk: {{ $product->nama_produk }}</h1>
        <form method="post" action="{{ route('rating.product.submit', $product->produk_id) }}">
            @csrf
            <div class="mb-3">
                <label class="form-label">Bintang (1-5)</label>
                <input type="number" class="form-control" name="bintang" min="1" max="5" value="5">
            </div>
            <div class="mb-3">
                <label class="form-label">Judul</label>
                <input type="text" class="form-control" name="judul_ulasan">
            </div>
            <div class="mb-3">
                <label class="form-label">Ulasan</label>
                <textarea class="form-control" name="isi_ulasan" rows="4"></textarea>
            </div>
            <button class="btn btn-primary" type="submit">Kirim Ulasan</button>
        </form>
    </div>
</div>

@endsection
