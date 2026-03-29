<?php
namespace App\Services\AI;

use Illuminate\Support\Facades\Http;
use App\Models\AiProvider;

class GroqService implements AIProviderInterface
{
    private string $apiKey;
    private string $model;
    private string $baseUrl;

    public function __construct()
    {
        $this->apiKey  = config('ai.providers.groq.api_key');
        $this->model   = config('ai.providers.groq.model');
        $this->baseUrl = config('ai.providers.groq.base_url');
    }

    public function generate(string $prompt, int $maxTokens = 1000): string
    {
        $systemPrompt = config('ai.scrypt_context');

        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $this->apiKey,
            'Content-Type'  => 'application/json',
        ])->post($this->baseUrl . '/chat/completions', [
            'model'      => $this->model,
            'max_tokens' => $maxTokens,
            'messages'   => [
                ['role' => 'system', 'content' => $systemPrompt],
                ['role' => 'user',   'content' => $prompt],
            ],
        ]);

        if ($response->failed()) {
            throw new \Exception('Groq API error: ' . $response->body());
        }

        $content = $response->json('choices.0.message.content');

        // Track usage in DB
        AiProvider::where('name', 'groq')->increment('requests_made');

        return $content;
    }

    public function getProviderName(): string { return 'groq'; }
    public function getModel(): string { return $this->model; }
}