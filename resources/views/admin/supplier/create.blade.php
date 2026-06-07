@extends('layouts.admin')

@section('title', 'Add New Supplier')

@section('content')
<div class="mx-auto max-w-7xl px-4 py-6">
    <div class="mb-4">
        <a href="{{ route('admin.supplier.index') }}" class="text-slate-600 hover:text-slate-900">← Back to Suppliers</a>
    </div>

    <div class="flex items-start justify-between gap-4 mb-6">
        <div>
            <h1 class="font-bold text-2xl text-slate-900">Add New Supplier</h1>
            <p class="text-sm text-slate-500 mt-1">Form supplier dengan koordinat lokasi dan peta interaktif.</p>
        </div>
    </div>

    <div class="mt-6 rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
        <form method="POST" action="{{ route('admin.supplier.store') }}" enctype="multipart/form-data">
            @csrf

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <div class="space-y-5">
                    <div>
                        <label class="text-sm font-semibold text-slate-700">Supplier Logo</label>
                        <div class="mt-2 flex items-center gap-4">
                            <div class="flex h-24 w-24 items-center justify-center overflow-hidden rounded-2xl border border-slate-200 bg-slate-50">
                                <i id="supplierLogoIcon" class="fas fa-store text-3xl text-slate-300"></i>
                                <img id="supplierLogoPreview" src="" alt="Preview Supplier Logo" class="hidden h-full w-full object-cover">
                            </div>
                            <div class="flex-1">
                                <input id="foto_toko" type="file" name="foto_toko" accept="image/*,.webp" class="mt-2 w-full text-sm text-slate-600 file:mr-3 file:rounded-lg file:border-0 file:bg-slate-100 file:px-3 file:py-2 file:text-sm file:font-semibold file:text-slate-700 hover:file:bg-slate-200">
                                <div class="mt-2 text-xs text-slate-500">JPG, PNG, WEBP. Preview akan muncul setelah file dipilih.</div>
                            </div>
                        </div>
                    </div>

                    <div class="grid gap-4 sm:grid-cols-2">
                        <div>
                            <label class="text-sm font-semibold text-slate-700">Supplier Category</label>
                            <select name="kategori_supplier" class="mt-2 w-full rounded-xl border border-slate-200 px-3 py-2 text-sm focus:border-[#63A2BB] focus:outline-none">
                                <option value="">(optional)</option>
                                @foreach(($categories ?? collect()) as $c)
                                    <option value="{{ $c->nama_kategori }}">{{ $c->nama_kategori }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="text-sm font-semibold text-slate-700">Status</label>
                            <select name="is_verified" required class="mt-2 w-full rounded-xl border border-slate-200 px-3 py-2 text-sm focus:border-[#63A2BB] focus:outline-none">
                                <option value="1" {{ old('is_verified', '1') == '1' ? 'selected' : '' }}>Active</option>
                                <option value="0" {{ old('is_verified') == '0' ? 'selected' : '' }}>Inactive</option>
                            </select>
                        </div>
                    </div>

                    <div class="grid gap-4 sm:grid-cols-2">
                        <div>
                            <label class="text-sm font-semibold text-slate-700">Store Name</label>
                            <input type="text" name="nama_toko" value="{{ old('nama_toko') }}" required class="mt-2 w-full rounded-xl border border-slate-200 px-3 py-2 text-sm focus:border-[#63A2BB] focus:outline-none">
                        </div>
                        <div>
                            <label class="text-sm font-semibold text-slate-700">Owner Name</label>
                            <input type="text" name="nama_owner" value="{{ old('nama_owner') }}" required class="mt-2 w-full rounded-xl border border-slate-200 px-3 py-2 text-sm focus:border-[#63A2BB] focus:outline-none">
                        </div>
                        <div>
                            <label class="text-sm font-semibold text-slate-700">Phone Number</label>
                            <input type="text" name="no_telepon" value="{{ old('no_telepon') }}" class="mt-2 w-full rounded-xl border border-slate-200 px-3 py-2 text-sm focus:border-[#63A2BB] focus:outline-none" placeholder="+62...">
                        </div>
                        <div>
                            <label class="text-sm font-semibold text-slate-700">Email Address</label>
                            <input type="email" name="email" value="{{ old('email') }}" class="mt-2 w-full rounded-xl border border-slate-200 px-3 py-2 text-sm focus:border-[#63A2BB] focus:outline-none">
                        </div>
                    </div>
                </div>

                <div class="space-y-5">
                    <div>
                        <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-1.5">Alamat Lengkap Toko</label>
                        <textarea name="alamat_toko" rows="3" id="alamat-input" placeholder="Jl. Contoh No. 1, Kota..." class="w-full px-4 py-3 border-2 border-gray-200 focus:border-[#63A2BB] rounded-xl text-sm focus:outline-none transition resize-none">{{ old('alamat_toko') }}</textarea>
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-1.5">Latitude</label>
                            <input type="text" name="latitude" id="lat-input" value="{{ old('latitude') }}" placeholder="-6.2088" class="w-full px-4 py-3 border-2 border-gray-200 focus:border-[#63A2BB] rounded-xl text-sm font-mono focus:outline-none transition">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-1.5">Longitude</label>
                            <input type="text" name="longitude" id="lng-input" value="{{ old('longitude') }}" placeholder="106.8456" class="w-full px-4 py-3 border-2 border-gray-200 focus:border-[#63A2BB] rounded-xl text-sm font-mono focus:outline-none transition">
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-1.5">Lokasi di Peta <span class="text-gray-400 font-normal normal-case ml-1">(Klik peta untuk set lokasi)</span></label>
                        <div id="supplier-map" class="w-full h-64 rounded-2xl border-2 border-gray-200 overflow-hidden bg-gray-100"></div>
                        <p class="text-xs text-gray-400 mt-1.5">💡 Klik pada peta untuk set koordinat otomatis, atau isi manual kolom Latitude/Longitude di atas.</p>
                    </div>
                </div>
            </div>

            <div class="flex items-center justify-end gap-3 mt-6">
                <a href="{{ route('admin.supplier.index') }}" class="rounded-xl border border-slate-200 bg-white px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50">← Back to Suppliers</a>
                <button type="submit" class="rounded-xl bg-[#63A2BB] px-4 py-2 text-sm font-semibold text-white hover:bg-[#4A8BA3]">Save Supplier</button>
            </div>
        </form>
    </div>
</div>

<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"/>
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    let defaultLat = parseFloat(document.getElementById('lat-input')?.value);
    let defaultLng = parseFloat(document.getElementById('lng-input')?.value);
    
    if (isNaN(defaultLat)) defaultLat = -6.2088;
    if (isNaN(defaultLng)) defaultLng = 106.8456;

    const map = L.map('supplier-map').setView([defaultLat, defaultLng], 13);

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '© OpenStreetMap contributors'
    }).addTo(map);

    let marker = null;
    if (document.getElementById('lat-input')?.value && document.getElementById('lng-input')?.value) {
        marker = L.marker([defaultLat, defaultLng]).addTo(map);
    }

    map.on('click', function(e) {
        const { lat, lng } = e.latlng;
        if (marker) {
            map.removeLayer(marker);
        }
        marker = L.marker([lat, lng]).addTo(map);
        const latInput = document.getElementById('lat-input');
        const lngInput = document.getElementById('lng-input');
        if (latInput) latInput.value = lat.toFixed(6);
        if (lngInput) lngInput.value = lng.toFixed(6);
    });

    ['lat-input', 'lng-input'].forEach(id => {
        document.getElementById(id)?.addEventListener('change', function() {
            const latVal = document.getElementById('lat-input')?.value;
            const lngVal = document.getElementById('lng-input')?.value;
            const lat = parseFloat(latVal);
            const lng = parseFloat(lngVal);
            if (!isNaN(lat) && !isNaN(lng)) {
                map.setView([lat, lng], 14);
                if (marker) {
                    map.removeLayer(marker);
                }
                marker = L.marker([lat, lng]).addTo(map);
            }
        });
    });

    const supplierLogoInput = document.getElementById('foto_toko');
    const supplierLogoPreview = document.getElementById('supplierLogoPreview');
    const supplierLogoIcon = document.getElementById('supplierLogoIcon');
    if (supplierLogoInput) {
        supplierLogoInput.addEventListener('change', function () {
            const file = this.files && this.files[0];
            if (!file) return;
            const reader = new FileReader();
            reader.onload = (event) => {
                if (supplierLogoPreview) {
                    supplierLogoPreview.src = event.target.result;
                    supplierLogoPreview.classList.remove('hidden');
                    supplierLogoPreview.style.display = 'block';
                }
                if (supplierLogoIcon) {
                    supplierLogoIcon.classList.add('hidden');
                    supplierLogoIcon.style.display = 'none';
                }
            };
            reader.readAsDataURL(file);
        });
    }
});
</script>
@endsection

