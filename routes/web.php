<?php

use App\Http\Controllers\Admin\PostController;
use App\Http\Controllers\BlogController;
use App\Http\Controllers\ProfileController;
use App\Models\Post;
use Illuminate\Support\Facades\Route;

Route::redirect('/', '/blog')->name('home');

Route::get('/blog', [BlogController::class, 'index'])->name('blog.index');
Route::get('/blog/{post}', [BlogController::class, 'show'])->name('blog.show');
Route::get('/feeds', [BlogController::class, 'feeds'])->name('feeds.index');

Route::get('/sitemap.xml', function () {
    return response()
        ->view('sitemap', [
            'posts' => Post::published()->latest('published_at')->orderByDesc('id')->get(),
        ])
        ->header('Content-Type', 'application/xml');
})->name('sitemap');

Route::get('/rss.xml', function () {
    return response()
        ->view('rss', [
            'posts' => Post::published()->latest('published_at')->orderByDesc('id')->limit(20)->get(),
        ])
        ->header('Content-Type', 'application/rss+xml');
})->name('rss');

Route::get('/feed', function () {
    return response()
        ->view('rss', [
            'posts' => Post::published()->latest('published_at')->orderByDesc('id')->limit(20)->get(),
        ])
        ->header('Content-Type', 'application/rss+xml');
})->name('feed');

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::middleware('auth')->prefix('admin')->name('admin.')->group(function () {
    Route::post('posts/fetch', [PostController::class, 'fetch'])->name('posts.fetch');
    Route::post('fetch-logs/{log}/retry', [PostController::class, 'retry'])->name('fetch-logs.retry');
    Route::patch('posts/{post}/category', [PostController::class, 'updateCategory'])->name('posts.category');
    Route::post('posts/{post}/publish', [PostController::class, 'publish'])->name('posts.publish');
    Route::post('posts/{post}/unpublish', [PostController::class, 'unpublish'])->name('posts.unpublish');
    Route::resource('posts', PostController::class)->except(['show']);
});

require __DIR__.'/auth.php';
