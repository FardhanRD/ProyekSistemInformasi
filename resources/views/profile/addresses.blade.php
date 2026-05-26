@extends('layouts.app')

@section('content')
<div class="container mt-5">
    <div class="row">
        <div class="col-md-8 mx-auto">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2>{{ __('ui.profile_addresses') }}</h2>
                <a href="{{ route('profile.address.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus"></i> {{ __('ui.add_address') }}
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
                    <p class="mb-0">{{ __('ui.no_address') }} <a href="{{ route('profile.address.create') }}">{{ __('ui.add_new_address') }}</a></p>
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
                                            <span class="badge bg-success ms-2">{{ __('ui.default_address') }}</span>
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
                                        <i class="fas fa-edit"></i> {{ __('ui.edit') }}
                                    </a>
                                    <form action="{{ route('profile.address.delete', $address->alamat_id) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger" onclick="return confirm('{{ __('ui.address_confirm_delete') }}')">
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
                    <i class="fas fa-arrow-left"></i> {{ __('ui.back_to_profile') }}
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
