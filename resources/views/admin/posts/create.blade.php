<x-app-layout>
    <x-slot name="header">
        <div>
            <p class="text-xs font-semibold uppercase tracking-[0.2em] text-accent">Editorial desk</p>
            <h1 class="mt-2 font-serif text-3xl font-semibold text-ink">Create a post</h1>
        </div>
    </x-slot>

    <div class="mx-auto max-w-4xl px-4 py-8 sm:px-6 lg:px-8">
        @if ($errors->any())
            <div role="alert" class="mb-6 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-900">
                Review the fields below and try again.
            </div>
        @endif

        <form
            method="POST"
            action="{{ route('admin.posts.store') }}"
            class="rounded-2xl border border-rule bg-white p-5 shadow-sm sm:p-8"
        >
            @include('admin.posts._form', ['post' => $post])
        </form>
    </div>
</x-app-layout>
