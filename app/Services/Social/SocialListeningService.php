<?php
namespace App\Services\Social;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Models\SocialEngagement;
use App\Services\AI\AIProviderFactory;

class SocialListeningService
{
    private array $keywords = [
        'institutional DeFi',
        'crypto custody',
        'FINMA crypto',
        'institutional crypto yield',
        'MiCA compliance',
        'crypto hedge fund',
        'bitcoin ETF institutional',
        'regulated DeFi',
        'institutional staking',
        'crypto asset manager',
    ];

    private array $excludeKeywords = [
        'giveaway', 'airdrop', 'pump', 'moon', 'lambo',
        'presale', 'ICO', 'rugpull', 'shitcoin', 'meme coin',
    ];

    public function searchTwitter(): array
    {
        $bearerToken = config('services.twitter.bearer_token');

        if (empty($bearerToken)) {
            Log::info('[Twitter Listening MOCK] No bearer token — returning mock posts');
            return $this->getMockPosts('twitter');
        }

        $results = [];

        foreach (array_slice($this->keywords, 0, 3) as $keyword) {
            try {
                $response = Http::withToken($bearerToken)
                    ->get('https://api.twitter.com/2/tweets/search/recent', [
                        'query'        => $keyword . ' -is:retweet lang:en',
                        'max_results'  => 10,
                        'tweet.fields' => 'created_at,author_id,public_metrics',
                        'expansions'   => 'author_id',
                        'user.fields'  => 'username,name',
                    ]);

                if ($response->successful()) {
                    $tweets = $response->json('data', []);
                    $users  = collect($response->json('includes.users', []))
                        ->keyBy('id');

                    foreach ($tweets as $tweet) {
                        $author = $users->get($tweet['author_id'], []);
                        $results[] = [
                            'platform'           => 'twitter',
                            'platform_post_id'   => $tweet['id'],
                            'platform_post_url'  => 'https://twitter.com/i/web/status/' . $tweet['id'],
                            'author_handle'      => '@' . ($author['username'] ?? 'unknown'),
                            'author_name'        => $author['name'] ?? 'Unknown',
                            'original_post'      => $tweet['text'],
                            'keyword_matched'    => $keyword,
                            'original_posted_at' => $tweet['created_at'] ?? now(),
                        ];
                    }
                }
            } catch (\Exception $e) {
                Log::error("Twitter search failed for '{$keyword}': " . $e->getMessage());
            }
        }

        return $results;
    }

    public function getMockPosts(string $platform): array
    {
        return [
            [
                'platform'           => $platform,
                'platform_post_id'   => 'mock_' . time() . '_1',
                'platform_post_url'  => 'https://twitter.com/mock/1',
                'author_handle'      => '@institutional_defi',
                'author_name'        => 'DeFi Research Desk',
                'original_post'      => 'Institutional DeFi adoption is accelerating but regulatory clarity remains the #1 blocker for European asset managers. FINMA\'s framework is one of the few that actually works.',
                'keyword_matched'    => 'institutional DeFi',
                'original_posted_at' => now()->subHours(2),
            ],
            [
                'platform'           => $platform,
                'platform_post_id'   => 'mock_' . time() . '_2',
                'platform_post_url'  => 'https://twitter.com/mock/2',
                'author_handle'      => '@cryptocompliance',
                'author_name'        => 'Crypto Compliance Weekly',
                'original_post'      => 'MiCA is forcing institutions to choose regulated crypto counterparties carefully. The days of trading on unregulated venues are numbered for serious asset managers.',
                'keyword_matched'    => 'MiCA compliance',
                'original_posted_at' => now()->subHours(4),
            ],
            [
                'platform'           => $platform,
                'platform_post_id'   => 'mock_' . time() . '_3',
                'platform_post_url'  => 'https://twitter.com/mock/3',
                'author_handle'      => '@hedgefundcrypto',
                'author_name'        => 'Hedge Fund Alpha',
                'original_post'      => 'Our fund is actively looking for a regulated crypto execution partner with proper custody. Most platforms fail on compliance. Who are people actually using?',
                'keyword_matched'    => 'crypto hedge fund',
                'original_posted_at' => now()->subHours(1),
            ],
            [
                'platform'           => $platform,
                'platform_post_id'   => 'mock_' . time() . '_4',
                'platform_post_url'  => 'https://twitter.com/mock/4',
                'author_handle'      => '@defi_yield_desk',
                'author_name'        => 'Yield Strategy Desk',
                'original_post'      => 'DeFi yield on stablecoins is genuinely attractive at 4-12% annualised but the compliance wrapper question for institutions remains unsolved for most providers.',
                'keyword_matched'    => 'institutional crypto yield',
                'original_posted_at' => now()->subMinutes(90),
            ],
        ];
    }

    public function filterRelevant(array $posts): array
    {
        return array_filter($posts, function ($post) {
            $text = strtolower($post['original_post']);

            foreach ($this->excludeKeywords as $exclude) {
                if (str_contains($text, strtolower($exclude))) {
                    return false;
                }
            }

            return true;
        });
    }

    public function scoreRelevance(string $text): int
    {
        $text  = strtolower($text);
        $score = 0;

        $highValue = ['institutional', 'regulated', 'finma', 'mica', 'compliance',
                      'custody', 'asset manager', 'hedge fund', 'family office'];
        $medValue  = ['defi', 'yield', 'staking', 'crypto', 'bitcoin', 'stablecoin'];

        foreach ($highValue as $kw) {
            if (str_contains($text, $kw)) $score += 3;
        }
        foreach ($medValue as $kw) {
            if (str_contains($text, $kw)) $score += 1;
        }

        return $score;
    }

    public function generateReply(array $post, string $provider = 'groq'): ?string
    {
        $prompt = "You are the social media voice for SCRYPT (scrypt.swiss), 
            Switzerland's leading FINMA-licensed institutional crypto execution platform.

            Someone posted this on {$post['platform']}:
            Author: {$post['author_name']} ({$post['author_handle']})
            Post: \"{$post['original_post']}\"
            Keyword context: {$post['keyword_matched']}

            Write a single authoritative reply that:
            1. Adds genuine value — a specific insight, data point, or perspective
            2. Is relevant to SCRYPT's expertise (FINMA licence, DeFi via Gauntlet, \$25B+ volume, 
               300+ institutional clients, Swiss regulatory framework)
            3. Feels like it comes from a knowledgeable insider, not a brand account
            4. Under 240 characters for Twitter, under 500 for LinkedIn
            5. Does NOT start with 'Great post!' or any sycophantic opener
            6. May naturally reference scrypt.swiss only if it fits — never forced
            7. Institutional tone — no emojis, no hype

            Reply only with the text of the reply. Nothing else.";

        try {
            $ai = AIProviderFactory::make($provider);
            return $ai->generate($prompt, 300);
        } catch (\Exception $e) {
            Log::error('Reply generation failed: ' . $e->getMessage());
            return null;
        }
    }

    public function isDuplicate(string $platformPostId): bool
    {
        return SocialEngagement::where('platform_post_id', $platformPostId)->exists();
    }
}