<?php
namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\AI\AIProviderFactory;
use App\Models\ContentItem;
use App\Models\ContentPillar;

class TestContentGeneration extends Command
{
    protected $signature   = 'scrypt:test-generate {--provider=groq} {--type=linkedin}';
    protected $description = 'Test AI content generation for SCRYPT';

    public function handle(): void
    {
        $provider = $this->option('provider');
        $type     = $this->option('type');

        $this->info("🚀 Testing SCRYPT content generation...");
        $this->info("   Provider : {$provider}");
        $this->info("   Type     : {$type}");
        $this->newLine();

        // Build prompt based on content type
        $prompt = $this->buildPrompt($type);

        $this->info("📝 Prompt sent to AI:");
        $this->line($prompt);
        $this->newLine();

        try {
            $ai      = AIProviderFactory::make($provider);
            $this->info("⏳ Generating content...");
            $content = $ai->generate($prompt, 800);

            $this->info("✅ Content generated successfully!");
            $this->newLine();
            $this->line("─────────────────────────────────────────");
            $this->line($content);
            $this->line("─────────────────────────────────────────");
            $this->newLine();

            // Save to database
            $pillar = ContentPillar::first();

            $item = ContentItem::create([
                'content_pillar_id' => $pillar?->id ?? 1,
                'type'              => $type,
                'ai_provider'       => $ai->getProviderName(),
                'ai_model'          => $ai->getModel(),
                'prompt_used'       => $prompt,
                'content'           => $content,
                'status'            => 'draft',
            ]);

            $this->info("💾 Saved to database with ID: {$item->id}");
            $this->info("   Model used : {$ai->getModel()}");
            $this->info("   Status     : draft");
            $this->newLine();
            $this->info("🎯 AI layer is working. Ready to build the full system.");

        } catch (\Exception $e) {
            $this->error("❌ Generation failed: " . $e->getMessage());
        }
    }

    private function buildPrompt(string $type): string
    {
        return match($type) {

            'linkedin' => "Write a high-impact LinkedIn post for SCRYPT targeting institutional investors 
                (hedge funds, banks, family offices). 

                Topic: SCRYPT's February 2026 partnership with Gauntlet — the leading quantitative DeFi 
                risk manager — now gives European institutional investors compliant, auditable access to 
                DeFi yield strategies for the first time. Total DeFi TVL exceeds \$103.8 billion but has 
                been inaccessible to regulated institutions until now.

                Requirements:
                - Hook in the first line (no fluff, make it sharp)
                - 3-4 short paragraphs maximum
                - Include 1-2 specific data points (use real SCRYPT stats)
                - End with a clear CTA to book a call at scrypt.swiss
                - Institutional tone — no emojis, no retail language, no hype words
                - Maximum 280 words",

            'twitter'  => "Write a 5-tweet thread for SCRYPT on X (Twitter) targeting institutional crypto 
                professionals.

                Topic: Why institutional DeFi is now a reality — not a concept. Focus on what SCRYPT 
                + Gauntlet have solved (regulatory uncertainty, infrastructure complexity, risk opacity).

                Requirements:
                - Tweet 1: Bold hook that stops the scroll
                - Tweets 2-4: One insight per tweet, data-driven
                - Tweet 5: CTA to scrypt.swiss
                - Each tweet under 280 characters
                - No hashtag spam — maximum 2 hashtags in the entire thread
                - Institutional, sharp tone",

            'email'    => "Write an institutional email newsletter for SCRYPT's weekly yield strategy update.

                Topic: Institutional DeFi yield — what it is, why now, and how SCRYPT + Gauntlet 
                deliver it compliantly. Target audience: CFOs, treasurers, and portfolio managers 
                at banks, hedge funds, and family offices.

                Requirements:
                - Subject line included
                - 400-500 words
                - Structure: context → problem → SCRYPT solution → data → CTA
                - Authoritative, no-fluff tone
                - CTA: download the strategy deck or book a call",

            default    => "Write a LinkedIn post for SCRYPT about institutional crypto adoption in 2026.",
        };
    }
}