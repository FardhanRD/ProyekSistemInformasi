@extends('layouts.buyer')

@section('content')
<div class="container mt-5">
    <div class="row">
        <div class="col-md-6 mx-auto">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0">
                        {{ isset($address) ? 'Ubah Alamat' : 'Tambah Alamat Baru' }}
                    </h4>
                </div>
                <div class="card-body">
                    @if($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form action="{{ isset($address) ? route('profile.address.update', $address->alamat_id) : route('profile.address.store') }}" method="POST">
                        @csrf
                        @if(isset($address))
                            @method('PUT')
                        @endif

                        <div class="mb-3">
                            <label for="label" class="form-label">Label Alamat <span class="text-danger">*</span></label>
                            <select name="label" id="label" class="form-select @error('label') is-invalid @enderror" required>
                                <option value="">-- Pilih Label --</option>
                                <option value="Rumah" {{ isset($address) && $address->label === 'Rumah' ? 'selected' : '' }}>Rumah</option>
                                <option value="Kantor" {{ isset($address) && $address->label === 'Kantor' ? 'selected' : '' }}>Kantor</option>
                                <option value="Lainnya" {{ isset($address) && $address->label === 'Lainnya' ? 'selected' : '' }}>Lainnya</option>
                            </select>
                            @error('label')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="nama_penerima" class="form-label">Nama Penerima <span class="text-danger">*</span></label>
                            <input type="text" name="nama_penerima" id="nama_penerima" class="form-control @error('nama_penerima') is-invalid @enderror" 
                                   value="{{ isset($address) ? $address->nama_penerima : old('nama_penerima') }}" required>
                            @error('nama_penerima')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="no_telepon" class="form-label">No. Telepon <span class="text-danger">*</span></label>
                            <input type="text" name="no_telepon" id="no_telepon" class="form-control @error('no_telepon') is-invalid @enderror" 
                                   value="{{ isset($address) ? $address->no_telepon : old('no_telepon') }}" required>
                            @error('no_telepon')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="provinsi" class="form-label">Provinsi <span class="text-danger">*</span></label>
                            <input type="text" name="provinsi" id="provinsi" class="form-control @error('provinsi') is-invalid @enderror" 
                                   value="{{ isset($address) ? $address->provinsi : old('provinsi') }}" required>
                            @error('provinsi')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="kota" class="form-label">Kota/Kabupaten <span class="text-danger">*</span></label>
                            <input type="text" name="kota" id="kota" class="form-control @error('kota') is-invalid @enderror" 
                                   value="{{ isset($address) ? $address->kota : old('kota') }}" required>
                            @error('kota')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="kecamatan" class="form-label">Kecamatan <span class="text-danger">*</span></label>
                            <input type="text" name="kecamatan" id="kecamatan" class="form-control @error('kecamatan') is-invalid @enderror" 
                                   value="{{ isset($address) ? $address->kecamatan : old('kecamatan') }}" required>
                            @error('kecamatan')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="kelurahan" class="form-label">Kelurahan <span class="text-danger">*</span></label>
                            <input type="text" name="kelurahan" id="kelurahan" class="form-control @error('kelurahan') is-invalid @enderror" 
                                   value="{{ isset($address) ? $address->kelurahan : old('kelurahan') }}" required>
                            @error('kelurahan')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="kode_pos" class="form-label">Kode Pos <span class="text-danger">*</span></label>
                            <input type="text" name="kode_pos" id="kode_pos" class="form-control @error('kode_pos') is-invalid @enderror" 
                                   value="{{ isset($address) ? $address->kode_pos : old('kode_pos') }}" required>
                            @error('kode_pos')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="alamat_lengkap" class="form-label">Alamat Lengkap <span class="text-danger">*</span></label>
                            <textarea name="alamat_lengkap" id="alamat_lengkap" rows="4" class="form-control @error('alamat_lengkap') is-invalid @enderror" required>{{ isset($address) ? $address->alamat_lengkap : old('alamat_lengkap') }}</textarea>
                            @error('alamat_lengkap')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3 form-check">
                            <input type="checkbox" name="is_utama" id="is_utama" class="form-check-input" 
                                   {{ isset($address) && $address->is_utama ? 'checked' : '' }}>
                            <label class="form-check-label" for="is_utama">
                                Jadikan alamat utama
                            </label>
                        </div>

                        <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                            <a href="{{ route('profile.addresses') }}" class="btn btn-secondary">Batal</a>
                            <button type="submit" class="btn btn-primary">
                                {{ isset($address) ? 'Perbarui Alamat' : 'Simpan Alamat' }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
