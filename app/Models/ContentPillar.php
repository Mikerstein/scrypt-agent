<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class ContentPillar extends Model {
    protected $fillable =
     [
        'name', 
        'slug', 
        'day_of_week',
         'description', 
         'primary_cta',
          'is_active'
          ];

    public function contentItems() {
        return $this->hasMany(ContentItem::class);
    }
}