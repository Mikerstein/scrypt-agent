<?php
namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\Content\ContentOptimizationService;
use App\Models\OptimizationRule;

class TestOptimizationEngine extends Command
{
    protected $signature   = 'scrypt:test-optimization {--provider=groq}';
    protected $description = 'Test the self-improvement optimization engine';

    public function handle(): void
    {
        $this->info('🧠 Testing SCRYPT Optimization Engine...');
        $this->newLine();

        $optimizer = new ContentOptimizationService();

        // Show current rules
        $rules = OptimizationRule::active()->byWeight()->get();
        $this->info('📋 Current active optimization rules (' . $rules->count() . '):');
        $this->table(
            ['Type', 'Platform', 'Weight', 'Source', 'Confidence', 'Instruction'],
            $rules->map(fn($r) => [
                $r->rule_type,
                $r->platform ?? 'all',
                $r->weight,
                $r->source,
                $r->confidence_score,
                \Illuminate\Support\Str::limit($r->instruction, 60),
            ])->toArray()
        );

        $this->newLine();
        $this->info('📊 Analysing performance data...');

        $result = $optimizer->analysePerformance($this->option('provider'));

        if ($result['analysis']) {
            $this->newLine();
            $this->info('🤖 AI Optimization Analysis:');
            $this->line('─────────────────────────────────────────');
            $this->line($result['analysis']);
            $this->line('─────────────────────────────────────────');

            $this->newLine();
            $this->info('💾 Parsing and saving new rules...');
            $saved = $optimizer->parseAndSaveRules($result['analysis']);
            $this->info("Saved {$saved} new optimization rules.");

            if ($saved > 0) {
                $newRules = OptimizationRule::where('source', 'ai_generated')
                    ->latest()->take($saved)->get();

                $this->newLine();
                $this->info('✨ New rules added:');
                $this->table(
                    ['Type', 'Platform', 'Instruction'],
                    $newRules->map(fn($r) => [
                        $r->rule_type,
                        $r->platform,
                        \Illuminate\Support\Str::limit($r->instruction, 80),
                    ])->toArray()
                );
            }
        }

        $this->newLine();
        $totalRules = OptimizationRule::active()->count();
        $this->info("✅ Optimization engine working. {$totalRules} total active rules.");
        $this->info('   Every content generation job now uses these rules automatically.');
    }
}