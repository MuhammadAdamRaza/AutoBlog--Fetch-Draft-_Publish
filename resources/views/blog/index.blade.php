@extends('layouts.blog')

@section('title', 'Journal | '.config('app.name'))
@section('description', 'Published articles from '.config('app.name'))

@section('content')
    <section class="grid gap-8 border-b border-rule pb-10 sm:gap-10 sm:pb-14 lg:grid-cols-[minmax(0,1fr)_16rem] lg:items-end lg:gap-16">
        <div>
            <p class="inline-flex items-center gap-2 text-xs font-semibold uppercase tracking-[0.22em] text-accent">
                <span class="size-2 rounded-full bg-accent" aria-hidden="true"></span>
                The AutoBlog journal
            </p>
            <h1 class="mt-5 max-w-4xl font-serif text-4xl font-semibold leading-[1.08] tracking-tight text-ink sm:text-6xl lg:text-7xl">
                A fresh perspective, one story at a time.
            </h1>
            <p class="mt-5 max-w-2xl text-base leading-7 text-stone-600 sm:text-lg sm:leading-8">
                Original articles, thoughtfully edited and ready to explore.
            </p>
            <form method="GET" action="{{ route('blog.index') }}" class="mt-7 flex flex-col gap-3 sm:flex-row">
                <label for="article-search" class="sr-only">Search published articles</label>
                <input
                    id="article-search"
                    type="search"
                    name="q"
                    value="{{ $search }}"
                    placeholder="Search published articles"
                    class="min-h-12 min-w-0 flex-1 rounded-xl border-rule bg-white px-4 py-3 text-ink shadow-sm focus:border-accent focus:ring-accent"
                >
                <button
                    type="submit"
                    class="min-h-12 rounded-xl bg-accent px-5 py-3 text-sm font-semibold text-white shadow-sm shadow-accent/20 transition hover:bg-ink focus:outline-none focus:ring-2 focus:ring-accent focus:ring-offset-2"
                >
                    Search
                </button>
            </form>
            @if ($search !== '')
                <p class="mt-3 text-sm text-stone-600">
                    {{ $posts->total() }} {{ \Illuminate\Support\Str::plural('result', $posts->total()) }} for “{{ $search }}”
                    <span aria-hidden="true">·</span>
                    <a href="{{ route('blog.index') }}" class="font-semibold text-accent underline underline-offset-4 hover:text-ink">Clear search</a>
                </p>
            @endif
            <div class="mt-7 flex flex-wrap items-center gap-3">
                <a href="#latest-articles" class="inline-flex min-h-11 items-center justify-center gap-2 rounded-full bg-accent px-5 py-3 text-sm font-semibold text-white shadow-sm shadow-accent/20 transition hover:bg-ink">
                    Explore the journal <span aria-hidden="true">↓</span>
                </a>
                <a href="{{ route('feeds.index') }}" class="inline-flex min-h-11 items-center justify-center rounded-full border border-rule bg-white px-5 py-3 text-sm font-semibold text-ink transition hover:border-accent/40 hover:text-accent">
                    Follow new stories
                </a>
            </div>
        </div>

        <aside class="flex items-center gap-4 border-t border-rule pt-5 lg:border-l lg:border-t-0 lg:pl-7 lg:pt-0">
            <span class="font-serif text-5xl font-semibold tracking-tight text-ink">{{ $publishedCount }}</span>
            <span class="max-w-28 text-sm leading-5 text-stone-600">
                published {{ \Illuminate\Support\Str::plural('article', $publishedCount) }} to explore
            </span>
        </aside>
    </section>

    <section aria-labelledby="latest-heading" id="latest-articles" class="scroll-mt-8 py-10 sm:py-14">
        <div class="mb-5 flex flex-wrap items-end justify-between gap-3 sm:mb-7">
            <div>
                <p class="text-xs font-semibold uppercase tracking-[0.2em] text-accent">{{ $search !== '' ? 'Search results' : 'The latest' }}</p>
                <h2 id="latest-heading" class="mt-2 font-serif text-2xl font-semibold text-ink sm:text-3xl">
                    {{ $search !== '' ? 'Stories matching your search' : 'Stories to spend time with' }}
                </h2>
            </div>
            <span class="text-sm text-stone-500">{{ $posts->total() }} {{ \Illuminate\Support\Str::plural('story', $posts->total()) }}</span>
        </div>

        @forelse ($posts as $post)
            <article class="group mb-4 rounded-2xl border border-rule bg-white p-5 shadow-sm transition duration-200 hover:-translate-y-0.5 hover:border-accent/30 hover:shadow-lg hover:shadow-ink/5 sm:mb-5 sm:p-7">
                <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                    <p class="flex flex-wrap items-center gap-2 text-xs font-semibold uppercase tracking-[0.18em] text-stone-500">
                        <time datetime="{{ $post->published_at->toAtomString() }}">{{ $post->published_at->format('F j, Y') }}</time>
                        <span aria-hidden="true">·</span>
                        <span>{{ $post->readingMinutes() }} min read</span>
                    </p>
                    @if ($post->source_url)
                        <span class="inline-flex w-fit items-center gap-1.5 rounded-full bg-paper px-3 py-1 text-xs font-medium text-stone-600">
                            Source credited
                        </span>
                    @endif
                </div>
                <h3 class="mt-3 font-serif text-2xl font-semibold leading-snug text-ink sm:text-3xl">
                    <a href="{{ route('blog.show', $post) }}" class="transition hover:text-accent">
                        {{ $post->title }}
                    </a>
                </h3>
                <p class="mt-3 max-w-3xl leading-7 text-stone-600">
                    {{ $post->excerpt ?: \Illuminate\Support\Str::limit($post->body, 220) }}
                </p>
                <a href="{{ route('blog.show', $post) }}" class="mt-5 inline-flex items-center gap-2 text-sm font-semibold text-accent transition group-hover:gap-3 hover:text-ink">
                    Read article <span aria-hidden="true">→</span>
                </a>
            </article>
        @empty
            @if ($search !== '')
                <div class="border-y border-rule py-10 text-center sm:py-14">
                    <h3 class="font-serif text-2xl font-semibold text-ink">No stories found</h3>
                    <p class="mx-auto mt-2 max-w-md text-sm leading-6 text-stone-600">
                        Try a different title, phrase, or topic. Search checks article titles, summaries, and body text.
                    </p>
                    <a href="{{ route('blog.index') }}" class="mt-5 inline-flex min-h-11 items-center justify-center rounded-full border border-rule bg-white px-5 py-2.5 text-sm font-semibold text-ink transition hover:border-accent/40 hover:text-accent">
                        Clear search
                    </a>
                </div>
            @else
                <div class="rounded-2xl border border-dashed border-rule bg-white/70 px-5 py-12 text-center sm:py-16">
                    <span class="mx-auto grid size-12 place-items-center rounded-2xl bg-[#eaf0ff] text-accent" aria-hidden="true">
                        <svg class="size-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M8 6.75h8M8 10.75h8M8 14.75h5m-8.5 5h15a1.5 1.5 0 0 0 1.5-1.5v-13a1.5 1.5 0 0 0-1.5-1.5h-15A1.5 1.5 0 0 0 3 5.25v13a1.5 1.5 0 0 0 1.5 1.5Z" />
                        </svg>
                    </span>
                    <h3 class="mt-4 font-serif text-2xl font-semibold text-ink">The next story is on its way</h3>
                    <p class="mx-auto mt-2 max-w-md text-sm leading-6 text-stone-600">
                        There are no published articles just yet. Check back soon or follow the feed for new posts.
                    </p>
                    <a href="{{ route('feeds.index') }}" class="mt-5 inline-flex min-h-11 items-center justify-center rounded-full bg-accent px-5 py-2.5 text-sm font-semibold text-white transition hover:bg-ink">
                        Follow the feed
                    </a>
                </div>
            @endif
        @endforelse

        @if ($posts->hasPages())
            <nav aria-label="Pagination" class="mt-6 border-t border-rule pt-6">
                {{ $posts->links() }}
            </nav>
        @endif
    </section>

    <section aria-labelledby="about-heading" class="border-y border-rule py-10 sm:py-12">
        <div class="mb-7 max-w-2xl">
            <p class="text-xs font-semibold uppercase tracking-[0.2em] text-accent">A little about this journal</p>
            <h2 id="about-heading" class="mt-2 font-serif text-2xl font-semibold text-ink sm:text-3xl">Clear sources. Considered stories.</h2>
            <p class="mt-3 text-sm leading-7 text-stone-600 sm:text-base">
                Every article here is reviewed before publishing. Source links are kept with the story, and readers can follow new posts without an inbox or an account.
            </p>
        </div>
        <div class="grid gap-6 sm:grid-cols-3 sm:gap-0">
            <article class="border-rule sm:border-r sm:pr-6">
                <p class="font-serif text-3xl font-semibold text-accent">01</p>
                <h3 class="mt-3 font-semibold text-ink">Start with a source</h3>
                <p class="mt-2 text-sm leading-6 text-stone-600">Articles include a source credit whenever one is provided.</p>
            </article>
            <article class="border-rule sm:border-r sm:px-6">
                <p class="font-serif text-3xl font-semibold text-accent">02</p>
                <h3 class="mt-3 font-semibold text-ink">Reviewed by a person</h3>
                <p class="mt-2 text-sm leading-6 text-stone-600">Drafts stay private until an editor chooses to publish them.</p>
            </article>
            <article class="sm:pl-6">
                <p class="font-serif text-3xl font-semibold text-accent">03</p>
                <h3 class="mt-3 font-semibold text-ink">Read your way</h3>
                <p class="mt-2 text-sm leading-6 text-stone-600">Follow the RSS feed in the reader you already use.</p>
            </article>
        </div>
    </section>

    <section class="flex flex-col gap-4 py-8 sm:flex-row sm:items-center sm:justify-between sm:py-10">
        <div>
            <h2 class="font-serif text-xl font-semibold text-ink">Prefer a feed reader?</h2>
            <p class="mt-1 text-sm leading-6 text-stone-600">Subscribe to new posts with the RSS feed.</p>
        </div>
        <a href="{{ route('feeds.index') }}" class="inline-flex min-h-11 items-center justify-center gap-2 self-start rounded-full border border-rule bg-white px-5 py-3 text-sm font-semibold text-ink transition hover:border-accent/40 hover:text-accent sm:self-auto">
            Explore feed options <span aria-hidden="true">→</span>
        </a>
        <a href="{{ route('rss') }}" class="inline-flex min-h-11 items-center justify-center self-start text-sm font-semibold text-accent underline underline-offset-4 transition hover:text-ink sm:self-auto">
            Subscribe with RSS
        </a>
    </section>
@endsection
