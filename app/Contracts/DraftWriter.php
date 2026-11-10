<?php

namespace App\Contracts;

interface DraftWriter
{
    /**
     * Rewrite an article into a blog draft.
     *
     * @param array{title: string, body: string, source_url: string} $article
     * @return array{title: string, excerpt: string, body: string, meta_description: string}
     */
    public function write(array $article): array;
}
