<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class LeadActivity extends Model {
    protected $fillable = ['lead_id', 'type', 'description', 'occurred_at'];

    protected $casts = ['occurred_at' => 'datetime'];

    public function lead() {
        return $this->belongsTo(Lead::class);
    }
}