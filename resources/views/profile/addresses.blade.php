@extends('layouts.app')

@section('content')
<div class="container mt-5">
    <div class="row">
        <div class="col-md-8 mx-auto">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2>Alamat Pengiriman Saya</h2>
                <a href="{{ route('profile.address.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Tambah Alamat
                </a>
            </div>

            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if($addresses->isEmpty())
                <div class="alert alert-info text-center">
                    <p class="mb-0">Belum ada alamat tersimpan. <a href="{{ route('profile.address.create') }}">Tambahkan alamat baru</a></p>
                </div>
            @else
                <div class="row">
                    @foreach($addresses as $address)
                        <div class="col-md-6 mb-4">
                            <div class="card border-{{ $address->is_utama ? 'success' : 'secondary' }}">
                                <div class="card-header bg-{{ $address->is_utama ? 'success' : 'light' }} d-flex justify-content-between align-items-center">
                                    <span>
                                        <strong>{{ $address->label }}</strong>
                                        @if($address->is_utama)
                                            <span class="badge bg-success ms-2">Utama</span>
                                        @endif
                                    </span>
                                </div>
                                <div class="card-body">
                                    <p class="card-text mb-2">
                                        <strong>{{ $address->nama_penerima }}</strong><br>
                                        <small class="text-muted">{{ $address->no_telepon }}</small>
                                    </p>
                                    <p class="card-text mb-3">
                                        {{ $address->alamat_lengkap }}<br>
                                        <small class="text-muted">{{ $address->kelurahan }}, {{ $address->kecamatan }}<br>{{ $address->kota }}, {{ $address->provinsi }} {{ $address->kode_pos }}</small>
                                    </p>
                                </div>
                                <div class="card-footer bg-light d-flex gap-2">
                                    <a href="{{ route('profile.address.edit', $address->alamat_id) }}" class="btn btn-sm btn-outline-primary">
                                        <i class="fas fa-edit"></i> Ubah
                                    </a>
                                    <form action="{{ route('profile.address.delete', $address->alamat_id) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger" onclick="return confirm('Yakin ingin menghapus alamat ini?')">
                                            <i class="fas fa-trash"></i> Hapus
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif

            <div class="mt-4">
                <a href="{{ route('profile.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Kembali ke Profil
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
