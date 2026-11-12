<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\View\View;

class BlogController extends Controller
{
    public function index(Request $request): View
    {
        $query = $request->query('q');
        $search = is_string($query) ? trim($query) : '';
        $categoryQuery = $request->query('category');
        $category = is_string($categoryQuery) ? trim($categoryQuery) : '';

        $posts = Post::published()
            ->search($search)
            ->when($category !== '', fn (Builder $builder) => $builder->where('category', $category))
            ->latest('published_at')
            ->orderByDesc('id')
            ->paginate(10)
            ->withQueryString();

        return view('blog.index', [
            'publishedCount' => Post::published()->count(),
            'featuredPost' => ($search === '' && $category === '' && $posts->currentPage() === 1) ? $posts->first() : null,
            'posts' => $posts,
            'search' => $search,
            'activeCategory' => $category,
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
