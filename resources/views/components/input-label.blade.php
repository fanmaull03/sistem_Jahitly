@props(['value'])

<label {{ $attributes->merge([
    'class' => 'block text-[11px] font-bold uppercase tracking-widest text-muted dark:text-stone-400'
]) }}>
    {{ $value ?? $slot }}
</label>
