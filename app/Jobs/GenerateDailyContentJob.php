<?php
namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\ContentPillar;
use App\Models\ContentItem;
use App\Services\AI\AIProviderFactory;
use Illuminate\Support\Facades\Log;

class GenerateDailyContentJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries   = 3;
    public int $timeout = 120;

    public function __construct(
        public readonly string $dayOfWeek,
        public readonly string $provider = 'groq'
    ) {}

    public function handle(): void
    {
        $pillar = ContentPillar::where('day_of_week', $this->dayOfWeek)
            ->where('is_active', true)
            ->first();

        if (!$pillar) {
            Log::warning("No active pillar found for {$this->dayOfWeek}");
            return;
        }

        Log::info("Generating daily content for pillar: {$pillar->name}");

        $ai    = AIProviderFactory::make($this->provider);
        $types = ['linkedin', 'twitter', 'email', 'case_study', 'strategy_deck', 'guide'];
        
        // Fetch news context once to avoid redundant DB queries in the loop
        $newsContext = $this->getNewsContext();

        foreach ($types as $type) {
            try {
                $segment = match($this->dayOfWeek) {
                    'Monday', 'Tuesday'              => 'hedge_fund',
                    'Wednesday', 'Thursday'          => 'bank',
                    'Friday', 'Saturday', 'Sunday'   => 'market_maker',
                    default                          => 'hedge_fund',
                };
                $optRules = $this->getOptimizationRules($type);
                $prompt   = $this->buildPrompt($pillar, $type, $segment, $newsContext . $optRules);
                $content  = $ai->generate($prompt, 1000);

                ContentItem::create([
                    'content_pillar_id' => $pillar->id,
                    'type'              => $type,
                    'ai_provider'       => $ai->getProviderName(),
                    'ai_model'          => $ai->getModel(),
                    'prompt_used'       => $prompt,
                    'content'           => $content,
                    'status'            => 'draft',
                ]);

                Log::info("Generated {$type} content for {$pillar->name}");

            } catch (\Exception $e) {
                Log::error("Failed to generate {$type} for {$pillar->name}: " . $e->getMessage());
            }
        }
    }

    private function buildPrompt(ContentPillar $pillar, string $type, string $segment, string $newsContext = ''): string
    {
        $segmentFocus = match($segment) {
            'hedge_fund'   => "Target Audience: Hedge Funds. Focus heavily on execution quality, tight spreads, DeFi yield strategies, and deep liquidity.",
            'bank'         => "Target Audience: Banks. Focus heavily on regulatory compliance (FINMA), white-labeling APIs, risk management, and security.",
            'market_maker' => "Target Audience: Market Makers. Focus heavily on zero-latency infrastructure, API stability, and robust connectivity.",
            default        => "Target Audience: Institutional Investors."
        };

        $day     = $this->dayOfWeek;
        $context = "Content pillar: {$pillar->name}. {$pillar->description}. CTA: {$pillar->primary_cta}";

        return match($type) {
            'linkedin' => "Write a high-impact LinkedIn post for SCRYPT.
            {$segmentFocus}
            Today is {$day} — {$context}{$newsContext}
            Requirements: Sharp hook in line 1. 3-4 paragraphs max.
            Include 1-2 real SCRYPT data points (\$25B+ volume, 300+ clients,
            40+ jurisdictions, FINMA/VQF licensed, Gauntlet DeFi partnership Feb 2026,
            OKX integration Dec 2025, \$5M Braza Bank investment).
            If today's market context above is relevant, reference it naturally.
            End with CTA. Institutional tone. No emojis. No retail language. Max 280 words.",

            'twitter'  => "Write a 5-tweet thread for SCRYPT on X (Twitter).
            {$segmentFocus}
            Today is {$day} — {$context}{$newsContext}
            Requirements: Tweet 1 is a bold hook that stops the scroll.
            Tweets 2-4 are data-driven insights, one point each.
            If today's market context is relevant, weave it into tweets 2-4.
            Tweet 5 is a clear CTA. Each tweet under 280 characters.
            Max 2 hashtags in the entire thread. Sharp, institutional tone.",

            'email'    => "Write an institutional email newsletter for SCRYPT.
            {$segmentFocus}
            Today is {$day} — {$context}{$newsContext}
            Requirements: Write a compelling subject line first (prefix with 'Subject: ').
            Then 400-500 words. Structure: context → problem → SCRYPT solution → data → CTA.
            If today's market context is relevant, open with it as the news hook.
            Authoritative tone. No fluff.",

            'case_study' => "Write an institutional case study for SCRYPT.
            {$segmentFocus}
            Today is {$day} — {$context}{$newsContext}
            Requirements: Format as a Markdown document. Structure: 
            ## The Problem
            ## The SCRYPT Solution
            ## The Outcome
            Include real data points (\$25B+ volume, 300+ clients, 40+ jurisdictions, Gauntlet DeFi integration).
            Authoritative, data-driven tone.",

            'strategy_deck' => "Write the outline and narrative copy for an institutional Strategy Deck regarding SCRYPT.
            {$segmentFocus}
            Today is {$day} — {$context}{$newsContext}
            Requirements: Format as a Markdown document with 'Slide 1:', 'Slide 2:', etc. 
            Highlight FINMA regulation, deep liquidity, and execution quality. 
            Include a clear CTA for allocations or API integration.",

            'guide' => "Write a comprehensive downloadable guide for SCRYPT.
            {$segmentFocus}
            Today is {$day} — {$context}{$newsContext}
            Requirements: Format as a Markdown document. Provide deep, actionable insights on institutional crypto, regulation (FINMA/VQF), and market structure.
            Tone: Executive, sharp, authoritative.",

            default    => "Write a LinkedIn post for SCRYPT about {$pillar->name}. {$context}{$newsContext}",
        };
    }


    private function getNewsContext(): string
    {
        $insights = \App\Models\MarketInsight::recent(24)
            ->where('relevance_score', '>', 0)
            ->where('category', '!=', 'ai_analysis')
            ->topRelevant(3)
            ->get();

        if ($insights->isEmpty()) return '';

        $lines = $insights->map(fn($i) =>
            "- [{$i->source}] {$i->headline}"
        )->implode("\n");

        return "\n\nTODAY'S MARKET CONTEXT (use this to make content timely and relevant):\n{$lines}\n";
    }

    private function getOptimizationRules(string $type): string
    {
        return (new \App\Services\Content\ContentOptimizationService())
            ->getActiveRules($type);
    }
}