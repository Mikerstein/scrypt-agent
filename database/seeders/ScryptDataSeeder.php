<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\AiProvider;
use App\Models\ContentPillar;
use App\Models\Lead;
use App\Models\KpiMetric;
use App\Models\OptimizationRule;

class ScryptDataSeeder extends Seeder
{
    public function run(): void
    {
        $this->seedAiProviders();
        $this->seedContentPillars();
        $this->seedLeads();
        $this->seedKpiMetrics();
        $this->command->info('✅ SCRYPT data seeded successfully.');
        $this->seedOptimizationRules();
    }

    private function seedAiProviders(): void
    {
        $providers = [
            ['name' => 'groq',      'model' => 'llama-3.3-70b-versatile',    'is_active' => true],
            ['name' => 'anthropic', 'model' => 'claude-sonnet-4-20250514',    'is_active' => false],
            ['name' => 'openai',    'model' => 'gpt-4o',                      'is_active' => false],
            ['name' => 'gemini', 'model' => 'gemini-2.0-flash', 'is_active' => true],
        ];

        foreach ($providers as $provider) {
            AiProvider::firstOrCreate(['name' => $provider['name']], $provider);
        }

        $this->command->info('   AI providers seeded.');
    }

    private function seedContentPillars(): void
    {
        $pillars = [
            [
                'name'        => 'Regulatory Authority',
                'slug'        => 'regulatory-authority',
                'day_of_week' => 'Monday',
                'description' => 'Deep dives into FINMA, MiCA, VQF compliance. Position SCRYPT as the most regulated institutional crypto platform in Europe.',
                'primary_cta' => 'Download our institutional compliance guide at scrypt.swiss',
                'is_active'   => true,
            ],
            [
                'name'        => 'Social Proof',
                'slug'        => 'social-proof',
                'day_of_week' => 'Tuesday',
                'description' => 'Partnership announcements, client wins, and credibility signals. Highlight OKX, Gauntlet, Braza Bank, Archax, BCB Group partnerships.',
                'primary_cta' => 'Book a call with our institutional team at scrypt.swiss',
                'is_active'   => true,
            ],
            [
                'name'        => 'Yield & DeFi',
                'slug'        => 'yield-defi',
                'day_of_week' => 'Wednesday',
                'description' => 'Institutional yield strategies, DeFi access via Gauntlet, staking. 4-12% annualised on stablecoins. $103.8B DeFi TVL now accessible.',
                'primary_cta' => 'Request our institutional DeFi strategy deck at scrypt.swiss',
                'is_active'   => true,
            ],
            [
                'name'        => 'Market Intelligence',
                'slug'        => 'market-intelligence',
                'day_of_week' => 'Thursday',
                'description' => 'Weekly institutional crypto market report. Trends, regulatory developments, macro signals relevant to institutional allocators.',
                'primary_cta' => 'Subscribe to our weekly institutional report at scrypt.swiss',
                'is_active'   => true,
            ],
            [
                'name'        => 'Conversion',
                'slug'        => 'conversion',
                'day_of_week' => 'Friday',
                'description' => 'Case studies, ROI breakdowns, execution quality proof. Show $25B+ volume, 300+ clients, 40+ jurisdictions. Drive pipeline.',
                'primary_cta' => 'Talk to our execution team at scrypt.swiss',
                'is_active'   => true,
            ],
            [
                'name'        => 'Founder Authority',
                'slug'        => 'founder-authority',
                'day_of_week' => 'Saturday',
                'description' => 'Contrarian POVs and thought leadership from Norman Wooding (CEO) and Sylvan Martin (CGO). Industry insights, not product pitches.',
                'primary_cta' => 'Follow Norman Wooding and Sylvan Martin on LinkedIn',
                'is_active'   => true,
            ],
            [
                'name'        => 'Distribution & Engagement',
                'slug'        => 'distribution-engagement',
                'day_of_week' => 'Sunday',
                'description' => 'Repurpose top content, engage high-intent users, warm leads via replies and DMs. No new content — amplify what already worked.',
                'primary_cta' => 'Engage warm leads and reply to high-intent comments',
                'is_active'   => true,
            ],
        ];

        foreach ($pillars as $pillar) {
            ContentPillar::firstOrCreate(['slug' => $pillar['slug']], $pillar);
        }

        $this->command->info('   Content pillars seeded (7 days).');
    }

    private function seedLeads(): void
    {
        $leads = [
            [
                'name'    => 'James Thornton',
                'email'   => 'j.thornton@alphahedge.com',
                'company' => 'Alpha Hedge Capital',
                'title'   => 'Head of Digital Assets',
                'segment' => 'hedge_fund',
                'source'  => 'linkedin',
                'status'  => 'qualified',
                'notes'   => 'Interested in OTC execution and DeFi yield. Managing $400M AUM. Wants FINMA-compliant wrapper.',
            ],
            [
                'name'    => 'Sophie Berger',
                'email'   => 's.berger@privatebank-geneva.ch',
                'company' => 'Banque Privée Genève',
                'title'   => 'Chief Investment Officer',
                'segment' => 'bank',
                'source'  => 'email',
                'status'  => 'meeting',
                'notes'   => 'Swiss private bank exploring crypto allocation for UHNW clients. Needs white-label custody solution.',
            ],
            [
                'name'    => 'Marco Alencar',
                'email'   => 'm.alencar@brazilcapital.com.br',
                'company' => 'Brazil Capital Gestora',
                'title'   => 'Portfolio Manager',
                'segment' => 'family_office',
                'source'  => 'twitter',
                'status'  => 'contacted',
                'notes'   => 'LATAM family office. Intro via Braza Bank network. Interested in Bitcoin Growth strategy.',
            ],
            [
                'name'    => 'Ravi Menon',
                'email'   => 'ravi@finflowpay.io',
                'company' => 'FinFlow Payments',
                'title'   => 'CEO',
                'segment' => 'fintech',
                'source'  => 'linkedin',
                'status'  => 'new',
                'notes'   => 'Fintech needing stablecoin FX rails. USDC/USDT conversion across USD, EUR, AED corridors.',
            ],
            [
                'name'    => 'Anna Kowalski',
                'email'   => 'a.kowalski@menadigital.ae',
                'company' => 'MENA Digital Assets',
                'title'   => 'Head of Treasury',
                'segment' => 'bank',
                'source'  => 'email',
                'status'  => 'qualified',
                'notes'   => 'UAE-based. Interested in institutional custody and staking yield. VARA-regulated environment.',
            ],
        ];

        foreach ($leads as $lead) {
            Lead::firstOrCreate(['email' => $lead['email']], $lead);
        }

        $this->command->info('   Sample leads seeded (5 institutional prospects).');
    }

    private function seedKpiMetrics(): void
    {
        $today = now()->toDateString();

        $metrics = [
            ['metric_type' => 'leads',       'platform' => 'overall',  'value' => 5,   'recorded_date' => $today, 'period' => 'daily'],
            ['metric_type' => 'impressions',  'platform' => 'linkedin', 'value' => 0,   'recorded_date' => $today, 'period' => 'daily'],
            ['metric_type' => 'impressions',  'platform' => 'twitter',  'value' => 0,   'recorded_date' => $today, 'period' => 'daily'],
            ['metric_type' => 'meetings',     'platform' => 'overall',  'value' => 1,   'recorded_date' => $today, 'period' => 'daily'],
            ['metric_type' => 'conversions',  'platform' => 'overall',  'value' => 0,   'recorded_date' => $today, 'period' => 'daily'],
            ['metric_type' => 'engagement',   'platform' => 'linkedin', 'value' => 0,   'recorded_date' => $today, 'period' => 'daily'],
        ];

        foreach ($metrics as $metric) {
            KpiMetric::firstOrCreate(
                ['metric_type' => $metric['metric_type'], 'platform' => $metric['platform'], 'recorded_date' => $today],
                $metric
            );
        }

        $this->command->info('   KPI baseline metrics seeded.');
    }

    private function seedOptimizationRules(): void
{
    $rules = [
        [
            'rule_type'        => 'hook',
            'platform'         => 'linkedin',
            'instruction'      => 'Open with a specific data point or contrarian statement. Never start with "I" or "We". Lead with the insight, not the company.',
            'weight'           => 3,
            'source'           => 'manual',
            'confidence_score' => 0.90,
            'evidence'         => 'Institutional LinkedIn best practice: data-led hooks outperform brand-led hooks.',
        ],
        [
            'rule_type'        => 'hook',
            'platform'         => 'twitter',
            'instruction'      => 'First tweet must create tension or make a bold claim. Use numbers when possible. Under 180 characters for maximum retweet potential.',
            'weight'           => 3,
            'source'           => 'manual',
            'confidence_score' => 0.85,
            'evidence'         => 'Twitter engagement data shows numeric hooks get 2x more engagement.',
        ],
        [
            'rule_type'        => 'tone',
            'platform'         => 'all',
            'instruction'      => 'Write as an insider, not a marketer. Use "institutional investors" not "customers". Use "mandate" not "plan". Use "yield" not "returns". Avoid superlatives.',
            'weight'           => 4,
            'source'           => 'manual',
            'confidence_score' => 0.95,
            'evidence'         => 'SCRYPT brand voice guidelines: institutional language builds credibility.',
        ],
        [
            'rule_type'        => 'cta',
            'platform'         => 'linkedin',
            'instruction'      => 'CTAs that reference a specific next step (book a call, download deck) outperform generic ones. Always include scrypt.swiss. Make the CTA feel like a logical conclusion, not a sales pitch.',
            'weight'           => 2,
            'source'           => 'manual',
            'confidence_score' => 0.80,
            'evidence'         => 'LinkedIn B2B data: specific CTAs convert 3x vs generic.',
        ],
        [
            'rule_type'        => 'topic',
            'platform'         => 'all',
            'instruction'      => 'Prioritise regulatory clarity content (FINMA, MiCA, VQF) as this is SCRYPT\'s core differentiator. Compliance-focused posts attract the highest-quality institutional leads.',
            'weight'           => 3,
            'source'           => 'manual',
            'confidence_score' => 0.88,
            'evidence'         => 'SCRYPT competitive moat: Swiss FINMA licence is unique vs competitors.',
        ],
        [
            'rule_type'        => 'format',
            'platform'         => 'email',
            'instruction'      => 'Subject lines with specific numbers or questions outperform generic ones. Always reference a concrete event or data point in the subject line.',
            'weight'           => 2,
            'source'           => 'manual',
            'confidence_score' => 0.82,
            'evidence'         => 'B2B email: numeric subject lines get 15% higher open rates.',
        ],
    ];

    foreach ($rules as $rule) {
        OptimizationRule::firstOrCreate(
            ['rule_type' => $rule['rule_type'], 'platform' => $rule['platform']],
            $rule
        );
    }

    $this->command->info('   Optimization rules seeded (6 baseline rules).');
}
}