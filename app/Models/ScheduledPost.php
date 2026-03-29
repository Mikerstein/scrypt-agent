<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class ScheduledPost extends Model {
    protected $fillable = [
        'content_item_id', 'platform', 'scheduled_at',
        'published_at', 'status', 'error_message', 'platform_post_id'
    ];

    protected $casts = [
        'scheduled_at'  => 'datetime',
        'published_at'  => 'datetime',
    ];

    public function contentItem() {
        return $this->belongsTo(ContentItem::class);
    }
}