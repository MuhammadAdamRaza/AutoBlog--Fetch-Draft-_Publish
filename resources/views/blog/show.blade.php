@extends('layouts.blog')

@section('title', $post->title.' | '.config('app.name'))
@section('description', $post->meta_description ?: ($post->excerpt ?: \Illuminate\Support\Str::limit($post->body, 155)))
@section('og_type', 'article')

@php
    $structuredData = [
        '@context' => 'https://schema.org',
        '@type' => 'Article',
        'headline' => $post->title,
        'description' => $post->meta_description ?: ($post->excerpt ?: \Illuminate\Support\Str::limit($post->body, 155)),
        'datePublished' => $post->published_at->toIso8601String(),
        'dateModified' => $post->updated_at->toIso8601String(),
        'mainEntityOfPage' => route('blog.show', $post),
        'publisher' => [
            '@type' => 'Organization',
            'name' => config('app.name'),
        ],
    ];

    $shareUrl = urlencode(route('blog.show', $post));
    $shareTitle = urlencode($post->title);
@endphp

@push('head')
    <meta property="article:published_time" content="{{ $post->published_at->toAtomString() }}">
    <script type="application/ld+json">
        @json($structuredData)
    </script>
@endpush

@push('scripts')
    <script>
        const copyLinkButton = document.getElementById('copy-article-link');
        const copyLinkStatus = document.getElementById('copy-link-status');

        if (copyLinkButton && copyLinkStatus) {
            copyLinkButton.addEventListener('click', async () => {
                try {
                    await navigator.clipboard.writeText(@json(route('blog.show', $post)));
                    copyLinkStatus.textContent = 'Article link copied.';
                } catch (error) {
                    copyLinkStatus.textContent = 'Copy failed. You can copy the address from your browser bar.';
                }
            });
        }
    </script>
@endpush

@section('content')
    <article>
        {{-- Header Area (Centered, max-w-3xl) --}}
        <header class="mx-auto max-w-3xl text-center">
            <nav class="mb-5 flex justify-center text-xs font-medium text-gray-500" aria-label="Breadcrumb">
                <ol class="flex flex-wrap items-center gap-2">
                    <li><a href="{{ route('blog.index') }}" class="text-accent hover:underline">Journal</a></li>
                    <li aria-hidden="true">/</li>
                    <li aria-current="page" class="text-gray-700">{{ \Illuminate\Support\Str::limit($post->title, 48) }}</li>
                </ol>
            </nav>

            <span class="inline-block rounded bg-accent/10 px-2.5 py-1 text-xs font-semibold uppercase tracking-wider text-accent">
                {{ $post->category ?: 'Tech' }}
            </span>

            <h1 class="mt-4 font-serif text-3xl font-bold leading-tight tracking-tight text-ink sm:text-5xl">
                {{ $post->title }}
            </h1>

            @if ($post->excerpt)
                <p class="mt-4 text-lg leading-relaxed text-gray-600">
                    {{ $post->excerpt }}
                </p>
            @endif

            <div class="mt-6 flex flex-wrap items-center justify-center gap-x-3 gap-y-2 border-y border-rule py-3 text-xs font-medium text-gray-500">
                <span class="font-semibold text-ink">AutoBlog Editorial</span>
                <span aria-hidden="true">&bull;</span>
                <time datetime="{{ $post->published_at->toAtomString() }}">{{ $post->published_at->format('F j, Y') }}</time>
                <span aria-hidden="true">&bull;</span>
                <span>{{ $post->readingMinutes() }} min read</span>
                @if ($post->source_url)
                    <span aria-hidden="true">&bull;</span>
                    <span>
                        Source Reference:
                        <a href="{{ $post->source_url }}" rel="nofollow noopener" target="_blank" class="font-semibold text-accent underline underline-offset-4 hover:text-ink">
                            {{ parse_url($post->source_url, PHP_URL_HOST) ?: $post->source_url }}
                        </a>
                    </span>
                @endif
            </div>
        </header>

        {{-- Cover Image (max-w-4xl, wide and crisp, shown when cover_image is set) --}}
        @if ($post->coverUrl())
            <figure class="mx-auto mt-8 max-w-4xl overflow-hidden rounded-lg border border-rule bg-white">
                <div class="aspect-video w-full">
                    <img
                        src="{{ $post->coverUrl() }}"
                        alt="{{ $post->title }}"
                        class="h-full w-full object-cover"
                    >
                </div>
            </figure>
        @endif

        {{-- Reading Body (max-w-[680px] centered, 18-19px, line-height 1.75) --}}
        <div class="mx-auto mt-8 max-w-[680px] space-y-6 break-words font-serif text-[17px] leading-[1.75] text-ink sm:mt-10 sm:text-[19px] [&_h2]:mt-10 [&_h2]:font-sans [&_h2]:text-xl [&_h2]:font-bold sm:[&_h2]:text-2xl [&_h3]:mt-8 [&_h3]:font-sans [&_h3]:text-lg [&_h3]:font-semibold sm:[&_h3]:text-xl">
            @foreach (preg_split('/\R{2,}/', trim($post->body)) ?: [] as $paragraph)
                <p>{{ trim($paragraph) }}</p>
            @endforeach
        </div>

        <div class="mx-auto mt-10 max-w-[680px] space-y-6 border-t border-rule pt-6">
            @if ($post->source_url)
                <aside class="rounded-lg border border-rule bg-white p-4 text-sm text-gray-600">
                    <span class="font-semibold text-ink">Source Reference:</span>
                    <a href="{{ $post->source_url }}" rel="nofollow noopener" target="_blank" class="break-all font-medium text-accent underline underline-offset-4 hover:text-ink">
                        {{ $post->source_url }}
                    </a>
                </aside>
            @endif

            <section aria-label="Share this article" class="flex flex-wrap items-center gap-2.5">
                <span class="w-full text-sm font-semibold text-ink sm:mr-2 sm:w-auto">Share this article</span>
                <a
                    href="https://twitter.com/intent/tweet?url={{ $shareUrl }}&amp;text={{ $shareTitle }}"
                    target="_blank"
                    rel="noopener noreferrer"
                    class="inline-flex items-center rounded-md border border-rule bg-white px-3.5 py-2 text-xs font-semibold text-gray-700 transition hover:border-gray-400 hover:text-ink"
                >X</a>
                <a
                    href="https://www.linkedin.com/sharing/share-offsite/?url={{ $shareUrl }}"
                    target="_blank"
                    rel="noopener noreferrer"
                    class="inline-flex items-center rounded-md border border-rule bg-white px-3.5 py-2 text-xs font-semibold text-gray-700 transition hover:border-gray-400 hover:text-ink"
                >LinkedIn</a>
                <a
                    href="https://wa.me/?text={{ $shareTitle }}%20{{ $shareUrl }}"
                    target="_blank"
                    rel="noopener noreferrer"
                    class="inline-flex items-center rounded-md border border-rule bg-white px-3.5 py-2 text-xs font-semibold text-gray-700 transition hover:border-gray-400 hover:text-ink"
                >WhatsApp</a>
                <button
                    id="copy-article-link"
                    type="button"
                    class="inline-flex items-center rounded-md bg-accent px-3.5 py-2 text-xs font-semibold text-white transition hover:bg-ink"
                >
                    Copy link
                </button>
                <span id="copy-link-status" class="text-xs text-gray-600" role="status" aria-live="polite"></span>
            </section>
        </div>

        {{-- Related Posts (3 cards, same category) --}}
        @if ($related->isNotEmpty())
            <section aria-labelledby="related-heading" class="mt-14 border-t border-rule pt-10">
                <div class="mb-6 flex items-baseline justify-between">
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-wider text-accent">Related Posts</p>
                        <h2 id="related-heading" class="mt-1 font-serif text-2xl font-bold text-ink">More from the journal</h2>
                    </div>
                    <a href="{{ route('blog.index') }}" class="text-sm font-semibold text-accent hover:text-ink">
                        All articles &rarr;
                    </a>
                </div>
                <div class="grid grid-cols-1 gap-6 md:grid-cols-3">
                    @foreach ($related as $item)
                        <a href="{{ route('blog.show', $item) }}" class="group flex flex-col overflow-hidden rounded-lg border border-rule bg-white transition hover:border-gray-400">
                            @if ($item->coverUrl())
                                <div class="aspect-video w-full overflow-hidden border-b border-rule bg-paper">
                                    <img
                                        src="{{ $item->coverUrl() }}"
                                        alt="{{ $item->title }}"
                                        class="h-full w-full object-cover transition duration-300 group-hover:scale-105"
                                        loading="lazy"
                                    >
                                </div>
                            @endif
                            <div class="p-4">
                                <p class="text-xs font-semibold uppercase tracking-wider text-accent">
                                    {{ $item->category ?: 'Tech' }} &bull; {{ $item->readingMinutes() }} min read
                                </p>
                                <h3 class="mt-2 line-clamp-2 font-serif text-lg font-bold leading-snug text-ink group-hover:text-accent">
                                    {{ $item->title }}
                                </h3>
                                @if ($item->excerpt)
                                    <p class="mt-2 line-clamp-2 text-sm text-gray-600">{{ $item->excerpt }}</p>
                                @endif
                            </div>
                        </a>
                    @endforeach
                </div>
            </section>
        @endif
    </article>
@endsection
