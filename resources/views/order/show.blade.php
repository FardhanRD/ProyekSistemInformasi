@extends('layouts.app')

@section('title','Detail Pesanan')

@section('content')
<div class="card border-0 shadow-sm mb-3">
    <div class="card-body">
        <h1 class="h4">Detail Pesanan {{ $order->kode_transaksi }}</h1>
        <p class="mb-1">Status: <strong>{{ $order->status }}</strong></p>
        <p class="mb-0">Total: <strong>Rp {{ number_format($order->total_harga,0,',','.') }}</strong></p>
    </div>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-body">
        <h3 class="h6">Item</h3>
        <div class="table-responsive">
            <table class="table align-middle mb-0">
                <thead><tr><th>Nama</th><th>Harga</th><th>Qty</th><th>Subtotal</th></tr></thead>
                <tbody>
                @foreach($order->details as $d)
                    <tr>
                        <td>{{ $d->nama_produk_snap }}</td>
                        <td>Rp {{ number_format($d->harga_snap,0,',','.') }}</td>
                        <td>{{ $d->quantity }}</td>
                        <td>Rp {{ number_format($d->subtotal,0,',','.') }}</td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

@endsection
