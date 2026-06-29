@props(['status'])

@if ($status)
    <div {{ $attributes->merge([
        'class' => 'flex items-center gap-2 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3
                    text-sm font-medium text-emerald-800
                    dark:border-emerald-800 dark:bg-emerald-900/20 dark:text-emerald-400'
    ]) }}>
        <svg class="h-4 w-4 shrink-0 text-emerald-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
        </svg>
        {{ $status }}
    </div>
@endif
