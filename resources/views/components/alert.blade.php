@props(['type' => 'info', 'title' => '', 'message' => '', 'icon' => ''])

@php
    $typeConfig = [
        'success' => [
            'bg' => 'bg-emerald-50',
            'border' => 'border-emerald-200',
            'text' => 'text-emerald-800',
            'icon' => '✓',
            'bgLight' => 'bg-emerald-500/10',
            'borderLight' => 'border-emerald-400/30',
        ],
        'error' => [
            'bg' => 'bg-rose-50',
            'border' => 'border-rose-200',
            'text' => 'text-rose-800',
            'icon' => '✕',
            'bgLight' => 'bg-rose-500/10',
            'borderLight' => 'border-rose-400/30',
        ],
        'warning' => [
            'bg' => 'bg-yellow-50',
            'border' => 'border-yellow-200',
            'text' => 'text-yellow-800',
            'icon' => '⚠',
            'bgLight' => 'bg-yellow-500/10',
            'borderLight' => 'border-yellow-400/30',
        ],
        'info' => [
            'bg' => 'bg-blue-50',
            'border' => 'border-blue-200',
            'text' => 'text-blue-800',
            'icon' => 'ℹ',
            'bgLight' => 'bg-blue-500/10',
            'borderLight' => 'border-blue-400/30',
        ],
    ];
    
    $config = $typeConfig[$type] ?? $typeConfig['info'];
    $icon = $icon ?: $config['icon'];
@endphp

<div class="rounded-2xl border {{ $config['border'] }} {{ $config['bg'] }} p-4" role="alert">
    <div class="flex gap-3">
        @if($icon)
            <div class="text-lg font-bold flex-shrink-0">{{ $icon }}</div>
        @endif
        <div class="flex-1">
            @if($title)
                <div class="font-semibold {{ $config['text'] }}">{{ $title }}</div>
            @endif
            @if($message || $slot)
                <div class="text-sm {{ $config['text'] }} {{ $title ? 'mt-1' : '' }}">
                    {!! $message ?: $slot !!}
                </div>
            @endif
        </div>
    </div>
</div>
