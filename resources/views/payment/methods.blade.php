@extends('layouts.buyer')

@section('title','Metode Pembayaran')

@section('content')
<div class="card border-0 shadow-sm">
    <div class="card-body">
        <h1 class="h4">Metode Pembayaran</h1>
        @if($methods->isEmpty())
            <div class="text-muted">Tidak ada metode pembayaran terdaftar.</div>
        @else
            <div class="row g-3">
                @foreach($methods as $m)
                    <div class="col-md-4 col-lg-3">
                        <div class="border rounded p-3 h-100">
                            <div class="fw-semibold">{{ $m->metode }}</div>
                            <div class="text-muted small">{{ $m->jenis }}</div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</div>

@endsection
