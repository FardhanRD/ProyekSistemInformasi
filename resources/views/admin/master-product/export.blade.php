@extends('layouts.admin')

@section('title','Master Product Export')

@section('content')
<div class="mx-auto max-w-7xl px-4 py-6">
    <h1 class="font-bold text-xl mb-4">Master Product Export</h1>
    <table class="min-w-full text-sm border-collapse">
        <thead>
            <tr class="text-left text-xs uppercase tracking-wider text-slate-500">
                <th class="py-2 px-2">ID</th>
                <th class="py-2 px-2">Nama Produk</th>
                <th class="py-2 px-2">Kategori</th>
                <th class="py-2 px-2">Supplier</th>
                <th class="py-2 px-2">Gender</th>
                <th class="py-2 px-2">Price</th>
                <th class="py-2 px-2">Status</th>
                <th class="py-2 px-2">Created At</th>
            </tr>
        </thead>
        <tbody>
            @foreach($items as $p)
                <tr>
                    <td class="py-2 px-2">{{ $p->formatted_id ?? $p->produk_id }}</td>
                    <td class="py-2 px-2">{{ $p->nama_produk }}</td>
                    <td class="py-2 px-2">{{ $p->kategori->nama_kategori ?? '-' }}</td>
                    <td class="py-2 px-2">{{ $p->supplier->nama_toko ?? '-' }}</td>
                    <td class="py-2 px-2">{{ $p->gender }}</td>
                    <td class="py-2 px-2">{{ $p->harga_dasar }}</td>
                    <td class="py-2 px-2">{{ $p->status_publish }}</td>
                    <td class="py-2 px-2">{{ $p->created_at->toDateTimeString() }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection
