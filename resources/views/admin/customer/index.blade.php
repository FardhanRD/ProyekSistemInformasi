@extends('layouts.admin')

@section('title', 'Customer Management')

@section('content')
<div style="padding: 32px; max-width: 1600px; margin: 0 auto;">

    {{-- Page Header --}}
    <div class="page-header" style="margin-bottom: 28px;">
        <div>
            <p style="font-size: 11px; font-weight: 700; color: #63A2BB; text-transform: uppercase; letter-spacing: 0.15em; margin: 0 0 6px;">Pengguna</p>
            <h1 class="page-header-title" style="margin: 0; font-size: 28px; font-weight: 800; tracking: -0.5px; color: #0F172A;">Customer Management</h1>
            <p class="page-header-sub" style="margin: 4px 0 0; color: #64748B; font-size: 14px;">Kelola data pelanggan, view riwayat pembelian, dan blokir/buka akun.</p>
        </div>
    </div>

    {{-- Filter & Search Panel --}}
    <div class="panel" style="margin-bottom: 28px; border-radius: 16px; border: 1px solid #E2E8F0; box-shadow: 0 4px 20px -2px rgba(148, 163, 184, 0.08);">
        <div class="panel-body" style="padding: 24px;">
            <form method="GET" style="display: flex; gap: 16px; align-items: flex-end; flex-wrap: wrap;">
                <div style="flex: 1; min-width: 300px;">
                    <label class="form-label" style="font-size: 11px; font-weight: 700; color: #475569; text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 8px;">Pencarian</label>
                    <div style="position: relative; display: flex; align-items: center;">
                        <svg width="15" height="15" fill="none" stroke="#94A3B8" stroke-width="2.5" viewBox="0 0 24 24" style="position: absolute; left: 14px; pointer-events: none;"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                        <input type="text" name="search" value="{{ $search_filter ?? '' }}" placeholder="Cari nama, email, atau no HP..." class="form-input" style="padding-left: 40px; height: 42px; border-radius: 10px; font-size: 13.5px; background-color: #F8FAFC;">
                    </div>
                </div>

                <div style="width: 180px; flex-shrink: 0;">
                    <label class="form-label" style="font-size: 11px; font-weight: 700; color: #475569; text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 8px;">Status Akun</label>
                    <select name="status" class="form-input" style="height: 42px; cursor: pointer; border-radius: 10px; font-size: 13.5px; padding: 0 12px; background-color: #F8FAFC; padding-right: 28px;">
                        <option value="">All Status</option>
                        <option value="1" {{ ($status_filter ?? '') === '1' ? 'selected' : '' }}>Aktif</option>
                        <option value="0" {{ ($status_filter ?? '') === '0' ? 'selected' : '' }}>Diblokir</option>
                    </select>
                </div>

                <div style="display: flex; gap: 8px; flex-shrink: 0;">
                    <button type="submit" class="btn-primary" style="height: 42px; padding: 0 20px; border-radius: 10px; display: inline-flex; align-items: center; gap: 8px; background: #63A2BB;">
                        <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/></svg>
                        Filter
                    </button>
                    @if(request('search') || request('status') !== null)
                        <a href="{{ route('admin.customer.index') }}" class="btn-secondary" style="height: 42px; display: inline-flex; align-items: center; justify-content: center; padding: 0 16px; border-radius: 10px; font-weight: 600; color: #64748B; border: 1.5px solid #E2E8F0; background: white;">Reset</a>
                    @endif
                </div>
            </form>
        </div>
    </div>

    {{-- Customers List Table Panel --}}
    <div class="panel" style="border-radius: 16px; border: 1px solid #E2E8F0; box-shadow: 0 4px 20px -2px rgba(148, 163, 184, 0.08); overflow: hidden;">
        <div style="overflow-x: auto;">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th style="width: 80px; text-align: center; padding: 16px 20px;">Foto</th>
                        <th style="padding: 16px 20px;">Nama Customer</th>
                        <th style="padding: 16px 20px;">Email</th>
                        <th style="padding: 16px 20px;">No HP</th>
                        <th style="padding: 16px 20px;">Tanggal Terdaftar</th>
                        <th style="text-align: center; width: 110px; padding: 16px 20px;">Total Order</th>
                        <th style="text-align: right; width: 160px; padding: 16px 20px;">Total Belanja</th>
                        <th style="text-align: center; width: 120px; padding: 16px 20px;">Status</th>
                        <th style="text-align: center; width: 140px; padding: 16px 20px;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($customers as $cust)
                        <tr>
                            <td style="text-align: center; padding: 12px 20px;">
                                @if($cust->foto_profil)
                                    <img src="{{ Storage::url($cust->foto_profil) }}" alt="{{ $cust->nama_pengguna }}" style="height: 40px; width: 40px; border-radius: 50%; object-fit: cover; border: 2px solid #E2E8F0; background: #F8FAFC; margin: 0 auto; box-shadow: 0 2px 4px rgba(0,0,0,0.05);">
                                @else
                                    <div style="height: 40px; width: 40px; border-radius: 50%; background: linear-gradient(135deg, #E0F2FE, #BAE6FD); color: #0369A1; display: flex; align-items: center; justify-content: center; font-size: 14px; font-weight: 800; border: 1.5px solid #90E0EF; margin: 0 auto; box-shadow: 0 2px 4px rgba(0,0,0,0.05);">
                                        {{ mb_strtoupper(mb_substr($cust->nama_pengguna, 0, 1)) }}
                                    </div>
                                @endif
                            </td>
                            <td style="padding: 16px 20px;">
                                <div style="font-weight: 700; color: #0F172A; font-size: 14.5px;">{{ $cust->nama_pengguna }}</div>
                                <div style="font-size: 11px; color: #94A3B8; font-weight: 600; margin-top: 2px;">ID: #{{ $cust->pengguna_id }}</div>
                            </td>
                            <td style="color: #475569; font-weight: 500; padding: 16px 20px; font-size: 13.5px;">{{ $cust->email }}</td>
                            <td style="font-family: monospace; color: #475569; font-size: 13px; padding: 16px 20px; font-weight: 500;">{{ $cust->no_telepon ?: '-' }}</td>
                            <td style="color: #64748B; font-size: 13px; padding: 16px 20px;">
                                {{ $cust->created_at ? $cust->created_at->format('d M Y') : '-' }}
                            </td>
                            <td style="text-align: center; font-weight: 700; color: #334155; padding: 16px 20px; font-size: 14px;">{{ $cust->total_order }}</td>
                            <td style="text-align: right; font-weight: 800; color: #0F172A; padding: 16px 20px; font-family: monospace; font-size: 14px;">
                                Rp {{ number_format($cust->total_belanja ?? 0, 0, ',', '.') }}
                            </td>
                            <td style="text-align: center; padding: 16px 20px;">
                                @if($cust->is_active)
                                    <span class="badge badge-success" style="font-size: 11px; padding: 4px 10px;">Aktif</span>
                                @else
                                    <span class="badge badge-danger" style="font-size: 11px; padding: 4px 10px;">Diblokir</span>
                                @endif
                            </td>
                            <td style="text-align: center; padding: 16px 20px;">
                                <form method="POST" action="{{ route('admin.customer.block', $cust->pengguna_id) }}" onsubmit="return confirm('Apakah Anda yakin ingin {{ $cust->is_active ? 'memblokir' : 'mengaktifkan kembali' }} customer ini?')" style="display: inline-block; margin: 0;">
                                    @csrf
                                    @method('PUT')
                                    @if($cust->is_active)
                                        <button type="submit" class="btn-danger" style="padding: 6px 12px; border-radius: 8px; display: inline-flex; align-items: center; gap: 6px; font-size: 12px; font-weight: 600;" title="Blokir Customer">
                                            <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/></svg>
                                            Blokir
                                        </button>
                                    @else
                                        <button type="submit" style="display: inline-flex; align-items: center; gap: 6px; padding: 6px 12px; background: #ECFDF5; color: #059669; border: 1.5px solid #A7F3D0; border-radius: 8px; font-size: 12px; font-weight: 600; cursor: pointer; transition: all 0.15s ease;" onmouseover="this.style.background='#D1FAE5'; this.style.borderColor='#34D399';" onmouseout="this.style.background='#ECFDF5'; this.style.borderColor='#A7F3D0';" title="Aktifkan Customer">
                                            <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                                            Aktifkan
                                        </button>
                                    @endif
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" style="text-align: center; color: #94A3B8; padding: 48px; font-size: 14px; font-weight: 500;">
                                <div style="display: flex; flex-direction: column; align-items: center; justify-content: center; gap: 8px;">
                                    <svg width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" style="opacity: 0.5;"><path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                                    <span>Tidak ada data customer yang ditemukan.</span>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        @if(method_exists($customers ?? null, 'links') && $customers->hasPages())
            <div style="border-top: 1px solid #F1F5F9; padding: 20px; display: flex; justify-content: center; background: white;">
                {{ $customers->links() }}
            </div>
        @endif
    </div>

</div>
@endsection
