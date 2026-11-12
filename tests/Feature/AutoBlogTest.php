<?php

use App\Contracts\DraftWriter;
use App\Jobs\GenerateDraftFromUrl;
use App\Models\Post;
use App\Models\PostFetchLog;
use App\Models\User;
use App\Services\OpenRouterDraftWriter;
use Illuminate\Http\Client\Request as ClientRequest;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Queue;

it('redirects the homepage to the public blog', function () {
    $this->get(route('home'))
        ->assertRedirect(route('blog.index'));
});

it('uses the forwarded HTTPS host for assets behind the local tunnel', function () {
    $this->withServerVariables([
        'REMOTE_ADDR' => '127.0.0.1',
    ])->withHeaders([
        'X-Forwarded-Host' => 'upstream-mustang-shorten.ngrok-free.dev',
        'X-Forwarded-Proto' => 'https',
        'X-Forwarded-Port' => '443',
    ])->get(route('blog.index'))
        ->assertOk()
        ->assertSee('https://upstream-mustang-shorten.ngrok-free.dev/build/assets/', false)
        ->assertDontSee('http://localhost:8000/build/assets/', false)
        ->assertDontSee('localhost:5173', false);
});

it('keeps the published total independent from the current search results', function () {
    Post::factory()->published()->count(2)->create();
    Post::factory()->published()->create([
        'title' => 'A searchable article',
    ]);

    $this->get(route('blog.index', ['q' => 'searchable']))
        ->assertOk()
        ->assertViewHas('publishedCount', 3)
        ->assertSee('published articles to explore')
        ->assertSee('1 result for');
});

it('links public pages through named routes for guests and signed-in editors', function () {
    $this->get(route('blog.index'))
        ->assertOk()
        ->assertSee('href="' . route('feeds.index') . '"', false)
        ->assertSee('href="' . route('rss') . '"', false)
        ->assertSee('href="' . route('sitemap') . '"', false)
        ->assertSee('href="#latest-articles"', false)
        ->assertSee('href="' . route('login') . '"', false);

    $this->actingAs(User::factory()->create())
        ->get(route('blog.index'))
        ->assertOk()
        ->assertSee('href="' . route('admin.posts.index') . '"', false);
});

it('shows only published posts on the blog index', function () {
    $publishedPost = Post::factory()->published()->create([
        'title' => 'Published article',
    ]);

    Post::factory()->create([
        'title' => 'Private draft',
    ]);

    $this->get(route('blog.index'))
        ->assertOk()
        ->assertSee($publishedPost->title)
        ->assertDontSee('Private draft');
});

it('filters published blog posts by search term and keeps the query in pagination', function () {
    foreach (range(1, 11) as $number) {
        Post::factory()->published()->create([
            'title' => "Queues article {$number}",
        ]);
    }

    Post::factory()->published()->create([
        'title' => 'Cooking pasta at home',
    ]);

    $this->get(route('blog.index', ['q' => 'Queues']))
        ->assertOk()
        ->assertSee('Queues article 11')
        ->assertDontSee('Cooking pasta at home')
        ->assertSee('q=Queues', false);
});

it('searches published post excerpts and bodies without exposing drafts', function () {
    Post::factory()->published()->create([
        'title' => 'Editorial notes',
        'excerpt' => 'A guide to research.',
        'body' => 'How careful indexing improves search.',
    ]);

    Post::factory()->published()->create([
        'title' => 'A separate summary',
        'excerpt' => 'Notes about indexing.',
        'body' => 'A different body.',
    ]);

    Post::factory()->create([
        'title' => 'Private research notes',
        'body' => 'How careful indexing improves search.',
    ]);

    $this->get(route('blog.index', ['q' => 'indexing']))
        ->assertOk()
        ->assertSee('Editorial notes')
        ->assertSee('A separate summary')
        ->assertDontSee('Private research notes');
});

it('treats search wildcard characters as literal text', function () {
    Post::factory()->published()->create([
        'title' => 'A story with 100% coverage',
    ]);

    Post::factory()->published()->create([
        'title' => 'A story about careful editing',
    ]);

    $this->get(route('blog.index', ['q' => '%']))
        ->assertOk()
        ->assertSee('A story with 100% coverage')
        ->assertDontSee('A story about careful editing');
});

it('displays the reading time on the blog list and article page', function () {
    $post = Post::factory()->published()->create([
        'title' => 'A longer article',
        'body' => str_repeat('word ', 201),
    ]);

    $this->get(route('blog.index'))
        ->assertOk()
        ->assertSee('2 min read');

    $this->get(route('blog.show', $post))
        ->assertOk()
        ->assertSee('2 min read');
});

it('shows related published posts but excludes the current post', function () {
    $currentPost = Post::factory()->published()->create([
        'title' => 'Current article',
        'published_at' => now()->subDays(10),
    ]);

    foreach (range(1, 4) as $number) {
        Post::factory()->published()->create([
            'title' => "Related article {$number}",
            'published_at' => now()->subDays($number),
        ]);
    }

    Post::factory()->create([
        'title' => 'Draft related article',
    ]);

    $response = $this->get(route('blog.show', $currentPost))
        ->assertOk()
        ->assertSee('More from the journal')
        ->assertSee('Related article 1')
        ->assertSee('Related article 3')
        ->assertDontSee('Related article 4')
        ->assertDontSee('Draft related article');

    expect(substr_count($response->getContent(), 'href="' . route('blog.show', $currentPost) . '"'))
        ->toBe(1);
});

it('returns 404 when a draft is opened publicly', function () {
    $post = Post::factory()->create();

    $this->get(route('blog.show', $post))
        ->assertNotFound()
        ->assertSee('Post not found');

    $this->get(route('blog.show', 'missing-article'))
        ->assertNotFound()
        ->assertSee('Post not found');
});

it('returns 404 for a post scheduled in the future', function () {
    $post = Post::factory()->published()->create([
        'published_at' => now()->addDay(),
    ]);

    $this->get(route('blog.show', $post))
        ->assertNotFound();
});

it('renders published posts with SEO metadata and their credited source', function () {
    $post = Post::factory()->published()->create([
        'title' => 'Public article',
        'excerpt' => 'An editorial summary.',
        'meta_description' => 'A search result description.',
        'body' => "First paragraph.\n\nSecond paragraph.",
        'source_url' => 'https://source.example/article',
    ]);

    $this->get(route('blog.show', $post))
        ->assertOk()
        ->assertSee('A search result description.')
        ->assertSee('property="og:type" content="article"', false)
        ->assertSee('"@type":"Article"', false)
        ->assertSee('schema.org', false)
        ->assertSee('https://source.example/article')
        ->assertSee('First paragraph.')
        ->assertSee('aria-label="Breadcrumb"', false)
        ->assertSee('Share this article')
        ->assertSee('Copy link')
        ->assertSee('navigator.clipboard.writeText', false)
        ->assertSee('Article link copied.')
        ->assertSee('https://twitter.com/intent/tweet', false)
        ->assertSee('https://www.linkedin.com/sharing/share-offsite/', false)
        ->assertSee('https://wa.me/', false);
});

it('serves published posts in the sitemap and RSS feed only', function () {
    config()->set('app.name', 'AutoBlog: Fetch, Draft & Publish');

    $publishedPost = Post::factory()->published()->create([
        'title' => 'Feed article',
    ]);

    Post::factory()->create([
        'title' => 'Unpublished article',
    ]);

    $this->get(route('sitemap'))
        ->assertOk()
        ->assertHeader('Content-Type', 'application/xml')
        ->assertSee(route('blog.show', $publishedPost), false)
        ->assertDontSee('Unpublished article');

    $this->get(route('rss'))
        ->assertOk()
        ->assertHeader('Content-Type', 'application/rss+xml')
        ->assertSee('<rss version="2.0">', false)
        ->assertSee('<title>AutoBlog: Fetch, Draft &amp; Publish</title>', false)
        ->assertSee('<description>Published articles from AutoBlog: Fetch, Draft &amp; Publish</description>', false)
        ->assertSee('Feed article')
        ->assertDontSee('Unpublished article');
});

it('explains RSS feeds on a page while preserving the RSS XML endpoint', function () {
    $this->get(route('feeds.index'))
        ->assertOk()
        ->assertSee('It is normal to see XML instead of a designed webpage.')
        ->assertSee(route('rss'))
        ->assertSee(route('sitemap'));

    $this->get(route('rss'))
        ->assertOk()
        ->assertHeader('Content-Type', 'application/rss+xml')
        ->assertSee('<rss version="2.0">', false);
});

it('brands the sign-in page with the application title', function () {
    config()->set('app.name', 'AutoBlog: Fetch, Draft & Publish');

    $this->get(route('login'))
        ->assertOk()
        ->assertSee('<title>AutoBlog: Fetch, Draft &amp; Publish</title>', false)
        ->assertSee('AutoBlog: Fetch, Draft & Publish')
        ->assertSee('Back to home')
        ->assertSee('href="' . route('blog.index') . '"', false);
});

it('requires authentication to open the admin posts page', function () {
    $this->get(route('admin.posts.index'))
        ->assertRedirect(route('login'));
});

it('shows the post form and fetch form to authenticated users', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->get(route('admin.posts.index'))
        ->assertOk()
        ->assertSee('Fetch a source URL')
        ->assertSee('Create post')
        ->assertSee('Toggle navigation')
        ->assertSee('Search posts')
        ->assertSee('All posts');

    $this->actingAs($user)
        ->get(route('admin.posts.create'))
        ->assertOk()
        ->assertSee('name="title"', false)
        ->assertSee('name="body"', false);
});

it('filters admin posts by title and status without hiding the other records', function () {
    $user = User::factory()->create();

    Post::factory()->create([
        'title' => 'Matching draft',
        'excerpt' => 'Draft summary',
        'status' => 'draft',
    ]);

    Post::factory()->published()->create([
        'title' => 'Matching published',
        'excerpt' => 'Published summary',
    ]);

    Post::factory()->create([
        'title' => 'Other draft',
        'excerpt' => 'A different post',
        'status' => 'draft',
    ]);

    $this->actingAs($user)
        ->get(route('admin.posts.index', [
            'search' => 'Matching',
            'status' => 'draft',
        ]))
        ->assertOk()
        ->assertSee('Matching draft')
        ->assertDontSee('Matching published')
        ->assertDontSee('Other draft');
});

it('creates a draft with a unique slug from validated fields', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->post(route('admin.posts.store'), [
            'title' => 'A New Article',
            'excerpt' => 'A summary.',
            'meta_description' => 'A description.',
            'body' => 'The article body.',
            'slug' => 'untrusted-slug',
            'status' => 'published',
        ])
        ->assertRedirect(route('admin.posts.index'))
        ->assertSessionHas('success');

    $this->assertDatabaseHas('posts', [
        'title' => 'A New Article',
        'slug' => 'a-new-article',
        'body' => 'The article body.',
        'status' => 'draft',
        'published_at' => null,
    ]);
});

it('returns validation errors when a post is missing its title or body', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->post(route('admin.posts.store'), [])
        ->assertSessionHasErrors(['title', 'body']);
});

it('updates post content without changing its public slug', function () {
    $user = User::factory()->create();
    $post = Post::factory()->published()->create([
        'title' => 'Original title',
        'slug' => 'original-title',
    ]);

    $this->actingAs($user)
        ->put(route('admin.posts.update', $post), [
            'title' => 'Updated title',
            'excerpt' => 'Updated summary.',
            'meta_description' => 'Updated description.',
            'body' => 'Updated article body.',
        ])
        ->assertRedirect(route('admin.posts.index'))
        ->assertSessionHas('success');

    expect($post->fresh())
        ->title->toBe('Updated title')
        ->slug->toBe('original-title')
        ->status->toBe('published')
        ->body->toBe('Updated article body.');
});

it('publishes and unpublishes a post', function () {
    $user = User::factory()->create();
    $post = Post::factory()->create();

    $this->actingAs($user)
        ->post(route('admin.posts.publish', $post))
        ->assertRedirect();

    expect($post->fresh())
        ->status->toBe('published')
        ->published_at->not->toBeNull();

    $this->actingAs($user)
        ->post(route('admin.posts.unpublish', $post->fresh()))
        ->assertRedirect();

    expect($post->fresh()->status)->toBe('draft');
});

it('deletes a post from the admin page', function () {
    $user = User::factory()->create();
    $post = Post::factory()->create();

    $this->actingAs($user)
        ->delete(route('admin.posts.destroy', $post))
        ->assertRedirect(route('admin.posts.index'))
        ->assertSessionHas('success');

    $this->assertDatabaseMissing('posts', [
        'id' => $post->id,
    ]);
});

it('rejects non-http URLs before queueing a fetch', function () {
    $user = User::factory()->create();
    Queue::fake([GenerateDraftFromUrl::class]);

    $this->actingAs($user)
        ->from(route('admin.posts.index'))
        ->post(route('admin.posts.fetch'), [
            'url' => 'ftp://source.example/article',
        ])
        ->assertSessionHasErrors('url');

    Queue::assertNothingPushed();
});

it('rejects URLs that exceed the database column length', function () {
    $user = User::factory()->create();
    Queue::fake([GenerateDraftFromUrl::class]);

    $this->actingAs($user)
        ->from(route('admin.posts.index'))
        ->post(route('admin.posts.fetch'), [
            'url' => 'https://source.example/' . str_repeat('a', 240),
        ])
        ->assertSessionHasErrors('url');

    Queue::assertNothingPushed();
});

it('queues an article fetch for an authenticated request', function () {
    $user = User::factory()->create();
    Queue::fake([GenerateDraftFromUrl::class]);

    $this->actingAs($user)
        ->post(route('admin.posts.fetch'), [
            'url' => 'https://source.example/article',
        ])
        ->assertRedirect()
        ->assertSessionHas('success', 'Your article is being prepared.');

    Queue::assertPushed(GenerateDraftFromUrl::class, function (GenerateDraftFromUrl $job): bool {
        return $job->url === 'https://source.example/article';
    });
});

it('shows live queue statuses and allows retrying failed fetches on the admin dashboard', function () {
    $user = User::factory()->create();
    Queue::fake([GenerateDraftFromUrl::class]);

    $failedLog = PostFetchLog::create([
        'source_url' => 'https://source.example/article',
        'status' => 'failed',
        'error' => 'HTTP request returned status code 402: This request requires more credits.',
    ]);

    PostFetchLog::create([
        'source_url' => 'https://source.example/successful-article',
        'status' => 'success',
        'post_id' => Post::factory()->create()->id,
    ]);

    $this->actingAs($user)
        ->get(route('admin.posts.index'))
        ->assertOk()
        ->assertSee('Queue &amp; AI Generation Activity', false)
        ->assertSee('Draft Ready')
        ->assertSee('Failed')
        ->assertSee('Retry')
        ->assertDontSee('HTTP request returned status code 402');

    $this->actingAs($user)
        ->post(route('admin.fetch-logs.retry', $failedLog))
        ->assertRedirect();

    expect($failedLog->fresh()->status)->toBe('queued');
    Queue::assertPushed(GenerateDraftFromUrl::class, fn(GenerateDraftFromUrl $job): bool => $job->url === 'https://source.example/article');
});

it('shows extracted source text in the split-screen editor', function () {
    $user = User::factory()->create();
    $post = Post::factory()->create([
        'source_url' => 'https://source.example/original',
        'source_body' => 'Original extracted paragraph from source URL.',
    ]);

    $this->actingAs($user)
        ->get(route('admin.posts.edit', $post))
        ->assertOk()
        ->assertSee('Source Reference (Read-Only)')
        ->assertSee('Original extracted paragraph from source URL.');
});

it('updates a post category directly from the admin posts table dropdown', function () {
    $user = User::factory()->create();
    $post = Post::factory()->create(['category' => 'Tech']);

    $this->actingAs($user)
        ->patch(route('admin.posts.category', $post), [
            'category' => 'AI',
        ])
        ->assertRedirect();

    expect($post->fresh()->category)->toBe('AI');
});

it('fails a draft job once when OpenRouter has insufficient credits', function () {
    Http::preventStrayRequests();
    config()->set('queue.default', 'database');
    config()->set('services.openrouter.key', 'test-openrouter-key');
    config()->set('services.openrouter.url', 'https://openrouter.example/api/v1/chat/completions');
    config()->set('services.openrouter.model', 'openai/gpt-4o-mini');

    Http::fake([
        'source.example/article' => Http::response(
            '<html><head><title>Source title</title></head><body><article><p>'
                . str_repeat('Readable article paragraph. ', 5)
                . '</p></article></body></html>',
        ),
        'openrouter.example/api/v1/chat/completions' => Http::response([
            'error' => [
                'message' => 'This request requires more credits.',
            ],
        ], 402),
    ]);

    GenerateDraftFromUrl::dispatch('https://source.example/article');

    Artisan::call('queue:work', [
        'connection' => 'database',
        '--once' => true,
        '--tries' => 3,
        '--no-interaction' => true,
    ]);

    $output = Artisan::output();

    expect($output)
        ->toContain('FAIL')
        ->not->toContain('DONE');

    Http::assertSentCount(2);

    $this->assertDatabaseCount('failed_jobs', 1);
    $this->assertDatabaseCount('jobs', 0);
    $this->assertDatabaseCount('posts', 0);
    $this->assertDatabaseHas('post_fetch_logs', [
        'source_url' => 'https://source.example/article',
        'status' => 'failed',
        'error' => 'OpenRouter could not fund this draft request. Add credits or use a lower-cost model.',
    ]);
});

it('creates a draft and success log from a fetched article', function () {
    Http::preventStrayRequests();
    Http::fake([
        'source.example/article' => Http::response(
            '<html><head><title>Source title</title></head><body>'
                . '<nav><p>Navigation content that should never be part of the article.</p></nav>'
                . '<article><p>' . str_repeat('Readable article paragraph. ', 5) . '</p></article>'
                . '<footer><p>Footer content that should never be part of the article.</p></footer>'
                . '</body></html>',
        ),
    ]);

    $this->app->instance(DraftWriter::class, new class implements DraftWriter
    {
        public function write(array $article): array
        {
            return [
                'title' => 'Rewritten article',
                'excerpt' => 'A concise summary.',
                'body' => 'An original editorial draft.',
                'meta_description' => 'A concise search description.',
            ];
        }
    });

    GenerateDraftFromUrl::dispatchSync('https://source.example/article');

    $post = Post::query()->where('title', 'Rewritten article')->firstOrFail();

    expect($post->status)->toBe('draft')
        ->and($post->source_url)->toBe('https://source.example/article');

    $this->assertDatabaseHas('post_fetch_logs', [
        'post_id' => $post->id,
        'status' => 'success',
    ]);
});

it('records a failed fetch after the job exhausts its attempts', function () {
    $job = new GenerateDraftFromUrl('https://source.example/broken');

    $job->failed(new RuntimeException('The source could not be fetched.'));

    $this->assertDatabaseHas('post_fetch_logs', [
        'source_url' => 'https://source.example/broken',
        'status' => 'failed',
        'error' => 'The source could not be fetched.',
    ]);
});

it('generates structured article content through OpenRouter', function () {
    Http::preventStrayRequests();
    config()->set('services.openrouter.key', 'test-openrouter-key');
    config()->set('services.openrouter.url', 'https://openrouter.example/api/v1/chat/completions');
    config()->set('services.openrouter.model', 'openai/gpt-4o-mini');

    Http::fake([
        'openrouter.example/api/v1/chat/completions' => Http::response([
            'choices' => [
                [
                    'message' => [
                        'content' => json_encode([
                            'title' => 'An original title',
                            'excerpt' => 'A concise excerpt.',
                            'body' => "First paragraph.\n\nSecond paragraph.",
                            'meta_description' => 'A concise description.',
                        ], JSON_THROW_ON_ERROR),
                    ],
                ],
            ],
        ]),
    ]);

    $draft = app(OpenRouterDraftWriter::class)->write([
        'title' => 'Source title',
        'body' => 'Source article text.',
        'source_url' => 'https://source.example/article',
    ]);

    expect($draft)->toBe([
        'title' => 'An original title',
        'excerpt' => 'A concise excerpt.',
        'body' => "First paragraph.\n\nSecond paragraph.",
        'meta_description' => 'A concise description.',
    ]);

    Http::assertSent(
        fn(ClientRequest $request): bool => $request->url() === 'https://openrouter.example/api/v1/chat/completions'
            && $request->hasHeader('Authorization', 'Bearer test-openrouter-key')
            && $request['model'] === 'openai/gpt-4o-mini'
            && $request['max_tokens'] === 1200
            && $request['response_format'] === ['type' => 'json_object']
    );
});

it('preserves paragraph breaks when OpenRouter returns raw newlines in JSON strings', function () {
    Http::preventStrayRequests();
    config()->set('services.openrouter.key', 'test-openrouter-key');
    config()->set('services.openrouter.url', 'https://openrouter.example/api/v1/chat/completions');
    config()->set('services.openrouter.model', 'openai/gpt-4o-mini');

    Http::fake([
        'openrouter.example/api/v1/chat/completions' => Http::response([
            'choices' => [
                [
                    'message' => [
                        'content' => "{\"title\":\"An original title\",\"excerpt\":\"A concise excerpt.\",\"body\":\"First paragraph.\nSecond paragraph.\",\"meta_description\":\"A concise description.\"}",
                    ],
                ],
            ],
        ]),
    ]);

    $draft = app(OpenRouterDraftWriter::class)->write([
        'title' => 'Source title',
        'body' => 'Source article text.',
        'source_url' => 'https://source.example/article',
    ]);

    expect($draft['body'])->toBe("First paragraph.\nSecond paragraph.");
});

it('fails clearly when OpenRouter has no API key', function () {
    config()->set('services.openrouter.key', '');

    expect(fn() => app(OpenRouterDraftWriter::class)->write([
        'title' => 'Source title',
        'body' => 'Source article text.',
        'source_url' => 'https://source.example/article',
    ]))->toThrow(RuntimeException::class, 'OpenRouter API key is not configured.');
});

it('rewrites the current article with AI without re-fetching the source URL', function () {
    $user = User::factory()->create();
    $post = Post::factory()->create([
        'title' => 'Old draft title',
        'excerpt' => 'Old excerpt',
        'body' => 'Current frontend article body to rewrite.',
        'meta_description' => 'Old meta',
        'source_url' => 'https://source.example/mvc',
        'source_body' => 'Original extracted source text about MVC architecture.',
        'ai_model' => 'openai/gpt-4o-mini',
        'tone' => 'Informative',
    ]);

    Http::fake();

    app()->bind(DraftWriter::class, fn() => new class implements DraftWriter
    {
        public function write(array $article): array
        {
            expect($article['body'])->toBe('Current frontend article body to rewrite.')
                ->and($article['rewrite'] ?? false)->toBeTrue()
                ->and($article['rewrite_pattern'] ?? '')->not->toBeEmpty();

            return [
                'title' => 'Regenerated MVC Guide',
                'excerpt' => 'Fresh summary from AI.',
                'body' => 'Freshly rewritten article body from AI.',
                'meta_description' => 'Fresh meta description.',
            ];
        }
    });

    $this->actingAs($user)
        ->get(route('admin.posts.edit', $post))
        ->assertOk()
        ->assertSee('Regenerate Article');

    $this->actingAs($user)
        ->post(route('admin.posts.regenerate', $post))
        ->assertRedirect()
        ->assertSessionHas('success', 'Article rewritten with AI.');

    Http::assertNothingSent();

    $post->refresh();
    expect($post->title)->toBe('Regenerated MVC Guide')
        ->and($post->excerpt)->toBe('Fresh summary from AI.')
        ->and($post->body)->toBe('Freshly rewritten article body from AI.')
        ->and($post->meta_description)->toBe('Fresh meta description.');
});
