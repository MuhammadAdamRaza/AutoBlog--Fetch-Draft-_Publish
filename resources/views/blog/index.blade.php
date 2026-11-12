@extends('layouts.blog')

@section('title', 'Journal | '.config('app.name'))
@section('description', 'Curated essays, technology coverage, and practical guides published on '.config('app.name').'.')

@php
    $siteStructuredData = [
        '@context' => 'https://schema.org',
        '@type' => 'WebSite',
        'name' => config('app.name'),
        'url' => route('blog.index'),
        'description' => 'An independent editorial journal featuring curated stories across technology, AI, business, and guides.',
        'potentialAction' => [
            '@type' => 'SearchAction',
            'target' => route('blog.index').'?q={search_term_string}',
            'query-input' => 'required name=search_term_string',
        ],
    ];

    $gridPosts = $featuredPost ? $posts->getCollection()->skip(1) : $posts->getCollection();
    $previewData = $posts->getCollection()->map(fn ($p) => [
        'slug' => $p->slug,
        'title' => $p->title,
        'category' => $p->category ?: 'Tech',
        'minutes' => $p->readingMinutes(),
        'date' => $p->published_at?->format('M j, Y') ?? '',
        'excerpt' => $p->excerpt ?: \Illuminate\Support\Str::limit($p->body, 180),
        'snippet' => \Illuminate\Support\Str::limit($p->body, 420),
        'url' => route('blog.show', $p),
    ])->values();
@endphp

@push('head')
    <script type="application/ld+json">
        @json($siteStructuredData)
    </script>
@endpush

@section('content')
    <div x-data="{
        filter: @js($activeCategory !== '' ? $activeCategory : 'all'),
        bookmarks: JSON.parse(localStorage.getItem('autoblog_bookmarks') || '[]'),
        showSavedOnly: false,
        previewPost: null,
        toggleBookmark(post) {
            const exists = this.bookmarks.some(item => item.slug === post.slug);
            this.bookmarks = exists
                ? this.bookmarks.filter(item => item.slug !== post.slug)
                : [post, ...this.bookmarks];
            localStorage.setItem('autoblog_bookmarks', JSON.stringify(this.bookmarks));
        },
        isBookmarked(slug) {
            return this.bookmarks.some(item => item.slug === slug);
        },
        clearBookmarks() {
            this.bookmarks = [];
            this.showSavedOnly = false;
            localStorage.removeItem('autoblog_bookmarks');
        }
    }" @keydown.escape.window="previewPost = null">
        {{-- Section B: Hero Featured Section --}}
        @if ($featuredPost)
            <section aria-label="Featured story" class="mb-8 rounded-lg border border-rule bg-white p-6 sm:p-8">
                @if ($featuredPost->coverUrl())
                    <div class="grid gap-6 lg:grid-cols-12 lg:items-center lg:gap-8">
                        <a href="{{ route('blog.show', $featuredPost) }}" class="group block overflow-hidden rounded-lg border border-rule bg-paper lg:col-span-7">
                            <div class="aspect-video w-full overflow-hidden">
                                <img
                                    src="{{ $featuredPost->coverUrl() }}"
                                    alt="{{ $featuredPost->title }}"
                                    class="h-full w-full object-cover transition duration-300 group-hover:scale-105"
                                >
                            </div>
                        </a>

                        <div class="lg:col-span-5">
                @else
                    <div class="max-w-3xl">
                @endif
                        <div class="flex items-center gap-3">
                            <span class="inline-block rounded bg-accent/10 px-2.5 py-1 text-xs font-semibold uppercase tracking-wider text-accent">
                                {{ $featuredPost->category ?: 'Technology' }}
                            </span>
                            <span class="text-xs font-medium text-gray-500">
                                {{ $publishedCount }} published {{ \Illuminate\Support\Str::plural('article', $publishedCount) }} to explore
                            </span>
                        </div>

                        <h1 class="mt-4 font-serif text-3xl font-bold leading-tight tracking-tight text-ink sm:text-[36px]">
                            <a href="{{ route('blog.show', $featuredPost) }}" class="transition-colors hover:text-accent">
                                {{ $featuredPost->title }}
                            </a>
                        </h1>

                        <p class="mt-3 line-clamp-2 text-base leading-relaxed text-gray-600">
                            {{ $featuredPost->excerpt ?: \Illuminate\Support\Str::limit($featuredPost->body, 180) }}
                        </p>

                        <div class="mt-5 flex flex-wrap items-center justify-between gap-2 border-t border-rule pt-4 text-xs font-medium text-gray-500">
                            <div class="flex flex-wrap items-center gap-2">
                                <span class="font-semibold text-ink">AutoBlog Editorial</span>
                                <span aria-hidden="true">&bull;</span>
                                <time datetime="{{ $featuredPost->published_at->toAtomString() }}">{{ $featuredPost->published_at->format('M j, Y') }}</time>
                                <span aria-hidden="true">&bull;</span>
                                <span>{{ $featuredPost->readingMinutes() }} min read</span>
                            </div>
                            <a href="#latest-articles" class="font-semibold text-accent hover:text-ink">All stories &darr;</a>
                        </div>
                @if ($featuredPost->coverUrl())
                        </div>
                    </div>
                @else
                    </div>
                @endif
            </section>
        @else
            <section class="mb-8 rounded-lg border border-rule bg-white p-6">
                <div class="flex flex-wrap items-center justify-between gap-4">
                    <div>
                        <span class="inline-block rounded bg-accent/10 px-2.5 py-1 text-xs font-semibold uppercase tracking-wider text-accent">
                            Editorial Journal
                        </span>
                        <h1 class="mt-3 font-serif text-3xl font-bold tracking-tight text-ink">
                            {{ $search !== '' ? 'Search results' : ($activeCategory !== '' ? $activeCategory.' Articles' : 'AutoBlog Journal') }}
                        </h1>
                    </div>
                    <div class="flex items-center gap-4 text-sm text-gray-600">
                        <p>
                            <span class="font-semibold text-ink">{{ $publishedCount }}</span> published {{ \Illuminate\Support\Str::plural('article', $publishedCount) }} to explore
                        </p>
                        <a href="#latest-articles" class="font-semibold text-accent hover:text-ink">Latest &darr;</a>
                    </div>
                </div>

                @if ($search !== '')
                    <p class="mt-3 text-sm text-gray-600">
                        {{ $posts->total() }} {{ \Illuminate\Support\Str::plural('result', $posts->total()) }} for &ldquo;{{ $search }}&rdquo;
                        <span aria-hidden="true">&middot;</span>
                        <a href="{{ route('blog.index') }}" class="font-semibold text-accent underline underline-offset-4 hover:text-ink">Clear search</a>
                    </p>
                @endif
            </section>
        @endif

        {{-- Section C: Topic Filter Strip (Height: 48px) --}}
        <section aria-label="Topic filters" class="mb-8 flex min-h-12 flex-col gap-3 rounded-lg border-y border-rule bg-white px-3 py-2.5 sm:px-4 md:flex-row md:flex-wrap md:items-center md:justify-between">
            <div class="flex items-center gap-2 overflow-x-auto pb-1 md:flex-wrap md:overflow-visible md:pb-0">
                @foreach ([
                    ['key' => 'all', 'label' => 'All Posts'],
                    ['key' => 'latest', 'label' => 'Latest'],
                    ['key' => 'Tech', 'label' => 'Tech'],
                    ['key' => 'AI', 'label' => 'AI'],
                    ['key' => 'Business', 'label' => 'Business'],
                    ['key' => 'Guides', 'label' => 'Guides'],
                    ['key' => 'Tutorials', 'label' => 'Tutorials'],
                    ['key' => 'News', 'label' => 'News'],
                ] as $tab)
                    <button
                        type="button"
                        @click="showSavedOnly = false; filter = @js($tab['key'])"
                        :class="!showSavedOnly && filter === @js($tab['key']) ? 'bg-accent text-white border-accent' : 'bg-paper text-gray-700 border-rule hover:border-gray-400 hover:text-ink'"
                        class="rounded border px-3 py-1.5 text-xs font-semibold transition"
                    >
                        {{ $tab['label'] }}
                    </button>
                @endforeach
                <button
                    type="button"
                    @click="showSavedOnly = !showSavedOnly"
                    :class="showSavedOnly ? 'bg-ink text-white border-ink' : 'bg-paper text-gray-700 border-rule hover:border-gray-400 hover:text-ink'"
                    class="rounded border px-3 py-1.5 text-xs font-semibold transition"
                >
                    Saved (<span x-text="bookmarks.length">0</span>)
                </button>
            </div>

            <form method="GET" action="{{ route('blog.index') }}" class="flex w-full items-center gap-2 md:w-auto">
                <label for="article-search" class="sr-only">Search published articles</label>
                <input
                    id="article-search"
                    type="search"
                    name="q"
                    value="{{ $search }}"
                    placeholder="Filter by keyword..."
                    class="h-9 min-w-0 flex-1 rounded-md border-rule bg-paper px-3 text-xs text-ink focus:border-accent focus:ring-accent md:h-8 md:w-52 md:flex-none"
                >
                <button type="submit" class="h-9 shrink-0 rounded-md bg-ink px-3.5 text-xs font-semibold text-white transition hover:bg-accent md:h-8 md:px-3">
                    Search
                </button>
            </form>
        </section>

        {{-- Section D: Main Article Feed Grid (3-Column Grid) --}}
        <section id="latest-articles" aria-labelledby="latest-heading" class="scroll-mt-20">
            <div class="mb-6 flex items-baseline justify-between">
                <h2 id="latest-heading" class="font-serif text-2xl font-bold text-ink">
                    {{ $search !== '' ? 'Matching stories' : 'Latest stories' }}
                </h2>
                <span class="text-xs font-medium uppercase tracking-wider text-gray-500">
                    {{ $posts->total() }} {{ \Illuminate\Support\Str::plural('story', $posts->total()) }}
                </span>
            </div>

            {{-- Interactive Section 1: Saved Reading List Bar --}}
            <div
                x-cloak
                x-show="showSavedOnly"
                x-transition
                class="mb-6 rounded-lg border border-rule bg-white p-5"
            >
                <div class="flex flex-wrap items-center justify-between gap-3 border-b border-rule pb-3">
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-wider text-accent">Reading List</p>
                        <h3 class="font-serif text-lg font-bold text-ink">Saved articles (<span x-text="bookmarks.length"></span>)</h3>
                    </div>
                    <div class="flex items-center gap-2">
                        <button type="button" x-show="bookmarks.length > 0" @click="clearBookmarks()" class="rounded-md border border-rule bg-paper px-3 py-1.5 text-xs font-semibold text-gray-700 hover:text-ink">
                            Clear all
                        </button>
                        <button type="button" @click="showSavedOnly = false" class="rounded-md bg-ink px-3 py-1.5 text-xs font-semibold text-white hover:bg-accent">
                            Back to feed
                        </button>
                    </div>
                </div>
                <template x-if="bookmarks.length === 0">
                    <p class="pt-4 text-sm text-gray-600">No saved articles yet. Click the bookmark icon on any story card below to save it for later.</p>
                </template>
                <div class="mt-4 grid gap-3 sm:grid-cols-2 lg:grid-cols-3" x-show="bookmarks.length > 0">
                    <template x-for="item in bookmarks" :key="item.slug">
                        <div class="flex items-start justify-between gap-3 rounded-md border border-rule bg-paper p-3.5">
                            <a :href="item.url" class="min-w-0 flex-1">
                                <span class="text-[11px] font-semibold uppercase tracking-wider text-accent" x-text="item.category"></span>
                                <p class="mt-1 line-clamp-2 font-serif text-sm font-bold text-ink hover:text-accent" x-text="item.title"></p>
                            </a>
                            <button type="button" @click="toggleBookmark(item)" aria-label="Remove saved article" class="rounded p-1 text-xs text-gray-400 hover:bg-white hover:text-red-700">&times;</button>
                        </div>
                    </template>
                </div>
            </div>

            @if ($posts->isEmpty())
                <div class="rounded-lg border border-rule bg-white px-6 py-12 text-center">
                    <h3 class="font-serif text-xl font-bold text-ink">No stories found</h3>
                    <p class="mx-auto mt-2 max-w-md text-sm text-gray-600">
                        @if ($search !== '')
                            Try another keyword or clear your search filter.
                        @else
                            No published articles are available yet. Check the RSS feed or publish a draft from Admin.
                        @endif
                    </p>
                    <a href="{{ route('blog.index') }}" class="mt-4 inline-flex rounded-md bg-accent px-4 py-2 text-sm font-semibold text-white transition hover:bg-ink">
                        View all posts
                    </a>
                </div>
            @else
                <div class="grid grid-cols-1 gap-8 md:grid-cols-2 lg:grid-cols-3">
                    @foreach ($gridPosts as $index => $post)
                        <a
                            href="{{ route('blog.show', $post) }}"
                            x-show="(showSavedOnly && isBookmarked(@js($post->slug))) || (!showSavedOnly && (filter === 'all' || (filter === 'latest' && {{ $index < 6 ? 'true' : 'false' }}) || filter === @js($post->category ?: 'Tech')))"
                            x-transition
                            class="group flex flex-col overflow-hidden rounded-lg border border-rule bg-white transition hover:border-gray-400"
                        >
                            @if ($post->coverUrl())
                                <div class="aspect-video w-full overflow-hidden border-b border-rule bg-paper">
                                    <img
                                        src="{{ $post->coverUrl() }}"
                                        alt="{{ $post->title }}"
                                        class="h-full w-full object-cover transition duration-300 group-hover:scale-105"
                                        loading="lazy"
                                    >
                                </div>
                            @endif
                            <div class="flex flex-1 flex-col p-5">
                                <div class="flex items-center justify-between gap-2">
                                    <p class="text-[12px] font-semibold uppercase tracking-wider text-accent">
                                        {{ $post->category ?: 'Tech' }} &bull; {{ $post->readingMinutes() }} min read
                                    </p>
                                    <div class="flex items-center gap-1.5">
                                        <button
                                            type="button"
                                            @click.prevent.stop="previewPost = {
                                                slug: @js($post->slug),
                                                title: @js($post->title),
                                                category: @js($post->category ?: 'Tech'),
                                                minutes: {{ $post->readingMinutes() }},
                                                date: @js($post->published_at->format('M j, Y')),
                                                excerpt: @js($post->excerpt ?: \Illuminate\Support\Str::limit($post->body, 180)),
                                                snippet: @js(\Illuminate\Support\Str::limit($post->body, 420)),
                                                url: @js(route('blog.show', $post))
                                            }"
                                            class="rounded border border-rule bg-paper px-2 py-0.5 text-[11px] font-semibold text-gray-600 transition hover:border-gray-400 hover:text-ink"
                                        >
                                            Preview
                                        </button>
                                    </div>
                                </div>
                                <h3 class="mt-2 line-clamp-2 font-serif text-[20px] font-bold leading-snug text-ink transition-colors group-hover:text-accent">
                                    {{ $post->title }}
                                </h3>
                                <p class="mt-2 line-clamp-2 text-[14px] leading-relaxed text-[#4B5563]">
                                    {{ $post->excerpt ?: \Illuminate\Support\Str::limit($post->body, 140) }}
                                </p>
                            </div>
                        </a>
                    @endforeach
                </div>

                @if ($featuredPost && $gridPosts->isEmpty())
                    <p class="mt-4 text-sm text-gray-500">More published stories will appear in the 3-column grid as you publish drafts.</p>
                @endif
            @endif

            @if ($posts->hasPages())
                <nav aria-label="Pagination" class="mt-8 border-t border-rule pt-6">
                    {{ $posts->links() }}
                </nav>
            @endif
        </section>

        {{-- Interactive Section 2: Quick Story Preview Modal --}}
        <div
            x-cloak
            x-show="previewPost !== null"
            x-transition.opacity
            class="fixed inset-0 z-50 flex items-center justify-center bg-ink/40 p-3 backdrop-blur-xs sm:p-4"
            role="dialog"
            aria-modal="true"
            aria-label="Article quick preview"
            @click.self="previewPost = null"
        >
            <div class="max-h-[85vh] w-full max-w-xl overflow-y-auto rounded-lg border border-rule bg-white p-4 sm:p-6" x-show="previewPost !== null">
                <div class="flex items-center justify-between gap-3 border-b border-rule pb-3">
                    <div class="flex items-center gap-2 text-xs font-semibold uppercase tracking-wider text-accent">
                        <span x-text="previewPost?.category"></span>
                        <span aria-hidden="true">&bull;</span>
                        <span x-text="previewPost?.minutes + ' min read'"></span>
                    </div>
                    <button type="button" @click="previewPost = null" class="rounded-md px-2 py-1 text-xs font-semibold text-gray-500 hover:bg-paper hover:text-ink">
                        Close
                    </button>
                </div>
                <h3 class="mt-4 font-serif text-2xl font-bold leading-snug text-ink" x-text="previewPost?.title"></h3>
                <p class="mt-2 text-xs text-gray-500" x-text="'Published ' + (previewPost?.date || '')"></p>
                <p class="mt-4 text-sm font-medium leading-relaxed text-gray-700" x-text="previewPost?.excerpt"></p>
                <p class="mt-3 border-l-2 border-accent pl-3 font-serif text-sm leading-relaxed text-gray-600" x-text="previewPost?.snippet"></p>
                <div class="mt-6 flex items-center justify-between border-t border-rule pt-4">
                    <button
                        type="button"
                        @click="previewPost && toggleBookmark(previewPost)"
                        class="rounded-md border border-rule bg-paper px-3.5 py-2 text-xs font-semibold text-ink hover:border-gray-400"
                        x-text="previewPost && isBookmarked(previewPost.slug) ? 'Saved to Reading List' : 'Save for later'"
                    ></button>
                    <a :href="previewPost?.url" class="rounded-md bg-accent px-4 py-2 text-xs font-semibold text-white transition hover:bg-ink">
                        Read full article &rarr;
                    </a>
                </div>
            </div>
        </div>

        {{-- Interactive Section 3: Browse by Reading Pace & Reader RSS Subscribe --}}
        <section id="about-autoblog" class="mt-14 grid grid-cols-1 gap-6 lg:grid-cols-12">
            <div
                class="min-w-0 rounded-lg border border-rule bg-white p-4 sm:p-6 lg:col-span-7"
                x-data="{
                    pace: 'quick',
                    stories: @js($previewData)
                }"
            >
                <div class="flex flex-wrap items-center justify-between gap-3 border-b border-rule pb-4">
                    <div class="min-w-0">
                        <span class="inline-block rounded bg-accent/10 px-2.5 py-1 text-xs font-semibold uppercase tracking-wider text-accent">
                            Pick Your Pace
                        </span>
                        <h2 class="mt-2 font-serif text-xl font-bold text-ink">Browse stories by reading time</h2>
                    </div>
                    <div class="flex flex-wrap gap-2">
                        <button
                            type="button"
                            @click="pace = 'quick'"
                            :class="pace === 'quick' ? 'bg-accent text-white border-accent' : 'bg-paper text-gray-700 border-rule hover:border-gray-400'"
                            class="rounded-md border px-3 py-1.5 text-xs font-semibold transition"
                        >
                            Quick Reads (&le; 3 min)
                        </button>
                        <button
                            type="button"
                            @click="pace = 'deep'"
                            :class="pace === 'deep' ? 'bg-accent text-white border-accent' : 'bg-paper text-gray-700 border-rule hover:border-gray-400'"
                            class="rounded-md border px-3 py-1.5 text-xs font-semibold transition"
                        >
                            Deep Dives (4+ min)
                        </button>
                    </div>
                </div>

                <div class="mt-4 divide-y divide-rule">
                    <template x-for="story in stories.filter(s => pace === 'quick' ? s.minutes <= 3 : s.minutes >= 4).slice(0, 4)" :key="story.slug">
                        <a :href="story.url" class="flex min-w-0 items-center justify-between gap-3 py-3 transition hover:text-accent">
                            <div class="min-w-0 flex-1">
                                <span class="text-[11px] font-semibold uppercase tracking-wider text-accent" x-text="story.category"></span>
                                <p class="truncate font-serif text-base font-bold text-ink" x-text="story.title"></p>
                            </div>
                            <span class="shrink-0 rounded border border-rule bg-paper px-2.5 py-1 text-xs font-medium text-gray-600" x-text="story.minutes + ' min'"></span>
                        </a>
                    </template>
                    <p
                        x-show="stories.filter(s => pace === 'quick' ? s.minutes <= 3 : s.minutes >= 4).length === 0"
                        class="py-6 text-center text-sm text-gray-500"
                    >
                        No stories in this reading-time range on the current page.
                    </p>
                </div>
            </div>

            <div
                id="contact-autoblog"
                class="min-w-0 flex flex-col justify-between rounded-lg border border-rule bg-white p-4 sm:p-6 lg:col-span-5"
                x-data="{ copied: false }"
            >
                <div class="min-w-0">
                    <span class="inline-block rounded bg-accent/10 px-2.5 py-1 text-xs font-semibold uppercase tracking-wider text-accent">
                        Reader Feed
                    </span>
                    <h2 class="mt-2 font-serif text-xl font-bold text-ink">Follow new stories in your reader</h2>
                    <p class="mt-2 text-sm leading-relaxed text-gray-600">
                        Skip the inbox clutter. Copy the journal RSS address below into Feedly, NetNewsWire, or any feed reader to get new essays as soon as they publish.
                    </p>
                    <div class="mt-4 flex min-w-0 items-center justify-between gap-2 rounded-md border border-rule bg-paper px-3 py-2.5">
                        <code class="min-w-0 flex-1 truncate text-xs font-medium text-ink">{{ route('feed') }}</code>
                        <button
                            type="button"
                            @click="navigator.clipboard?.writeText(@js(route('feed'))).then(() => { copied = true; setTimeout(() => copied = false, 2000); })"
                            class="shrink-0 rounded-md bg-ink px-3 py-1 text-xs font-semibold text-white transition hover:bg-accent"
                            x-text="copied ? 'Copied!' : 'Copy RSS'"
                        ></button>
                    </div>
                </div>

                <div class="mt-5 flex flex-wrap items-center gap-4 border-t border-rule pt-4 text-xs font-semibold">
                    <a href="{{ route('feeds.index') }}" class="text-accent hover:text-ink">Feed guide &rarr;</a>
                    <a href="{{ route('rss') }}" class="text-gray-600 hover:text-ink">RSS XML</a>
                    <a href="{{ route('sitemap') }}" class="text-gray-600 hover:text-ink">Sitemap</a>
                </div>
            </div>
        </section>
    </div>
@endsection
