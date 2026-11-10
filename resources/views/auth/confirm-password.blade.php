<x-guest-layout>
    <a href="{{ route('blog.index') }}" class="mb-6 inline-flex min-h-11 items-center gap-2 rounded-full border border-rule bg-white px-4 py-2.5 text-sm font-semibold text-ink transition hover:border-accent/40 hover:text-accent">
        <span aria-hidden="true">←</span>
        Back to home
    </a>

    <div class="mb-6">
        <p class="text-xs font-semibold uppercase tracking-[0.2em] text-accent">Account security</p>
        <h2 class="mt-2 font-serif text-3xl font-semibold tracking-tight text-ink">{{ __('Confirm your password') }}</h2>
        <p class="mt-3 text-sm leading-6 text-stone-600">
            {{ __('This is a secure area of the application. Please confirm your password before continuing.') }}
        </p>
    </div>

    <form method="POST" action="{{ route('password.confirm') }}" class="space-y-5">
        @csrf

        <div>
            <x-input-label for="password" :value="__('Password')" />
            <x-text-input id="password" class="mt-2 block w-full px-4 py-3" type="password" name="password" required autocomplete="current-password" />
            <x-input-error :messages="$errors->get('password')" class="mt-2 text-sm text-red-700" />
        </div>

        <div class="pt-2">
            <x-primary-button class="w-full">
                {{ __('Confirm') }}
            </x-primary-button>
        </div>
    </form>
</x-guest-layout>
