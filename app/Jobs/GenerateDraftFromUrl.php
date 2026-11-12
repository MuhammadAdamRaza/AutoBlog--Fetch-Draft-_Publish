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

    public function __construct(
        public string $url,
        public ?int $fetchLogId = null,
        public ?string $model = null,
        public ?string $tone = null,
        public ?string $category = null,
    ) {}

    public function handle(ArticleFetcher $fetcher, DraftWriter $writer): void
    {
        $log = $this->fetchLogId !== null ? PostFetchLog::find($this->fetchLogId) : null;

        if ($log !== null) {
            $log->update(['status' => 'processing', 'error' => null]);
        }

        $article = $fetcher->fetch($this->url);
        $draft = $writer->write([
            ...$article,
            ...($this->model !== null ? ['model' => $this->model] : []),
            ...($this->tone !== null ? ['tone' => $this->tone] : []),
        ]);

        $post = Post::create([
            'title' => $draft['title'],
            'slug' => Post::uniqueSlugFor($draft['title']),
            'category' => $this->category ?: 'Tech',
            'excerpt' => $draft['excerpt'],
            'body' => $draft['body'],
            'meta_description' => $draft['meta_description'],
            'source_url' => $this->url,
            'source_body' => $article['body'],
            'ai_model' => $this->model,
            'tone' => $this->tone,
            'status' => 'draft',
        ]);

        if ($log !== null) {
            $log->update([
                'post_id' => $post->id,
                'status' => 'success',
                'error' => null,
            ]);
        } else {
            PostFetchLog::create([
                'post_id' => $post->id,
                'source_url' => $this->url,
                'ai_model' => $this->model,
                'tone' => $this->tone,
                'status' => 'success',
            ]);
        }
    }

    public function failed(Throwable $e): void
    {
        $log = $this->fetchLogId !== null ? PostFetchLog::find($this->fetchLogId) : null;

        if ($log !== null) {
            $log->update([
                'status' => 'failed',
                'error' => $e->getMessage(),
            ]);

            return;
        }

        PostFetchLog::create([
            'source_url' => $this->url,
            'ai_model' => $this->model,
            'tone' => $this->tone,
            'status' => 'failed',
            'error' => $e->getMessage(),
        ]);
    }
}
