<button {{ $attributes->merge([
  'type' => 'submit',
  'class' => 'inline-flex items-center justify-center rounded-xl bg-primary px-5 py-2.5
              text-sm font-bold text-white shadow-sm transition
              hover:bg-primary-hover hover:shadow-md hover:shadow-primary/20
              focus:outline-none focus:ring-2 focus:ring-primary/30
              disabled:opacity-50 disabled:cursor-not-allowed'
]) }}>
    {{ $slot }}
</button>
