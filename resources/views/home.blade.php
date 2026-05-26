@extends('layouts.buyer')

@section('title', __('ui.home') ?? 'Home')

@section('content')
<div class="p-4 p-md-5 mb-4 bg-white rounded-3 shadow-sm">
    <div class="container-fluid py-2">
        <h1 class="display-6 fw-bold mb-2">{{ __('ui.home_welcome') }}</h1>
        <p class="col-md-8 fs-5 text-muted mb-0">{{ __('ui.home_subtitle') }}</p>
    </div>
</div>

<div class="d-flex justify-content-between align-items-center mb-3">
    <h2 class="h4 mb-0">{{ __('ui.featured_products') }}</h2>
    <a href="{{ url('/produk') }}" class="text-decoration-none">{{ __('ui.view_all') }}</a>


</div>

<div class="row g-3">
    @forelse($featured as $p)
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
                'addCartUrl'=>url('/keranjang')
            ])
        </div>
    @empty
        <div class="col-12">
            <div class="alert alert-warning mb-0">{{ __('ui.no_featured_products') }}</div>
        </div>
    @endforelse
</div>

<h2>{{ __('ui.categories') }}</h2>
<div class="row g-3">
    @foreach($categories as $c)
        <div class="col-6 col-md-4 col-lg-3">
            <a href="{{ route('category.show',$c->slug) }}" class="text-decoration-none">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body">
                        <div class="fw-semibold">{{ $c->nama_kategori }}</div>
                        <div class="text-muted small">{{ __('ui.open_category') }}</div>
                    </div>
                </div>
            </a>
        </div>
    @endforeach
</div>

@endsection
