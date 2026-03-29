<?php
namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\Social\SocialListeningService;
use App\Models\SocialEngagement;

class TestEngagementEngine extends Command
{
    protected $signature   = 'scrypt:test-engagement {--provider=groq}';
    protected $description = 'Test the autonomous engagement engine';

    public function handle(): void
    {
        $this->info('💬 Testing SCRYPT Engagement Engine...');
        $this->newLine();

        $listener = new SocialListeningService();

        $this->info('🔍 Scanning for relevant posts (mock mode)...');
        $posts    = $listener->getMockPosts('twitter');
        $filtered = array_values($listener->filterRelevant($posts));

        $this->info('Found ' . count($filtered) . ' relevant posts after filtering.');
        $this->newLine();

        foreach ($filtered as $i => $post) {
            $score = $listener->scoreRelevance($post['original_post']);
            $this->line("Post " . ($i+1) . " | Score: {$score} | {$post['author_handle']}");
            $this->line("  " . \Illuminate\Support\Str::limit($post['original_post'], 90));

            if ($score < 3) {
                $this->line("  → Skipped (low relevance)");
                continue;
            }

            $this->line("  → Generating reply...");
            $reply = $listener->generateReply($post, $this->option('provider'));

            if ($reply) {
                $this->line("  → Reply: " . \Illuminate\Support\Str::limit($reply, 120));

                if (!$listener->isDuplicate($post['platform_post_id'])) {
                    SocialEngagement::create([
                        'platform'           => $post['platform'],
                        'platform_post_id'   => $post['platform_post_id'],
                        'platform_post_url'  => $post['platform_post_url'],
                        'author_handle'      => $post['author_handle'],
                        'author_name'        => $post['author_name'],
                        'original_post'      => $post['original_post'],
                        'generated_reply'    => $reply,
                        'keyword_matched'    => $post['keyword_matched'],
                        'relevance_score'    => $score,
                        'status'             => 'pending_review',
                        'original_posted_at' => $post['original_posted_at'],
                    ]);
                    $this->info("  → Saved for review.");
                } else {
                    $this->line("  → Duplicate, skipped.");
                }
            }
            $this->newLine();
        }

        $total = SocialEngagement::pendingReview()->count();
        $this->newLine();
        $this->info("✅ Engagement engine working.");
        $this->info("   {$total} replies pending review at: /engagement");
    }
}