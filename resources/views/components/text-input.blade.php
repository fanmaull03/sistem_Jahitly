@props(['disabled' => false])

<input
    @disabled($disabled)
    {{ $attributes->merge([
        'class' => 'w-full rounded-xl border border-border bg-surface px-4 py-2.5 text-sm text-ink
                    placeholder-muted/50 shadow-none transition
                    focus:border-primary focus:outline-none focus:ring-2 focus:ring-primary/20
                    disabled:bg-surface/60 disabled:cursor-not-allowed
                    dark:border-stone-600 dark:bg-stone-800 dark:text-stone-100
                    dark:placeholder-stone-500 dark:focus:border-primary'
    ]) }}
>
