<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Jobs\GenerateDraftFromUrl;
use App\Models\Post;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class PostController extends Controller
{
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
            'postCounts' => [
                'all' => Post::query()->count(),
                'draft' => Post::query()->where('status', 'draft')->count(),
                'published' => Post::query()->where('status', 'published')->count(),
            ],
            'filters' => $filters,
        ]);
    }

    public function create(): View
    {
        return view('admin.posts.create', [
            'post' => new Post,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validatePost($request);

        Post::create([
            ...$data,
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
        ]);
    }

    public function update(Request $request, Post $post): RedirectResponse
    {
        $post->update($this->validatePost($request));

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

    public function fetch(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'url' => ['required', 'url:http,https', 'max:255'],
        ]);

        GenerateDraftFromUrl::dispatch($data['url']);

        return back()->with('success', 'Your article is being prepared.');
    }

    /**
     * @return array{title: string, excerpt: ?string, body: string, meta_description: ?string}
     */
    protected function validatePost(Request $request): array
    {
        return $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'excerpt' => ['nullable', 'string', 'max:500'],
            'body' => ['required', 'string'],
            'meta_description' => ['nullable', 'string', 'max:160'],
        ]);
    }
}
