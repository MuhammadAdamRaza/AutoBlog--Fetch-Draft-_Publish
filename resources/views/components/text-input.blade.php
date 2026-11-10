@props(['disabled' => false])

<input @disabled($disabled) {{ $attributes->merge(['class' => 'rounded-xl border-rule bg-paper/50 text-ink shadow-sm focus:border-accent focus:ring-accent disabled:cursor-not-allowed disabled:opacity-60']) }}>
