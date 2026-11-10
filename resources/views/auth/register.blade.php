<x-guest-layout>
    <div class="mb-7">
        <p class="text-xs font-semibold uppercase tracking-[0.2em] text-accent">Get started</p>
        <h2 class="mt-2 font-serif text-3xl font-semibold tracking-tight text-ink">{{ __('Create your account') }}</h2>
        <p class="mt-2 text-sm leading-6 text-stone-600">Set up your editorial workspace.</p>
    </div>

    <form method="POST" action="{{ route('register') }}" class="space-y-5">
        @csrf

        <div>
            <x-input-label for="name" :value="__('Name')" />
            <x-text-input id="name" class="mt-2 block w-full px-4 py-3" type="text" name="name" :value="old('name')" required autofocus autocomplete="name" />
            <x-input-error :messages="$errors->get('name')" class="mt-2 text-sm text-red-700" />
        </div>

        <div>
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" class="mt-2 block w-full px-4 py-3" type="email" name="email" :value="old('email')" required autocomplete="username" />
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

        <div class="flex flex-col-reverse items-stretch gap-4 pt-2 sm:flex-row sm:items-center sm:justify-between">
            <a class="text-center text-sm font-medium text-accent underline-offset-4 hover:underline focus:outline-none focus:ring-2 focus:ring-accent sm:text-start" href="{{ route('login') }}">
                {{ __('Already registered?') }}
            </a>
            <x-primary-button class="w-full sm:w-auto">
                {{ __('Register') }}
            </x-primary-button>
        </div>
    </form>
</x-guest-layout>
