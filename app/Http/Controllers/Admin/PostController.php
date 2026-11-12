<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Jobs\GenerateDraftFromUrl;
use App\Models\Post;
use App\Models\PostFetchLog;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class PostController extends Controller
{
    /** @var array<string, string> */
    public const OPENROUTER_MODELS = [
        'anthropic/claude-3.5-sonnet' => 'Claude 3.5 Sonnet',
        'openai/gpt-4o-mini' => 'GPT-4o Mini',
        'openai/gpt-4o' => 'GPT-4o',
        'meta-llama/llama-3.1-70b-instruct' => 'Llama 3.1 70B',
    ];

    /** @var array<int, string> */
    public const TONES = ['Informative', 'Casual', 'News', 'Technical'];

    public function index(Request $request): View
    {
        $filters = $request->validate([
            'search' => ['nullable', 'string', 'max:100'],
            'status' => ['nullable', Rule::in(['draft', 'published'])],
        ]);

        $posts = Post::query()
            ->when($filters['search'] ?? null, function (Builder $query, string $search): void {
                $query->where(function (Builder $query) use ($search): void {
                    $query->where('title', 'like', '%'.$search.'%')
                        ->orWhere('excerpt', 'like', '%'.$search.'%');
                });
            })
            ->when($filters['status'] ?? null, fn (Builder $query, string $status): Builder => $query->where('status', $status))
            ->latest()
            ->orderByDesc('id')
            ->paginate(12)
            ->withQueryString();

        return view('admin.posts.index', [
            'posts' => $posts,
            'fetchLogs' => PostFetchLog::query()
                ->with('post:id,title,slug,status')
                ->latest()
                ->orderByDesc('id')
                ->limit(8)
                ->get(),
            'postCounts' => [
                'all' => Post::query()->count(),
                'draft' => Post::query()->where('status', 'draft')->count(),
                'published' => Post::query()->where('status', 'published')->count(),
            ],
            'filters' => $filters,
            'categories' => Post::CATEGORIES,
            'models' => self::OPENROUTER_MODELS,
            'tones' => self::TONES,
        ]);
    }

    public function create(): View
    {
        return view('admin.posts.create', [
            'post' => new Post(['category' => 'Tech']),
            'categories' => Post::CATEGORIES,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validatePost($request);

        Post::create([
            ...$data,
            'category' => $data['category'] ?? 'Tech',
            'slug' => Post::uniqueSlugFor($data['title']),
            'status' => 'draft',
        ]);

        return redirect()
            ->route('admin.posts.index')
            ->with('success', 'Draft saved. Publish it when you are ready.');
    }

    public function edit(Post $post): View
    {
        return view('admin.posts.edit', [
            'post' => $post,
            'categories' => Post::CATEGORIES,
        ]);
    }

    public function update(Request $request, Post $post): RedirectResponse
    {
        $data = $this->validatePost($request);

        $post->update([
            ...$data,
            'category' => $data['category'] ?? $post->category ?? 'Tech',
        ]);

        return redirect()
            ->route('admin.posts.index')
            ->with('success', 'Post updated. The public link stays the same.');
    }

    public function destroy(Post $post): RedirectResponse
    {
        $post->delete();

        return redirect()
            ->route('admin.posts.index')
            ->with('success', 'Post deleted.');
    }

    public function publish(Post $post): RedirectResponse
    {
        $post->update([
            'status' => 'published',
            'published_at' => $post->published_at ?? now(),
        ]);

        return back()->with('success', 'Post published.');
    }

    public function unpublish(Post $post): RedirectResponse
    {
        $post->update([
            'status' => 'draft',
        ]);

        return back()->with('success', 'Post moved back to drafts.');
    }

    public function updateCategory(Request $request, Post $post): RedirectResponse
    {
        $data = $request->validate([
            'category' => ['required', 'string', Rule::in(Post::CATEGORIES)],
        ]);

        $post->update([
            'category' => $data['category'],
        ]);

        return back()->with('success', 'Post category updated to '.$data['category'].'.');
    }

    public function fetch(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'url' => ['required', 'url:http,https', 'max:255'],
            'category' => ['nullable', 'string', Rule::in(Post::CATEGORIES)],
            'ai_model' => ['nullable', 'string', Rule::in(array_keys(self::OPENROUTER_MODELS))],
            'tone' => ['nullable', 'string', Rule::in(self::TONES)],
        ]);

        $model = $data['ai_model'] ?? (string) config('services.openrouter.model');
        $tone = $data['tone'] ?? 'Informative';
        $category = $data['category'] ?? 'Tech';

        $log = PostFetchLog::create([
            'source_url' => $data['url'],
            'ai_model' => $model,
            'tone' => $tone,
            'status' => 'queued',
        ]);

        GenerateDraftFromUrl::dispatch($data['url'], $log->id, $model, $tone, $category);

        return back()->with('success', 'Your article is being prepared.');
    }

    public function retry(PostFetchLog $log): RedirectResponse
    {
        $log->update([
            'status' => 'queued',
            'error' => null,
        ]);

        GenerateDraftFromUrl::dispatch(
            $log->source_url,
            $log->id,
            $log->ai_model ?: (string) config('services.openrouter.model'),
            $log->tone ?: 'Informative',
        );

        return back()->with('success', 'Fetch re-queued. Refresh in a moment to check draft status.');
    }

    /**
     * @return array{title: string, category?: ?string, excerpt: ?string, body: string, meta_description: ?string, cover_image?: ?string}
     */
    protected function validatePost(Request $request): array
    {
        return $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'category' => ['nullable', 'string', 'max:50'],
            'excerpt' => ['nullable', 'string', 'max:500'],
            'body' => ['required', 'string'],
            'meta_description' => ['nullable', 'string', 'max:160'],
            'cover_image' => ['nullable', 'url:http,https', 'max:500'],
        ]);
    }
}
