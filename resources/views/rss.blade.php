<?xml version="1.0" encoding="UTF-8"?>
<rss version="2.0">
    <channel>
        <title>{{ config('app.name', 'AutoBlog: Fetch, Draft & Publish') }}</title>
        <link>{{ route('blog.index') }}</link>
        <description>Published articles from {{ config('app.name', 'AutoBlog: Fetch, Draft & Publish') }}</description>
        <atom:link xmlns:atom="http://www.w3.org/2005/Atom" href="{{ route('rss') }}" rel="self" type="application/rss+xml" />
        @foreach ($posts as $post)
            <item>
                <title>{{ $post->title }}</title>
                <link>{{ route('blog.show', $post) }}</link>
                <guid isPermaLink="true">{{ route('blog.show', $post) }}</guid>
                <pubDate>{{ $post->published_at->toRfc2822String() }}</pubDate>
                <description>{{ $post->meta_description ?: $post->excerpt }}</description>
            </item>
        @endforeach
    </channel>
</rss>
