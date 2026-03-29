<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OptimizationRule extends Model
{
    protected $fillable = [
        'rule_type', 'platform', 'instruction',
        'weight', 'is_active', 'source',
        'confidence_score', 'evidence'
    ];

    protected $casts = [
        'is_active'        => 'boolean',
        'confidence_score' => 'float',
    ];

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeForPlatform($query, string $platform)
    {
        return $query->where(function ($q) use ($platform) {
            $q->where('platform', $platform)
              ->orWhere('platform', 'all')
              ->orWhereNull('platform');
        });
    }

    public function scopeByWeight($query)
    {
        return $query->orderByDesc('weight')->orderByDesc('confidence_score');
    }
}