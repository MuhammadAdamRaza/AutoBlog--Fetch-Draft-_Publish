<x-app-layout>
    <x-slot name="header">
        <div>
            <p class="text-xs font-semibold uppercase tracking-wider text-accent">Editorial Desk</p>
            <h1 class="mt-1 font-serif text-2xl font-bold text-ink">Create post</h1>
        </div>
    </x-slot>

    <div class="mx-auto max-w-4xl px-4 py-8 sm:px-6 lg:px-8">
        <form
            method="POST"
            action="{{ route('admin.posts.store') }}"
            class="rounded-lg border border-rule bg-white p-6"
        >
            @include('admin.posts._form', ['post' => $post, 'categories' => $categories ?? \App\Models\Post::CATEGORIES])
        </form>
    </div>
</x-app-layout>
