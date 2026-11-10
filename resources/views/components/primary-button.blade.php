<button {{ $attributes->merge(['type' => 'submit', 'class' => 'inline-flex items-center justify-center rounded-full border border-transparent bg-accent px-5 py-3 text-sm font-semibold text-white transition hover:bg-ink focus:outline-none focus:ring-2 focus:ring-accent focus:ring-offset-2']) }}>
    {{ $slot }}
</button>
