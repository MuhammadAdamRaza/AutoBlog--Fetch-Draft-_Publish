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
    <article class="mx-auto max-w-3xl">
        <nav class="mb-4 text-sm text-stone-500" aria-label="Breadcrumb">
            <ol class="flex flex-wrap items-center gap-2">
                <li><a href="{{ route('blog.index') }}" class="font-medium text-accent underline-offset-4 hover:underline">Journal</a></li>
                <li aria-hidden="true">/</li>
                <li aria-current="page" class="max-w-full break-words text-ink">{{ \Illuminate\Support\Str::limit($post->title, 40) }}</li>
            </ol>
        </nav>

        <header class="border-b border-rule py-8 sm:py-12">
            <p class="flex flex-wrap items-center gap-2 text-xs font-semibold uppercase tracking-[0.18em] text-stone-500">
                <time datetime="{{ $post->published_at->toAtomString() }}">{{ $post->published_at->format('F j, Y') }}</time>
                <span aria-hidden="true">·</span>
                <span>{{ $post->readingMinutes() }} min read</span>
            </p>
            <h1 class="mt-4 font-serif text-4xl font-semibold leading-tight tracking-tight text-ink sm:text-5xl">
                {{ $post->title }}
            </h1>
            @if ($post->excerpt)
                <p class="mt-5 text-lg leading-8 text-stone-600">{{ $post->excerpt }}</p>
            @endif
        </header>

        <div class="whitespace-pre-line py-8 font-serif text-lg leading-9 text-ink sm:py-10">
            {{ $post->body }}
        </div>

        @if ($post->source_url)
            <aside class="border-t border-rule py-6 text-sm leading-6 text-stone-600">
                Source:
                <a href="{{ $post->source_url }}" rel="nofollow noopener" target="_blank" class="break-all font-medium text-accent underline underline-offset-4">
                    {{ $post->source_url }}
                </a>
            </aside>
        @endif

        <section aria-label="Share this article" class="border-t border-rule py-6">
            <div class="flex flex-wrap items-center gap-3">
                <span class="text-sm font-semibold text-ink">Share this article</span>
                <a
                    href="https://twitter.com/intent/tweet?url={{ $shareUrl }}&amp;text={{ $shareTitle }}"
                    target="_blank"
                    rel="noopener noreferrer"
                    class="inline-flex min-h-10 items-center rounded-full border border-rule bg-white px-4 py-2 text-sm font-medium text-stone-700 transition hover:border-accent/40 hover:text-accent"
                >X</a>
                <a
                    href="https://www.linkedin.com/sharing/share-offsite/?url={{ $shareUrl }}"
                    target="_blank"
                    rel="noopener noreferrer"
                    class="inline-flex min-h-10 items-center rounded-full border border-rule bg-white px-4 py-2 text-sm font-medium text-stone-700 transition hover:border-accent/40 hover:text-accent"
                >LinkedIn</a>
                <a
                    href="https://wa.me/?text={{ $shareTitle }}%20{{ $shareUrl }}"
                    target="_blank"
                    rel="noopener noreferrer"
                    class="inline-flex min-h-10 items-center rounded-full border border-rule bg-white px-4 py-2 text-sm font-medium text-stone-700 transition hover:border-accent/40 hover:text-accent"
                >WhatsApp</a>
                <button
                    id="copy-article-link"
                    type="button"
                    class="inline-flex min-h-10 items-center rounded-full bg-accent px-4 py-2 text-sm font-semibold text-white transition hover:bg-ink focus:outline-none focus:ring-2 focus:ring-accent focus:ring-offset-2"
                >
                    Copy link
                </button>
                <span id="copy-link-status" class="text-sm text-stone-600" role="status" aria-live="polite"></span>
            </div>
        </section>

        @if ($related->isNotEmpty())
            <section aria-labelledby="related-heading" class="border-t border-rule py-8 sm:py-10">
                <p class="text-xs font-semibold uppercase tracking-[0.2em] text-accent">Keep reading</p>
                <h2 id="related-heading" class="mt-2 font-serif text-2xl font-semibold text-ink">More from the journal</h2>
                <div class="mt-5 grid gap-3">
                    @foreach ($related as $item)
                        <article class="rounded-2xl border border-rule bg-white p-5 transition hover:border-accent/30 hover:shadow-sm">
                            <p class="text-xs font-medium text-stone-500">
                                <time datetime="{{ $item->published_at->toAtomString() }}">{{ $item->published_at->format('F j, Y') }}</time>
                                <span aria-hidden="true">·</span>
                                {{ $item->readingMinutes() }} min read
                            </p>
                            <h3 class="mt-2 font-serif text-lg font-semibold leading-7 text-ink">
                                <a href="{{ route('blog.show', $item) }}" class="transition hover:text-accent">{{ $item->title }}</a>
                            </h3>
                            @if ($item->excerpt)
                                <p class="mt-2 text-sm leading-6 text-stone-600">{{ $item->excerpt }}</p>
                            @endif
                        </article>
                    @endforeach
                </div>
            </section>
        @endif

        <a href="{{ route('blog.index') }}" class="inline-flex min-h-11 items-center gap-2 text-sm font-semibold text-accent transition hover:text-ink">
            <span aria-hidden="true">←</span>
            Back to all articles
        </a>
    </article>
@endsection
