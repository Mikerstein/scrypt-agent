<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class AiProvider extends Model {
    protected $fillable = [
        'name', 
        'model', 
        'is_active', 
        'tokens_used', 
        'requests_made'
        ];
}