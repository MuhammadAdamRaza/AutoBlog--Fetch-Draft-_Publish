<?php

namespace App\Providers;

use App\Contracts\DraftWriter;
use App\Services\OpenRouterDraftWriter;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(DraftWriter::class, OpenRouterDraftWriter::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
