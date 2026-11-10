<x-app-layout>
    <x-slot name="header">
        <p class="text-xs font-semibold uppercase tracking-[0.2em] text-accent">Editorial desk</p>
        <h1 class="mt-2 font-serif text-3xl font-semibold text-ink">{{ __('Welcome to AutoBlog') }}</h1>
    </x-slot>

    <div class="mx-auto max-w-7xl px-4 py-10 sm:px-6 lg:px-8">
        <section class="rounded-3xl border border-rule bg-white p-6 shadow-sm sm:p-10">
            <p class="text-sm leading-7 text-stone-600">
                Your editorial workspace is ready. Create a post or review drafts waiting to be published.
            </p>
            <div class="mt-6 flex flex-wrap gap-3">
                <a href="{{ route('admin.posts.index') }}" class="inline-flex items-center justify-center rounded-full bg-accent px-5 py-3 text-sm font-semibold text-white transition hover:bg-ink">
                    Manage posts
                </a>
                <a href="{{ route('blog.index') }}" class="inline-flex items-center justify-center rounded-full border border-rule px-5 py-3 text-sm font-semibold text-ink transition hover:bg-paper">
                    View public blog
                </a>
            </section>
        </div>
    </div>
</x-app-layout>
