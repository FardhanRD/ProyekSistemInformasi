@extends('layouts.buyer')

@section('title','Tracking Pesanan')

@section('content')
<div class="card border-0 shadow-sm">
	<div class="card-body">
		<h1 class="h4">Tracking - {{ $trans->kode_transaksi ?? '-' }}</h1>
		<p class="mb-1">Status: <strong>{{ $trans->status ?? '-' }}</strong></p>
		<p class="mb-0 text-muted">Alamat: {{ optional($trans->alamat)->alamat_lengkap ?? '-' }}</p>
	</div>
</div>

@endsection
