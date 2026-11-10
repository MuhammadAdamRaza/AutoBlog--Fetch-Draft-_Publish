<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\View\View;

class BlogController extends Controller
{
    public function index(Request $request): View
    {
        $query = $request->query('q');
        $search = is_string($query) ? trim($query) : '';

        return view('blog.index', [
            'publishedCount' => Post::published()->count(),
            'posts' => Post::published()
                ->search($search)
                ->latest('published_at')
                ->orderByDesc('id')
                ->paginate(10)
                ->withQueryString(),
            'search' => $search,
        ]);
    }

    public function feeds(): View
    {
        return view('feeds.index');
    }

    public function show(Post $post): View
    {
        abort_unless($post->isPublished(), 404);

        return view('blog.show', [
            'post' => $post,
            'related' => $post->related(),
        ]);
    }
}
