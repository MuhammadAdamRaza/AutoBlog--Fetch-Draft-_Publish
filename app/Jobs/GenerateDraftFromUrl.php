<?php

namespace App\Jobs;

use App\Contracts\DraftWriter;
use App\Models\Post;
use App\Models\PostFetchLog;
use App\Services\ArticleFetcher;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Throwable;

class GenerateDraftFromUrl implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;

    public int $timeout = 100;

    /** @var array<int, int> */
    public array $backoff = [10, 60, 300];

    public function __construct(public string $url) {}

    public function handle(ArticleFetcher $fetcher, DraftWriter $writer): void
    {
        $article = $fetcher->fetch($this->url);
        $draft = $writer->write($article);

        $post = Post::create([
            'title' => $draft['title'],
            'slug' => Post::uniqueSlugFor($draft['title']),
            'excerpt' => $draft['excerpt'],
            'body' => $draft['body'],
            'meta_description' => $draft['meta_description'],
            'source_url' => $this->url,
            'status' => 'draft',
        ]);

        PostFetchLog::create([
            'post_id' => $post->id,
            'source_url' => $this->url,
            'status' => 'success',
        ]);
    }

    public function failed(Throwable $e): void
    {
        PostFetchLog::create([
            'source_url' => $this->url,
            'status' => 'failed',
            'error' => $e->getMessage(),
        ]);
    }
}
