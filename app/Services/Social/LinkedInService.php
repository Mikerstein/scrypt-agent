<?php
namespace App\Services\Social;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class LinkedInService
{
    private string $accessToken;
    private string $personId;

    public function __construct()
    {
        $this->accessToken = config('services.linkedin.access_token', '');
        $this->personId    = config('services.linkedin.person_id', '');
    }

    public function publish(string $content): string
    {
        if (empty($this->accessToken)) {
            Log::info('[LinkedIn MOCK] Would post: ' . substr($content, 0, 80) . '...');
            return 'mock_linkedin_' . time();
        }

        $response = Http::withToken($this->accessToken)
            ->post('https://api.linkedin.com/v2/ugcPosts', [
                'author'          => "urn:li:person:{$this->personId}",
                'lifecycleState'  => 'PUBLISHED',
                'specificContent' => [
                    'com.linkedin.ugc.ShareContent' => [
                        'shareCommentary'  => ['text' => $content],
                        'shareMediaCategory' => 'NONE',
                    ],
                ],
                'visibility' => [
                    'com.linkedin.ugc.MemberNetworkVisibility' => 'PUBLIC',
                ],
            ]);

        if ($response->failed()) {
            throw new \Exception('LinkedIn API error: ' . $response->body());
        }

        return $response->json('id');
    }
}