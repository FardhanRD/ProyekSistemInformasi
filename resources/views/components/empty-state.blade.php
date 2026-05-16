@props(['image' => '', 'title' => 'Data Tidak Ditemukan', 'message' => '', 'action' => ''])

<div class="rounded-3xl border border-white/10 bg-white/5 p-10 text-center">
    @if($image)
        <img src="{{ $image }}" alt="{{ $title }}" class="mx-auto mb-6 h-48 w-48 object-contain">
    @else
        <div class="mx-auto mb-6 h-48 w-48 flex items-center justify-center rounded-2xl bg-white/10">
            <svg class="h-24 w-24 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
            </svg>
        </div>
    @endif
    <h2 class="text-2xl font-bold">{{ $title }}</h2>
    @if($message)
        <p class="mt-2 text-sm text-slate-300">{{ $message }}</p>
    @else
        {{ $slot }}
    @endif
    @if($action)
        <div class="mt-6">
            {!! $action !!}
        </div>
    @endif
</div>
