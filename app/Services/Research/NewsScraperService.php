<?php
namespace App\Services\Research;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Models\MarketInsight;
use App\Services\AI\AIProviderFactory;

class NewsScraperService
{
    private array  $feeds;
    private array  $keywords;
    private int    $maxItemsPerFeed = 5;

    public function __construct()
    {
        $this->feeds    = config('ai.news_feeds', []);
        $this->keywords = config('ai.relevance_keywords', []);
    }

    public function scrapeAll(): array
    {
        $allItems = [];

        foreach ($this->feeds as $feed) {
            try {
                $items = $this->scrapeFeed($feed);
                $allItems = array_merge($allItems, $items);
                Log::info("Scraped {$feed['name']}: " . count($items) . " items");
            } catch (\Exception $e) {
                Log::error("Failed to scrape {$feed['name']}: " . $e->getMessage());
            }
        }

        return $allItems;
    }

    private function scrapeFeed(array $feed): array
    {
        $response = Http::timeout(15)
            ->withHeaders(['User-Agent' => 'SCRYPT-MarketingAgent/1.0'])
            ->get($feed['url']);

        if ($response->failed()) {
            throw new \Exception("HTTP {$response->status()} for {$feed['url']}");
        }

        return $this->parseRss($response->body(), $feed);
    }

    private function parseRss(string $xml, array $feed): array
    {
        $items = [];

        try {
            $doc = new \SimpleXMLElement($xml);
            $entries = $doc->channel->item ?? $doc->entry ?? [];

            $count = 0;
            foreach ($entries as $entry) {
                if ($count >= $this->maxItemsPerFeed) break;

                $headline = trim((string) ($entry->title ?? ''));
                $link     = trim((string) ($entry->link ?? $entry->id ?? ''));
                $desc     = trim(strip_tags((string) ($entry->description
                    ?? $entry->summary
                    ?? $entry->children('content', true)->encoded
                    ?? '')));
                $pubDate  = trim((string) ($entry->pubDate ?? $entry->published ?? ''));

                if (empty($headline)) continue;

                $relevanceScore = $this->scoreRelevance($headline . ' ' . $desc);

                // Only keep items with some relevance
                if ($relevanceScore < 1) {
                    $count++;
                    continue;
                }

                $items[] = [
                    'headline'      => $headline,
                    'description'   => $desc ? substr($desc, 0, 500) : $headline,
                    'source'        => $feed['name'],
                    'source_url'    => $link,
                    'category'      => $feed['category'],
                    'relevance_score' => $relevanceScore,
                    'published_at'  => $pubDate ? now()->parse($pubDate) : now(),
                ];

                $count++;
            }
        } catch (\Exception $e) {
            Log::warning("RSS parse error for {$feed['name']}: " . $e->getMessage());
        }

        return $items;
    }

    private function scoreRelevance(string $text): int
    {
        $text  = strtolower($text);
        $score = 0;

        foreach ($this->keywords as $keyword) {
            if (str_contains($text, strtolower($keyword))) {
                $score++;
            }
        }

        return $score;
    }

    public function summariseWithAI(array $rawItems, string $provider = 'groq'): array
    {
        if (empty($rawItems)) return [];

        // Sort by relevance and take top 10
        usort($rawItems, fn($a, $b) => $b['relevance_score'] - $a['relevance_score']);
        $topItems = array_slice($rawItems, 0, 10);

        $headlines = collect($topItems)
            ->map(fn($i, $k) => ($k+1) . ". [{$i['source']}] {$i['headline']}")
            ->implode("\n");

        $prompt = "You are the market intelligence analyst for SCRYPT (scrypt.swiss), 
            Switzerland's leading institutional crypto execution platform.

            Here are today's top crypto/institutional finance headlines:
            {$headlines}

            For each headline that is relevant to institutional crypto investors, provide:
            - A 1-sentence institutional insight (what it means for banks, hedge funds, family offices)
            - A relevance rating: HIGH / MEDIUM / LOW for SCRYPT's target audience
            - Skip headlines with LOW relevance entirely

            Format your response as a numbered list. Be concise and analytical. 
            Focus on regulatory changes, DeFi developments, institutional adoption, 
            custody, yield, and compliance topics. Ignore retail price speculation.";

        try {
            $ai       = AIProviderFactory::make($provider);
            $analysis = $ai->generate($prompt, 800);

            return [
                'raw_items' => $topItems,
                'analysis'  => $analysis,
            ];
        } catch (\Exception $e) {
            Log::error("AI summarisation failed: " . $e->getMessage());
            return ['raw_items' => $topItems, 'analysis' => null];
        }
    }

    public function saveInsights(array $rawItems, ?string $analysis): int
    {
        $saved = 0;

        foreach ($rawItems as $item) {
            // Skip duplicates by headline
            $exists = MarketInsight::where('headline', $item['headline'])
                ->where('created_at', '>=', now()->subDay())
                ->exists();

            if ($exists) continue;

            MarketInsight::create([
                'headline'        => $item['headline'],
                'summary'         => $item['description'],
                'source'          => $item['source'],
                'source_url'      => $item['source_url'],
                'category'        => $item['category'],
                'relevance_score' => $item['relevance_score'],
                'published_at'    => $item['published_at'],
            ]);

            $saved++;
        }

        // Save AI analysis as a meta-insight if we got one
        if ($analysis) {
            MarketInsight::create([
                'headline'        => 'Daily AI Market Analysis — ' . now()->format('M j, Y'),
                'summary'         => $analysis,
                'source'          => 'SCRYPT AI Engine',
                'category'        => 'ai_analysis',
                'relevance_score' => 99,
            ]);
        }

        return $saved;
    }
}