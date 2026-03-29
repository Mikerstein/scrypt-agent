<?php
namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Services\Research\NewsScraperService;
use App\Services\Research\CompetitorMonitorService;
use Illuminate\Support\Facades\Log;

class ScrapeMarketNewsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries   = 3;
    public int $timeout = 180;

    public function __construct(public readonly string $provider = 'groq') {}

    public function handle(): void
    {
        Log::info('Starting market news & competitor scrape...');

        // 1. General News Scrape
        $scraper  = new NewsScraperService();
        $rawItems = $scraper->scrapeAll();

        Log::info('Scraped ' . count($rawItems) . ' raw items across all feeds.');

        if (!empty($rawItems)) {
            $result = $scraper->summariseWithAI($rawItems, $this->provider);
            $saved  = $scraper->saveInsights($result['raw_items'], $result['analysis'] ?? null);
            Log::info("Market news scrape complete. Saved {$saved} new insights.");
        }

        // 2. Competitor Monitor Scrape
        $competitorMonitor = new CompetitorMonitorService();
        $compItems         = $competitorMonitor->monitorAll();

        Log::info('Scraped ' . count($compItems) . ' competitor updates.');

        if (!empty($compItems)) {
            $compResult = $competitorMonitor->summariseWithAI($compItems, $this->provider);
            $compSaved  = $competitorMonitor->saveInsights($compResult['raw_items'], $compResult['analysis'] ?? null);
            Log::info("Competitor scrape complete. Saved {$compSaved} new insights.");
        }
    }
}