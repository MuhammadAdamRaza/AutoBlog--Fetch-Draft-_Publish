<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use RuntimeException;
use Symfony\Component\DomCrawler\Crawler;

class ArticleFetcher
{
    /**
     * @return array{title: string, body: string, source_url: string}
     */
    public function fetch(string $url): array
    {
        $response = Http::withHeaders([
            'User-Agent' => 'AutoBlogBot/1.0 (editorial draft assistant)',
            'Accept' => 'text/html,application/xhtml+xml',
        ])->connectTimeout(5)->timeout(15)->get($url);

        $response->throw();

        $crawler = new Crawler($response->body());
        $this->removeNoise($crawler);

        $title = $crawler->filter('title')->count()
            ? trim($crawler->filter('title')->text())
            : 'Untitled';

        $selector = $crawler->filter('article p')->count()
            ? 'article p'
            : ($crawler->filter('main p')->count() ? 'main p' : 'p');

        $paragraphs = $crawler->filter($selector)
            ->each(fn (Crawler $node) => trim(preg_replace('/\s+/u', ' ', $node->text()) ?? ''));

        $body = implode("\n\n", array_values(array_filter(
            $paragraphs,
            fn (string $paragraph) => mb_strlen($paragraph) > 40,
        )));

        if ($body === '') {
            throw new RuntimeException('No readable text found at this URL.');
        }

        return [
            'title' => $title !== '' ? $title : 'Untitled',
            'body' => $body,
            'source_url' => $url,
        ];
    }

    private function removeNoise(Crawler $crawler): void
    {
        foreach (['script', 'style', 'noscript', 'svg', 'nav', 'footer', 'aside', 'form'] as $selector) {
            $nodes = [];

            $crawler->filter($selector)->each(function (Crawler $node) use (&$nodes): void {
                if ($node->getNode(0) !== null) {
                    $nodes[] = $node->getNode(0);
                }
            });

            foreach ($nodes as $domNode) {
                $domNode->parentNode?->removeChild($domNode);
            }
        }
    }
}
