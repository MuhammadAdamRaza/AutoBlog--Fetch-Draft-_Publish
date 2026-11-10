<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" x-data="{ menuOpen: false }" @keydown.escape.window="menuOpen = false">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="theme-color" content="#f3f6fb">
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
    <body class="min-h-screen bg-paper font-sans text-ink antialiased">
        <header class="border-b border-rule bg-paper/90">
            <div class="mx-auto flex max-w-6xl items-center justify-between gap-4 px-4 py-4 sm:px-8 sm:py-5">
                <a href="{{ route('blog.index') }}" class="flex min-w-0 items-center gap-3 font-serif text-lg font-semibold tracking-tight text-ink sm:text-xl">
                    <span class="grid size-10 shrink-0 place-items-center rounded-2xl bg-accent text-sm font-bold text-white shadow-sm shadow-accent/20" aria-hidden="true">A</span>
                    <span class="truncate">{{ config('app.name', 'AutoBlog: Fetch, Draft & Publish') }}</span>
                </a>
                <nav aria-label="Main navigation" class="hidden items-center gap-1 text-sm font-medium sm:flex">
                    <a href="{{ route('blog.index') }}" @class([
                        'rounded-full px-4 py-2 transition',
                        'bg-white text-ink shadow-sm' => request()->routeIs('blog.index', 'blog.show'),
                        'text-stone-600 hover:bg-white/70 hover:text-ink' => ! request()->routeIs('blog.index', 'blog.show'),
                    ])>Journal</a>
                    <a href="{{ route('feeds.index') }}" @class([
                        'rounded-full px-4 py-2 transition',
                        'bg-white text-ink shadow-sm' => request()->routeIs('feeds.*'),
                        'text-stone-600 hover:bg-white/70 hover:text-ink' => ! request()->routeIs('feeds.*'),
                    ])>Feeds</a>
                    @auth
                        <a href="{{ route('admin.posts.index') }}" class="ml-2 rounded-full bg-accent px-4 py-2 font-semibold text-white transition hover:bg-ink">
                            Admin
                        </a>
                    @else
                        <a href="{{ route('login') }}" class="ml-2 rounded-full bg-accent px-4 py-2 font-semibold text-white transition hover:bg-ink">
                            Admin
                        </a>
                    @endauth
                </nav>
                <button
                    type="button"
                    @click="menuOpen = ! menuOpen"
                    :aria-expanded="menuOpen.toString()"
                    aria-label="Toggle navigation"
                    class="grid size-10 shrink-0 place-items-center rounded-xl border border-rule bg-white text-ink shadow-sm transition hover:border-accent/40 sm:hidden"
                >
                    <svg x-show="! menuOpen" class="size-5" stroke="currentColor" fill="none" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M4 7h16M4 12h16M4 17h16" />
                    </svg>
                    <svg x-show="menuOpen" x-cloak class="size-5" stroke="currentColor" fill="none" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="m6 6 12 12M18 6 6 18" />
                    </svg>
                </button>
            </div>
            <nav x-cloak x-show="menuOpen" x-transition class="border-t border-rule bg-white px-4 py-3 shadow-lg sm:hidden" aria-label="Mobile navigation">
                <div class="mx-auto grid max-w-6xl gap-1">
                    <a href="{{ route('blog.index') }}" class="rounded-xl px-4 py-3 text-sm font-medium text-ink transition hover:bg-paper">Journal</a>
                    <a href="{{ route('feeds.index') }}" class="rounded-xl px-4 py-3 text-sm font-medium text-ink transition hover:bg-paper">Feeds &amp; RSS</a>
                    @auth
                        <a href="{{ route('admin.posts.index') }}" class="rounded-xl px-4 py-3 text-sm font-semibold text-accent transition hover:bg-paper">Admin</a>
                    @else
                        <a href="{{ route('login') }}" class="rounded-xl px-4 py-3 text-sm font-semibold text-accent transition hover:bg-paper">Admin sign in</a>
                    @endauth
                </div>
            </nav>
        </header>

        <main class="mx-auto min-h-[70vh] max-w-6xl px-5 py-10 sm:px-8 sm:py-16">
            @yield('content')
        </main>

        <footer class="border-t border-rule">
            <div class="mx-auto flex max-w-6xl flex-col gap-3 px-5 py-7 text-sm text-stone-600 sm:flex-row sm:items-center sm:justify-between sm:px-8">
                <p>{{ config('app.name') }}</p>
                <div class="flex gap-5">
                    <a href="{{ route('sitemap') }}" class="transition hover:text-accent">Sitemap</a>
                    <a href="{{ route('feeds.index') }}" class="transition hover:text-accent">Feeds &amp; RSS</a>
                </div>
            </div>
        </footer>
        @stack('scripts')
    </body>
</html>
