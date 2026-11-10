<x-guest-layout>
    <a
        href="{{ route('blog.index') }}"
        class="mb-6 inline-flex min-h-11 items-center gap-2 rounded-full border border-rule bg-white px-4 py-2.5 text-sm font-semibold text-ink transition hover:border-accent/40 hover:text-accent focus:outline-none focus:ring-2 focus:ring-accent focus:ring-offset-2"
    >
        <span aria-hidden="true">←</span>
        Back to home
    </a>

    <div class="mb-7">
        <p class="text-xs font-semibold uppercase tracking-[0.2em] text-accent">Welcome back</p>
        <h2 class="mt-2 font-serif text-3xl font-semibold tracking-tight text-ink">{{ __('Sign in') }}</h2>
        <p class="mt-2 text-sm leading-6 text-stone-600">Continue to your editorial workspace.</p>
    </div>

    <x-auth-session-status class="mb-5 rounded-xl border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-900" :status="session('status')" />

    <form method="POST" action="{{ route('login') }}" class="space-y-5">
        @csrf

        <div>
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" class="mt-2 block w-full px-4 py-3" type="email" name="email" :value="old('email')" required autofocus autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="mt-2 text-sm text-red-700" />
        </div>

        <div>
            <x-input-label for="password" :value="__('Password')" />
            <x-text-input id="password" class="mt-2 block w-full px-4 py-3" type="password" name="password" required autocomplete="current-password" />
            <x-input-error :messages="$errors->get('password')" class="mt-2 text-sm text-red-700" />
        </div>

        <div class="flex flex-wrap items-center justify-between gap-3">
            <label for="remember_me" class="inline-flex items-center gap-2 text-sm text-stone-600">
                <input id="remember_me" type="checkbox" class="rounded border-rule text-accent shadow-sm focus:ring-accent" name="remember">
                <span>{{ __('Remember me') }}</span>
            </label>
            @if (Route::has('password.request'))
                <a class="text-sm font-medium text-accent underline-offset-4 hover:underline focus:outline-none focus:ring-2 focus:ring-accent" href="{{ route('password.request') }}">
                    {{ __('Forgot your password?') }}
                </a>
            @endif
        </div>

        <div class="pt-2">
            <x-primary-button class="w-full">
                {{ __('Log in') }}
            </x-primary-button>
        </div>
    </form>
</x-guest-layout>
