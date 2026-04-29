@extends('movr.layouts.app')

@section('content')

{{-- BREADCRUMB --}}
<div class="bg-white border-b border-gray-100">
    <div class="max-w-7xl mx-auto px-6 py-4">
        <nav class="flex text-xs font-bold uppercase tracking-wider text-gray-500">
            <ol class="inline-flex items-center space-x-2">
                <li><a href="{{ route('home') }}" class="hover:text-black transition">Home</a></li>
                <li><span class="text-gray-300">/</span></li>
                <li class="text-black">Account Settings</li>
            </ol>
        </nav>
    </div>
</div>

<section class="py-12 bg-gray-50 min-h-screen">
    <div class="max-w-7xl mx-auto px-6">
        
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
            
            {{-- SIDEBAR MENU (Sticky) --}}
            <div class="lg:col-span-3">
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 sticky top-8">
                    {{-- User Short Info --}}
                    <div class="flex items-center gap-4 mb-8">
                        <div class="w-12 h-12 rounded-full bg-black text-white flex items-center justify-center text-xl font-bold">
                            {{ substr($user->name, 0, 1) }}
                        </div>
                        <div class="overflow-hidden">
                            <h3 class="font-bold text-black truncate">{{ $user->name }}</h3>
                            <p class="text-xs text-gray-500 truncate">{{ $user->email }}</p>
                        </div>
                    </div>

                    {{-- Navigation --}}
                    <nav class="space-y-1">
                        <button onclick="switchTab('profile')" id="nav-profile" class="nav-item w-full flex items-center gap-3 px-4 py-3 text-sm font-medium rounded-lg transition-colors bg-gray-100 text-black">
                            <i class="far fa-user w-5"></i> Personal Info
                        </button>
                        <button onclick="switchTab('security')" id="nav-security" class="nav-item w-full flex items-center gap-3 px-4 py-3 text-sm font-medium rounded-lg text-gray-500 hover:bg-gray-50 hover:text-black transition-colors">
                            <i class="fas fa-lock w-5"></i> Security
                        </button>
                        <button onclick="switchTab('address')" id="nav-address" class="nav-item w-full flex items-center gap-3 px-4 py-3 text-sm font-medium rounded-lg text-gray-500 hover:bg-gray-50 hover:text-black transition-colors">
                            <i class="fas fa-map-marker-alt w-5"></i> Address Book
                        </button>
                        <a href="{{ route('customer.dashboard') }}" class="w-full flex items-center gap-3 px-4 py-3 text-sm font-medium rounded-lg text-gray-500 hover:bg-gray-50 hover:text-black transition-colors">
                            <i class="fas fa-box-open w-5"></i> My Orders
                        </a>
                    </nav>
                </div>
            </div>

            {{-- MAIN CONTENT --}}
            <div class="lg:col-span-9">
                
                {{-- Global Alert Messages --}}
                @if(session('status'))
                    <div class="mb-6 bg-green-50 border-l-4 border-green-500 p-4 flex items-center">
                        <i class="fas fa-check-circle text-green-500 mr-3"></i>
                        <span class="text-sm text-green-700">{{ session('status') }}</span>
                    </div>
                @endif
                @if(session('error'))
                    <div class="mb-6 bg-blue-50 border-l-4 border-blue-500 p-4 flex items-center">
                        <i class="fas fa-exclamation-circle text-blue-500 mr-3"></i>
                        <span class="text-sm text-blue-700">{{ session('error') }}</span>
                    </div>
                @endif

                {{-- TAB 1: PERSONAL INFO --}}
                <div id="tab-profile" class="tab-content block">
                    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-8">
                        <h2 class="text-2xl font-bold text-black mb-1">Personal Information</h2>
                        <p class="text-gray-500 text-sm mb-8">Update your personal details here.</p>

                        <form action="{{ route('profil.update') }}" method="POST">
                            @csrf
                            @method('PUT')
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                                <div>
                                    <label class="block text-xs font-bold uppercase text-gray-500 mb-2">Full Name</label>
                                    <input type="text" name="name" value="{{ old('name', $user->name) }}" class="w-full bg-gray-50 border border-gray-200 rounded-lg px-4 py-3 text-black focus:outline-none focus:border-black focus:ring-0 transition">
                                    @error('name') <p class="text-blue-500 text-xs mt-1">{{ $message }}</p> @enderror
                                </div>
                                <div>
                                    <label class="block text-xs font-bold uppercase text-gray-500 mb-2">Email Address</label>
                                    <input type="email" name="email" value="{{ old('email', $user->email) }}" class="w-full bg-gray-50 border border-gray-200 rounded-lg px-4 py-3 text-black focus:outline-none focus:border-black focus:ring-0 transition">
                                    @error('email') <p class="text-blue-500 text-xs mt-1">{{ $message }}</p> @enderror
                                </div>
                            </div>
                            <div class="flex justify-end">
                                <button type="submit" class="bg-black text-white px-8 py-3 rounded-lg font-bold uppercase tracking-widest text-xs hover:bg-gray-800 transition">
                                    Save Changes
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                {{-- TAB 2: SECURITY --}}
                <div id="tab-security" class="tab-content hidden">
                    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-8">
                        <h2 class="text-2xl font-bold text-black mb-1">Login & Security</h2>
                        <p class="text-gray-500 text-sm mb-8">Manage your password to keep your account safe.</p>

                        <form action="{{ route('profil.password.update') }}" method="POST" class="max-w-lg">
                            @csrf
                            <div class="space-y-6 mb-8">
                                <div>
                                    <label class="block text-xs font-bold uppercase text-gray-500 mb-2">Current Password</label>
                                    <input type="password" name="current_password" class="w-full bg-gray-50 border border-gray-200 rounded-lg px-4 py-3 text-black focus:outline-none focus:border-black focus:ring-0 transition">
                                    @error('current_password') <p class="text-blue-500 text-xs mt-1">{{ $message }}</p> @enderror
                                </div>
                                <div>
                                    <label class="block text-xs font-bold uppercase text-gray-500 mb-2">New Password</label>
                                    <input type="password" name="new_password" class="w-full bg-gray-50 border border-gray-200 rounded-lg px-4 py-3 text-black focus:outline-none focus:border-black focus:ring-0 transition">
                                    @error('new_password') <p class="text-blue-500 text-xs mt-1">{{ $message }}</p> @enderror
                                </div>
                                <div>
                                    <label class="block text-xs font-bold uppercase text-gray-500 mb-2">Confirm New Password</label>
                                    <input type="password" name="new_password_confirmation" class="w-full bg-gray-50 border border-gray-200 rounded-lg px-4 py-3 text-black focus:outline-none focus:border-black focus:ring-0 transition">
                                </div>
                            </div>
                            <div class="flex justify-end">
                                <button type="submit" class="bg-black text-white px-8 py-3 rounded-lg font-bold uppercase tracking-widest text-xs hover:bg-gray-800 transition">
                                    Update Password
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                {{-- TAB 3: ADDRESS BOOK --}}
                <div id="tab-address" class="tab-content hidden">
                    <div class="flex justify-between items-end mb-6">
                        <div>
                            <h2 class="text-2xl font-bold text-black mb-1">Address Book</h2>
                            <p class="text-gray-500 text-sm">Manage your shipping destinations.</p>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        {{-- Add New Card Button --}}
                        <button onclick="addAlamat()" class="group flex flex-col items-center justify-center h-full min-h-[180px] border-2 border-dashed border-gray-300 rounded-xl hover:border-black hover:bg-gray-50 transition-all cursor-pointer">
                            <div class="w-12 h-12 bg-gray-100 rounded-full flex items-center justify-center text-gray-400 group-hover:bg-black group-hover:text-white transition-colors mb-3">
                                <i class="fas fa-plus"></i>
                            </div>
                            <span class="font-bold text-gray-500 group-hover:text-black">Add New Address</span>
                        </button>

                        {{-- Address List --}}
                        @foreach($alamat as $item)
                            <div class="bg-white border border-gray-200 rounded-xl p-6 relative group hover:shadow-md transition-shadow">
                                @if($item->is_default)
                                    <span class="absolute top-4 right-4 bg-green-100 text-green-700 text-[10px] font-bold uppercase px-2 py-1 rounded">Default</span>
                                @endif
                                
                                <div class="mb-4">
                                    <h4 class="font-bold text-black text-lg mb-1">{{ $item->label }}</h4>
                                    <p class="text-gray-500 text-sm leading-relaxed">
                                        {{ $item->detail_alamat }}<br>
                                        {{ $item->kecamatan }}, {{ $item->kota }}<br>
                                        {{ $item->provinsi }} {{ $item->kode_pos }}
                                    </p>
                                </div>

                                <div class="flex items-center gap-3 border-t border-gray-100 pt-4 mt-auto">
                                    <button onclick="editAlamat({{ $item->id }})" class="text-sm font-bold text-gray-500 hover:text-black flex items-center gap-1">
                                        <i class="far fa-edit"></i> Edit
                                    </button>
                                    <div class="h-4 w-px bg-gray-300"></div>
                                    <form action="{{ route('profil.alamat.destroy', $item->id) }}" method="POST">
                                        @csrf @method('DELETE')
                                        <button type="submit" onclick="return confirm('Delete this address?')" class="text-sm font-bold text-gray-500 hover:text-blue-600 flex items-center gap-1">
                                            <i class="far fa-trash-alt"></i> Remove
                                        </button>
                                    </form>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

            </div>
        </div>
    </div>
</section>

{{-- MODAL: Modern & Centered --}}
<div id="alamatModal" class="fixed inset-0 z-50 hidden" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    {{-- Backdrop --}}
    <div class="fixed inset-0 bg-black/50 backdrop-blur-sm transition-opacity" onclick="closeModal()"></div>

    <div class="fixed inset-0 z-10 overflow-y-auto">
        <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
            
            <div class="relative transform overflow-hidden rounded-2xl bg-white text-left shadow-2xl transition-all sm:my-8 sm:w-full sm:max-w-lg">
                {{-- Header --}}
                <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4 border-b border-gray-100">
                    <h3 class="text-xl font-bold text-black" id="modalTitle">Add New Address</h3>
                </div>

                {{-- Form Content --}}
                <form id="alamatForm" action="{{ route('profil.alamat.store') }}" method="POST">
                    @csrf
                    <div id="method-spoofing"></div> {{-- Placeholder for PUT method --}}
                    <input type="hidden" id="alamatId" name="id">

                    <div class="px-6 py-6 space-y-4 max-h-[60vh] overflow-y-auto custom-scrollbar">
                        <div>
                            <label class="block text-xs font-bold uppercase text-gray-500 mb-1">Address Label</label>
                            <input type="text" id="label" name="label" placeholder="e.g. Home, Office" class="w-full bg-gray-50 border border-gray-200 rounded-lg px-3 py-2 text-sm focus:border-black focus:outline-none" required>
                        </div>
                        
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-bold uppercase text-gray-500 mb-1">Province</label>
                                <input type="text" id="provinsi" name="provinsi" class="w-full bg-gray-50 border border-gray-200 rounded-lg px-3 py-2 text-sm focus:border-black focus:outline-none" required>
                            </div>
                            <div>
                                <label class="block text-xs font-bold uppercase text-gray-500 mb-1">City</label>
                                <input type="text" id="kota" name="kota" class="w-full bg-gray-50 border border-gray-200 rounded-lg px-3 py-2 text-sm focus:border-black focus:outline-none" required>
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-bold uppercase text-gray-500 mb-1">District</label>
                                <input type="text" id="kecamatan" name="kecamatan" class="w-full bg-gray-50 border border-gray-200 rounded-lg px-3 py-2 text-sm focus:border-black focus:outline-none" required>
                            </div>
                            <div>
                                <label class="block text-xs font-bold uppercase text-gray-500 mb-1">Postal Code</label>
                                <input type="text" id="kode_pos" name="kode_pos" class="w-full bg-gray-50 border border-gray-200 rounded-lg px-3 py-2 text-sm focus:border-black focus:outline-none" required>
                            </div>
                        </div>

                        <div>
                            <label class="block text-xs font-bold uppercase text-gray-500 mb-1">Full Address</label>
                            <textarea id="detail_alamat" name="detail_alamat" rows="3" class="w-full bg-gray-50 border border-gray-200 rounded-lg px-3 py-2 text-sm focus:border-black focus:outline-none" required></textarea>
                        </div>

                        <div class="flex items-center">
                            <input type="hidden" name="is_default" value="0">
                            <input type="checkbox" id="is_default" name="is_default" value="1" class="h-4 w-4 rounded border-gray-300 text-black focus:ring-black">
                            <label for="is_default" class="ml-2 block text-sm text-gray-900 font-medium">Set as default address</label>
                        </div>
                    </div>

                    {{-- Footer --}}
                    <div class="bg-gray-50 px-6 py-4 flex justify-end gap-3">
                        <button type="button" onclick="closeModal()" class="px-4 py-2 bg-white border border-gray-300 rounded-lg text-sm font-bold text-gray-700 hover:bg-gray-50">Cancel</button>
                        <button type="submit" class="px-6 py-2 bg-black text-white rounded-lg text-sm font-bold hover:bg-gray-800">Save Address</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    // 1. Tab Switching Logic
    function switchTab(tabName) {
        // Hide all contents
        document.querySelectorAll('.tab-content').forEach(el => el.classList.add('hidden'));
        // Show target content
        document.getElementById(`tab-${tabName}`).classList.remove('hidden');

        // Reset nav styles
        document.querySelectorAll('.nav-item').forEach(el => {
            el.classList.remove('bg-gray-100', 'text-black');
            el.classList.add('text-gray-500', 'hover:bg-gray-50', 'hover:text-black');
        });

        // Set active nav style
        const activeNav = document.getElementById(`nav-${tabName}`);
        activeNav.classList.remove('text-gray-500', 'hover:bg-gray-50');
        activeNav.classList.add('bg-gray-100', 'text-black');
    }

    // 2. Modal Logic
    function addAlamat() {
        document.getElementById('modalTitle').textContent = 'Add New Address';
        const form = document.getElementById('alamatForm');
        form.action = '{{ route('profil.alamat.store') }}';
        form.reset(); // Clear form
        
        // Remove method spoofing if exists (reset to POST)
        document.getElementById('method-spoofing').innerHTML = '';

        document.getElementById('alamatModal').classList.remove('hidden');
    }

    function editAlamat(id) {
        document.getElementById('modalTitle').textContent = 'Edit Address';

        // Fetch data
        fetch(`/profil/alamat/${id}`)
            .then(res => res.json())
            .then(data => {
                document.getElementById('alamatId').value = data.id;
                document.getElementById('label').value = data.label;
                document.getElementById('provinsi').value = data.provinsi;
                document.getElementById('kota').value = data.kota;
                document.getElementById('kecamatan').value = data.kecamatan;
                document.getElementById('detail_alamat').value = data.detail_alamat;
                document.getElementById('kode_pos').value = data.kode_pos;
                document.getElementById('is_default').checked = data.is_default == 1;

                // Set Action to Update Route
                const form = document.getElementById('alamatForm');
                // Ganti dengan route update yang sesuai, contoh: /profil/alamat/{id}/update
                // Di Laravel resource biasanya PUT /profil/alamat/{id}
                let updateUrl = '{{ route('profil.alamat.edit', ':id') }}'; 
                updateUrl = updateUrl.replace(':id', id);
                form.action = updateUrl;

                // Add PUT method spoofing
                document.getElementById('method-spoofing').innerHTML = '<input type="hidden" name="_method" value="PUT">';

                document.getElementById('alamatModal').classList.remove('hidden');
            })
            .catch(err => console.error(err));
    }

    function closeModal() {
        document.getElementById('alamatModal').classList.add('hidden');
    }

    // Check URL hash to switch tab on load (optional)
    document.addEventListener("DOMContentLoaded", () => {
        if(window.location.hash) {
            const hash = window.location.hash.substring(1); // e.g. 'security'
            if(['profile', 'security', 'address'].includes(hash)) {
                switchTab(hash);
            }
        }
    });
</script>

@endsection