<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-wrap items-center justify-between gap-4">
            <div>
                <p class="text-xs font-semibold uppercase tracking-wider text-accent">Split-Screen Editorial Review</p>
                <h1 class="mt-1 font-serif text-2xl font-bold text-ink">Edit post</h1>
            </div>
            <div class="flex flex-wrap items-center gap-2.5">
                <span @class([
                    'rounded px-2.5 py-1 text-xs font-semibold uppercase tracking-wider',
                    'bg-emerald-100 text-emerald-900' => $post->status === 'published',
                    'bg-amber-100 text-amber-900' => $post->status !== 'published',
                ])>
                    {{ $post->status }}
                </span>

                @if ($post->status === 'published')
                    <a href="{{ route('blog.show', $post) }}" class="rounded-md border border-rule bg-paper px-3 py-1.5 text-xs font-semibold text-accent hover:border-gray-400">
                        View live article
                    </a>
                    <form method="POST" action="{{ route('admin.posts.unpublish', $post) }}">
                        @csrf
                        <button type="submit" class="rounded-md border border-rule bg-white px-3 py-1.5 text-xs font-semibold text-amber-800 hover:bg-paper">
                            Move to Drafts
                        </button>
                    </form>
                @else
                    <form method="POST" action="{{ route('admin.posts.publish', $post) }}">
                        @csrf
                        <button type="submit" class="rounded-md bg-accent px-3.5 py-1.5 text-xs font-semibold text-white transition hover:bg-ink">
                            Publish Post
                        </button>
                    </form>
                @endif
            </div>
        </div>
    </x-slot>

    <div class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8">
        @if (session('success'))
            <div role="status" class="mb-6 rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-medium text-emerald-900">
                {{ session('success') }}
            </div>
        @endif

        <div class="grid gap-6 lg:grid-cols-12 lg:items-start">
            {{-- Left Pane (40% width): Read-only Original Source Extracted Text --}}
            <aside class="rounded-lg border border-rule bg-white p-4 sm:p-5 lg:col-span-5 lg:sticky lg:top-20">
                <div class="flex flex-wrap items-center justify-between gap-2 border-b border-rule pb-3">
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-wider text-gray-500">Original Extracted Source</p>
                        <h2 class="mt-0.5 font-serif text-base font-bold text-ink">Source Reference (Read-Only)</h2>
                    </div>
                    @if ($post->ai_model)
                        <span class="rounded bg-paper px-2 py-1 text-[11px] font-medium text-gray-600 border border-rule">
                            {{ $post->ai_model }}
                        </span>
                    @endif
                </div>

                @if ($post->source_url)
                    <p class="mt-3 break-all rounded-md border border-rule bg-paper px-3 py-2 text-xs text-gray-600">
                        <span class="font-semibold text-ink">URL:</span>
                        <a href="{{ $post->source_url }}" rel="nofollow noopener" target="_blank" class="text-accent underline underline-offset-2 hover:text-ink">
                            {{ $post->source_url }}
                        </a>
                    </p>
                @endif

                <div class="mt-4 max-h-[40vh] overflow-y-auto rounded-md border border-rule bg-paper p-3.5 text-sm leading-relaxed text-gray-700 whitespace-pre-line sm:p-4 lg:max-h-[60vh]">
                    @if (filled($post->source_body))
                        {{ $post->source_body }}
                    @else
                        <span class="text-gray-500">No extracted source text was saved for this post. You can verify the source URL above or edit the draft directly on the right.</span>
                    @endif
                </div>
            </aside>

            {{-- Right Pane (60% width): Draft & Publish Editor --}}
            <section class="lg:col-span-7">
                <form
                    method="POST"
                    action="{{ route('admin.posts.update', $post) }}"
                    class="rounded-lg border border-rule bg-white p-4 sm:p-6"
                >
                    @method('PUT')
                    @include('admin.posts._form', ['post' => $post, 'categories' => $categories ?? \App\Models\Post::CATEGORIES])
                </form>
            </section>
        </div>
    </div>
</x-app-layout>
