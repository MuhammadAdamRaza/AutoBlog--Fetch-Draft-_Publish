<x-guest-layout>
    <a href="{{ route('blog.index') }}" class="mb-6 inline-flex min-h-11 items-center gap-2 rounded-full border border-rule bg-white px-4 py-2.5 text-sm font-semibold text-ink transition hover:border-accent/40 hover:text-accent">
        <span aria-hidden="true">←</span>
        Back to home
    </a>

    <div class="mb-7">
        <p class="text-xs font-semibold uppercase tracking-[0.2em] text-accent">Account security</p>
        <h2 class="mt-2 font-serif text-3xl font-semibold tracking-tight text-ink">{{ __('Choose a new password') }}</h2>
    </div>

    <form method="POST" action="{{ route('password.store') }}" class="space-y-5">
        @csrf

        <input type="hidden" name="token" value="{{ $request->route('token') }}">

        <div>
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" class="mt-2 block w-full px-4 py-3" type="email" name="email" :value="old('email', $request->email)" required autofocus autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="mt-2 text-sm text-red-700" />
        </div>

        <div>
            <x-input-label for="password" :value="__('Password')" />
            <x-text-input id="password" class="mt-2 block w-full px-4 py-3" type="password" name="password" required autocomplete="new-password" />
            <x-input-error :messages="$errors->get('password')" class="mt-2 text-sm text-red-700" />
        </div>

        <div>
            <x-input-label for="password_confirmation" :value="__('Confirm Password')" />
            <x-text-input id="password_confirmation" class="mt-2 block w-full px-4 py-3" type="password" name="password_confirmation" required autocomplete="new-password" />
            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2 text-sm text-red-700" />
        </div>

        <div class="pt-2">
            <x-primary-button class="w-full">
                {{ __('Reset Password') }}
            </x-primary-button>
        </div>
    </form>
</x-guest-layout>
