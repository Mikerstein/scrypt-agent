<?php
namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Services\Social\SocialListeningService;
use App\Models\SocialEngagement;
use Illuminate\Support\Facades\Log;

class AutonomousEngagementJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries   = 2;
    public int $timeout = 120;

    public function __construct(public readonly string $provider = 'groq') {}

    public function handle(): void
    {
        Log::info('Starting autonomous engagement scan...');

        $listener = new SocialListeningService();

        // Search Twitter (real or mock)
        $rawPosts = $listener->searchTwitter();

        // Filter out retail noise
        $filtered = $listener->filterRelevant($rawPosts);

        $queued = 0;

        foreach ($filtered as $post) {

            // Skip duplicates
            if ($listener->isDuplicate($post['platform_post_id'])) {
                continue;
            }

            // Score relevance
            $score = $listener->scoreRelevance($post['original_post']);

            // Only engage with sufficiently relevant posts
            if ($score < 3) {
                Log::info("Skipping low-relevance post (score: {$score}): " .
                    substr($post['original_post'], 0, 60));
                continue;
            }

            // Generate reply
            $reply = $listener->generateReply($post, $this->provider);

            if (!$reply) continue;

            // Save for human review — NEVER auto-publish
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

            $queued++;
            Log::info("Queued reply for review: {$post['author_handle']}");
        }

        Log::info("Engagement scan complete. {$queued} replies queued for review.");
    }
}