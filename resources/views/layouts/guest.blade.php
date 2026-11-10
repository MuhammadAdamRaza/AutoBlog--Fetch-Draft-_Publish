<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <meta name="theme-color" content="#f3f6fb">
        <title>{{ config('app.name', 'AutoBlog: Fetch, Draft & Publish') }}</title>
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="min-h-screen bg-paper font-sans text-ink antialiased">
        <div class="mx-auto grid min-h-screen max-w-7xl items-center gap-8 px-4 py-8 sm:px-8 sm:py-12 lg:grid-cols-[1fr_0.85fr] lg:gap-20 lg:py-16">
            <section class="hidden lg:block">
                <a href="{{ route('blog.index') }}" class="inline-flex max-w-full items-center gap-3 font-serif text-lg font-semibold tracking-tight text-ink">
                    <span class="grid size-10 shrink-0 place-items-center rounded-2xl bg-accent text-sm font-bold text-white shadow-sm shadow-accent/20" aria-hidden="true">A</span>
                    {{ config('app.name', 'AutoBlog: Fetch, Draft & Publish') }}
                </a>
                <p class="mt-16 text-xs font-semibold uppercase tracking-[0.24em] text-accent">An independent journal</p>
                <h1 class="mt-5 max-w-xl font-serif text-5xl font-semibold leading-tight tracking-tight text-ink">
                    A thoughtful place for your next story.
                </h1>
                <p class="mt-6 max-w-lg text-lg leading-8 text-stone-600">
                    Collect source material, shape each draft, and publish when it is ready.
                </p>
                <a href="{{ route('blog.index') }}" class="mt-8 inline-flex items-center gap-2 text-sm font-semibold text-accent transition hover:text-ink">
                    Browse the journal <span aria-hidden="true">→</span>
                </a>
            </section>

            <main class="mx-auto w-full max-w-md">
                <div class="mb-5 flex items-center justify-between gap-3 lg:hidden">
                    <a href="{{ route('blog.index') }}" class="flex min-w-0 items-center gap-2 font-serif text-base font-semibold tracking-tight text-ink sm:text-lg">
                        <span class="grid size-9 shrink-0 place-items-center rounded-xl bg-accent text-xs font-bold text-white" aria-hidden="true">A</span>
                        <span class="truncate">{{ config('app.name', 'AutoBlog: Fetch, Draft & Publish') }}</span>
                    </a>
                    <a href="{{ route('blog.index') }}" class="shrink-0 rounded-full border border-rule bg-white px-3 py-2 text-xs font-semibold text-ink transition hover:border-accent/40 sm:text-sm">
                        Home
                    </a>
                </div>
                <div class="rounded-3xl border border-rule bg-white p-5 shadow-[0_24px_70px_-35px_rgba(23,36,58,0.24)] sm:p-9">
                    {{ $slot }}
                </div>
                <p class="mt-5 text-center text-xs leading-5 text-stone-500">
                    Review and approve every draft before it goes live.
                </p>
            </main>
        </div>
    </body>
</html>
