@extends('layouts.buyer')

@section('title','Rating Toko')

@section('content')
<div class="card border-0 shadow-sm">
    <div class="card-body">
        <h1 class="h4">Rating Toko: {{ $supplier->nama_toko ?? 'Toko' }}</h1>
        <form method="post" action="{{ route('rating.toko.submit', $supplier->supplier_id) }}">
            @csrf
            <div class="mb-3">
                <label class="form-label">Bintang (1-5)</label>
                <input type="number" class="form-control" name="bintang" min="1" max="5" value="5">
            </div>
            <div class="mb-3">
                <label class="form-label">Komentar</label>
                <textarea class="form-control" name="komentar" rows="4"></textarea>
            </div>
            <button class="btn btn-primary" type="submit">Kirim Rating</button>
        </form>
    </div>
</div>

@endsection
