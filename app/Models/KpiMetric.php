<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class KpiMetric extends Model {
    protected $fillable = ['metric_type', 'platform', 'value', 'recorded_date', 'period'];

    protected $casts = ['recorded_date' => 'date'];
}