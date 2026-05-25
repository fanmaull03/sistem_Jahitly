@props(['title', 'value', 'helper' => null, 'accent' => 'slate'])

@php
    $accents = [
        'slate' => 'border-slate-200 text-slate-900',
        'blue' => 'border-blue-200 text-blue-900',
        'amber' => 'border-amber-200 text-amber-900',
        'emerald' => 'border-emerald-200 text-emerald-900',
        'violet' => 'border-violet-200 text-violet-900',
    ];

    $accentClass = $accents[$accent] ?? $accents['slate'];
@endphp

<div class="rounded-2xl border {{ $accentClass }} bg-white p-5 shadow-sm">
    <div class="text-sm font-semibold text-slate-500">{{ $title }}</div>
    <div class="mt-3 text-3xl font-bold">{{ $value }}</div>
    @if ($helper)
        <div class="mt-2 text-sm text-slate-500">{{ $helper }}</div>
    @endif
</div>
