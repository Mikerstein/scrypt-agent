<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Lead extends Model {
    protected $fillable = [
        'name', 'email', 'company', 'title', 'segment',
        'source', 'source_content_id', 'status', 'notes'
    ];

    public function activities() {
        return $this->hasMany(LeadActivity::class);
    }
}