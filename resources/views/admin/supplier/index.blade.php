@extends('layouts.admin')

@section('title', 'Supplier Management')

@section('head')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"/>
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<style>
    .leaflet-container { z-index: 1; border-radius: 1rem; }
    #createMap, #detailMap { min-height: 200px; }
</style>
@endsection

@section('content')
<div class="mx-auto max-w-7xl px-4 py-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
        <div>
            <p class="text-xs font-bold text-admin uppercase tracking-widest mb-1">MOVR ADMIN</p>
            <h1 class="font-bold text-2xl text-slate-900">Supplier Management</h1>
            <p class="text-sm text-slate-500 mt-1">Kelola supplier dan toko yang terhubung dengan produk MOVR.</p>
        </div>

        <a href="{{ route('admin.supplier.create') }}"
           class="inline-flex items-center gap-2 rounded-xl bg-admin px-5 py-3 text-sm font-bold text-white shadow-lg shadow-admin/25 hover:bg-admin-dark hover:-translate-y-0.5 hover:shadow-xl hover:shadow-admin/30 transition-all duration-200 flex-shrink-0">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Tambah Supplier
        </a>
    </div>

    <!-- Search & Filter Bar -->
    <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm mb-6">
        <form method="GET" class="flex flex-col sm:flex-row gap-3">
            <div class="relative flex-1">
                <svg class="absolute left-3.5 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
                <input type="text" name="q" value="{{ $search ?? '' }}"
                       placeholder="Cari nama toko atau nama supplier..."
                       class="w-full pl-10 pr-4 py-2.5 rounded-xl border border-slate-200 text-sm focus:border-admin focus:outline-none transition">
            </div>

            <select name="sort" class="rounded-xl border border-slate-200 px-4 py-2.5 text-sm focus:border-admin focus:outline-none bg-white">
                <option value="recent" {{ ($sort ?? '') === 'recent' ? 'selected' : '' }}>Recently Added</option>
                <option value="name_az" {{ ($sort ?? '') === 'name_az' ? 'selected' : '' }}>Name A-Z</option>
                <option value="name_za" {{ ($sort ?? '') === 'name_za' ? 'selected' : '' }}>Name Z-A</option>
            </select>

            <button type="submit" class="rounded-xl bg-admin text-white px-5 py-2.5 text-sm font-bold hover:bg-[#4e8fa8] flex items-center gap-2 transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/></svg>
                Filter
            </button>
        </form>
    </div>

    <!-- Supplier Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-4">
        @forelse(($suppliers ?? collect()) as $s)
            @php
                $isActive = ($s->is_verified ?? 0) == 1;
                $avatar = strtoupper(substr($s->nama_owner ?? $s->nama_toko ?? '-', 0, 1));
                $fotoUrl = !empty($s->foto_toko) ? asset('storage/' . $s->foto_toko) : '';
            @endphp

            <div class="rounded-3xl border border-slate-200 bg-white shadow-sm overflow-hidden hover:shadow-md hover:-translate-y-0.5 transition-all duration-200">
                <!-- Foto Toko -->
                <div class="relative h-28 w-full bg-slate-50 flex items-center justify-center overflow-hidden">
                    @if(!empty($s->foto_toko))
                        <img src="{{ asset('storage/' . $s->foto_toko) }}" alt="{{ $s->nama_toko }}" class="w-full h-full object-cover">
                    @else
                        <div class="flex flex-col items-center justify-center text-slate-300">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                            <span class="text-xs font-semibold mt-1">No Photo</span>
                        </div>
                    @endif
                    <div class="absolute top-3 left-3">
                        <span class="text-xs font-bold px-2.5 py-1 rounded-full {{ $isActive ? 'bg-admin text-white' : 'bg-slate-100 text-slate-600' }}">
                            {{ $isActive ? '● ACTIVE' : '○ INACTIVE' }}
                        </span>
                    </div>
                </div>

                <div class="p-4">
                    <div class="flex items-center gap-3 mb-3">
                        <div class="h-10 w-10 rounded-full bg-admin text-white flex items-center justify-center font-bold text-sm flex-shrink-0">
                            {{ $avatar }}
                        </div>
                        <div class="min-w-0">
                            <div class="font-bold text-slate-900 truncate">{{ $s->nama_toko }}</div>
                            <div class="text-xs text-slate-500">{{ $s->kategori_supplier ?? '-' }}</div>
                        </div>
                    </div>

                    <div class="text-xs text-slate-500 flex items-start gap-1.5 mb-4">
                        <svg class="w-3.5 h-3.5 text-slate-400 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                        <span class="line-clamp-2">{{ $s->alamat_toko ?? '-' }}</span>
                    </div>

                    <div class="flex gap-2">
                        <button type="button"
                            class="openSupplierDetail flex-1 rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50 hover:border-admin hover:text-admin transition"
                            data-supplier-id="{{ $s->supplier_id }}"
                            data-nama-toko="{{ $s->nama_toko ?? '-' }}"
                            data-nama-owner="{{ $s->nama_owner ?? '-' }}"
                            data-kategori="{{ $s->kategori_supplier ?? '-' }}"
                            data-no-telepon="{{ $s->no_telepon ?? '-' }}"
                            data-email="{{ $s->email ?? '-' }}"
                            data-alamat="{{ $s->alamat_toko ?? '-' }}"
                            data-foto-url="{{ $fotoUrl }}"
                            data-delete-url="{{ route('admin.supplier.destroy', $s->supplier_id) }}">
                            Lihat Detail & Peta
                        </button>
                        <form method="POST" action="{{ route('admin.supplier.destroy', $s->supplier_id) }}" onsubmit="return confirm('Hapus supplier ini?')">
                            @csrf @method('DELETE')
                            <button type="submit" class="rounded-xl border border-red-200 bg-red-50 text-red-600 px-3 py-2 text-sm font-semibold hover:bg-red-100 transition">Hapus</button>
                        </form>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-span-full bg-white rounded-2xl border border-slate-200 p-16 text-center">
                <div class="w-16 h-16 bg-slate-100 rounded-2xl mx-auto mb-4 flex items-center justify-center">
                    <svg class="w-8 h-8 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                </div>
                <p class="text-slate-500 font-semibold mb-1">Belum ada supplier</p>
                <p class="text-slate-400 text-sm">Mulai tambahkan supplier pertama Anda</p>
            </div>
        @endforelse
    </div>

    @if(method_exists($suppliers ?? null, 'links') && $suppliers->hasPages())
        <div class="mt-6">{{ $suppliers->links() }}</div>
    @endif
</div>

<!-- ==================== SUPPLIER DETAIL MODAL ==================== -->
<div id="supplierDetailModal" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/50 p-4">
    <div class="w-full max-w-2xl rounded-3xl bg-white shadow-2xl max-h-[90vh] overflow-y-auto">
        <!-- Modal Header -->
        <div class="flex items-center justify-between px-6 py-4 border-b border-slate-100">
            <div>
                <h2 class="text-lg font-bold text-slate-900">Detail Supplier</h2>
                <p class="text-sm text-slate-500">Informasi toko, kontak, dan lokasi pada peta.</p>
            </div>
            <button type="button" id="closeSupplierDetailModal" class="w-9 h-9 rounded-xl bg-slate-100 flex items-center justify-center hover:bg-slate-200 transition text-slate-500">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>

        <div class="p-6">
            <div class="grid gap-6 lg:grid-cols-[200px_1fr]">
                <!-- Photo -->
                <div class="overflow-hidden rounded-2xl border border-slate-200 bg-slate-50 aspect-square flex items-center justify-center">
                    <img id="supplierDetailPhoto" src="" alt="Supplier Photo" class="hidden h-full w-full object-cover">
                    <div id="supplierDetailPhotoPlaceholder" class="flex flex-col items-center justify-center text-slate-300">
                        <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                        <span class="mt-2 text-xs font-semibold">No Photo</span>
                    </div>
                </div>

                <!-- Info -->
                <div class="space-y-3 text-sm">
                    <div>
                        <p class="text-xs font-bold uppercase tracking-wider text-slate-400">Nama Toko</p>
                        <p id="supplierDetailName" class="mt-1 text-base font-bold text-slate-900">-</p>
                    </div>
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <p class="text-xs font-bold uppercase tracking-wider text-slate-400">Owner</p>
                            <p id="supplierDetailOwner" class="mt-1 text-slate-700">-</p>
                        </div>
                        <div>
                            <p class="text-xs font-bold uppercase tracking-wider text-slate-400">Kategori</p>
                            <p id="supplierDetailCategory" class="mt-1 text-slate-700">-</p>
                        </div>
                    </div>
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <p class="text-xs font-bold uppercase tracking-wider text-slate-400">Telepon</p>
                            <p id="supplierDetailPhone" class="mt-1 text-slate-700">-</p>
                        </div>
                        <div>
                            <p class="text-xs font-bold uppercase tracking-wider text-slate-400">Email</p>
                            <p id="supplierDetailEmail" class="mt-1 text-slate-700 break-all">-</p>
                        </div>
                    </div>
                    <div>
                        <p class="text-xs font-bold uppercase tracking-wider text-slate-400">Alamat</p>
                        <p id="supplierDetailAddress" class="mt-1 text-slate-700 leading-relaxed">-</p>
                    </div>
                </div>
            </div>

            <!-- Leaflet Map in Detail Modal -->
            <div class="mt-5">
                <div class="flex items-center justify-between mb-2">
                    <p class="text-xs font-bold uppercase tracking-wider text-slate-400">Lokasi di Peta</p>
                    <span id="mapStatusBadge" class="text-xs text-slate-400"></span>
                </div>
                <div id="detailMap" class="h-56 rounded-2xl border border-slate-200 bg-slate-100 flex items-center justify-center">
                    <p class="text-slate-400 text-sm">Memuat peta...</p>
                </div>
            </div>
        </div>

        <div class="flex items-center justify-end gap-2 px-6 py-4 border-t border-slate-100">
            <button type="button" id="deleteFromDetailBtn"
                    class="rounded-xl bg-red-600 px-4 py-2 text-sm font-semibold text-white hover:bg-red-700 transition">Hapus Supplier</button>
            <button type="button" id="closeSupplierDetailModalBottom"
                    class="rounded-xl border border-slate-200 bg-white px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50 transition">Tutup</button>
        </div>
    </div>
</div>

<form id="supplierDeleteFromDetailForm" method="POST" action="#" class="hidden">
    @csrf @method('DELETE')
</form>

<script>
    // ===================== Modal Open/Close =====================
    const supplierDetailModal = document.getElementById('supplierDetailModal');
    const supplierDetailPhoto = document.getElementById('supplierDetailPhoto');
    const supplierDetailPhotoPlaceholder = document.getElementById('supplierDetailPhotoPlaceholder');
    const supplierDeleteFromDetailForm = document.getElementById('supplierDeleteFromDetailForm');

    function openSupplierDetailModal() {
        supplierDetailModal.classList.remove('hidden');
        supplierDetailModal.classList.add('flex');
    }

    function closeSupplierDetailModal() {
        supplierDetailModal.classList.add('hidden');
        supplierDetailModal.classList.remove('flex');
    }

    // ===================== Leaflet Map (Detail Modal) =====================
    let detailMap = null;
    let detailMapMarker = null;

    function initDetailMap() {
        if (detailMap) {
            detailMap.remove();
            detailMap = null;
        }

        // Small delay to ensure modal is visible
        setTimeout(() => {
            const mapEl = document.getElementById('detailMap');
            mapEl.innerHTML = '';

            detailMap = L.map('detailMap').setView([-6.2, 106.8], 10);
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '© OpenStreetMap contributors',
                maxZoom: 18,
            }).addTo(detailMap);
        }, 100);
    }

    async function geocodeAddress(address) {
        if (!address || address === '-') return null;
        try {
            const encoded = encodeURIComponent(address);
            const resp = await fetch(`https://nominatim.openstreetmap.org/search?format=json&q=${encoded}&limit=1`, {
                headers: { 'Accept-Language': 'id,en', 'User-Agent': 'MOVR-Admin/1.0' }
            });
            const data = await resp.json();
            if (data && data.length > 0) {
                return { lat: parseFloat(data[0].lat), lng: parseFloat(data[0].lon), display: data[0].display_name };
            }
        } catch (e) {
            console.error('Geocode error:', e);
        }
        return null;
    }

    async function showAddressOnMap(address) {
        const statusBadge = document.getElementById('mapStatusBadge');
        statusBadge.textContent = 'Mencari lokasi...';

        if (!detailMap) {
            initDetailMap();
            await new Promise(r => setTimeout(r, 200));
        }

        const result = await geocodeAddress(address);
        if (result) {
            detailMap.setView([result.lat, result.lng], 15);
            if (detailMapMarker) detailMapMarker.remove();
            detailMapMarker = L.marker([result.lat, result.lng])
                .addTo(detailMap)
                .bindPopup(`<strong>${document.getElementById('supplierDetailName').textContent}</strong><br>${address}`)
                .openPopup();
            statusBadge.textContent = '📍 Lokasi ditemukan';
            statusBadge.className = 'text-xs text-green-600 font-semibold';
        } else {
            statusBadge.textContent = '⚠ Lokasi tidak ditemukan di peta';
            statusBadge.className = 'text-xs text-amber-600';
        }
    }

    // ===================== Open Detail Buttons =====================
    document.querySelectorAll('.openSupplierDetail').forEach((button) => {
        button.addEventListener('click', async () => {
            document.getElementById('supplierDetailName').textContent = button.dataset.namaToko || '-';
            document.getElementById('supplierDetailOwner').textContent = button.dataset.namaOwner || '-';
            document.getElementById('supplierDetailCategory').textContent = button.dataset.kategori || '-';
            document.getElementById('supplierDetailPhone').textContent = button.dataset.noTelepon || '-';
            document.getElementById('supplierDetailEmail').textContent = button.dataset.email || '-';
            document.getElementById('supplierDetailAddress').textContent = button.dataset.alamat || '-';

            const fotoUrl = button.dataset.fotoUrl || '';
            if (fotoUrl) {
                supplierDetailPhoto.src = fotoUrl;
                supplierDetailPhoto.classList.remove('hidden');
                supplierDetailPhotoPlaceholder.classList.add('hidden');
            } else {
                supplierDetailPhoto.classList.add('hidden');
                supplierDetailPhotoPlaceholder.classList.remove('hidden');
            }

            supplierDeleteFromDetailForm.action = button.dataset.deleteUrl || '#';
            openSupplierDetailModal();

            // Initialize map and geocode
            initDetailMap();
            await showAddressOnMap(button.dataset.alamat || '');
        });
    });

    // Close handlers
    document.getElementById('closeSupplierDetailModal').addEventListener('click', closeSupplierDetailModal);
    document.getElementById('closeSupplierDetailModalBottom').addEventListener('click', closeSupplierDetailModal);
    supplierDetailModal.addEventListener('click', (e) => {
        if (e.target === supplierDetailModal) closeSupplierDetailModal();
    });

    // Delete from detail
    document.getElementById('deleteFromDetailBtn').addEventListener('click', () => {
        if (!supplierDeleteFromDetailForm.action || supplierDeleteFromDetailForm.action.endsWith('#')) return;
        if (!confirm('Hapus supplier ini?')) return;
        supplierDeleteFromDetailForm.submit();
    });

    @if($errors->any())
        openSupplierCreate();
    @endif
</script>
@endsection
