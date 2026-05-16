@extends('layouts.buyer')

@section('title','Voucher')

@section('content')
<div class="row g-4">
    <div class="col-lg-7">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <h1 class="h4">Voucher</h1>
                @if($vouchers->isEmpty())
                    <div class="text-muted">Tidak ada voucher aktif.</div>
                @else
                    <div class="list-group list-group-flush">
                        @foreach($vouchers as $v)
                            <div class="list-group-item px-0">
                                <div class="fw-semibold">{{ $v->kode_voucher }} - {{ $v->nama_voucher }}</div>
                                <div class="text-muted small">Berlaku sampai {{ $v->berlaku_sampai }}</div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    </div>
    <div class="col-lg-5">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <h3 class="h6">Gunakan Voucher</h3>
                <form method="post" action="{{ route('voucher.apply') }}">
                    @csrf
                    <input type="text" name="kode" class="form-control mb-3" placeholder="KODEVOUCHER">
                    <button class="btn btn-primary w-100" type="submit">Terapkan</button>
                </form>
            </div>
        </div>
    </div>
</div>

@endsection
