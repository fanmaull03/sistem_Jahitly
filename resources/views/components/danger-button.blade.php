<button {{ $attributes->merge([
    'type' => 'submit',
    'class' => 'inline-flex items-center justify-center rounded-xl bg-rose-600 px-5 py-2.5
                text-sm font-bold text-white shadow-sm transition
                hover:bg-rose-500 hover:shadow-md
                focus:outline-none focus:ring-2 focus:ring-rose-500/30
                disabled:opacity-50'
]) }}>
    {{ $slot }}
</button>
