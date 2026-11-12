<?php

namespace App\Models;

use Database\Factories\PostFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Post extends Model
{
    /** @use HasFactory<PostFactory> */
    use HasFactory;

    /** @var array<int, string> */
    public const CATEGORIES = ['Tech', 'AI', 'Business', 'Guides', 'Tutorials', 'News'];

    protected $fillable = [
        'title',
        'slug',
        'category',
        'excerpt',
        'body',
        'meta_description',
        'cover_image',
        'source_url',
        'source_body',
        'ai_model',
        'tone',
        'status',
        'published_at',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'published_at' => 'datetime',
        ];
    }

    public function scopePublished(Builder $query): void
    {
        $query->where('status', 'published')
            ->whereNotNull('published_at')
            ->where('published_at', '<=', now());
    }

    public function scopeSearch(Builder $query, ?string $term): void
    {
        if (! filled($term)) {
            return;
        }

        $escapedTerm = str_replace(['!', '%', '_'], ['!!', '!%', '!_'], $term);
        $like = '%'.$escapedTerm.'%';

        $query->where(function (Builder $query) use ($like): void {
            $query->whereRaw("title LIKE ? ESCAPE '!'", [$like])
                ->orWhereRaw("excerpt LIKE ? ESCAPE '!'", [$like])
                ->orWhereRaw("body LIKE ? ESCAPE '!'", [$like]);
        });
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function readingMinutes(): int
    {
        $words = Str::wordCount(strip_tags($this->body));

        return max(1, (int) ceil($words / 200));
    }

    /**
     * @return Collection<int, static>
     */
    public function related(int $limit = 3): Collection
    {
        $sameCategory = static::published()
            ->whereKeyNot($this->getKey())
            ->where('category', $this->category ?: 'Tech')
            ->latest('published_at')
            ->orderByDesc('id')
            ->limit($limit)
            ->get();

        if ($sameCategory->count() >= $limit) {
            return $sameCategory;
        }

        $fallback = static::published()
            ->whereKeyNot($this->getKey())
            ->whereNotIn('id', $sameCategory->pluck('id'))
            ->latest('published_at')
            ->orderByDesc('id')
            ->limit($limit - $sameCategory->count())
            ->get();

        return $sameCategory->concat($fallback);
    }

    public function coverUrl(): ?string
    {
        return filled($this->cover_image) ? $this->cover_image : null;
    }

    public function isPublished(): bool
    {
        return $this->status === 'published'
            && $this->published_at !== null
            && $this->published_at->lte(now());
    }

    public static function uniqueSlugFor(string $title, ?int $ignoreId = null): string
    {
        $base = Str::slug($title) ?: 'post';
        $slug = $base;
        $i = 2;

        while (static::query()
            ->where('slug', $slug)
            ->when($ignoreId, fn (Builder $query) => $query->where('id', '!=', $ignoreId))
            ->exists()) {
            $slug = $base.'-'.$i++;
        }

        return $slug;
    }
}
