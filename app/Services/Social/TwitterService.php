<?php
namespace App\Services\Social;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class TwitterService
{
    private string $bearerToken;
    private string $apiKey;
    private string $apiSecret;
    private string $accessToken;
    private string $accessSecret;

    public function __construct()
    {
        $this->bearerToken  = config('services.twitter.bearer_token', '');
        $this->apiKey       = config('services.twitter.client_id', '');
        $this->apiSecret    = config('services.twitter.client_secret', '');
        $this->accessToken  = config('services.twitter.access_token', '');
        $this->accessSecret = config('services.twitter.access_secret', '');
    }

    public function publish(string $content): string
    {
        // Extract first tweet for single post, or post thread
        $tweets = $this->parseThread($content);

        if (empty($this->accessToken)) {
            Log::info('[Twitter MOCK] Would post: ' . substr($tweets[0], 0, 80) . '...');
            return 'mock_tweet_' . time();
        }

        // Post first tweet
        $response = Http::withToken($this->bearerToken)
            ->post('https://api.twitter.com/2/tweets', [
                'text' => $tweets[0],
            ]);

        if ($response->failed()) {
            throw new \Exception('Twitter API error: ' . $response->body());
        }

        return $response->json('data.id');
    }

    private function parseThread(string $content): array
    {
        // Split on "Tweet N:" or numbered lines
        $lines = preg_split('/Tweet\s*\d+[:.]?\s*/i', $content);
        $tweets = array_filter(array_map('trim', $lines));
        return array_values($tweets) ?: [substr($content, 0, 280)];
    }

    public function publishReply(string $content, string $inReplyToId): string
{
    if (empty($this->accessToken)) {
        Log::info('[Twitter MOCK] Would reply to ' . $inReplyToId . ': ' . substr($content, 0, 60) . '...');
        return 'mock_reply_' . time();
    }

    $response = Http::withToken($this->bearerToken)
        ->post('https://api.twitter.com/2/tweets', [
            'text'  => $content,
            'reply' => ['in_reply_to_tweet_id' => $inReplyToId],
        ]);

    if ($response->failed()) {
        throw new \Exception('Twitter reply failed: ' . $response->body());
    }

    return $response->json('data.id');
}
}