@extends('layouts.buyer')

@section('title','Produk')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h1 class="h4 mb-0">Daftar Produk</h1>
    <span class="text-muted small">{{ $products->total() }} produk</span>
</div>

<form method="get" action="{{ route('product.index') }}" class="card border-0 shadow-sm mb-4">
    <div class="card-body row g-3 align-items-end">
        <div class="col-md-4">
            <label class="form-label">Cari</label>
            <input type="search" name="q" value="{{ request('q') }}" class="form-control" placeholder="Nama produk">
        </div>
        <div class="col-md-2">
            <label class="form-label">Kategori</label>
            <input type="text" name="kategori" value="{{ request('kategori') }}" class="form-control" placeholder="ID kategori">
        </div>
        <div class="col-md-2">
            <label class="form-label">Min Harga</label>
            <input type="number" name="min_price" value="{{ request('min_price') }}" class="form-control">
        </div>
        <div class="col-md-2">
            <label class="form-label">Max Harga</label>
            <input type="number" name="max_price" value="{{ request('max_price') }}" class="form-control">
        </div>
        <div class="col-md-2">
            <label class="form-label">Rating</label>
            <select name="rating" class="form-select">
                <option value="">Semua</option>
                <option value="4" @selected(request('rating') == 4)>4+</option>
                <option value="3" @selected(request('rating') == 3)>3+</option>
                <option value="2" @selected(request('rating') == 2)>2+</option>
            </select>
        </div>
        <div class="col-md-2">
            <label class="form-label">Gender</label>
            <select name="gender" class="form-select">
                <option value="">Semua</option>
                <option value="cowo" @selected(request('gender') === 'cowo')>Cowo</option>
                <option value="cewe" @selected(request('gender') === 'cewe')>Cewe</option>
                <option value="unisex" @selected(request('gender') === 'unisex')>Unisex</option>
            </select>
        </div>
        <div class="col-md-2 d-grid">
            <button class="btn btn-primary" type="submit">Filter</button>
        </div>
    </div>
</form>

<div class="row g-3">
    @forelse($products as $p)
        @php
            $img = $p->images->first()->url_gambar ?? 'https://via.placeholder.com/400x300?text=No+Image';
            $detail = $p->details->first();
        @endphp
        <div class="col-6 col-md-4 col-lg-3">
            @include('components.product-card',[
                'image'=>$img,
                'title'=>$p->nama_produk,
                'price'=>$p->harga_dasar,
                'category'=>$p->kategori->nama_kategori ?? null,
                'rating'=>$p->average_rating ?? null,
                'productId'=>$p->produk_id,
                'detailId'=>$detail->detail_produk_id ?? 0,
                'detailUrl'=>route('product.show',$p->slug),
                'addCartUrl'=>route('cart.add')
            ])
        </div>
    @empty
        <div class="col-12"><div class="alert alert-warning">Produk belum tersedia.</div></div>
    @endforelse
</div>

<div class="mt-4">{{ $products->links() }}</div>

@endsection
