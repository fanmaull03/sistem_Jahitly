<button {{ $attributes->merge([
    'type' => 'button',
    'class' => 'inline-flex items-center justify-center rounded-xl border border-border bg-white
                px-5 py-2.5 text-sm font-semibold text-ink shadow-none transition
                hover:border-primary/40 hover:bg-surface hover:text-primary
                focus:outline-none focus:ring-2 focus:ring-primary/20
                disabled:opacity-50 disabled:cursor-not-allowed
                dark:border-stone-600 dark:bg-stone-700 dark:text-stone-300 dark:hover:bg-stone-600'
]) }}>
    {{ $slot }}
</button>
