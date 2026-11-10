@extends('layouts.blog')

@section('title', 'Post not found | '.config('app.name'))
@section('description', 'This article could not be found.')

@section('content')
    <section class="mx-auto max-w-2xl py-10 text-center sm:py-16">
        <p class="text-xs font-semibold uppercase tracking-[0.2em] text-accent">404 · Page unavailable</p>
        <h1 class="mt-4 font-serif text-4xl font-semibold tracking-tight text-ink sm:text-5xl">Post not found</h1>
        <p class="mx-auto mt-4 max-w-lg text-base leading-7 text-stone-600">
            This article may have moved, the address may be incorrect, or it may not be published.
        </p>
        <a
            href="{{ route('blog.index') }}"
            class="mt-7 inline-flex min-h-11 items-center justify-center rounded-full bg-accent px-5 py-3 text-sm font-semibold text-white shadow-sm shadow-accent/20 transition hover:bg-ink focus:outline-none focus:ring-2 focus:ring-accent focus:ring-offset-2"
        >
            Back to the journal
        </a>
    </section>
@endsection
