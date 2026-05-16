@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8" x-data="{ tab: window.location.hash ? window.location.hash.substring(1) : 'profile' }" @hashchange.window="tab = window.location.hash.substring(1) || 'profile'">
    <div class="max-w-6xl mx-auto flex flex-col md:flex-row gap-6">
        
        <!-- Sidebar Navigation -->
        <aside class="w-full md:w-1/4">
            <div class="bg-white p-6 rounded-lg shadow-sm border border-gray-200">
                <div class="flex items-center gap-4 mb-6 pb-6 border-b">
                    <img src="{{ $user->foto_profil ? asset('storage/'.$user->foto_profil) : 'https://ui-avatars.com/api/?name='.urlencode($user->name) }}" alt="Profile" class="w-16 h-16 rounded-full object-cover">
                    <div>
                        <h2 class="font-bold text-lg">{{ $user->name }}</h2>
                        <p class="text-sm text-gray-500">{{ $user->username }}</p>
                    </div>
                </div>
                
                <nav class="space-y-2">
                    <a href="#profile" @click="tab = 'profile'" :class="tab === 'profile' ? 'bg-blue-50 text-blue-600 font-medium' : 'text-gray-600 hover:bg-gray-50'" class="block px-4 py-2 rounded-md transition">Data Diri</a>
                    <a href="#addresses" @click="tab = 'addresses'" :class="tab === 'addresses' ? 'bg-blue-50 text-blue-600 font-medium' : 'text-gray-600 hover:bg-gray-50'" class="block px-4 py-2 rounded-md transition">Alamat Saya</a>
                    <a href="#payment-methods" @click="tab = 'payment-methods'" :class="tab === 'payment-methods' ? 'bg-blue-50 text-blue-600 font-medium' : 'text-gray-600 hover:bg-gray-50'" class="block px-4 py-2 rounded-md transition">Metode Pembayaran</a>
                    <a href="#security" @click="tab = 'security'" :class="tab === 'security' ? 'bg-blue-50 text-blue-600 font-medium' : 'text-gray-600 hover:bg-gray-50'" class="block px-4 py-2 rounded-md transition">Keamanan</a>
                </nav>
            </div>
        </aside>

        <!-- Main Content -->
        <div class="flex-1 bg-white p-6 rounded-lg shadow-sm border border-gray-200">
            @if(session('success'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-6">
                    {{ session('success') }}
                </div>
            @endif
            @if($errors->any())
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-6">
                    <ul class="list-disc list-inside">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <!-- Tab: Data Diri -->
            <div x-show="tab === 'profile'" x-cloak>
                <h2 class="text-xl font-bold mb-6">Informasi Profil</h2>
                <form action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Nama Lengkap</label>
                            <input type="text" name="name" value="{{ old('name', $user->name) }}" class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500" required>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Username</label>
                            <input type="text" name="username" value="{{ old('username', $user->username) }}" class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500" required>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                            <input type="email" name="email" value="{{ old('email', $user->email) }}" class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500" required>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">No. Telepon</label>
                            <input type="text" name="no_telepon" value="{{ old('no_telepon', $user->no_telepon) }}" class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Lahir</label>
                            <input type="date" name="tanggal_lahir" value="{{ old('tanggal_lahir', $user->tanggal_lahir ? \Carbon\Carbon::parse($user->tanggal_lahir)->format('Y-m-d') : '') }}" class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Jenis Kelamin</label>
                            <select name="jenis_kelamin" class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                                <option value="">Pilih Jenis Kelamin</option>
                                <option value="L" {{ old('jenis_kelamin', $user->jenis_kelamin) == 'L' ? 'selected' : '' }}>Laki-laki</option>
                                <option value="P" {{ old('jenis_kelamin', $user->jenis_kelamin) == 'P' ? 'selected' : '' }}>Perempuan</option>
                            </select>
                        </div>
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Foto Profil</label>
                            <input type="file" name="foto_profil" accept="image/*" class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                            <p class="text-xs text-gray-500 mt-1">Format: JPG, PNG, GIF. Maksimal 2MB.</p>
                        </div>
                    </div>
                    
                    <div class="mt-6 flex justify-end">
                        <button type="submit" class="bg-blue-600 text-white font-medium py-2 px-6 rounded-md hover:bg-blue-700 transition">Simpan Perubahan</button>
                    </div>
                </form>
            </div>

            <!-- Tab: Alamat Saya -->
            <div x-show="tab === 'addresses'" x-cloak>
                <div class="flex justify-between items-center mb-6">
                    <h2 class="text-xl font-bold">Daftar Alamat</h2>
                    <button x-data @click="$dispatch('open-modal', 'add-address-modal')" class="bg-blue-600 text-white text-sm font-medium py-2 px-4 rounded-md hover:bg-blue-700 transition">+ Tambah Alamat</button>
                </div>
                
                @if($addresses->count() > 0)
                    <div class="space-y-4">
                        @foreach($addresses as $address)
                            <div class="border rounded-lg p-4 {{ $address->is_utama ? 'border-blue-500 bg-blue-50' : 'border-gray-200' }}">
                                <div class="flex justify-between items-start mb-2">
                                    <div class="flex items-center gap-2">
                                        <span class="font-bold text-gray-800">{{ $address->label }}</span>
                                        @if($address->is_utama)
                                            <span class="bg-blue-100 text-blue-700 text-xs font-semibold px-2 py-0.5 rounded">Utama</span>
                                        @endif
                                    </div>
                                    <div class="flex gap-2 flex-wrap">
                                        <a href="{{ route('profile.address.edit', $address->alamat_id) }}" class="text-sm text-blue-600 hover:underline">Edit</a>
                                        <span class="text-gray-300">|</span>
                                        @if(!$address->is_utama)
                                            <form action="{{ route('profile.address.set-primary', $address->alamat_id) }}" method="POST" class="inline">
                                                @csrf
                                                @method('PUT')
                                                <button type="submit" class="text-sm text-blue-600 hover:underline">Jadikan Utama</button>
                                            </form>
                                            <span class="text-gray-300">|</span>
                                        @endif
                                        <form action="{{ route('profile.address.delete', $address->alamat_id) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus alamat ini?')" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-sm text-red-600 hover:underline">Hapus</button>
                                        </form>
                                    </div>
                                </div>
                                <p class="font-medium">{{ $address->nama_penerima }} <span class="font-normal text-gray-500">| {{ $address->no_telepon }}</span></p>
                                <p class="text-sm text-gray-600 mt-1">{{ $address->alamat_lengkap }}</p>
                                <p class="text-sm text-gray-600">{{ $address->kelurahan }}, {{ $address->kecamatan }}, {{ $address->kota }}, {{ $address->provinsi }}, {{ $address->kode_pos }}</p>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-10 text-gray-500">
                        <p>Belum ada alamat yang ditambahkan.</p>
                    </div>
                @endif
            </div>

            <!-- Tab: Metode Pembayaran -->
            <div x-show="tab === 'payment-methods'" x-cloak>
                <div class="flex justify-between items-center mb-6">
                    <h2 class="text-xl font-bold">Metode Pembayaran Tersimpan</h2>
                    <button x-data @click="$dispatch('open-modal', 'add-payment-modal')" class="bg-blue-600 text-white text-sm font-medium py-2 px-4 rounded-md hover:bg-blue-700 transition">+ Tambah Metode</button>
                </div>

                @if($paymentMethods->count() > 0)
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        @foreach($paymentMethods as $method)
                            <div class="border border-gray-200 rounded-lg p-4 flex justify-between items-center hover:shadow-sm transition">
                                <div>
                                    <p class="font-bold">{{ $method->metodePembayaran->nama_metode }}</p>
                                    <p class="text-sm text-gray-600">{{ substr($method->nomor_akun, 0, 4) . str_repeat('*', max(0, strlen($method->nomor_akun) - 8)) . substr($method->nomor_akun, -4) }}</p>
                                    <p class="text-xs text-gray-500">{{ $method->nama_akun }}</p>
                                </div>
                                <form action="{{ route('profile.payment-methods.delete', $method->akun_pembayaran_id) }}" method="POST" onsubmit="return confirm('Hapus metode pembayaran ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-500 hover:text-red-700 p-2">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                            <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd" />
                                        </svg>
                                    </button>
                                </form>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-10 text-gray-500">
                        <p>Belum ada metode pembayaran yang ditambahkan.</p>
                    </div>
                @endif
            </div>

            <!-- Tab: Keamanan -->
            <div x-show="tab === 'security'" x-cloak>
                <h2 class="text-xl font-bold mb-6">Ubah Password</h2>
                <form action="{{ route('profile.change-password') }}" method="POST" class="max-w-md">
                    @csrf
                    @method('PUT')
                    
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Password Lama</label>
                        <input type="password" name="current_password" class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500" required>
                    </div>
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Password Baru</label>
                        <input type="password" name="new_password" class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500" required minlength="8">
                    </div>
                    <div class="mb-6">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Konfirmasi Password Baru</label>
                        <input type="password" name="new_password_confirmation" class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500" required minlength="8">
                    </div>
                    
                    <button type="submit" class="bg-blue-600 text-white font-medium py-2 px-6 rounded-md hover:bg-blue-700 transition">Update Password</button>
                </form>
            </div>
            
        </div>
    </div>
</div>

<!-- Modal Tambah Alamat -->
<div x-data="{ show: false }" x-show="show" @open-modal.window="if ($event.detail === 'add-address-modal') show = true" @keydown.escape.window="show = false" class="fixed inset-0 z-50 overflow-y-auto" style="display: none;" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <div x-show="show" x-transition.opacity class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" @click="show = false" aria-hidden="true"></div>
        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
        <div x-show="show" x-transition class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-2xl sm:w-full">
            <form action="{{ route('profile.address.store') }}" method="POST">
                @csrf
                <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4" id="modal-title">Tambah Alamat Baru</h3>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div class="sm:col-span-2">
                            <label class="block text-sm font-medium text-gray-700">Label (Rumah/Kantor)</label>
                            <input type="text" name="label" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm" placeholder="Contoh: Rumah">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Nama Penerima</label>
                            <input type="text" name="nama_penerima" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">No. Telepon</label>
                            <input type="text" name="no_telepon" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Provinsi</label>
                            <input type="text" name="provinsi" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Kota/Kabupaten</label>
                            <input type="text" name="kota" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Kecamatan</label>
                            <input type="text" name="kecamatan" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Kelurahan/Desa</label>
                            <input type="text" name="kelurahan" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Kode Pos</label>
                            <input type="text" name="kode_pos" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                        </div>
                        <div class="sm:col-span-2">
                            <label class="block text-sm font-medium text-gray-700">Alamat Lengkap</label>
                            <textarea name="alamat_lengkap" rows="3" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm" placeholder="Nama jalan, gedung, no. rumah/unit"></textarea>
                        </div>
                        <div class="sm:col-span-2">
                            <div class="flex items-start">
                                <div class="flex items-center h-5">
                                    <input id="is_utama" name="is_utama" type="checkbox" value="1" class="focus:ring-blue-500 h-4 w-4 text-blue-600 border-gray-300 rounded">
                                </div>
                                <div class="ml-3 text-sm">
                                    <label for="is_utama" class="font-medium text-gray-700">Jadikan alamat utama</label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                    <button type="submit" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-blue-600 text-base font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:ml-3 sm:w-auto sm:text-sm">
                        Simpan Alamat
                    </button>
                    <button type="button" @click="show = false" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                        Batal
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Tambah Metode Pembayaran -->
<div x-data="{ show: false }" x-show="show" @open-modal.window="if ($event.detail === 'add-payment-modal') show = true" @keydown.escape.window="show = false" class="fixed inset-0 z-50 overflow-y-auto" style="display: none;" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <div x-show="show" x-transition.opacity class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" @click="show = false" aria-hidden="true"></div>
        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
        <div x-show="show" x-transition class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
            <form action="{{ route('profile.payment-methods.store') }}" method="POST">
                @csrf
                <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4" id="modal-title">Tambah Metode Pembayaran</h3>
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Pilih Bank / E-Wallet</label>
                            <select name="metode_id" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                                <option value="">Pilih...</option>
                                @foreach($availableMethods ?? [] as $method)
                                    <option value="{{ $method->metode_id }}">{{ $method->nama_metode }} ({{ ucfirst(str_replace('_', ' ', $method->jenis_metode)) }})</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Nomor Rekening / No. Handphone</label>
                            <input type="text" name="nomor_akun" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Nama Pemilik Rekening</label>
                            <input type="text" name="nama_akun" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                        </div>
                    </div>
                </div>
                <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                    <button type="submit" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-blue-600 text-base font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:ml-3 sm:w-auto sm:text-sm">
                        Simpan Metode
                    </button>
                    <button type="button" @click="show = false" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                        Batal
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection