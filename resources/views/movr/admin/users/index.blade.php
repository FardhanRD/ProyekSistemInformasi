@extends('movr.layouts.admin')

@section('content')
<div class="space-y-6">
    <h2 class="text-2xl font-bold text-movr-black">Manajemen Customer</h2>

    <div class="overflow-hidden rounded-lg border border-[#E5E5E5] bg-white shadow-sm">
        <table class="w-full text-sm">
            <thead class="border-b border-[#E5E5E5] bg-[#FAFAFA]">
                <tr>
                    <th class="px-6 py-3 text-left font-semibold text-movr-black">Nama</th>
                    <th class="px-6 py-3 text-left font-semibold text-movr-black">Email</th>
                    <th class="px-6 py-3 text-center font-semibold text-movr-black">Pesanan</th>
                    <th class="px-6 py-3 text-center font-semibold text-movr-black">Status</th>
                    <th class="px-6 py-3 text-left font-semibold text-movr-black">Bergabung</th>
                    <th class="px-6 py-3 text-right font-semibold text-movr-black">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-[#E5E5E5]">
                @if(isset($data) && count($data) > 0)
                    @foreach($data as $user)
                    <tr class="hover:bg-[#FAFAFA]">
                        <td class="px-6 py-4 font-semibold">{{ $user['name'] ?? '-' }}</td>
                        <td class="px-6 py-4">{{ $user['email'] ?? '-' }}</td>
                        <td class="px-6 py-4 text-center">{{ $user['orders_count'] ?? 0 }}</td>
                        <td class="px-6 py-4 text-center">
                            <span class="inline-block rounded px-2 py-1 text-xs font-semibold {{ $user['is_blocked'] ? 'bg-blue-100 text-blue-700' : 'bg-green-100 text-green-700' }}">
                                {{ $user['is_blocked'] ? 'Diblokir' : 'Aktif' }}
                            </span>
                        </td>
                        <td class="px-6 py-4">{{ isset($user['created_at']) ? date('d/m/Y', strtotime($user['created_at'])) : '-' }}</td>
                        <td class="px-6 py-4 text-right">
                            <a href="#" class="text-sm text-movr-red hover:underline">Detail</a>
                        </td>
                    </tr>
                    @endforeach
                @else
                    <tr>
                        <td colspan="6" class="px-6 py-8 text-center text-gray-500">Tidak ada customer</td>
                    </tr>
                @endif
            </tbody>
        </table>
    </div>
</div>
@endsection
