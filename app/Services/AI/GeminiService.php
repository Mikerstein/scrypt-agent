<?php
namespace App\Services\AI;

use Illuminate\Support\Facades\Http;
use App\Models\AiProvider;

class GeminiService implements AIProviderInterface
{
    private string $apiKey;
    private string $model;
    private string $baseUrl;

    public function __construct()
    {
        $this->apiKey  = config('ai.providers.gemini.api_key');
        $this->model   = config('ai.providers.gemini.model');
        $this->baseUrl = config('ai.providers.gemini.base_url');
    }

    public function generate(string $prompt, int $maxTokens = 1000): string
    {
        $systemPrompt = config('ai.scrypt_context');

        $response = Http::withHeaders([
            'Content-Type' => 'application/json',
        ])->post("{$this->baseUrl}/models/{$this->model}:generateContent?key={$this->apiKey}", [
            'system_instruction' => [
                'parts' => [['text' => $systemPrompt]],
            ],
            'contents' => [
                [
                    'role'  => 'user',
                    'parts' => [['text' => $prompt]],
                ],
            ],
            'generationConfig' => [
                'maxOutputTokens' => $maxTokens,
                'temperature'     => 0.7,
            ],
        ]);

        if ($response->failed()) {
            throw new \Exception('Gemini API error: ' . $response->body());
        }

        AiProvider::where('name', 'gemini')->increment('requests_made');

        return $response->json('candidates.0.content.parts.0.text');
    }

    public function getProviderName(): string { return 'gemini'; }
    public function getModel(): string { return $this->model; }
}