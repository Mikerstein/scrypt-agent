<?php
namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\SocialEngagement;
use App\Services\Social\TwitterService;
use App\Services\Social\LinkedInService;
use Illuminate\Support\Facades\Log;

class PublishEngagementJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries   = 3;
    public int $timeout = 60;

    public function __construct(public readonly int $engagementId) {}

    public function handle(): void
    {
        $engagement = SocialEngagement::find($this->engagementId);

        if (!$engagement || $engagement->status !== 'approved') {
            Log::warning("Engagement {$this->engagementId} not found or not approved.");
            return;
        }

        try {
            $replyId = match($engagement->platform) {
                'twitter'  => (new TwitterService())->publishReply(
                    $engagement->generated_reply,
                    $engagement->platform_post_id
                ),
                'linkedin' => (new LinkedInService())->publish(
                    $engagement->generated_reply
                ),
                default    => 'mock_reply_' . time(),
            };

            $engagement->update([
                'status'          => 'published',
                'reply_platform_id' => $replyId,
                'published_at'    => now(),
            ]);

            Log::info("Published reply to {$engagement->platform} post {$engagement->platform_post_id}");

        } catch (\Exception $e) {
            $engagement->update(['status' => 'failed']);
            Log::error("Failed to publish engagement {$this->engagementId}: " . $e->getMessage());
            throw $e;
        }
    }
}