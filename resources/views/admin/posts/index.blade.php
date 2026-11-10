<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-wrap items-end justify-between gap-4">
            <div>
                <p class="text-xs font-semibold uppercase tracking-[0.2em] text-accent">Editorial desk</p>
                <h1 class="mt-2 font-serif text-3xl font-semibold text-ink">Your posts</h1>
                <p class="mt-2 text-sm text-stone-600">Manage drafts and published articles.</p>
            </div>
            <a
                href="{{ route('admin.posts.create') }}"
                class="inline-flex min-h-11 items-center justify-center rounded-full bg-accent px-5 py-3 text-sm font-semibold text-white shadow-sm shadow-accent/20 transition hover:bg-ink"
            >
                Create post
            </a>
        </div>
    </x-slot>

    <div class="mx-auto max-w-7xl space-y-8 px-4 py-8 sm:px-6 lg:px-8">
        @if (session('success'))
            <div role="status" class="rounded-xl border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-900">
                {{ session('success') }}
            </div>
        @endif

        @if ($errors->any())
            <div role="alert" class="rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-900">
                @foreach ($errors->all() as $error)
                    <p>{{ $error }}</p>
                @endforeach
            </div>
        @endif

        <section aria-label="Post overview" class="grid grid-cols-1 gap-3 sm:grid-cols-3 sm:gap-4">
            @foreach ([
                ['label' => 'All posts', 'count' => $postCounts['all'], 'tone' => 'bg-white'],
                ['label' => 'Drafts', 'count' => $postCounts['draft'], 'tone' => 'bg-[#edf1ff]'],
                ['label' => 'Published', 'count' => $postCounts['published'], 'tone' => 'bg-[#e9f6f1]'],
            ] as $stat)
                <article class="flex items-center justify-between gap-4 rounded-2xl border border-rule {{ $stat['tone'] }} p-4 shadow-sm sm:p-5">
                    <div>
                        <p class="text-sm font-medium text-stone-600">{{ $stat['label'] }}</p>
                        <p class="mt-1 text-xs text-stone-500">In your editorial workspace</p>
                    </div>
                    <span class="font-serif text-3xl font-semibold text-ink">{{ $stat['count'] }}</span>
                </article>
            @endforeach
        </section>

        <section aria-labelledby="fetch-heading" class="rounded-2xl border border-rule bg-white p-5 shadow-sm sm:p-7">
            <div class="max-w-2xl">
                <p class="text-xs font-semibold uppercase tracking-[0.2em] text-accent">Source to draft</p>
                <h2 id="fetch-heading" class="mt-2 font-serif text-2xl font-semibold text-ink">Fetch a source URL</h2>
                <p class="mt-2 text-sm leading-6 text-stone-600">
                    The queued job extracts article text and asks OpenRouter to prepare an editable draft.
                </p>
            </div>

            <form method="POST" action="{{ route('admin.posts.fetch') }}" class="mt-5 flex flex-col gap-3 sm:flex-row">
                @csrf
                <label for="source-url" class="sr-only">Article URL</label>
                <input
                    id="source-url"
                    type="url"
                    name="url"
                    value="{{ old('url') }}"
                    placeholder="Paste a public article URL"
                    required
                    maxlength="255"
                    class="min-w-0 flex-1 rounded-xl border-rule bg-paper/50 px-4 py-3 text-ink shadow-sm focus:border-accent focus:ring-accent"
                >
                <button
                    type="submit"
                    class="min-h-12 rounded-xl bg-accent px-5 py-3 text-sm font-semibold text-white shadow-sm shadow-accent/20 transition hover:bg-ink focus:outline-none focus:ring-2 focus:ring-accent focus:ring-offset-2"
                >
                    Fetch &amp; create draft
                </button>
            </form>

        </section>

        <section aria-labelledby="posts-heading" class="overflow-hidden rounded-2xl border border-rule bg-white shadow-sm">
            <div class="flex flex-wrap items-center justify-between gap-4 border-b border-rule px-5 py-4 sm:px-7">
                <div>
                    <h2 id="posts-heading" class="font-serif text-xl font-semibold text-ink">All posts</h2>
                    <p class="mt-1 text-sm text-stone-600">{{ $posts->total() }} matching {{ \Illuminate\Support\Str::plural('post', $posts->total()) }}</p>
                </div>
                <div class="flex flex-wrap items-center gap-3">
                    <a href="{{ route('blog.index') }}" class="text-sm font-semibold text-accent underline underline-offset-4">
                        View public blog
                    </a>
                    <a href="{{ route('feeds.index') }}" class="text-sm font-semibold text-stone-600 underline underline-offset-4 hover:text-ink">
                        Feeds
                    </a>
                </div>
            </div>

            <form method="GET" action="{{ route('admin.posts.index') }}" class="grid gap-3 border-b border-rule bg-paper/60 p-4 sm:grid-cols-[minmax(0,1fr)_12rem_auto_auto] sm:items-end sm:px-7">
                <div>
                    <label for="post-search" class="mb-1.5 block text-xs font-semibold uppercase tracking-wider text-stone-600">Search posts</label>
                    <input
                        id="post-search"
                        type="search"
                        name="search"
                        value="{{ $filters['search'] ?? '' }}"
                        placeholder="Search title or excerpt"
                        class="min-h-11 w-full rounded-xl border-rule bg-white px-4 py-2.5 text-sm text-ink shadow-sm focus:border-accent focus:ring-accent"
                    >
                </div>
                <div>
                    <label for="post-status" class="mb-1.5 block text-xs font-semibold uppercase tracking-wider text-stone-600">Status</label>
                    <select id="post-status" name="status" class="min-h-11 w-full rounded-xl border-rule bg-white px-4 py-2.5 text-sm text-ink shadow-sm focus:border-accent focus:ring-accent">
                        <option value="">All statuses</option>
                        <option value="draft" @selected(($filters['status'] ?? '') === 'draft')>Drafts</option>
                        <option value="published" @selected(($filters['status'] ?? '') === 'published')>Published</option>
                    </select>
                </div>
                <button type="submit" class="min-h-11 rounded-xl bg-ink px-5 py-2.5 text-sm font-semibold text-white transition hover:bg-accent">
                    Apply filters
                </button>
                @if (($filters['search'] ?? '') !== '' || ($filters['status'] ?? '') !== '')
                    <a href="{{ route('admin.posts.index') }}" class="inline-flex min-h-11 items-center justify-center rounded-xl border border-rule bg-white px-4 py-2.5 text-sm font-semibold text-stone-600 transition hover:text-ink">
                        Clear
                    </a>
                @endif
            </form>

            <div class="divide-y divide-rule px-4 sm:hidden">
                @forelse ($posts as $post)
                    <article class="py-5">
                        <div class="flex items-start justify-between gap-3">
                            <a href="{{ route('admin.posts.edit', $post) }}" class="min-w-0 break-words font-semibold leading-6 text-ink hover:text-accent">
                                {{ $post->title }}
                            </a>
                            <span @class([
                                'shrink-0 rounded-full px-3 py-1 text-xs font-semibold capitalize',
                                'bg-green-100 text-green-900' => $post->status === 'published',
                                'bg-amber-100 text-amber-900' => $post->status !== 'published',
                            ])>
                                {{ $post->status }}
                            </span>
                        </div>
                        <p class="mt-1 break-all text-xs text-stone-500">/{{ $post->slug }}</p>
                        <p class="mt-2 text-xs text-stone-500">
                            {{ $post->published_at?->format('d M Y, H:i') ?? 'Not published' }}
                        </p>
                        <div class="mt-4 grid w-full grid-cols-2 gap-2 sm:max-w-[11rem]">
                            @if ($post->status === 'published')
                                <form method="POST" action="{{ route('admin.posts.unpublish', $post) }}" class="min-w-0">
                                    @csrf
                                    <button type="submit" class="inline-flex min-h-10 w-full items-center justify-center rounded-lg border border-rule px-2 py-2 text-center text-xs font-semibold text-amber-800 transition hover:bg-paper focus:outline-none focus:ring-2 focus:ring-accent focus:ring-offset-2">
                                        Unpublish
                                    </button>
                                </form>
                                <a href="{{ route('blog.show', $post) }}" class="inline-flex min-h-10 min-w-0 items-center justify-center rounded-lg border border-rule px-2 py-2 text-center text-xs font-semibold text-accent transition hover:bg-paper focus:outline-none focus:ring-2 focus:ring-accent focus:ring-offset-2">
                                    View
                                </a>
                            @else
                                <form method="POST" action="{{ route('admin.posts.publish', $post) }}" class="min-w-0">
                                    @csrf
                                    <button type="submit" class="inline-flex min-h-10 w-full items-center justify-center rounded-lg border border-rule px-2 py-2 text-center text-xs font-semibold text-green-800 transition hover:bg-paper focus:outline-none focus:ring-2 focus:ring-accent focus:ring-offset-2">
                                        Publish
                                    </button>
                                </form>
                            @endif
                            <a href="{{ route('admin.posts.edit', $post) }}" class="inline-flex min-h-10 min-w-0 items-center justify-center rounded-lg border border-rule px-2 py-2 text-center text-xs font-semibold text-ink transition hover:bg-paper focus:outline-none focus:ring-2 focus:ring-accent focus:ring-offset-2">
                                Edit
                            </a>
                            <form method="POST" action="{{ route('admin.posts.destroy', $post) }}" class="min-w-0" onsubmit="return confirm('Delete this post?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="inline-flex min-h-10 w-full items-center justify-center rounded-lg border border-red-200 px-2 py-2 text-center text-xs font-semibold text-red-700 transition hover:bg-red-50 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2">
                                    Delete
                                </button>
                            </form>
                        </div>
                    </article>
                @empty
                    <div class="py-10 text-center">
                        <p class="font-serif text-lg font-semibold text-ink">No posts yet</p>
                        <p class="mt-2 text-sm text-stone-600">Create a post or queue a source URL to get started.</p>
                    </div>
                @endforelse
            </div>

            <div class="hidden overflow-x-auto sm:block">
                <table class="w-full min-w-[720px] text-left text-sm">
                    <thead class="bg-paper/70 text-xs uppercase tracking-wider text-stone-600">
                        <tr>
                            <th scope="col" class="px-5 py-3 font-semibold sm:px-7">Post</th>
                            <th scope="col" class="px-5 py-3 font-semibold">Status</th>
                            <th scope="col" class="px-5 py-3 font-semibold">Published</th>
                            <th scope="col" class="px-5 py-3 font-semibold">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-rule">
                        @forelse ($posts as $post)
                            <tr class="align-top">
                                <td class="max-w-md px-5 py-4 sm:px-7">
                                    <a href="{{ route('admin.posts.edit', $post) }}" class="font-semibold text-ink hover:text-accent">
                                        {{ $post->title }}
                                    </a>
                                    <p class="mt-1 text-xs text-stone-500">/{{ $post->slug }}</p>
                                </td>
                                <td class="px-5 py-4">
                                    <span @class([
                                        'inline-flex rounded-full px-3 py-1 text-xs font-semibold capitalize',
                                        'bg-green-100 text-green-900' => $post->status === 'published',
                                        'bg-amber-100 text-amber-900' => $post->status !== 'published',
                                    ])>
                                        {{ $post->status }}
                                    </span>
                                </td>
                                <td class="whitespace-nowrap px-5 py-4 text-stone-600">
                                    {{ $post->published_at?->format('d M Y, H:i') ?? 'Not published' }}
                                </td>
                                <td class="px-5 py-4">
                                    <div class="grid w-44 grid-cols-2 gap-2">
                                        @if ($post->status === 'published')
                                            <form method="POST" action="{{ route('admin.posts.unpublish', $post) }}" class="min-w-0">
                                                @csrf
                                                <button type="submit" class="inline-flex min-h-10 w-full items-center justify-center rounded-lg border border-rule px-2 py-2 text-center text-xs font-semibold text-amber-800 transition hover:bg-paper focus:outline-none focus:ring-2 focus:ring-accent focus:ring-offset-2">
                                                    Unpublish
                                                </button>
                                            </form>
                                            <a href="{{ route('blog.show', $post) }}" class="inline-flex min-h-10 min-w-0 items-center justify-center rounded-lg border border-rule px-2 py-2 text-center text-xs font-semibold text-accent transition hover:bg-paper focus:outline-none focus:ring-2 focus:ring-accent focus:ring-offset-2">
                                                View
                                            </a>
                                        @else
                                            <form method="POST" action="{{ route('admin.posts.publish', $post) }}" class="min-w-0">
                                                @csrf
                                                <button type="submit" class="inline-flex min-h-10 w-full items-center justify-center rounded-lg border border-rule px-2 py-2 text-center text-xs font-semibold text-green-800 transition hover:bg-paper focus:outline-none focus:ring-2 focus:ring-accent focus:ring-offset-2">
                                                    Publish
                                                </button>
                                            </form>
                                        @endif
                                        <a href="{{ route('admin.posts.edit', $post) }}" class="inline-flex min-h-10 min-w-0 items-center justify-center rounded-lg border border-rule px-2 py-2 text-center text-xs font-semibold text-ink transition hover:bg-paper focus:outline-none focus:ring-2 focus:ring-accent focus:ring-offset-2">
                                            Edit
                                        </a>
                                        <form method="POST" action="{{ route('admin.posts.destroy', $post) }}" class="min-w-0" onsubmit="return confirm('Delete this post?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="inline-flex min-h-10 w-full items-center justify-center rounded-lg border border-red-200 px-2 py-2 text-center text-xs font-semibold text-red-700 transition hover:bg-red-50 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2">
                                                Delete
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-5 py-12 text-center sm:px-7">
                                    <p class="font-serif text-lg font-semibold text-ink">No posts yet</p>
                                    <p class="mt-2 text-sm text-stone-600">Create a post or queue a source URL to get started.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($posts->hasPages())
                <div class="border-t border-rule px-5 py-4 sm:px-7">
                    {{ $posts->links() }}
                </div>
            @endif
        </section>

        <section aria-labelledby="fetch-workflow-heading" class="rounded-2xl border border-rule bg-white p-5 shadow-sm sm:p-7">
            <div class="max-w-2xl">
                <p class="text-xs font-semibold uppercase tracking-[0.2em] text-accent">A simple process</p>
                <h2 id="fetch-workflow-heading" class="mt-2 font-serif text-xl font-semibold text-ink">From source link to published post</h2>
                <p class="mt-2 text-sm leading-6 text-stone-600">Fetched articles are prepared as drafts so you can review and edit them before publishing.</p>
            </div>

            <ol class="mt-5 grid gap-3 md:grid-cols-3">
                <li class="flex items-start gap-3 rounded-xl border border-rule bg-paper/50 p-4">
                    <span class="flex size-8 shrink-0 items-center justify-center rounded-full bg-accent text-sm font-semibold text-white">1</span>
                    <div>
                        <h3 class="text-sm font-semibold text-ink">Add a source link</h3>
                        <p class="mt-1 text-sm leading-5 text-stone-600">Paste an article URL in the form above.</p>
                    </div>
                </li>
                <li class="flex items-start gap-3 rounded-xl border border-rule bg-paper/50 p-4">
                    <span class="flex size-8 shrink-0 items-center justify-center rounded-full bg-accent text-sm font-semibold text-white">2</span>
                    <div>
                        <h3 class="text-sm font-semibold text-ink">Review your draft</h3>
                        <p class="mt-1 text-sm leading-5 text-stone-600">Open the new draft from the posts list and make your edits.</p>
                    </div>
                </li>
                <li class="flex items-start gap-3 rounded-xl border border-rule bg-paper/50 p-4">
                    <span class="flex size-8 shrink-0 items-center justify-center rounded-full bg-accent text-sm font-semibold text-white">3</span>
                    <div>
                        <h3 class="text-sm font-semibold text-ink">Publish when ready</h3>
                        <p class="mt-1 text-sm leading-5 text-stone-600">Publish your reviewed post to make it visible on the blog.</p>
                    </div>
                </li>
            </ol>
        </section>
    </div>
</x-app-layout>
