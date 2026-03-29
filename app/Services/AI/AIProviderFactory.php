<?php
namespace App\Services\AI;

class AIProviderFactory
{
    public static function make(?string $provider = null): AIProviderInterface
    {
        $provider = $provider ?? config('ai.default');

      return match($provider) {
    'anthropic' => new AnthropicService(),
    'openai'    => new OpenAIService(),
    'groq'      => new GroqService(),
    'gemini'    => new GeminiService(),
    default     => throw new \InvalidArgumentException("Unknown AI provider: {$provider}"),
};
    }
}