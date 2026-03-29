<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MarketInsight extends Model
{
    protected $fillable = [
        'headline', 'summary', 'source', 'source_url',
        'category', 'relevance_score', 'used_in_content', 'published_at'
    ];

    protected $casts = [
        'published_at'     => 'datetime',
        'used_in_content'  => 'boolean',
    ];

    public function scopeRecent($query, int $hours = 24)
    {
        return $query->where('created_at', '>=', now()->subHours($hours));
    }

    public function scopeTopRelevant($query, int $limit = 5)
    {
        return $query->orderByDesc('relevance_score')->limit($limit);
    }
}