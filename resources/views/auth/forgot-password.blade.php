<x-guest-layout>
    <a href="{{ route('blog.index') }}" class="mb-6 inline-flex min-h-11 items-center gap-2 rounded-full border border-rule bg-white px-4 py-2.5 text-sm font-semibold text-ink transition hover:border-accent/40 hover:text-accent">
        <span aria-hidden="true">←</span>
        Back to home
    </a>

    <div class="mb-6">
        <p class="text-xs font-semibold uppercase tracking-[0.2em] text-accent">Account help</p>
        <h2 class="mt-2 font-serif text-3xl font-semibold tracking-tight text-ink">{{ __('Reset your password') }}</h2>
        <p class="mt-3 text-sm leading-6 text-stone-600">
            {{ __('Enter your email address and we will send you a password reset link.') }}
        </p>
    </div>

    <x-auth-session-status class="mb-5 rounded-xl border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-900" :status="session('status')" />

    <form method="POST" action="{{ route('password.email') }}" class="space-y-5">
        @csrf

        <div>
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" class="mt-2 block w-full px-4 py-3" type="email" name="email" :value="old('email')" required autofocus autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="mt-2 text-sm text-red-700" />
        </div>

        <div class="pt-2">
            <x-primary-button class="w-full">
                {{ __('Email Password Reset Link') }}
            </x-primary-button>
        </div>
    </form>
</x-guest-layout>
