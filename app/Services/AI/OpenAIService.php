<?php
namespace App\Services\AI;

use Illuminate\Support\Facades\Http;
use App\Models\AiProvider;

class OpenAIService implements AIProviderInterface
{
    private string $apiKey;
    private string $model;
    private string $baseUrl;

    public function __construct()
    {
        $this->apiKey  = config('ai.providers.openai.api_key');
        $this->model   = config('ai.providers.openai.model');
        $this->baseUrl = config('ai.providers.openai.base_url');
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
            throw new \Exception('OpenAI API error: ' . $response->body());
        }

        AiProvider::where('name', 'openai')->increment('requests_made');

        return $response->json('choices.0.message.content');
    }

    public function getProviderName(): string { return 'openai'; }
    public function getModel(): string { return $this->model; }
}