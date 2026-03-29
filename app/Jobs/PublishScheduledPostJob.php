<?php
namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\ScheduledPost;
use App\Services\Social\TwitterService;
use App\Services\Social\LinkedInService;
use Illuminate\Support\Facades\Log;

class PublishScheduledPostJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries   = 3;
    public int $timeout = 60;

    public function __construct(public readonly int $scheduledPostId) {}

    public function handle(): void
    {
        $post = ScheduledPost::with('contentItem')->find($this->scheduledPostId);

        if (!$post || $post->status !== 'pending') {
            return;
        }

        $content = $post->contentItem->content;

        try {
            if ($post->platform === 'email') {
                // In production, we would map to a dynamic subscriber list
                \Illuminate\Support\Facades\Mail::to('institutions@scrypt.swiss')
                    ->send(new \App\Mail\NewsletterEmail($content));
                $platformPostId = 'email-' . uniqid();
            } else {
                $platformPostId = match($post->platform) {
                    'twitter'  => (new TwitterService())->publish($content),
                    'linkedin' => (new LinkedInService())->publish($content),
                    default    => null,
                };
            }

            $post->update([
                'status'           => 'published',
                'published_at'     => now(),
                'platform_post_id' => $platformPostId,
            ]);

            $post->contentItem->update(['status' => 'published']);

            Log::info("Published post ID {$post->id} to {$post->platform}");

        } catch (\Exception $e) {
            $post->update([
                'status'        => 'failed',
                'error_message' => $e->getMessage(),
            ]);
            Log::error("Failed to publish post ID {$post->id}: " . $e->getMessage());
            throw $e;
        }
    }
}