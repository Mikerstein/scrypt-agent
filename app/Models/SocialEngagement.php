<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SocialEngagement extends Model
{
    protected $fillable = [
        'platform', 'platform_post_id', 'platform_post_url',
        'author_handle', 'author_name', 'original_post',
        'generated_reply', 'keyword_matched', 'relevance_score',
        'status', 'reply_platform_id', 'original_posted_at',
        'published_at', 'rejection_reason',
    ];

    protected $casts = [
        'original_posted_at' => 'datetime',
        'published_at'       => 'datetime',
    ];

    public function scopePendingReview($query)
    {
        return $query->where('status', 'pending_review');
    }

    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    public function scopeByPlatform($query, string $platform)
    {
        return $query->where('platform', $platform);
    }
}