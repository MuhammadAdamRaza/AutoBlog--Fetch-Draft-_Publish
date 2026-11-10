<x-app-layout>
    <x-slot name="header">
        <p class="text-xs font-semibold uppercase tracking-[0.2em] text-accent">Account settings</p>
        <h1 class="mt-2 font-serif text-3xl font-semibold text-ink">{{ __('Your profile') }}</h1>
    </x-slot>

    <div class="mx-auto max-w-7xl space-y-5 px-4 py-8 sm:px-6 sm:py-10 lg:px-8">
            <div class="rounded-2xl border border-rule bg-white p-5 shadow-sm sm:p-8">
                <div class="max-w-xl">
                    @include('profile.partials.update-profile-information-form')
                </div>
            </div>

            <div class="rounded-2xl border border-rule bg-white p-5 shadow-sm sm:p-8">
                <div class="max-w-xl">
                    @include('profile.partials.update-password-form')
                </div>
            </div>

            <div class="rounded-2xl border border-red-200 bg-white p-5 shadow-sm sm:p-8">
                <div class="max-w-xl">
                    @include('profile.partials.delete-user-form')
                </div>
            </div>
    </div>
</x-app-layout>
