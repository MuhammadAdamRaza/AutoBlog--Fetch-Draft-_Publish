<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-wrap items-center justify-between gap-4">
            <div>
                <p class="text-xs font-semibold uppercase tracking-[0.2em] text-accent">Editorial desk</p>
                <h1 class="mt-2 font-serif text-3xl font-semibold text-ink">Edit post</h1>
            </div>
            <span class="rounded-full bg-paper px-3 py-1 text-xs font-semibold uppercase tracking-wider text-ink">
                {{ $post->status }}
            </span>
        </div>
    </x-slot>

    <div class="mx-auto max-w-4xl px-4 py-8 sm:px-6 lg:px-8">
        @if (session('success'))
            <div role="status" class="mb-6 rounded-xl border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-900">
                {{ session('success') }}
            </div>
        @endif

        <form
            method="POST"
            action="{{ route('admin.posts.update', $post) }}"
            class="rounded-2xl border border-rule bg-white p-5 shadow-sm sm:p-8"
        >
            @method('PUT')
            @include('admin.posts._form', ['post' => $post])
        </form>

        @if ($post->source_url)
            <p class="mt-5 break-all text-sm text-stone-600">
                Source:
                <a href="{{ $post->source_url }}" rel="nofollow noopener" target="_blank" class="font-medium text-accent underline underline-offset-4">
                    {{ $post->source_url }}
                </a>
            </p>
        @endif
    </div>
</x-app-layout>
