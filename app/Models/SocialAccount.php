<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class SocialAccount extends Model {
    protected $fillable = [
        'platform', 'account_name', 'account_id',
        'access_token', 'refresh_token', 'token_expires_at', 'is_active'
    ];

    protected $hidden = ['access_token', 'refresh_token'];

    protected $casts = ['token_expires_at' => 'datetime'];
}