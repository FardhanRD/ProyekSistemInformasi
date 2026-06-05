@extends('layouts.admin')

@section('title', 'Security & Audit Log')

@section('content')
<div style="padding: 32px; max-width: 1600px; margin: 0 auto;">

    {{-- Page Header --}}
    <div class="page-header" style="margin-bottom: 28px;">
        <div>
            <p style="font-size: 11px; font-weight: 700; color: #63A2BB; text-transform: uppercase; letter-spacing: 0.15em; margin: 0 0 6px;">Sistem & Keamanan</p>
            <h1 class="page-header-title" style="margin: 0; font-size: 28px; font-weight: 800; tracking: -0.5px; color: #0F172A;">Security & Audit Log</h1>
            <p class="page-header-sub" style="margin: 4px 0 0; color: #64748B; font-size: 14px;">Jejak aktivitas CRUD admin yang otomatis terekam dalam database admin_log.</p>
        </div>
    </div>

    {{-- Filter Panel --}}
    <div class="panel" style="margin-bottom: 28px; border-radius: 16px; border: 1px solid #E2E8F0; box-shadow: 0 4px 20px -2px rgba(148, 163, 184, 0.08);">
        <div class="panel-body" style="padding: 24px;">
            <form method="GET" style="display: flex; gap: 16px; align-items: flex-end; flex-wrap: wrap;">
                <div style="width: 200px; flex-shrink: 0;">
                    <label class="form-label" style="font-size: 11px; font-weight: 700; color: #475569; text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 8px;">Filter Admin</label>
                    <select name="admin_id" class="form-input" style="height: 42px; cursor: pointer; border-radius: 10px; font-size: 13.5px; padding: 0 12px; background-color: #F8FAFC;">
                        <option value="">All Admin (Semua)</option>
                        @foreach($admins as $admin)
                            <option value="{{ $admin->admin_id }}" @selected(request('admin_id') == $admin->admin_id)>
                                {{ $admin->pengguna?->nama_pengguna ?? 'Admin #'.$admin->admin_id }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div style="flex: 1; min-width: 200px;">
                    <label class="form-label" style="font-size: 11px; font-weight: 700; color: #475569; text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 8px;">Filter Aksi</label>
                    <input type="text" name="aksi" value="{{ request('aksi') }}" placeholder="Contoh: created, updated, deleted..." class="form-input" style="height: 42px; border-radius: 10px; font-size: 13.5px; padding: 0 16px; background-color: #F8FAFC;">
                </div>

                <div style="width: 160px; flex-shrink: 0;">
                    <label class="form-label" style="font-size: 11px; font-weight: 700; color: #475569; text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 8px;">Mulai Tanggal</label>
                    <input type="date" name="start_date" value="{{ request('start_date') }}" class="form-input" style="height: 42px; border-radius: 10px; font-size: 13.5px; padding: 0 12px; background-color: #F8FAFC;">
                </div>

                <div style="width: 160px; flex-shrink: 0;">
                    <label class="form-label" style="font-size: 11px; font-weight: 700; color: #475569; text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 8px;">Sampai Tanggal</label>
                    <input type="date" name="end_date" value="{{ request('end_date') }}" class="form-input" style="height: 42px; border-radius: 10px; font-size: 13.5px; padding: 0 12px; background-color: #F8FAFC;">
                </div>

                <div style="display: flex; gap: 8px; flex-shrink: 0;">
                    <button type="submit" class="btn-primary" style="height: 42px; padding: 0 20px; border-radius: 10px; display: inline-flex; align-items: center; gap: 8px; background: #63A2BB;">
                        <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/></svg>
                        Filter
                    </button>
                    @if(request('admin_id') || request('aksi') || request('start_date') || request('end_date'))
                        <a href="{{ route('admin.audit-log.index') }}" class="btn-secondary" style="height: 42px; display: inline-flex; align-items: center; justify-content: center; padding: 0 16px; border-radius: 10px; font-weight: 600; color: #64748B; border: 1.5px solid #E2E8F0; background: white;">Reset</a>
                    @endif
                </div>
            </form>
        </div>
    </div>

    {{-- Audit Log Table Panel --}}
    <div class="panel" style="border-radius: 16px; border: 1px solid #E2E8F0; box-shadow: 0 4px 20px -2px rgba(148, 163, 184, 0.08); overflow: hidden;">
        <div style="overflow-x: auto;">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th style="width: 180px; padding: 16px 20px;">Waktu Kejadian</th>
                        <th style="width: 160px; padding: 16px 20px;">Nama Admin</th>
                        <th style="width: 110px; padding: 16px 20px;">Aksi</th>
                        <th style="width: 150px; padding: 16px 20px;">Nama Tabel</th>
                        <th style="width: 100px; padding: 16px 20px; text-align: center;">Record ID</th>
                        <th style="width: 140px; padding: 16px 20px;">IP Address</th>
                        <th style="padding: 16px 20px;">Perubahan Data Detail</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($logs as $log)
                        <tr style="vertical-align: top;">
                            <td style="font-family: monospace; color: #64748B; font-size: 13px; padding: 16px 20px; font-weight: 500;">
                                {{ $log->created_at ? $log->created_at->format('d/m/Y H:i:s') : '-' }}
                            </td>
                            <td style="padding: 16px 20px;">
                                <span class="badge badge-admin" style="font-size: 11px; padding: 4px 10px;">{{ $log->admin?->pengguna?->nama_pengguna ?? 'Sistem' }}</span>
                            </td>
                            <td style="padding: 16px 20px;">
                                @php
                                    $actionBadges = [
                                        'created' => 'badge-success',
                                        'updated' => 'badge-info',
                                        'deleted' => 'badge-danger',
                                    ];
                                    $act = strtolower($log->aksi);
                                    $badgeClass = $actionBadges[$act] ?? 'badge';
                                @endphp
                                <span class="badge {{ $badgeClass }}" style="font-size: 10.5px; padding: 3px 9px; font-weight: 700;">{{ strtoupper($log->aksi) }}</span>
                            </td>
                            <td style="font-family: monospace; font-weight: 600; color: #475569; padding: 16px 20px; font-size: 13px;">{{ $log->tabel ?? '-' }}</td>
                            <td style="text-align: center; font-weight: 700; color: #0F172A; padding: 16px 20px; font-size: 13.5px;">{{ $log->record_id ?? '-' }}</td>
                            <td style="font-family: monospace; color: #64748B; font-size: 13px; padding: 16px 20px;">{{ $log->ip_address ?? '-' }}</td>
                            <td style="padding: 12px 20px;">
                                <details style="border: 1px solid #E2E8F0; border-radius: 12px; background: #F8FAFC; overflow: hidden; transition: all 0.2s ease;">
                                    <summary style="cursor: pointer; color: #63A2BB; font-weight: 700; font-size: 12.5px; outline: none; user-select: none; padding: 10px 14px; background: #F1F5F9; display: flex; align-items: center; justify-content: space-between; border-radius: 11px;">
                                        <span>Tampilkan Detail Payload</span>
                                        <svg width="12" height="12" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24" style="transition: transform 0.2s;"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>
                                    </summary>
                                    <div style="padding: 14px; display: grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 14px; font-size: 11.5px; border-top: 1px solid #E2E8F0; background: white;">
                                        <div>
                                            <p style="font-weight: 800; color: #94A3B8; margin: 0 0 6px; text-transform: uppercase; font-size: 9.5px; letter-spacing: 0.05em;">Data Lama</p>
                                            <pre style="white-space: pre-wrap; word-break: break-all; background: #FAFBFD; border: 1px solid #E2E8F0; border-radius: 8px; padding: 10px; margin: 0; font-family: monospace; color: #64748B; overflow: auto; max-height: 180px; font-size: 11px; line-height: 1.4;">{{ json_encode($log->data_lama, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
                                        </div>
                                        <div>
                                            <p style="font-weight: 800; color: #94A3B8; margin: 0 0 6px; text-transform: uppercase; font-size: 9.5px; letter-spacing: 0.05em;">Data Baru</p>
                                            <pre style="white-space: pre-wrap; word-break: break-all; background: #FAFBFD; border: 1px solid #E2E8F0; border-radius: 8px; padding: 10px; margin: 0; font-family: monospace; color: #1E293B; overflow: auto; max-height: 180px; font-size: 11px; line-height: 1.4;">{{ json_encode($log->data_baru, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
                                        </div>
                                    </div>
                                </details>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" style="text-align: center; color: #94A3B8; padding: 48px; font-size: 14px; font-weight: 500;">
                                <div style="display: flex; flex-direction: column; align-items: center; justify-content: center; gap: 8px;">
                                    <svg width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" style="opacity: 0.5;"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                                    <span>Belum ada riwayat aktivitas log keamanan untuk kriteria ini.</span>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        @if(method_exists($logs ?? null, 'links') && $logs->hasPages())
            <div style="border-top: 1px solid #F1F5F9; padding: 20px; display: flex; justify-content: center; background: white;">
                {{ $logs->links() }}
            </div>
        @endif
    </div>

</div>
@endsection
