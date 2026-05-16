@extends('layouts.buyer')

@section('title',$cat->nama_kategori)

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h1 class="h4 mb-0">{{ $cat->nama_kategori }}</h1>
    <span class="text-muted small">{{ $products->total() }} produk</span>
</div>
<div class="row g-3">
    @forelse($products as $p)
        @php $img = $p->images->first()->url_gambar ?? 'https://via.placeholder.com/400x300'; $detail = $p->details->first(); @endphp
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
        <div class="col-12"><div class="alert alert-warning">Belum ada produk di kategori ini.</div></div>
    @endforelse
</div>

<div class="mt-4">{{ $products->links() }}</div>

@endsection
