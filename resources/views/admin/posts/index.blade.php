<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-wrap items-center justify-between gap-4">
            <div>
                <p class="text-xs font-semibold uppercase tracking-wider text-accent">Control Room</p>
                <h1 class="mt-1 font-serif text-2xl font-bold text-ink sm:text-3xl">Editorial Desk</h1>
                <p class="mt-1 text-sm text-gray-600">Fetch source articles, monitor AI draft generation, and publish to your journal and RSS feed.</p>
            </div>
            <div class="flex items-center gap-3">
                <a
                    href="{{ route('blog.index') }}"
                    class="inline-flex items-center rounded-md border border-rule bg-paper px-3.5 py-2 text-sm font-medium text-ink transition hover:border-gray-400"
                >
                    View blog
                </a>
                <a
                    href="{{ route('admin.posts.create') }}"
                    class="inline-flex items-center rounded-md bg-accent px-4 py-2 text-sm font-semibold text-white transition hover:bg-ink"
                >
                    Create post
                </a>
            </div>
        </div>
    </x-slot>

    <div class="mx-auto max-w-7xl space-y-6 px-4 py-8 sm:px-6 lg:px-8">
        @if (session('success'))
            <div role="status" class="rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-medium text-emerald-900">
                {{ session('success') }}
            </div>
        @endif

        @if ($errors->any())
            <div role="alert" class="rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-900">
                @foreach ($errors->all() as $error)
                    <p>{{ $error }}</p>
                @endforeach
            </div>
        @endif

        {{-- Stats Row --}}
        <section aria-label="Post overview" class="grid grid-cols-1 gap-4 sm:grid-cols-3">
            @foreach ([
                ['label' => 'All posts', 'count' => $postCounts['all'], 'sub' => 'Total articles in workspace'],
                ['label' => 'Drafts', 'count' => $postCounts['draft'], 'sub' => 'Private until reviewed & published'],
                ['label' => 'Published', 'count' => $postCounts['published'], 'sub' => 'Live on journal & /feed RSS'],
            ] as $stat)
                <article class="flex items-center justify-between rounded-lg border border-rule bg-white p-5">
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-wider text-gray-500">{{ $stat['label'] }}</p>
                        <p class="mt-1 text-xs text-gray-500">{{ $stat['sub'] }}</p>
                    </div>
                    <span class="font-serif text-3xl font-bold text-ink">{{ $stat['count'] }}</span>
                </article>
            @endforeach
        </section>

        {{-- Top Action Box: URL Importer --}}
        <section aria-labelledby="fetch-heading" class="rounded-lg border border-rule bg-white p-4 sm:p-6">
            <div class="flex flex-wrap items-baseline justify-between gap-2">
                <div>
                    <h2 id="fetch-heading" class="font-serif text-xl font-bold text-ink">Fetch a source URL</h2>
                    <p class="mt-1 text-sm text-gray-600">Paste an article URL, pick an OpenRouter model and tone, and queue a clean AI draft.</p>
                </div>
            </div>

            <form method="POST" action="{{ route('admin.posts.fetch') }}" class="mt-5 grid gap-3 sm:grid-cols-2 lg:grid-cols-[minmax(0,1fr)_9.5rem_12rem_9.5rem_auto]">
                @csrf
                <div class="sm:col-span-2 lg:col-span-1">
                    <label for="source-url" class="sr-only">Source article URL</label>
                    <input
                        id="source-url"
                        type="url"
                        name="url"
                        value="{{ old('url') }}"
                        placeholder="Paste source article URL here..."
                        required
                        maxlength="255"
                        class="h-11 w-full rounded-md border-rule bg-paper px-3.5 text-sm text-ink focus:border-accent focus:ring-accent"
                    >
                </div>
                <div>
                    <label for="fetch-category" class="sr-only">Category</label>
                    <select
                        id="fetch-category"
                        name="category"
                        class="h-11 w-full rounded-md border-rule bg-paper px-3 text-sm text-ink focus:border-accent focus:ring-accent"
                    >
                        @foreach ($categories as $categoryOption)
                            <option value="{{ $categoryOption }}" @selected(old('category', 'Tech') === $categoryOption)>
                                Category: {{ $categoryOption }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label for="ai-model" class="sr-only">OpenRouter Model</label>
                    <select
                        id="ai-model"
                        name="ai_model"
                        class="h-11 w-full rounded-md border-rule bg-paper px-3 text-sm text-ink focus:border-accent focus:ring-accent"
                    >
                        @foreach ($models as $modelId => $modelLabel)
                            <option value="{{ $modelId }}" @selected(old('ai_model', config('services.openrouter.model')) === $modelId)>
                                {{ $modelLabel }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label for="ai-tone" class="sr-only">Tone</label>
                    <select
                        id="ai-tone"
                        name="tone"
                        class="h-11 w-full rounded-md border-rule bg-paper px-3 text-sm text-ink focus:border-accent focus:ring-accent"
                    >
                        @foreach ($tones as $toneOption)
                            <option value="{{ $toneOption }}" @selected(old('tone', 'Informative') === $toneOption)>
                                Tone: {{ $toneOption }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <button
                    type="submit"
                    class="h-11 rounded-md bg-accent px-5 text-sm font-semibold text-white transition hover:bg-ink focus:outline-none focus:ring-2 focus:ring-accent focus:ring-offset-2"
                >
                    Generate Draft
                </button>
            </form>
        </section>

        {{-- Queue & Drafts Status Table --}}
        @php
            $hasActiveFetchJobs = $fetchLogs->contains(fn ($log) => in_array($log->status, ['queued', 'processing'], true));
        @endphp
        @if ($fetchLogs->isNotEmpty())
            <section
                id="queue-section"
                data-has-active-jobs="{{ $hasActiveFetchJobs ? '1' : '0' }}"
                aria-labelledby="queue-heading"
                class="overflow-hidden rounded-lg border border-rule bg-white"
            >
                <div class="flex items-center justify-between border-b border-rule px-6 py-4">
                    <div>
                        <h2 id="queue-heading" class="font-serif text-lg font-bold text-ink">Queue &amp; AI Generation Activity</h2>
                        <p class="mt-0.5 text-xs text-gray-500">Live status of queued URL extractions and OpenRouter draft jobs.</p>
                    </div>
                    @if ($hasActiveFetchJobs)
                        <span class="inline-flex items-center gap-1.5 rounded bg-amber-50 px-2.5 py-1 text-xs font-medium text-amber-800">
                            <span class="size-1.5 animate-pulse rounded-full bg-amber-600"></span>
                            Auto-updating
                        </span>
                    @endif
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm">
                        <thead class="border-b border-rule bg-paper text-xs uppercase tracking-wider text-gray-500">
                            <tr>
                                <th class="px-6 py-3 font-semibold">Article Title / Source URL</th>
                                <th class="px-6 py-3 font-semibold">AI Status</th>
                                <th class="px-6 py-3 font-semibold">Created At</th>
                                <th class="px-6 py-3 text-right font-semibold">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-rule">
                            @foreach ($fetchLogs as $log)
                                <tr class="hover:bg-paper/60">
                                    <td class="max-w-md px-6 py-3.5">
                                        @if ($log->post)
                                            <a href="{{ route('admin.posts.edit', $log->post) }}" class="font-semibold text-ink hover:text-accent">
                                                {{ $log->post->title }}
                                            </a>
                                        @else
                                            <span class="font-medium text-ink">Queued Source Article</span>
                                        @endif
                                        <p class="mt-0.5 truncate text-xs text-gray-500">{{ $log->source_url }}</p>
                                    </td>
                                    <td class="whitespace-nowrap px-6 py-3.5">
                                        @if ($log->status === 'queued')
                                            <span class="inline-flex items-center gap-1.5 rounded bg-gray-100 px-2.5 py-1 text-xs font-semibold text-gray-700">
                                                Queued
                                            </span>
                                        @elseif ($log->status === 'processing')
                                            <span class="inline-flex items-center gap-1.5 rounded bg-amber-100 px-2.5 py-1 text-xs font-semibold text-amber-900">
                                                <svg class="size-3.5 animate-spin" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="3"></circle>
                                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v3a5 5 0 00-5 5H4z"></path>
                                                </svg>
                                                Extracting &amp; Writing...
                                            </span>
                                        @elseif ($log->status === 'success')
                                            <span class="inline-flex items-center gap-1.5 rounded bg-emerald-100 px-2.5 py-1 text-xs font-semibold text-emerald-900">
                                                Draft Ready
                                            </span>
                                        @else
                                            <span class="inline-flex items-center gap-1.5 rounded bg-red-100 px-2.5 py-1 text-xs font-semibold text-red-900" title="{{ $log->userFacingError() }}">
                                                Failed
                                            </span>
                                        @endif
                                    </td>
                                    <td class="whitespace-nowrap px-6 py-3.5 text-xs text-gray-500">
                                        {{ $log->created_at?->diffForHumans() }}
                                    </td>
                                    <td class="whitespace-nowrap px-6 py-3.5 text-right">
                                        @if ($log->status === 'failed')
                                            <form method="POST" action="{{ route('admin.fetch-logs.retry', $log) }}" class="inline">
                                                @csrf
                                                <button type="submit" class="inline-flex items-center gap-1 rounded-md border border-red-200 bg-white px-2.5 py-1 text-xs font-semibold text-red-700 transition hover:bg-red-50">
                                                    <svg class="size-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h5M20 20v-5h-5" />
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M5.64 17.36A9 9 0 0 0 19.54 14M18.36 6.64A9 9 0 0 0 4.46 10" />
                                                    </svg>
                                                    Retry
                                                </button>
                                            </form>
                                        @elseif ($log->post)
                                            <a href="{{ route('admin.posts.edit', $log->post) }}" class="inline-flex items-center rounded-md border border-rule bg-white px-2.5 py-1 text-xs font-semibold text-ink transition hover:border-gray-400">
                                                Open Editor
                                            </a>
                                        @else
                                            <span class="text-xs text-gray-400">Waiting...</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </section>
        @endif

        {{-- All Posts Table --}}
        <section aria-labelledby="posts-heading" class="overflow-hidden rounded-lg border border-rule bg-white">
            <div class="flex flex-wrap items-center justify-between gap-4 border-b border-rule px-6 py-4">
                <div>
                    <h2 id="posts-heading" class="font-serif text-xl font-bold text-ink">All posts</h2>
                    <p id="posts-count" class="mt-0.5 text-xs text-gray-500">{{ $posts->total() }} matching {{ \Illuminate\Support\Str::plural('post', $posts->total()) }}</p>
                </div>
                <div class="flex items-center gap-4 text-xs font-semibold">
                    <a href="{{ route('feeds.index') }}" class="text-gray-600 hover:text-ink">Feeds</a>
                    <a href="{{ route('sitemap') }}" class="text-gray-600 hover:text-ink">Sitemap</a>
                </div>
            </div>

            <form method="GET" action="{{ route('admin.posts.index') }}" class="grid gap-3 border-b border-rule bg-paper px-6 py-4 sm:grid-cols-[minmax(0,1fr)_11rem_auto_auto] sm:items-end">
                <div>
                    <label for="post-search" class="mb-1 block text-xs font-semibold uppercase tracking-wider text-gray-500">Search posts</label>
                    <input
                        id="post-search"
                        type="search"
                        name="search"
                        value="{{ $filters['search'] ?? '' }}"
                        placeholder="Search title or excerpt..."
                        class="h-10 w-full rounded-md border-rule bg-white px-3 text-sm text-ink focus:border-accent focus:ring-accent"
                    >
                </div>
                <div>
                    <label for="post-status" class="mb-1 block text-xs font-semibold uppercase tracking-wider text-gray-500">Status</label>
                    <select id="post-status" name="status" class="h-10 w-full rounded-md border-rule bg-white px-3 text-sm text-ink focus:border-accent focus:ring-accent">
                        <option value="">All statuses</option>
                        <option value="draft" @selected(($filters['status'] ?? '') === 'draft')>Drafts</option>
                        <option value="published" @selected(($filters['status'] ?? '') === 'published')>Published</option>
                    </select>
                </div>
                <button type="submit" class="h-10 rounded-md bg-ink px-4 text-sm font-semibold text-white transition hover:bg-accent">
                    Apply
                </button>
                @if (($filters['search'] ?? '') !== '' || ($filters['status'] ?? '') !== '')
                    <a href="{{ route('admin.posts.index') }}" class="inline-flex h-10 items-center justify-center rounded-md border border-rule bg-white px-4 text-sm font-medium text-gray-600 hover:text-ink">
                        Clear
                    </a>
                @endif
            </form>

            <div id="posts-live-region">
                <div class="overflow-x-auto">
                <table class="w-full min-w-[720px] text-left text-sm">
                    <thead class="border-b border-rule bg-paper text-xs uppercase tracking-wider text-gray-500">
                        <tr>
                            <th scope="col" class="px-6 py-3 font-semibold">Article Title / Slug</th>
                            <th scope="col" class="px-6 py-3 font-semibold">Category</th>
                            <th scope="col" class="px-6 py-3 font-semibold">Status</th>
                            <th scope="col" class="px-6 py-3 font-semibold">Published At</th>
                            <th scope="col" class="px-6 py-3 text-right font-semibold">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-rule">
                        @forelse ($posts as $post)
                            <tr class="align-middle hover:bg-paper/50">
                                <td class="max-w-md px-6 py-4">
                                    <a href="{{ route('admin.posts.edit', $post) }}" class="font-semibold text-ink hover:text-accent">
                                        {{ $post->title }}
                                    </a>
                                    <p class="mt-0.5 truncate text-xs text-gray-500">/blog/{{ $post->slug }}</p>
                                </td>
                                <td class="whitespace-nowrap px-6 py-4">
                                    <form method="POST" action="{{ route('admin.posts.category', $post) }}">
                                        @csrf
                                        @method('PATCH')
                                        <label for="post-category-{{ $post->id }}" class="sr-only">Category for {{ $post->title }}</label>
                                        <select
                                            id="post-category-{{ $post->id }}"
                                            name="category"
                                            onchange="this.form.submit()"
                                            class="h-8 rounded-md border-rule bg-paper px-2.5 pr-7 text-xs font-medium text-ink focus:border-accent focus:ring-accent"
                                        >
                                            @foreach ($categories as $categoryOption)
                                                <option value="{{ $categoryOption }}" @selected(($post->category ?: 'Tech') === $categoryOption)>
                                                    {{ $categoryOption }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </form>
                                </td>
                                <td class="whitespace-nowrap px-6 py-4">
                                    @if ($post->status === 'published')
                                        <span class="inline-flex rounded bg-emerald-100 px-2.5 py-1 text-xs font-semibold text-emerald-900">
                                            Published
                                        </span>
                                    @else
                                        <span class="inline-flex rounded bg-amber-100 px-2.5 py-1 text-xs font-semibold text-amber-900">
                                            Draft Ready
                                        </span>
                                    @endif
                                </td>
                                <td class="whitespace-nowrap px-6 py-4 text-xs text-gray-500">
                                    {{ $post->published_at?->format('M j, Y H:i') ?? 'Not published' }}
                                </td>
                                <td class="whitespace-nowrap px-6 py-4 text-right">
                                    <div class="inline-flex items-center gap-2">
                                        @if ($post->status === 'published')
                                            <form method="POST" action="{{ route('admin.posts.unpublish', $post) }}" class="inline">
                                                @csrf
                                                <button type="submit" class="rounded-md border border-rule bg-white px-2.5 py-1.5 text-xs font-semibold text-amber-800 transition hover:bg-paper">
                                                    Unpublish
                                                </button>
                                            </form>
                                            <a href="{{ route('blog.show', $post) }}" class="rounded-md border border-rule bg-white px-2.5 py-1.5 text-xs font-semibold text-accent transition hover:bg-paper">
                                                View
                                            </a>
                                        @else
                                            <form method="POST" action="{{ route('admin.posts.publish', $post) }}" class="inline">
                                                @csrf
                                                <button type="submit" class="rounded-md bg-accent px-2.5 py-1.5 text-xs font-semibold text-white transition hover:bg-ink">
                                                    Publish
                                                </button>
                                            </form>
                                        @endif
                                        <a href="{{ route('admin.posts.edit', $post) }}" class="rounded-md border border-rule bg-white px-2.5 py-1.5 text-xs font-semibold text-ink transition hover:bg-paper">
                                            Edit
                                        </a>
                                        <form method="POST" action="{{ route('admin.posts.destroy', $post) }}" class="inline" onsubmit="return confirm('Delete this post?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="rounded-md border border-red-200 bg-white px-2.5 py-1.5 text-xs font-semibold text-red-700 transition hover:bg-red-50">
                                                Delete
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-12 text-center text-sm text-gray-500">
                                    No posts yet. Paste a source URL above or click <strong>Create post</strong> to get started.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($posts->hasPages())
                <div class="border-t border-rule px-6 py-4">
                    {{ $posts->links() }}
                </div>
            @endif
            </div>
        </section>
    </div>

    @if ($hasActiveFetchJobs)
        <script>
            (() => {
                const pollIntervalMs = 2500;
                let timerId = null;
                let inFlight = false;

                async function refreshQueueState() {
                    const queueSection = document.getElementById('queue-section');
                    if (!queueSection || queueSection.dataset.hasActiveJobs !== '1' || inFlight) {
                        return;
                    }

                    inFlight = true;
                    try {
                        const response = await fetch(window.location.href, {
                            headers: { 'X-Requested-With': 'XMLHttpRequest' },
                            credentials: 'same-origin',
                        });
                        if (!response.ok) {
                            return;
                        }

                        const html = await response.text();
                        const doc = new DOMParser().parseFromString(html, 'text/html');

                        const nextQueue = doc.getElementById('queue-section');
                        if (nextQueue) {
                            queueSection.replaceWith(nextQueue);
                        }

                        const currentCount = document.getElementById('posts-count');
                        const nextCount = doc.getElementById('posts-count');
                        if (currentCount && nextCount) {
                            currentCount.replaceWith(nextCount);
                        }

                        const currentPosts = document.getElementById('posts-live-region');
                        const nextPosts = doc.getElementById('posts-live-region');
                        if (currentPosts && nextPosts) {
                            currentPosts.replaceWith(nextPosts);
                        }

                        if (!nextQueue || nextQueue.dataset.hasActiveJobs !== '1') {
                            clearInterval(timerId);
                        }
                    } catch (_error) {
                        // Ignore transient network errors and retry on next tick.
                    } finally {
                        inFlight = false;
                    }
                }

                timerId = setInterval(refreshQueueState, pollIntervalMs);
            })();
        </script>
    @endif
</x-app-layout>
