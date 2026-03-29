<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class ContentItem extends Model {
    protected $fillable = [
        'content_pillar_id', 'type', 'ai_provider', 'ai_model',
        'prompt_used', 'content', 'status', 'performance_score',
        'likes', 'comments', 'shares', 'impressions', 'clicks'
    ];

    public function pillar() {
        return $this->belongsTo(ContentPillar::class, 'content_pillar_id');
    }

    public function scheduledPosts() {
        return $this->hasMany(ScheduledPost::class);
    }
}