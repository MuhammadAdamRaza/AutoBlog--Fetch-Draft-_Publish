<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" x-data="{ menuOpen: false, searchOpen: false, rssCopied: false }" @keydown.escape.window="menuOpen = false; searchOpen = false">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="theme-color" content="#FAF9F6">
        <meta name="description" content="@yield('description', config('app.name'))">
        <meta property="og:site_name" content="{{ config('app.name') }}">
        <meta property="og:title" content="@yield('title', config('app.name'))">
        <meta property="og:description" content="@yield('description', config('app.name'))">
        <meta property="og:type" content="@yield('og_type', 'website')">
        <meta property="og:url" content="{{ url()->current() }}">
        <link rel="canonical" href="{{ url()->current() }}">
        <link rel="alternate" type="application/rss+xml" title="{{ config('app.name') }} RSS" href="{{ route('rss') }}">
        <title>@yield('title', config('app.name'))</title>

        @stack('head')

        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="min-h-screen overflow-x-hidden bg-paper font-sans text-ink antialiased">
        <header class="sticky top-0 z-30 h-16 border-b border-rule bg-paper/95 backdrop-blur-sm">
            <div class="mx-auto flex h-full max-w-6xl items-center justify-between gap-4 px-4 sm:px-6 lg:px-8">
                <a href="{{ route('blog.index') }}" class="flex shrink-0 items-center gap-2.5 font-serif text-xl font-bold tracking-tight text-ink">
                    <span class="grid size-8 place-items-center rounded bg-accent text-xs font-sans font-bold text-white" aria-hidden="true">A</span>
                    <span>AutoBlog</span>
                </a>

                <nav aria-label="Main navigation" class="hidden items-center gap-6 text-sm font-medium md:flex">
                    @foreach (['Tech', 'AI', 'Business', 'Guides'] as $navCategory)
                        <a
                            href="{{ route('blog.index', ['category' => $navCategory]) }}"
                            @class([
                                'transition-colors',
                                'font-semibold text-accent' => request()->query('category') === $navCategory,
                                'text-gray-600 hover:text-ink' => request()->query('category') !== $navCategory,
                            ])
                        >
                            {{ $navCategory }}
                        </a>
                    @endforeach
                    <a
                        href="{{ route('feeds.index') }}"
                        @class([
                            'transition-colors',
                            'font-semibold text-accent' => request()->routeIs('feeds.*'),
                            'text-gray-600 hover:text-ink' => ! request()->routeIs('feeds.*'),
                        ])
                    >
                        Feeds
                    </a>
                </nav>

                <div class="flex items-center gap-2">
                    <button
                        type="button"
                        @click="searchOpen = true; $nextTick(() => $refs.modalSearchInput?.focus())"
                        aria-label="Open article search"
                        class="grid size-9 place-items-center rounded-md border border-rule bg-white text-gray-700 transition hover:border-gray-400 hover:text-ink"
                    >
                        <svg class="size-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                            <circle cx="11" cy="11" r="7" />
                            <path stroke-linecap="round" d="m20 20-3.5-3.5" />
                        </svg>
                    </button>

                    <div class="relative flex items-center">
                        <a
                            href="{{ route('feeds.index') }}"
                            @click.prevent="
                                if (navigator.clipboard) {
                                    navigator.clipboard.writeText(@js(route('feed'))).then(() => {
                                        rssCopied = true;
                                        setTimeout(() => rssCopied = false, 2000);
                                    }).catch(() => {
                                        window.location.href = @js(route('feeds.index'));
                                    });
                                } else {
                                    window.location.href = @js(route('feeds.index'));
                                }
                            "
                            title="Copy /feed RSS link"
                            aria-label="RSS Feed"
                            class="relative grid size-9 place-items-center rounded-md border border-rule bg-white text-gray-700 transition hover:border-gray-400 hover:text-ink"
                        >
                            <svg class="size-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M4 11a9 9 0 0 1 9 9M4 4a16 16 0 0 1 16 16" />
                                <circle cx="5" cy="19" r="1.5" fill="currentColor" />
                            </svg>
                            <span class="absolute right-1.5 top-1.5 size-2 rounded-full bg-[#F97316]" aria-hidden="true"></span>
                        </a>
                        <span
                            x-cloak
                            x-show="rssCopied"
                            x-transition
                            class="absolute right-0 top-11 z-40 whitespace-nowrap rounded border border-rule bg-ink px-2.5 py-1 text-xs font-medium text-white"
                        >
                            Copied /feed URL
                        </span>
                    </div>

                    @auth
                        <a href="{{ route('admin.posts.index') }}" class="hidden rounded-md bg-accent px-3.5 py-2 text-xs font-semibold text-white transition hover:bg-ink sm:inline-flex">
                            Admin
                        </a>
                    @else
                        <a href="{{ route('login') }}" class="hidden rounded-md bg-accent px-3.5 py-2 text-xs font-semibold text-white transition hover:bg-ink sm:inline-flex">
                            Admin
                        </a>
                    @endauth

                    <button
                        type="button"
                        @click="menuOpen = ! menuOpen"
                        :aria-expanded="menuOpen.toString()"
                        aria-label="Toggle navigation"
                        class="grid size-9 place-items-center rounded-md border border-rule bg-white text-ink transition hover:border-gray-400 md:hidden"
                    >
                        <svg x-show="! menuOpen" class="size-5" stroke="currentColor" fill="none" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M4 7h16M4 12h16M4 17h16" />
                        </svg>
                        <svg x-show="menuOpen" x-cloak class="size-5" stroke="currentColor" fill="none" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="m6 6 12 12M18 6 6 18" />
                        </svg>
                    </button>
                </div>
            </div>

            <nav x-cloak x-show="menuOpen" x-transition class="border-t border-rule bg-white px-4 py-3 md:hidden" aria-label="Mobile navigation">
                <div class="mx-auto grid max-w-6xl gap-1">
                    <a href="{{ route('blog.index') }}" class="rounded-md px-3 py-2 text-sm font-medium text-ink hover:bg-paper">All Posts</a>
                    @foreach (['Tech', 'AI', 'Business', 'Guides'] as $navCategory)
                        <a href="{{ route('blog.index', ['category' => $navCategory]) }}" class="rounded-md px-3 py-2 text-sm font-medium text-gray-700 hover:bg-paper">{{ $navCategory }}</a>
                    @endforeach
                    <a href="{{ route('feeds.index') }}" class="rounded-md px-3 py-2 text-sm font-medium text-gray-700 hover:bg-paper">RSS &amp; Feeds</a>
                    @auth
                        <a href="{{ route('admin.posts.index') }}" class="rounded-md px-3 py-2 text-sm font-semibold text-accent hover:bg-paper">Admin</a>
                    @else
                        <a href="{{ route('login') }}" class="rounded-md px-3 py-2 text-sm font-semibold text-accent hover:bg-paper">Admin sign in</a>
                    @endauth
                </div>
            </nav>
        </header>

        <div
            x-cloak
            x-show="searchOpen"
            x-transition.opacity
            class="fixed inset-0 z-50 flex items-start justify-center bg-ink/40 px-3 pt-14 backdrop-blur-xs sm:px-4 sm:pt-24"
            role="dialog"
            aria-modal="true"
            aria-label="Search articles"
            @click.self="searchOpen = false"
        >
            <div class="w-full max-w-xl rounded-lg border border-rule bg-white p-4 sm:p-5">
                <div class="flex items-center justify-between border-b border-rule pb-3">
                    <h2 class="font-serif text-lg font-bold text-ink">Search AutoBlog</h2>
                    <button type="button" @click="searchOpen = false" class="rounded-md px-2 py-1 text-xs font-medium text-gray-500 hover:bg-paper hover:text-ink">
                        Esc
                    </button>
                </div>
                <form method="GET" action="{{ route('blog.index') }}" class="mt-4 flex flex-col gap-2 sm:flex-row">
                    <label for="modal-article-search" class="sr-only">Search published articles</label>
                    <input
                        id="modal-article-search"
                        x-ref="modalSearchInput"
                        type="search"
                        name="q"
                        value="{{ request()->query('q') }}"
                        placeholder="Search headlines, topics, or keywords..."
                        class="min-w-0 flex-1 rounded-md border-rule bg-paper px-3.5 py-2.5 text-sm text-ink focus:border-accent focus:ring-accent"
                    >
                    <button type="submit" class="rounded-md bg-accent px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-ink">
                        Search
                    </button>
                </form>
            </div>
        </div>

        <main class="mx-auto min-h-[75vh] max-w-6xl px-4 py-8 sm:px-6 sm:py-12 lg:px-8">
            @yield('content')
        </main>

        <footer class="border-t border-rule bg-white">
            <div class="mx-auto flex max-w-6xl flex-col gap-4 px-4 py-8 text-sm text-gray-600 sm:flex-row sm:items-center sm:justify-between sm:px-6 lg:px-8">
                <div>
                    <p class="font-serif text-base font-bold text-ink">{{ config('app.name') }}</p>
                    <p class="mt-1 text-xs text-gray-500">AI-assisted journal curation, editorial review, and automated RSS publishing. &copy; {{ date('Y') }} AutoBlog.</p>
                </div>
                <nav aria-label="Footer links" class="flex flex-wrap items-center gap-5 text-sm font-medium">
                    <a href="{{ route('feed') }}" class="transition hover:text-accent">RSS Feed (/feed)</a>
                    <a href="{{ route('rss') }}" class="sr-only">RSS XML</a>
                    <a href="{{ route('feeds.index') }}" class="transition hover:text-accent">Feeds</a>
                    <a href="{{ route('sitemap') }}" class="transition hover:text-accent">XML Sitemap</a>
                    <a href="{{ route('blog.index') }}#about-autoblog" class="transition hover:text-accent">About</a>
                    <a href="{{ route('blog.index') }}#contact-autoblog" class="transition hover:text-accent">Contact</a>
                </nav>
            </div>
        </footer>
        @stack('scripts')
    </body>
</html>
