@extends('movr.layouts.admin')

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <h2 class="text-2xl font-bold text-movr-black">Pricing Varian</h2>
        <a href="#" class="inline-flex items-center gap-2 rounded-lg bg-movr-red px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-movr-red/90">
            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            Tambah Pricing
        </a>
    </div>

    <div class="overflow-hidden rounded-lg border border-[#E5E5E5] bg-white shadow-sm">
        <table class="w-full text-sm">
            <thead class="border-b border-[#E5E5E5] bg-[#FAFAFA]">
                <tr>
                    <th class="px-6 py-3 text-left font-semibold text-movr-black">Varian</th>
                    <th class="px-6 py-3 text-right font-semibold text-movr-black">Harga Beli</th>
                    <th class="px-6 py-3 text-right font-semibold text-movr-black">Harga Jual</th>
                    <th class="px-6 py-3 text-right font-semibold text-movr-black">Margin</th>
                    <th class="px-6 py-3 text-right font-semibold text-movr-black">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-[#E5E5E5]">
                @if(isset($data) && count($data) > 0)
                    @foreach($data as $pricing)
                    <tr class="hover:bg-[#FAFAFA]">
                        <td class="px-6 py-4">{{ $pricing['variant_name'] ?? '-' }}</td>
                        <td class="px-6 py-4 text-right">Rp {{ number_format($pricing['cost_price'] ?? 0, 0, ',', '.') }}</td>
                        <td class="px-6 py-4 text-right font-semibold">Rp {{ number_format($pricing['selling_price'] ?? 0, 0, ',', '.') }}</td>
                        <td class="px-6 py-4 text-right text-green-600">{{ $pricing['margin'] ?? 0 }}%</td>
                        <td class="px-6 py-4 text-right">
                            <a href="#" class="text-sm text-movr-red hover:underline">Edit</a>
                        </td>
                    </tr>
                    @endforeach
                @else
                    <tr>
                        <td colspan="5" class="px-6 py-8 text-center text-gray-500">Tidak ada pricing</td>
                    </tr>
                @endif
            </tbody>
        </table>
    </div>
</div>
@endsection
