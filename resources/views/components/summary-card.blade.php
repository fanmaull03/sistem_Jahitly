@props(['title', 'value', 'helper' => null, 'accent' => 'blue'])

@php
    $accents = [
        'blue' => 'border-l-blue-500',
        'amber' => 'border-l-amber-500',
        'emerald' => 'border-l-emerald-500',
        'violet' => 'border-l-violet-500',
        'slate' => 'border-l-slate-500',
    ];

    $accentClass = $accents[$accent] ?? $accents['blue'];
@endphp

<div class="hover-lift rounded-2xl border border-stone-200 bg-white p-6 shadow-sm border-l-4 {{ $accentClass }} dark:border-stone-700 dark:bg-stone-800">
    <div class="text-sm font-semibold text-stone-500 dark:text-stone-400">{{ $title }}</div>
    <div class="mt-3 truncate text-3xl font-bold text-stone-900 sm:text-4xl dark:text-stone-100" title="{{ $value }}">{{ $value }}</div>
    @if ($helper)
        <div class="mt-2 text-sm text-stone-500 dark:text-stone-400">{{ $helper }}</div>
    @endif
</div>
