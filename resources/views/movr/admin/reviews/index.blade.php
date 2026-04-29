@extends('movr.layouts.admin')

@section('content')
<div class="space-y-6">
    <h2 class="text-2xl font-bold text-movr-black">Review & Moderasi</h2>

    <div class="overflow-hidden rounded-lg border border-[#E5E5E5] bg-white shadow-sm">
        <table class="w-full text-sm">
            <thead class="border-b border-[#E5E5E5] bg-[#FAFAFA]">
                <tr>
                    <th class="px-6 py-3 text-left font-semibold text-movr-black">Produk</th>
                    <th class="px-6 py-3 text-left font-semibold text-movr-black">Dari</th>
                    <th class="px-6 py-3 text-center font-semibold text-movr-black">Rating</th>
                    <th class="px-6 py-3 text-left font-semibold text-movr-black">Komentar</th>
                    <th class="px-6 py-3 text-left font-semibold text-movr-black">Status</th>
                    <th class="px-6 py-3 text-right font-semibold text-movr-black">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-[#E5E5E5]">
                @if(isset($data) && count($data) > 0)
                    @foreach($data as $review)
                    <tr class="hover:bg-[#FAFAFA]">
                        <td class="px-6 py-4">{{ $review['product_name'] ?? '-' }}</td>
                        <td class="px-6 py-4">{{ $review['customer_name'] ?? '-' }}</td>
                        <td class="px-6 py-4 text-center">
                            <span class="inline-flex items-center gap-1">
                                @for($i = 0; $i < ($review['rating'] ?? 0); $i++)
                                    <span class="text-yellow-500">★</span>
                                @endfor
                            </span>
                        </td>
                        <td class="px-6 py-4 max-w-xs truncate">{{ $review['comment'] ?? '-' }}</td>
                        <td class="px-6 py-4">
                            <span class="inline-block rounded px-2 py-1 text-xs font-semibold {{ $review['status'] === 'approved' ? 'bg-green-100 text-green-700' : 'bg-yellow-100 text-yellow-700' }}">
                                {{ ucfirst($review['status'] ?? 'pending') }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-right">
                            <a href="#" class="text-sm text-movr-red hover:underline">Moderasi</a>
                        </td>
                    </tr>
                    @endforeach
                @else
                    <tr>
                        <td colspan="6" class="px-6 py-8 text-center text-gray-500">Tidak ada review</td>
                    </tr>
                @endif
            </tbody>
        </table>
    </div>
</div>
@endsection
