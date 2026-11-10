@extends('layouts.blog')

@section('title', 'Follow the journal | '.config('app.name'))
@section('description', 'Subscribe to the '.config('app.name').' RSS feed.')

@section('content')
    <div class="mx-auto max-w-4xl">
        <a href="{{ route('blog.index') }}" class="inline-flex items-center gap-2 text-sm font-semibold text-accent transition hover:text-ink">
            <span aria-hidden="true">←</span>
            Back to the journal
        </a>

        <section class="mt-10 border-b border-rule pb-9 sm:mt-14 sm:pb-12">
            <p class="text-xs font-semibold uppercase tracking-[0.2em] text-accent">Follow the journal</p>
            <h1 class="mt-3 max-w-3xl font-serif text-4xl font-semibold leading-tight tracking-tight text-ink sm:text-5xl">
                New stories, delivered your way.
            </h1>
            <p class="mt-4 max-w-2xl text-base leading-7 text-stone-600 sm:text-lg sm:leading-8">
                Follow the journal with any RSS reader. New published articles will appear automatically.
            </p>
        </section>

        <section class="grid gap-10 py-9 sm:grid-cols-[minmax(0,1fr)_15rem] sm:gap-12 sm:py-12">
            <div>
                <p class="text-xs font-semibold uppercase tracking-[0.18em] text-accent">RSS feed</p>
                <h2 class="mt-2 font-serif text-2xl font-semibold text-ink">A quieter way to keep up</h2>
                <p class="mt-3 text-sm leading-7 text-stone-600">
                    RSS is a standard feed format. Open the link below in your feed reader to subscribe. It is normal to see XML instead of a designed webpage. A regular browser displays the feed in its standard format.
                </p>
                <p class="mt-5 break-all border-y border-rule py-3 font-mono text-xs leading-6 text-stone-600">{{ route('rss') }}</p>
                <a
                    href="{{ route('rss') }}"
                    class="mt-5 inline-flex min-h-11 items-center justify-center gap-2 rounded-full bg-accent px-5 py-3 text-sm font-semibold text-white shadow-sm shadow-accent/20 transition hover:bg-ink focus:outline-none focus:ring-2 focus:ring-accent focus:ring-offset-2"
                >
                    Open RSS feed
                    <span aria-hidden="true">↗</span>
                </a>
                <a
                    href="{{ route('sitemap') }}"
                    class="mt-5 inline-flex min-h-11 items-center justify-center px-4 py-3 text-sm font-semibold text-accent underline underline-offset-4 transition hover:text-ink"
                >
                    View sitemap
                </a>
            </div>

            <aside class="border-t border-rule pt-6 sm:border-l sm:border-t-0 sm:pl-7 sm:pt-0">
                <h2 class="font-semibold text-ink">How to subscribe</h2>
                <ol class="mt-3 grid gap-3 text-sm leading-6 text-stone-600">
                    <li><span class="font-semibold text-ink">01.</span> Copy the feed address.</li>
                    <li><span class="font-semibold text-ink">02.</span> Paste it into your RSS reader.</li>
                    <li><span class="font-semibold text-ink">03.</span> Read each new post there.</li>
                </ol>
                <a href="{{ route('blog.index') }}" class="mt-5 inline-flex min-h-11 items-center text-sm font-semibold text-accent underline underline-offset-4 hover:text-ink">
                    Browse all articles
                </a>
            </div>
        </section>
    </div>
@endsection
