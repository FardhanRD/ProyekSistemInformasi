@extends('movr.layouts.admin')

@section('content')
<section class="space-y-6">
    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Kelola Supplier</h1>
            <p class="text-sm text-gray-500">Card menampilkan info ringkas supplier. Klik tombol Detail untuk melihat informasi lengkap.</p>
        </div>
        <button
            type="button"
            id="openSupplierModal"
            class="inline-flex items-center justify-center rounded-lg bg-accent-green px-4 py-2.5 text-sm font-semibold text-black transition hover:bg-opacity-90"
        >
            <i class="fas fa-plus mr-2"></i>
            Tambah Supplier
        </button>
    </div>

    @if(session('success'))
        <div class="rounded-lg border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-800">
            {{ session('success') }}
        </div>
    @endif

    @if($errors->any())
        <div class="rounded-lg border border-blue-200 bg-blue-50 px-4 py-3 text-sm text-blue-800">
            <p class="font-semibold">Gagal menambahkan supplier:</p>
            <ul class="mt-2 list-disc pl-5">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
        @forelse($suppliers as $supplier)
            @php
                $cleanPhone = preg_replace('/\D+/', '', $supplier->phone_number);
                if (str_starts_with($cleanPhone, '0')) {
                    $cleanPhone = '62' . substr($cleanPhone, 1);
                }
                $message = "Halo {$supplier->owner_name} dari {$supplier->store_name}, stok baju habis. Mohon restock secepatnya. Terima kasih.";
                $waUrl = "https://wa.me/{$cleanPhone}?text=" . urlencode($message);
            @endphp

            <div class="flex h-full flex-col rounded-2xl border border-gray-200 bg-white p-5 shadow-sm transition hover:-translate-y-1 hover:border-accent-green hover:shadow-md">
                <div class="flex items-start justify-between gap-3">
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-[0.2em] text-gray-500">Supplier</p>
                        <h2 class="mt-2 text-lg font-bold text-gray-900">{{ $supplier->store_name }}</h2>
                    </div>
                    <span class="rounded-full bg-blue-100 px-3 py-1 text-xs font-semibold text-blue-800">{{ $supplier->category }}</span>
                </div>

                <div class="mt-6 flex items-center gap-2">
                    <button
                        type="button"
                        class="openDetailModal inline-flex items-center justify-center rounded-lg bg-black px-4 py-2 text-sm font-semibold text-white transition hover:bg-gray-800"
                        data-supplier-id="{{ $supplier->id }}"
                        data-store-name="{{ $supplier->store_name }}"
                        data-category="{{ $supplier->category }}"
                        data-owner-name="{{ $supplier->owner_name }}"
                        data-address="{{ $supplier->address }}"
                        data-phone-number="{{ $supplier->phone_number }}"
                    >
                        Detail
                    </button>
                    <a href="{{ $waUrl }}" target="_blank" rel="noopener noreferrer" class="inline-flex items-center justify-center rounded-lg bg-accent-green px-4 py-2 text-sm font-semibold text-black transition hover:bg-opacity-90">
                        Hubungi
                    </a>
                </div>
            </div>
        @empty
            <div class="col-span-full rounded-2xl border border-dashed border-gray-300 bg-white px-6 py-10 text-center text-sm text-gray-500">
                Belum ada supplier. Klik tombol <span class="font-semibold text-gray-700">Tambah Supplier</span> untuk menambahkan data.
            </div>
        @endforelse
    </div>
</section>

<div id="supplierModal" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/50 p-4">
    <div class="w-full max-w-2xl rounded-2xl bg-white p-6 shadow-xl">
        <div class="mb-5 flex items-center justify-between">
            <h3 class="text-xl font-bold text-gray-900">Tambah Supplier</h3>
            <button type="button" id="closeSupplierModal" class="rounded-lg p-2 text-gray-500 transition hover:bg-gray-100 hover:text-gray-800">
                <i class="fas fa-times"></i>
            </button>
        </div>

        <form action="{{ route('admin.suppliers.store') }}" method="POST" class="space-y-4">
            @csrf
            <div class="grid gap-4 sm:grid-cols-2">
                <div>
                    <label for="store_name" class="mb-1 block text-sm font-semibold text-gray-700">Nama Toko Supplier</label>
                    <input id="store_name" name="store_name" type="text" value="{{ old('store_name') }}" required class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm text-gray-900 focus:border-accent-green focus:outline-none">
                </div>
                <div>
                    <label for="category_id" class="mb-1 block text-sm font-semibold text-gray-700">Kategori</label>
                    <select id="category_id" name="category_id" required class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm text-gray-900 focus:border-accent-green focus:outline-none">
                        <option value="">Pilih kategori</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}" {{ (string) old('category_id') === (string) $category->id ? 'selected' : '' }}>
                                {{ $category->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label for="owner_name" class="mb-1 block text-sm font-semibold text-gray-700">Nama Owner</label>
                    <input id="owner_name" name="owner_name" type="text" value="{{ old('owner_name') }}" required class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm text-gray-900 focus:border-accent-green focus:outline-none">
                </div>
                <div>
                    <label for="phone_number" class="mb-1 block text-sm font-semibold text-gray-700">No Telepon</label>
                    <input id="phone_number" name="phone_number" type="text" value="{{ old('phone_number') }}" placeholder="08xxxxxxxxxx" required class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm text-gray-900 focus:border-accent-green focus:outline-none">
                </div>
            </div>

            <div>
                <label for="address" class="mb-1 block text-sm font-semibold text-gray-700">Alamat Toko</label>
                <textarea id="address" name="address" rows="3" required class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm text-gray-900 focus:border-accent-green focus:outline-none">{{ old('address') }}</textarea>
            </div>

            <div class="flex justify-end gap-2 pt-2">
                <button type="button" id="cancelSupplierModal" class="rounded-lg border border-gray-300 px-4 py-2 text-sm font-semibold text-gray-700 transition hover:bg-gray-100">Batal</button>
                <button type="submit" class="rounded-lg bg-black px-4 py-2 text-sm font-semibold text-white transition hover:bg-gray-800">Simpan Supplier</button>
            </div>
        </form>
    </div>
</div>

<div id="supplierDetailModal" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/50 p-4">
    <div class="w-full max-w-xl rounded-2xl bg-white p-6 shadow-xl">
        <div class="mb-5 flex items-center justify-between">
            <h3 class="text-xl font-bold text-gray-900">Detail Supplier</h3>
            <button type="button" id="closeSupplierDetailModal" class="rounded-lg p-2 text-gray-500 transition hover:bg-gray-100 hover:text-gray-800">
                <i class="fas fa-times"></i>
            </button>
        </div>

        <div class="space-y-3 text-sm text-gray-700">
            <p><span class="font-semibold text-gray-900">Nama Toko:</span> <span id="detailStoreName">-</span></p>
            <p><span class="font-semibold text-gray-900">Kategori:</span> <span id="detailCategory">-</span></p>
            <p><span class="font-semibold text-gray-900">Nama Owner:</span> <span id="detailOwnerName">-</span></p>
            <p><span class="font-semibold text-gray-900">Alamat Toko:</span> <span id="detailAddress">-</span></p>
            <p><span class="font-semibold text-gray-900">No Telepon:</span> <span id="detailPhoneNumber">-</span></p>
        </div>

        <div class="mt-6 flex justify-end gap-2">
            <button type="button" id="detailModalCloseBtn" class="rounded-lg border border-gray-300 px-4 py-2 text-sm font-semibold text-gray-700 transition hover:bg-gray-100">Tutup</button>
            <button type="button" id="detailDeleteBtn" class="rounded-lg bg-blue-500 px-4 py-2 text-sm font-semibold text-white transition hover:bg-blue-600">
                Hapus
            </button>
        </div>
    </div>
</div>

<div id="supplierDeleteConfirmModal" class="fixed inset-0 z-[60] hidden items-center justify-center bg-black/55 p-4">
    <div class="w-full max-w-md rounded-2xl bg-white p-6 shadow-xl">
        <h3 class="text-lg font-bold text-gray-900">Konfirmasi Hapus</h3>
        <p class="mt-3 text-sm text-gray-700">
            Yakin dihapus supplier "<span id="deleteSupplierName" class="font-semibold text-gray-900">-</span>"?
        </p>

        <div class="mt-6 flex justify-end gap-2">
            <button type="button" id="cancelDeleteBtn" class="rounded-lg border border-gray-300 px-4 py-2 text-sm font-semibold text-gray-700 transition hover:bg-gray-100">
                Tidak
            </button>
            <form id="deleteSupplierForm" method="POST" action="#">
                @csrf
                @method('DELETE')
                <button type="submit" class="rounded-lg bg-blue-500 px-4 py-2 text-sm font-semibold text-white transition hover:bg-blue-600">
                    Ya
                </button>
            </form>
        </div>
    </div>
</div>

<script>
    const supplierModal = document.getElementById('supplierModal');
    const openSupplierModal = document.getElementById('openSupplierModal');
    const closeSupplierModal = document.getElementById('closeSupplierModal');
    const cancelSupplierModal = document.getElementById('cancelSupplierModal');
    const supplierDetailModal = document.getElementById('supplierDetailModal');
    const closeSupplierDetailModal = document.getElementById('closeSupplierDetailModal');
    const detailModalCloseBtn = document.getElementById('detailModalCloseBtn');
    const detailDeleteBtn = document.getElementById('detailDeleteBtn');
    const supplierDeleteConfirmModal = document.getElementById('supplierDeleteConfirmModal');
    const deleteSupplierName = document.getElementById('deleteSupplierName');
    const deleteSupplierForm = document.getElementById('deleteSupplierForm');
    const cancelDeleteBtn = document.getElementById('cancelDeleteBtn');

    const detailStoreName = document.getElementById('detailStoreName');
    const detailCategory = document.getElementById('detailCategory');
    const detailOwnerName = document.getElementById('detailOwnerName');
    const detailAddress = document.getElementById('detailAddress');
    const detailPhoneNumber = document.getElementById('detailPhoneNumber');
    let selectedSupplierId = null;
    let selectedSupplierName = '-';

    function showSupplierModal() {
        supplierModal.classList.remove('hidden');
        supplierModal.classList.add('flex');
    }

    function hideSupplierModal() {
        supplierModal.classList.add('hidden');
        supplierModal.classList.remove('flex');
    }

    function showSupplierDetailModal() {
        supplierDetailModal.classList.remove('hidden');
        supplierDetailModal.classList.add('flex');
    }

    function hideSupplierDetailModal() {
        supplierDetailModal.classList.add('hidden');
        supplierDetailModal.classList.remove('flex');
    }

    function showDeleteConfirmModal() {
        supplierDeleteConfirmModal.classList.remove('hidden');
        supplierDeleteConfirmModal.classList.add('flex');
    }

    function hideDeleteConfirmModal() {
        supplierDeleteConfirmModal.classList.add('hidden');
        supplierDeleteConfirmModal.classList.remove('flex');
    }

    openSupplierModal?.addEventListener('click', showSupplierModal);
    closeSupplierModal?.addEventListener('click', hideSupplierModal);
    cancelSupplierModal?.addEventListener('click', hideSupplierModal);

    closeSupplierDetailModal?.addEventListener('click', hideSupplierDetailModal);
    detailModalCloseBtn?.addEventListener('click', hideSupplierDetailModal);

    supplierModal?.addEventListener('click', (event) => {
        if (event.target === supplierModal) {
            hideSupplierModal();
        }
    });

    supplierDetailModal?.addEventListener('click', (event) => {
        if (event.target === supplierDetailModal) {
            hideSupplierDetailModal();
        }
    });

    supplierDeleteConfirmModal?.addEventListener('click', (event) => {
        if (event.target === supplierDeleteConfirmModal) {
            hideDeleteConfirmModal();
        }
    });

    cancelDeleteBtn?.addEventListener('click', hideDeleteConfirmModal);

    detailDeleteBtn?.addEventListener('click', () => {
        if (!selectedSupplierId) {
            return;
        }

        deleteSupplierName.textContent = selectedSupplierName;
        deleteSupplierForm.setAttribute('action', `{{ url('admin/suppliers') }}/${selectedSupplierId}`);
        showDeleteConfirmModal();
    });

    document.querySelectorAll('.openDetailModal').forEach((button) => {
        button.addEventListener('click', () => {
            const supplierId = button.dataset.supplierId || '';
            const storeName = button.dataset.storeName || '-';
            const category = button.dataset.category || '-';
            const ownerName = button.dataset.ownerName || '-';
            const address = button.dataset.address || '-';
            const phoneNumber = button.dataset.phoneNumber || '-';

            selectedSupplierId = supplierId;
            selectedSupplierName = storeName;

            detailStoreName.textContent = storeName;
            detailCategory.textContent = category;
            detailOwnerName.textContent = ownerName;
            detailAddress.textContent = address;
            detailPhoneNumber.textContent = phoneNumber;

            showSupplierDetailModal();
        });
    });

    @if($errors->any())
        showSupplierModal();
    @endif
</script>
@endsection
