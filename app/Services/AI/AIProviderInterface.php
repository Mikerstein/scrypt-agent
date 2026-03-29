<?php
namespace App\Services\AI;

interface AIProviderInterface
{
    public function generate(string $prompt, int $maxTokens = 1000): string;
    public function getProviderName(): string;
    public function getModel(): string;
}