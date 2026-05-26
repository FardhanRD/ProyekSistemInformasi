{{--
  // ── FILE: resources/views/profile/index.blade.php ──
  Profile page: tabs Data Diri & Alamat Saya.
--}}

@extends('layouts.buyer')

@section('title', __('ui.profile_page') . ' | MOVR')

@section('content')
<div class="space-y-6" x-data="profileTabs()">

    <div>
        <div class="text-xs font-semibold text-cyan-300">{{ __('ui.profile_page') }}</div>
        <h1 class="text-2xl md:text-3xl font-black">{{ __('ui.account') }}</h1>
    </div>

    <div class="rounded-3xl border border-white/10 bg-white/5 p-3">
        <div class="flex flex-wrap gap-2">
            <button type="button" class="px-4 py-2 rounded-2xl text-sm font-bold border border-white/10 bg-black/20" :class="tab==='diri'?'bg-cyan-500 text-slate-950 border-cyan-400':'text-white'" @click="tab='diri'">{{ __('ui.data_diri') }}</button>
            <button type="button" class="px-4 py-2 rounded-2xl text-sm font-bold border border-white/10 bg-black/20" :class="tab==='alamat'?'bg-cyan-500 text-slate-950 border-cyan-400':'text-white'" @click="tab='alamat'">{{ __('ui.alamat') }}</button>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">

        <div class="lg:col-span-8" x-show="tab==='diri'" x-cloak>
            <div class="rounded-3xl border border-white/10 bg-white/5 p-6">
                <h2 class="font-bold text-lg">{{ __('ui.data_diri') }}</h2>

                <form method="post" action="{{ route('profile.update') }}" class="mt-4 space-y-4">
                    @csrf
                    @method('PUT')

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="text-sm font-semibold">{{ __('ui.full_name') }}</label>
                            <input name="nama_pengguna" value="{{ Auth::user()->nama_pengguna ?? '' }}" class="mt-2 w-full rounded-2xl border border-white/10 bg-black/20 px-4 py-2 text-sm outline-none focus:border-cyan-400" />
                        </div>
                        <div>
                            <label class="text-sm font-semibold">Username</label>
                            <input name="username" value="{{ Auth::user()->username ?? '' }}" class="mt-2 w-full rounded-2xl border border-white/10 bg-black/20 px-4 py-2 text-sm outline-none focus:border-cyan-400" />
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="text-sm font-semibold">{{ __('ui.phone_number') }}</label>
                            <input name="no_telepon" value="{{ Auth::user()->no_telepon ?? '' }}" class="mt-2 w-full rounded-2xl border border-white/10 bg-black/20 px-4 py-2 text-sm outline-none focus:border-cyan-400" />
                        </div>
                        <div>
                            <label class="text-sm font-semibold">{{ __('ui.gender') }}</label>
                            <select name="jenis_kelamin" class="mt-2 w-full rounded-2xl border border-white/10 bg-black/20 px-4 py-2 text-sm outline-none focus:border-cyan-400">
                                <option value="L" {{ (Auth::user()->jenis_kelamin ?? '')==='L'?'selected':'' }}>{{ __('ui.gender_m') }}</option>
                                <option value="P" {{ (Auth::user()->jenis_kelamin ?? '')==='P'?'selected':'' }}>{{ __('ui.gender_f') }}</option>
                            </select>
                        </div>
                    </div>

                    <div>
                        <label class="text-sm font-semibold">{{ __('ui.birth_date') }}</label>
                        <input type="date" name="tanggal_lahir" value="{{ Auth::user()->tanggal_lahir ?? '' }}" class="mt-2 w-full rounded-2xl border border-white/10 bg-black/20 px-4 py-2 text-sm outline-none focus:border-cyan-400" />
                    </div>

                    <button type="submit" class="w-full rounded-3xl bg-cyan-500 px-6 py-3 text-sm font-bold text-slate-950 hover:bg-cyan-400">{{ __('ui.save_changes') }}</button>
                </form>
            </div>
        </div>

        <div class="lg:col-span-8" x-show="tab==='alamat'" x-cloak>
            <div class="rounded-3xl border border-white/10 bg-white/5 p-6">
                <h2 class="font-bold text-lg">{{ __('ui.alamat') }}</h2>

                <div class="mt-4 space-y-3">
                    @foreach($alamat as $a)
                        <div class="rounded-2xl border border-white/10 bg-black/20 p-4">
                            <div class="flex items-start justify-between gap-3">
                                <div>
                                    <div class="font-bold">{{ $a->label }} — {{ $a->nama_penerima }}</div>
                                    <div class="text-sm text-slate-300 mt-1">{{ $a->alamat_lengkap }}</div>
                                    <div class="text-xs text-slate-400 mt-2">{{ $a->kota }}, {{ $a->provinsi }}</div>
                                    @if($a->is_utama)
                                        <div class="mt-2 inline-flex rounded-full bg-emerald-500/15 border border-emerald-400/30 px-3 py-1 text-xs text-emerald-300 font-semibold">{{ __('ui.default_address') }}</div>
                                    @endif
                                </div>

                                <div class="flex flex-col gap-2">
                                    <a href="{{ route('profile.alamat.edit', $a->alamat_id) }}" class="rounded-2xl border border-white/10 px-4 py-2 text-sm font-bold hover:bg-white/5">{{ __('ui.edit') }}</a>
                                    <form method="post" action="{{ route('alamat.utama', $a->alamat_id) }}">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="rounded-2xl bg-cyan-500 px-4 py-2 text-sm font-bold text-slate-950 hover:bg-cyan-400">{{ __('ui.set_primary') }}</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="mt-5">
                    <a href="{{ route('profile.alamat.create') }}" class="inline-flex rounded-full bg-white/5 border border-white/10 px-5 py-3 text-sm font-bold hover:bg-white/10">{{ __('ui.add_address') }}</a>
                </div>
            </div>
        </div>

    </div>

</div>

<script>
    function profileTabs() {
        return { tab: 'diri' };
    }
</script>
@endsection

