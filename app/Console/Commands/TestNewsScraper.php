<?php
namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\Research\NewsScraperService;
use App\Models\MarketInsight;

class TestNewsScraper extends Command
{
    protected $signature   = 'scrypt:test-scraper {--provider=groq}';
    protected $description = 'Test the market news scraper and AI summarisation';

    public function handle(): void
    {
        $this->info('🔍 Testing SCRYPT Research Engine...');
        $this->newLine();

        $scraper = new NewsScraperService();

        $this->info('📡 Scraping RSS feeds...');
        $rawItems = $scraper->scrapeAll();

        $this->info('Found ' . count($rawItems) . ' raw items.');
        $this->newLine();

        if (empty($rawItems)) {
            $this->error('No items found. Check your internet connection or feed URLs.');
            return;
        }

        // Show top 5 by relevance
        usort($rawItems, fn($a, $b) => $b['relevance_score'] - $a['relevance_score']);
        $this->info('📊 Top items by relevance:');
        $this->table(
            ['Score', 'Source', 'Headline'],
            collect(array_slice($rawItems, 0, 5))->map(fn($i) => [
                $i['relevance_score'],
                $i['source'],
                \Illuminate\Support\Str::limit($i['headline'], 70),
            ])->toArray()
        );

        $this->newLine();
        $this->info('🤖 Running AI summarisation with ' . $this->option('provider') . '...');

        $result = $scraper->summariseWithAI($rawItems, $this->option('provider'));

        if ($result['analysis']) {
            $this->newLine();
            $this->line('─────────────────────────────────────────');
            $this->line($result['analysis']);
            $this->line('─────────────────────────────────────────');
        }

        $this->newLine();
        $this->info('💾 Saving insights to database...');
        $saved = $scraper->saveInsights($result['raw_items'], $result['analysis']);
        $this->info("Saved {$saved} new insights.");

        $total = MarketInsight::recent(24)->count();
        $this->info("Total insights in DB (last 24h): {$total}");

        $this->newLine();
        $this->info('✅ Research Engine working. Daily content will now include live market context.');
    }
}