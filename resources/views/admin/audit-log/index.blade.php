@extends('layouts.admin')

@section('title', 'Security & Audit Log')

@section('content')
<div class="space-y-6">
    <div class="rounded-3xl border border-slate-200 bg-white p-5 shadow-sm">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="text-2xl font-bold text-slate-900">Security & Audit Log</h1>
                <p class="text-slate-600">Jejak CRUD admin yang otomatis tersimpan di admin_log.</p>
            </div>
        </div>
    </div>

    <div class="rounded-3xl border border-slate-200 bg-white p-5 shadow-sm">
        <form method="GET" class="flex flex-wrap gap-3 items-end">
            <select name="admin_id" class="rounded-xl border px-4 py-2 text-sm">
                <option value="">All Admin</option>
                @foreach($admins as $admin)
                    <option value="{{ $admin->admin_id }}" @selected(request('admin_id') == $admin->admin_id)>{{ $admin->pengguna?->nama_pengguna ?? 'Admin #'.$admin->admin_id }}</option>
                @endforeach
            </select>
            <input type="text" name="aksi" value="{{ request('aksi') }}" placeholder="Aksi (created/updated/deleted)" class="rounded-xl border px-4 py-2 text-sm">
            <input type="date" name="start_date" value="{{ request('start_date') }}" class="rounded-xl border px-4 py-2 text-sm">
            <input type="date" name="end_date" value="{{ request('end_date') }}" class="rounded-xl border px-4 py-2 text-sm">
            <button class="rounded-xl bg-[#2B9BAF] px-4 py-2 text-white font-semibold">Filter</button>
        </form>
    </div>

    <div class="rounded-3xl border border-slate-200 bg-white shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-slate-50">
                    <tr class="text-left text-xs uppercase text-slate-600">
                        <th class="px-4 py-3">Waktu</th>
                        <th class="px-4 py-3">Admin</th>
                        <th class="px-4 py-3">Aksi</th>
                        <th class="px-4 py-3">Tabel</th>
                        <th class="px-4 py-3">Record ID</th>
                        <th class="px-4 py-3">IP Address</th>
                        <th class="px-4 py-3">Detail</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($logs as $log)
                        <tr class="border-t border-slate-100 align-top">
                            <td class="px-4 py-3 whitespace-nowrap">{{ $log->created_at }}</td>
                            <td class="px-4 py-3">{{ $log->admin?->pengguna?->nama_pengguna ?? '-' }}</td>
                            <td class="px-4 py-3">{{ $log->aksi }}</td>
                            <td class="px-4 py-3">{{ $log->tabel ?? '-' }}</td>
                            <td class="px-4 py-3">{{ $log->record_id ?? '-' }}</td>
                            <td class="px-4 py-3">{{ $log->ip_address ?? '-' }}</td>
                            <td class="px-4 py-3">
                                <details class="rounded-xl border border-slate-200 bg-slate-50 p-3">
                                    <summary class="cursor-pointer text-[#2B9BAF] font-semibold">Lihat detail</summary>
                                    <div class="mt-3 grid md:grid-cols-2 gap-3 text-xs">
                                        <div>
                                            <p class="font-semibold mb-2">Data Lama</p>
                                            <pre class="whitespace-pre-wrap break-words bg-white border rounded-lg p-3">{{ json_encode($log->data_lama, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
                                        </div>
                                        <div>
                                            <p class="font-semibold mb-2">Data Baru</p>
                                            <pre class="whitespace-pre-wrap break-words bg-white border rounded-lg p-3">{{ json_encode($log->data_baru, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
                                        </div>
                                    </div>
                                </details>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="7" class="px-4 py-6 text-center text-slate-500">Belum ada log.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="p-4 border-t border-slate-100">{{ $logs->links() }}</div>
    </div>
</div>
@endsection
