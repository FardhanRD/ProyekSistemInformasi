@extends('movr.layouts.admin')

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <h2 class="text-2xl font-bold text-movr-black">Promo & Voucher</h2>
        <a href="#" class="inline-flex items-center gap-2 rounded-lg bg-movr-red px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-movr-red/90">
            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            Buat Voucher
        </a>
    </div>

    <div class="overflow-hidden rounded-lg border border-[#E5E5E5] bg-white shadow-sm">
        <table class="w-full text-sm">
            <thead class="border-b border-[#E5E5E5] bg-[#FAFAFA]">
                <tr>
                    <th class="px-6 py-3 text-left font-semibold text-movr-black">Kode</th>
                    <th class="px-6 py-3 text-left font-semibold text-movr-black">Tipe</th>
                    <th class="px-6 py-3 text-right font-semibold text-movr-black">Nilai</th>
                    <th class="px-6 py-3 text-center font-semibold text-movr-black">Terpakai</th>
                    <th class="px-6 py-3 text-left font-semibold text-movr-black">Status</th>
                    <th class="px-6 py-3 text-right font-semibold text-movr-black">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-[#E5E5E5]">
                @if(isset($data) && count($data) > 0)
                    @foreach($data as $voucher)
                    <tr class="hover:bg-[#FAFAFA]">
                        <td class="px-6 py-4 font-mono font-semibold">{{ $voucher['code'] ?? '-' }}</td>
                        <td class="px-6 py-4">{{ $voucher['type'] === 'percentage' ? 'Persentase' : 'Nominal' }}</td>
                        <td class="px-6 py-4 text-right">
                            {{ $voucher['type'] === 'percentage' ? $voucher['value'] . '%' : 'Rp ' . number_format($voucher['value'] ?? 0, 0, ',', '.') }}
                        </td>
                        <td class="px-6 py-4 text-center">{{ $voucher['usage_count'] ?? 0 }}</td>
                        <td class="px-6 py-4">
                            <span class="inline-block rounded px-2 py-1 text-xs font-semibold {{ $voucher['is_active'] ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-700' }}">
                                {{ $voucher['is_active'] ? 'Aktif' : 'Nonaktif' }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-right">
                            <a href="#" class="text-sm text-movr-red hover:underline">Edit</a>
                        </td>
                    </tr>
                    @endforeach
                @else
                    <tr>
                        <td colspan="6" class="px-6 py-8 text-center text-gray-500">Tidak ada voucher</td>
                    </tr>
                @endif
            </tbody>
        </table>
    </div>
</div>
@endsection
