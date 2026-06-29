@props(['title', 'value', 'helper' => null, 'accent' => 'blue'])

@php
    $config = [
        'blue'    => ['bg' => 'bg-primary/10', 'dot' => 'bg-primary'],
        'amber'   => ['bg' => 'bg-accent/10', 'dot' => 'bg-accent'],
        'emerald' => ['bg' => 'bg-emerald-100', 'dot' => 'bg-emerald-500'],
        'violet'  => ['bg' => 'bg-violet-100', 'dot' => 'bg-violet-500'],
        'slate'   => ['bg' => 'bg-slate-100', 'dot' => 'bg-slate-500'],
    ];
    $c = $config[$accent] ?? $config['blue'];
@endphp

<div class="hover-lift rounded-2xl border border-border bg-white p-6 dark:border-stone-700 dark:bg-stone-800">
    <div class="flex items-start justify-between">
        <p class="text-sm font-medium text-muted dark:text-stone-400">{{ $title }}</p>
        <div class="flex h-9 w-9 items-center justify-center rounded-xl {{ $c['bg'] }}">
            <div class="h-4 w-4 rounded-full {{ $c['dot'] }} opacity-70"></div>
        </div>
    </div>
    <div class="mt-4 truncate text-3xl font-extrabold tracking-tight text-ink dark:text-stone-100" title="{{ $value }}">{{ $value }}</div>
    @if ($helper)
        <p class="mt-1 text-xs text-muted dark:text-stone-500">{{ $helper }}</p>
    @endif
</div>
